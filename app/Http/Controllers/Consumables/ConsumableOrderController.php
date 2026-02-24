<?php

namespace App\Http\Controllers\Consumables;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\ConsumableOrder;
use App\Models\ConsumableOrderDetail;
use App\Models\Consumable;
use App\Models\Department;
use App\Models\ConsumablesUsers;
use App\Models\Actionlog;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Barryvdh\DomPDF\Facade\Pdf;


class ConsumableOrderController extends Controller
{
    /**
     * Tampilkan daftar request consumable per user login
     */
    public function index(Request $request)
    {
        return view('consumable.orders.index');
    }

    /**
     * Form create request
     */
    private function generateNoReq()
    {
        $today = Carbon::now()->format('Ymd'); // Format tanggal (20251101)

        // Cari nomor terakhir berdasarkan tanggal hari ini
        $lastOrder = ConsumableOrder::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && preg_match('/(\d{3})$/', $lastOrder->no_req, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        // Format jadi 3 digit (001, 002, dst)
        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);

        // Hasil akhir: REQ/GA/20251101/001
        return 'REQ/GA/' . $today . '/' . $sequenceFormatted;
    }

    public function create()
    {
        $user = Auth::user();
        $consumables = Consumable::whereNull('deleted_at')->get();

        return view('consumable.orders.create', [
            'consumables' => $consumables,
            'noReq'       => $this->generateNoReq(), 
            'user'        => $user,
        ]);
    }

