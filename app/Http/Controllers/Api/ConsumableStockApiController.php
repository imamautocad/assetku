<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsumableOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ConsumableStockApiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query
        $query = ConsumableOrder::with(['user', 'department'])       
                ->whereNotNull('no_po');

        /**
         * 🔹 Logic Filter Akses:
         * 1. Admin / Superadmin → semua data
         * 2. Department ID = 11 → semua data
         * 3. End User → hanya data milik sendiri
         */
        if (!($user->isSuperUser() || $user->isAdmin()) && $user->department_id != 11) {
            $query->where('user_id', $user->id);
        }

        // ✅ Search
        if ($request->filled('search')) {
            $s = $request->get('search');
            $query->where(function ($q) use ($s) {
                $q->where('no_req', 'like', "%{$s}%")
                ->orWhere('status', 'like', "%{$s}%")
                ->orWhere('notes', 'like', "%{$s}%")
                ->orWhereDate('created_at', $s)
                // 🔹 Cari berdasarkan nama user
                ->orWhereHas('user', function ($u) use ($s) {
                    $u->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%")
                        ->orWhere('full_name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%");
                })
                // 🔹 Cari berdasarkan nama department
                ->orWhereHas('department', function ($d) use ($s) {
                    $d->where('name', 'like', "%{$s}%");
                });
            });
}

        // ✅ Filter khusus
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('no_req')) {
            $query->where('no_req', 'like', '%'.$request->get('no_req').'%');
        }
        if ($request->filled('no_po')) {
            $query->where('no_po', 'like', '%'.$request->get('no_po').'%');
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->get('department_id'));
        }
 
        // ✅ Sorting
        // $sort = $request->get('sort', 'created_at');
        // $order = $request->get('order', 'desc');
        // $allowed = ['no_req', 'status', 'created_at', 'department_id', 'user_id'];
        // if (!in_array($sort, $allowed)) {
        //     $sort = 'created_at';
        // }
        // $query->orderBy($sort, $order);
        // ✅ Sorting
            $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
            $sort_override = $request->input('sort');

            // Kolom frontend yang diizinkan untuk sorting
            $allowed_columns = ['no_req','no_po', 'user', 'department', 'status', 'request_date', 'notes'];

            // Default sorting ke created_at jika tidak valid
            $column_sort = in_array($sort_override, $allowed_columns) ? $sort_override : 'request_date';

            switch ($sort_override) {
                case 'user':
                    $query = $query->leftJoin('users', 'consumable_orders.user_id', '=', 'users.id')
                                ->orderBy('users.first_name', $order)
                                ->select('consumable_orders.*');
                    break;

                case 'department':
                    $query = $query->leftJoin('departments', 'consumable_orders.department_id', '=', 'departments.id')
                                ->orderBy('departments.name', $order)
                                ->select('consumable_orders.*');
                    break;

                case 'no_req': // Request Number
                    $query = $query->orderBy('no_req', $order);
                    break;
                case 'no_po': // Request Number
                    $query = $query->orderBy('no_po', $order);
                    break;
                case 'status':
                    $query = $query->orderBy('status', $order);
                    break;

                case 'notes':
                    $query = $query->orderBy('notes', $order);
                    break;
 
                case 'request_date': // Alias dari created_at
                    $query = $query->orderBy('created_at', $order);
                    break;

                default:
                    $query = $query->orderBy('created_at', $order);
                    break; 
        }
        // ✅ Pagination
        $offset = (int) $request->get('offset', 0);
        $limit = (int) $request->get('limit', 10);
        $total = $query->count();
        $rows = $query->skip($offset)->take($limit)->get();
 
        // ✅ Format JSON
        $data = $rows->map(function ($order) use ($user) {
            $canEdit = ($order->user_id === $user->id && strtolower($order->status) === 'draft');
            return [
                'id' => $order->id,
                'no_req' => $order->no_req,
                'no_po' => $order->no_po,
                'user' => $order->user->full_name ?? $order->user->first_name ?? ($order->user->email ?? '-'),
                'user_id' => $order->user_id,
                'department' => $order->department->name ?? '-',
                'department_id' => $order->department_id,
                'status' => ucfirst($order->status),
                'created_at' => $order->created_at->format('d-m-Y H:i'),
                'notes' => $order->notes,
                'can_edit' => $canEdit,
                'raw_user_id' => $order->user_id,
                'raw_status' => $order->status,
            ];
        });

        return response()->json([
            'total' => $total,
            'rows'  => $data,
        ]);
    }
}
