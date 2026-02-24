<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsumableOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumableReportApiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data total pemakaian per item consumable
        $query = ConsumableOrderDetail::select(
                'consumable_id',
                DB::raw('SUM(qty) as total_qty')
            )
            ->groupBy('consumable_id')
            ->with('consumable')
            ->orderByDesc(DB::raw('SUM(qty)'));
        print($query);

        // Optional: pencarian nama item
        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->whereHas('consumable', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $total = $query->count();
        $offset = (int) $request->get('offset', 0);
        $limit  = (int) $request->get('limit', 10);

        $rows = $query->skip($offset)->take($limit)->get();

        $data = $rows->map(function ($row) {
            return [
                'id'         => $row->consumable_id,
                'item_name'  => $row->consumable->name ?? '-',
                'total_used' => $row->total_qty ?? 0,
                'category'   => $row->consumable->category->name ?? '-',
                'location'   => $row->consumable->location->name ?? '-',
                'actions'    => '<a href="'.route('reports.consumables.show', $row->consumable_id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> Detail</a>',
            ];
        });

        return response()->json([
            'total' => $total,
            'rows'  => $data,
        ]);
    }
}
