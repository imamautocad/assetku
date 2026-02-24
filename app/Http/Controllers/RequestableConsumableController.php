<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestableConsumableRequest;
use App\Models\RequestableConsumable;
use App\Models\RequestableConsumableItem;
use App\Models\Consumable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RequestableConsumableController extends Controller
{
    /**
     * Show index page (blade will load bootstrap-table and call API)
     */
    public function index()
    {
        return view('requestable.index');
    }

    /**
     * API endpoint for bootstrap-table datatable (server-side)
     */
    public function indexApi(Request $request)
    {
        // Basic server-side: return JSON expected by bootstrap-table.
        // For now return all for admins, user-only for non-admin.
        $query = RequestableConsumable::with('user','department');

        // If user is not admin, show only own records
        $user = Auth::user();
        if (! $user->hasRole('admin') && ! $user->hasPermissionTo('requestable.view')) {
            $query->where('user_id', $user->id);
        }

        $rows = $query->orderBy('created_at','desc')->get();

        // Map to simple array for bootstrap table
        $data = $rows->map(function($r){
            return [
                'no_request' => $r->no_request,
                'user' => $r->user ? $r->user->full_name ?? $r->user->email : '',
                'department' => $r->department ? $r->department->name : '',
                'notes' => $r->notes,
                'status' => ucfirst($r->status),
                'created_at' => $r->created_at->format('Y-m-d H:i'),
                'actions' => view('requestable.partials.actions', compact('r'))->render()
            ];
        });

        // bootstrap-table expects { total: ..., rows: [...] } for server side pagination
        return response()->json([
            'total' => $data->count(),
            'rows' => $data->values()
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        // only requestable consumables
        $consumables = Consumable::where('requestable', 1)->get();
        return view('requestable.form', compact('consumables'));
    }

    /**
     * Store new request
     */
    public function store(RequestableConsumableRequest $req)
    {
        $data = $req->validated();

        // If submitting, check stock availability
        if ($data['status'] === 'submitted') {
            foreach ($data['items'] as $i) {
                $cons = Consumable::find($i['consumable_id']);
                if (!$cons) {
                    return back()->withInput()->withErrors(['items' => 'Consumable not found.']);
                }
                if ($i['quantity'] > $cons->qty) {
                    return back()->withInput()->withErrors(['items' => "Stock not enough for {$cons->name}. Available: {$cons->qty}"]);
                }
            }
        }

        // Generate no_request
        $noRequest = $this->generateNoRequest();

        // Create request header
        $requestable = RequestableConsumable::create([
            'user_id' => Auth::id(),
            'department_id' => Auth::user()->department_id,
            'no_request' => $noRequest,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        // Save items
        foreach ($data['items'] as $i) {
            $item = new RequestableConsumableItem([
                'consumable_id' => $i['consumable_id'],
                'quantity' => $i['quantity'],
                'notes' => $i['notes'] ?? null
            ]);
            $requestable->items()->save($item);

            // If submitted, reduce stock
            if ($data['status'] === 'submitted') {
                $cons = Consumable::find($i['consumable_id']);
                $cons->qty = max(0, $cons->qty - (int)$i['quantity']);
                $cons->save();
            }
        }

        return redirect()->route('requestable.index')->with('success', 'Request saved successfully.');
    }

    /**
     * Edit form for draft
     */
    public function edit(RequestableConsumable $requestable)
    {
        // only drafts can be edited — but we'll check permission elsewhere
        $consumables = Consumable::where('requestable', 1)
            ->orWhereIn('id', $requestable->items->pluck('consumable_id'))
            ->get();

        return view('requestable.form', compact('consumables','requestable'));
    }

    /**
     * Update existing request
     */
    public function update(RequestableConsumableRequest $req, RequestableConsumable $requestable)
    {
        $data = $req->validated();

        // If status submitted, check stock
        if ($data['status'] === 'submitted') {
            foreach ($data['items'] as $i) {
                $cons = Consumable::find($i['consumable_id']);
                if ($i['quantity'] > $cons->qty) {
                    return back()->withInput()->withErrors(['items' => "Stock not enough for {$cons->name}. Available: {$cons->qty}"]);
                }
            }
        }

        // Update header
        $requestable->update([
            'department_id' => Auth::user()->department_id,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        // Delete old items and save new items
        $requestable->items()->delete();

        foreach ($data['items'] as $i) {
            $item = new RequestableConsumableItem([
                'consumable_id' => $i['consumable_id'],
                'quantity' => $i['quantity'],
                'notes' => $i['notes'] ?? null
            ]);
            $requestable->items()->save($item);

            if ($data['status'] === 'submitted') {
                $cons = Consumable::find($i['consumable_id']);
                $cons->qty = max(0, $cons->qty - (int)$i['quantity']);
                $cons->save();
            }
        }

        return redirect()->route('requestable.index')->with('success', 'Request updated successfully.');
    }

    /**
     * Show detail
     */
    public function show(RequestableConsumable $requestable)
    {
        $requestable->load('items.consumable','user','department');
        return view('requestable.show', compact('requestable'));
    }

    /**
     * Helper: generate No Request RC-YYYY/MM/DD/0001 (daily counter)
     */
    protected function generateNoRequest()
    {
        $today = Carbon::now()->format('Y/m/d');

        // Count existing requests today and +1
        $countToday = RequestableConsumable::whereDate('created_at', Carbon::today())->count();
        $next = $countToday + 1;
        $nextPadded = str_pad($next, 4, '0', STR_PAD_LEFT);

        return "RC-{$today}/{$nextPadded}";
    }
}
