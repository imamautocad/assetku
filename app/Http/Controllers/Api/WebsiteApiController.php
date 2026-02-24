<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use Illuminate\Http\Request;

class WebsiteApiController extends Controller
{
    public function index(Request $request)
    {
        $limit  = (int) $request->input('limit', 10);
        $offset = (int) $request->input('offset', 0);
        $search = $request->input('search'); 
        $sort   = $request->input('sort', 'id');
        $order  = $request->input('order') === 'asc' ? 'asc' : 'desc';

        $query = Website::with(['manufacturer', 'category', 'company'])
            ->whereNull('deleted_at');

        /* ================= SEARCH ================= */
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('decs', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('id_subscribe', 'like', "%{$search}%")
                  ->orWhere('price', 'like', "%{$search}%")
                  ->orWhereHas('company', fn($c) =>
                        $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('category', fn($c) =>
                        $c->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('manufacturer', fn($m) =>
                        $m->where('name', 'like', "%{$search}%"));
            });
        }

        /* ================= FILTER ================= */
        if ($request->filled('manufacturer_id')) {
            $query->where('manufacturer_id', $request->manufacturer_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /* ================= SORT ================= */
        switch ($sort) {
            case 'company':
                $query->leftJoin('companies', 'websites.company_id', '=', 'companies.id')
                      ->orderBy('companies.name', $order)
                      ->select('websites.*');
                break;

            case 'category':
                $query->leftJoin('categories', 'websites.category_id', '=', 'categories.id')
                      ->orderBy('categories.name', $order)
                      ->select('websites.*');
                break;

            case 'manufacturer':
                $query->leftJoin('manufacturers', 'websites.manufacturer_id', '=', 'manufacturers.id')
                      ->orderBy('manufacturers.name', $order)
                      ->select('websites.*');
                break;

            default:
                $query->orderBy($sort, $order);
        }

        /* ================= PAGINATION ================= */
        $total = $query->count();
        $rows  = $query->skip($offset)->take($limit)->get();

        /* ================= RESPONSE ================= */
        return response()->json([
            'total' => $total,
            'rows'  => $rows->map(function ($w) {

                /* ===== ACTION BUTTONS ===== */
                $actions = '
                    <a href="/website/'.$w->id.'" 
                        class="btn btn-sm btn-primary" title="Detail">
                        <i class="bi bi-eye-fill"></i>
                    </a>

                    <a href="'.route('website.edit', $w->id).'" 
                        class="btn btn-sm btn-warning" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </a>

                    <form action="'.route('website.destroy', $w->id).'"
                        method="POST"
                        style="display:inline-block"
                        onsubmit="return confirm(\'Yakin ingin menghapus data ini?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-danger" title="Delete">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </form>
                ';

                // 🔥 TOMBOL RENEW H-30
                if ($w->isRenewable()) {
                    $actions .= '
                        <button
                            class="btn btn-sm btn-success btn-renew"
                            data-id="'.$w->id.'"
                            title="Update Subscribe">
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                    ';
                }

                return [
                    'id'               => $w->id,
                    'manufacturers'    => $w->manufacturer->name ?? '-',
                    'category'         => $w->category->name ?? '-',
                    'company'          => $w->company->name ?? '-',
                    'decs'             => $w->decs,
                    'id_subscribe'     => $w->id_subscribe,
                    'name'             => $w->name,
                    'period_subscribe' => $w->period_subscribe,
                    'pay_date'         => optional($w->pay_date)->format('d/m/Y'),
                    'expired_date'     => optional($w->expired_date)->format('d/m/Y'),
                    'status'           => $w->status,
                    'price'            => number_format($w->price, 0, ',', ','),
                    'actions'          => $actions,
                ];
            })
        ]);
    }
}