   public function store(Request $request)
    {
        $request->validate([
            'no_req'                => 'required',
            'user_id'               => 'required',
            'department_id'         => 'required',
            'items'                 => 'required|array|min:1',
            'items.*.consumable_id' => 'required|exists:consumables,id',
            'items.*.qty'           => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {

            // ===========================
            // HEADER ORDER
            // ===========================
            $order = ConsumableOrder::create([
                'no_req'        => $this->generateNoReq(),
                'user_id'       => $request->user_id,
                'department_id' => $request->department_id,
                'notes'         => $request->notes,
                'status'        => $request->action === 'submit' ? 'submitted' : 'draft',
            ]);

            // ===========================
            // LOOP ITEMS
            // ===========================
            foreach ($request->items as $item) {

                // 🔒 Lock row consumable
                $consumable = Consumable::lockForUpdate()
                    ->findOrFail($item['consumable_id']);

                $qty = (int) $item['qty'];

                // ===========================
                // VALIDASI STOK
                // ===========================
                if ($request->action === 'submit') {

                    if ($consumable->qty <= 0) {
                        throw new \Exception(
                            "Stok item {$consumable->name} sudah habis."
                        );
                    }

                    if ($qty > $consumable->qty) {
                        throw new \Exception(
                            "Stok item {$consumable->name} tidak mencukupi. 
                            Sisa stok: {$consumable->qty}"
                        );
                    }

                    // ===========================
                    // KURANGI STOK
                    // ===========================
                    $consumable->qty -= $qty;

                    // DOUBLE GUARD (anti minus)
                    if ($consumable->qty < 0) {
                        throw new \Exception(
                            "Stok item {$consumable->name} menjadi minus. 
                            Transaksi dibatalkan."
                        );
                    }

                    $consumable->save();

                    // ===========================
                    // SIMPAN KE consumables_users
                    // ===========================
                    ConsumablesUsers::create([
                        'created_by'    => Auth::id(),
                        'consumable_id' => $consumable->id,
                        'assigned_to'   => $request->user_id,
                        'note'          => $request->notes ?? '-',
                    ]);

                    // ===========================
                    // ACTION LOG
                    // ===========================
                    ActionLog::create([
                        'created_by'    => Auth::id(),
                        'action_type'   => 'checkout',
                        'target_id'     => $request->user_id,
                        'target_type'   => 'App\\Models\\User',
                        'note'          => $request->notes ?? 'Request consumable submitted',
                        'item_type'     => 'App\\Models\\Consumable',
                        'item_id'       => $consumable->id,
                        'action_date'   => now(),
                        'action_source' => 'gui',
                        'remote_ip'     => $request->ip(),
                        'user_agent'    => $request->header('User-Agent'),
                    ]);
                }

                // ===========================
                // DETAIL ORDER
                // ===========================
                ConsumableOrderDetail::create([
                    'consumable_order_id' => $order->id,
                    'no_req'              => $order->no_req,
                    'consumable_id'       => $consumable->id,
                    'category_id'         => $consumable->category_id,
                    'qty'                 => $qty,
                    'status'              => $request->action === 'submit' ? 'checkout' : 'draft',
                    'user_id'             => Auth::id(),
                ]);
            }

            DB::commit();

            // ===========================
            // EMAIL
            // ===========================
            if ($request->action === 'submit') {
                try {
                    Mail::to('ga@vasanta.co.id')
                        ->cc($order->user->email)
                        ->send(new \App\Mail\ConsumableOrderSubmittedMail($order));
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim email: ' . $e->getMessage());
                }
            }

            return redirect()
                ->route('consumable.orders.index')
                ->with(
                    'success',
                    $request->action === 'submit'
                        ? 'Request berhasil disubmit dan stok consumable telah diperbarui.'
                        : 'Draft berhasil disimpan.'
                );

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    /**
     * Show detail order
     */
    public function show($id)
    {
        $order = ConsumableOrder::with(['user', 'details.consumable', 'details.category'])->findOrFail($id);
        return view('consumable.orders.show', compact('order'));
    }

    /**
     * Form edit untuk status draft
     */
    public function edit($id)
    {
        $order = ConsumableOrder::with('details.consumable')->findOrFail($id);

        if ($order->status !== 'draft') {
            return redirect()->route('consumable.orders.index')->with('error', 'Order tidak dapat diedit karena status bukan draft.');
        }

        $consumables = Consumable::whereNull('deleted_at')->where('qty', '>', 0)->get();
        return view('consumable.orders.edit', compact('order', 'consumables'));
    }

    /**
     * Update data order (draft/submit)
     */
    public function update(Request $request, $id)
    {
        $order = ConsumableOrder::with('details')->findOrFail($id);

        DB::beginTransaction();

        try {

            // ===========================
            // JIKA ORDER SUDAH SUBMIT → KEMBALIKAN STOK LAMA
            // ===========================
            if ($order->status === 'submitted') {
                foreach ($order->details as $oldDetail) {
                    $consumable = Consumable::lockForUpdate()
                        ->find($oldDetail->consumable_id);

                    if ($consumable) {
                        $consumable->increment('qty', $oldDetail->qty);
                    }
                }
            }

            // ===========================
            // UPDATE HEADER
            // ===========================
            $order->update([
                'notes'  => $request->notes,
                'status' => $request->action === 'submit' ? 'submitted' : 'draft',
            ]);

            // Hapus detail lama
            $order->details()->delete();

            // ===========================
            // LOOP DETAIL BARU
            // ===========================
            foreach ($request->details as $item) {

                if (empty($item['consumable_id']) || empty($item['qty'])) {
                    throw new \Exception('Item dan Qty harus diisi.');
                }

                $qty = (int) $item['qty'];

                $consumable = Consumable::lockForUpdate()
                    ->findOrFail($item['consumable_id']);

                // ===========================
                // JIKA SUBMIT → VALIDASI & POTONG STOK
                // ===========================
                if ($request->action === 'submit') {

                    if ($qty > $consumable->qty) {
                        throw new \Exception(
                            "Stok item {$consumable->name} tidak mencukupi. 
                            Sisa stok: {$consumable->qty}"
                        );
                    }

                    // Kurangi stok
                    $consumable->qty -= $qty;

                    if ($consumable->qty < 0) {
                        throw new \Exception(
                            "Stok item {$consumable->name} menjadi minus."
                        );
                    }

                    $consumable->save();

                    // Simpan ke consumables_users
                    ConsumablesUsers::create([
                        'created_by'    => Auth::id(),
                        'consumable_id' => $consumable->id,
                        'assigned_to'   => $order->user_id,
                        'note'          => $request->notes ?? 'Update & submit consumable order',
                    ]);

                    // Action log
                    ActionLog::create([
                        'created_by'    => Auth::id(),
                        'action_type'   => 'checkout',
                        'target_id'     => $order->user_id,
                        'target_type'   => 'App\\Models\\User',
                        'note'          => $request->notes ?? 'Consumable order updated & submitted',
                        'item_type'     => 'App\\Models\\Consumable',
                        'item_id'       => $consumable->id,
                        'action_date'   => now(),
                        'action_source' => 'gui',
                        'remote_ip'     => $request->ip(),
                        'user_agent'    => $request->header('User-Agent'),
                    ]);
                }

                // ===========================
                // SIMPAN DETAIL
                // ===========================
                ConsumableOrderDetail::create([
                    'consumable_order_id' => $order->id,
                    'no_req'              => $order->no_req,
                    'consumable_id'       => $consumable->id,
                    'category_id'         => $consumable->category_id,
                    'qty'                 => $qty,
                    'status'              => $request->action === 'submit' ? 'checkout' : 'draft',
                    'user_id'             => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('consumable.orders.index')
                ->with(
                    'success',
                    $request->action === 'submit'
                        ? 'Request berhasil disubmit dan stok diperbarui.'
                        : 'Draft berhasil diperbarui.'
                );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function print($id)
    {
        $logoPath = public_path('img/logo-vasanta.png');
        $logoBase64 = null;

        if (file_exists($logoPath)) {
             $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $order = ConsumableOrder::with('details', 'user', 'department')->findOrFail($id);
        $pdf = Pdf::loadView('pdf.consumable-order', [
            'order' => $order,
            'logo' =>  $logoBase64
        ]);

        if ($order->status !== 'submitted') {
            abort(403, 'Only submitted orders can be printed.');
        }

        //$pdf = Pdf::loadView('pdf.consumable-order', compact('order'));

        return $pdf->stream('consumable-order-'.$order->no_req.'.pdf');
    }
}
