<?php

namespace App\Http\Controllers\Consumables;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConsumableOrder;
use App\Models\ConsumableOrderDetail;
use App\Models\Consumable; // untuk ambil stok consumable

class ConsumableOrderDetailController extends Controller
{
    /**
     * Tampilkan daftar item detail dalam satu order
     */
    public function index($order_id)
    {
        $order = ConsumableOrder::with('details.consumable')->findOrFail($order_id);

        return view('consumable_orders.details.index', compact('order'));
    }

    /**
     * Form tambah item request
     */
    public function create($order_id)
    {
        $order = ConsumableOrder::findOrFail($order_id);
        $consumables = Consumable::all(); // daftar stok yang bisa dipilih

        return view('consumable_orders.details.create', compact('order', 'consumables'));
    }

    /**
     * Simpan item request
     */
    public function store(Request $request, $order_id)
    {
        $request->validate([
            'consumable_id' => 'required|exists:consumables,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = ConsumableOrder::findOrFail($order_id);

        // Simpan detail
        ConsumableOrderDetail::create([
            'order_id' => $order->id,
            'consumable_id' => $request->consumable_id,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('consumable-orders.details.index', $order->id)
            ->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Edit item detail
     */
    public function edit($order_id, $id)
    {
        $detail = ConsumableOrderDetail::findOrFail($id);
        $consumables = Consumable::all();

        return view('consumable_orders.details.edit', compact('detail', 'consumables', 'order_id'));
    }

    /**
     * Update item detail
     */
    public function update(Request $request, $order_id, $id)
    {
        $request->validate([
            'consumable_id' => 'required|exists:consumables,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $detail = ConsumableOrderDetail::findOrFail($id);
        $detail->update([
            'consumable_id' => $request->consumable_id,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('consumable-orders.details.index', $order_id)
            ->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Hapus item
     */
    public function destroy($order_id, $id)
    {
        $detail = ConsumableOrderDetail::findOrFail($id);
        $detail->delete();

        return back()->with('success', 'Item berhasil dihapus.');
    }
}
