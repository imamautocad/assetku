<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Consumable;
use App\Models\ConsumableOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ConsumableReportController extends Controller
{
    // public function __construct()
    //     {
    //         $this->middleware('auth');
    //         $this->middleware(function ($request, $next) {
    //             if (! auth()->user()->hasAccess('reports_consumable.view')) {
    //                 abort(403);
    //             }
    //             return $next($request);
    //         });
    //     }
    /**
     * Page list laporan stock
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);
        $search  = $request->get('search');

        $data = Consumable::select(
                'consumables.id',
                'consumables.name',
                'consumables.qty as stok_akhir',
                'categories.name as category_name',
                DB::raw("SUM(CASE WHEN cod.status = 'checkin' THEN cod.qty ELSE 0 END) as stock_in"),
                DB::raw("SUM(CASE WHEN cod.status = 'checkout' THEN cod.qty ELSE 0 END) as used_stock"),
                DB::raw("
                    (
                        consumables.qty
                        + SUM(CASE WHEN cod.status = 'checkout' THEN cod.qty ELSE 0 END)
                        - SUM(CASE WHEN cod.status = 'checkin' THEN cod.qty ELSE 0 END)
                    ) as first_stock
                ")
            )
            ->leftJoin('categories', 'categories.id', '=', 'consumables.category_id')
            ->leftJoin('consumable_order_details as cod', 'cod.consumable_id', '=', 'consumables.id')
            ->whereNull('consumables.deleted_at')
            ->when($search, function ($query) use ($search) {
                $query->where('consumables.name', 'like', "%{$search}%");
            })
            ->groupBy(
                'consumables.id',
                'consumables.name',
                'consumables.qty',
                'categories.name'
            )
            ->orderBy('consumables.name', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        return view('reports.consumables.index', compact('data', 'perPage'));
    }

    /**
     * Laporan detail per item / kartu stok
     */
    public function show($consumable_id)
    {
        $consumable = Consumable::with('category')->findOrFail($consumable_id);

        $history = ConsumableOrderDetail::with(['order.user', 'order.department'])
            ->where('consumable_id', $consumable_id)
            ->whereIn('status', ['checkin', 'checkout'])
            ->orderBy('created_at', 'asc')
            ->get();

        $totalIn  = $history->where('status', 'checkin')->sum('qty');
        $totalOut = $history->where('status', 'checkout')->sum('qty');

        $dbStock  = (int) $consumable->qty;
        $stokAwal = $dbStock + $totalOut - $totalIn;

        $running   = $stokAwal;
        $stockRows = [];

        // Opening balance
        $stockRows[] = [
            'no_req'  => '',
            'date'    => '-',
            'notes'   => 'Opening Balance',
            'user'    => '-',
            'dept'    => '-',
            'in'      => '',
            'out'     => '',
            'balance' => $running,
        ];

        foreach ($history as $row) {
            if ($row->status === 'checkin') {
                $in  = (int) $row->qty;
                $out = '';
                $running += $in;
            } else {
                $in  = '';
                $out = (int) $row->qty;
                $running -= $out;
            }

            $stockRows[] = [
                'no_req'  => $row->no_req,
                'date'    => $row->created_at->format('d/m/Y'),
                'notes'   => $row->order->notes ?? $row->order->request_number ?? '-',
                'user'    => optional($row->order->user)->name ?? '-',
                'dept'    => optional($row->order->department)->name ?? '-',
                'in'      => $in,
                'out'     => $out,
                'balance' => $running,
            ];
        }

        return view('reports.consumables.show', [
            'consumable'    => $consumable,
            'stockRows'     => $stockRows,
            'stok_awal'     => $stokAwal,
            'current_stock' => $running,
            'totals'        => [
                'in'  => $totalIn,
                'out' => $totalOut,
            ],
        ]);
    }

    /**
     * Data reusable (Browser, PDF, Excel)
     */
    protected function detailData($id)
    {
        $consumable = Consumable::with('category')->findOrFail($id);

        $details = $consumable->orderDetails()
            ->with(['order.user', 'order.department'])
            ->whereIn('status', ['checkin', 'checkout'])
            ->orderBy('created_at', 'asc')
            ->get();

        $totalIn  = $details->where('status', 'checkin')->sum('qty');
        $totalOut = $details->where('status', 'checkout')->sum('qty');

        $balance = (int) $consumable->qty + $totalOut - $totalIn;

        $rows[] = [
            'no_req'  => '-',
            'date'    => '-',
            'user'    => 'Opening Balance',
            'dept'    => '-',
            'notes'   => 'Stock Awal',
            'in'      => '',
            'out'     => '',
            'balance' => $balance,
        ];

        foreach ($details as $detail) {
            $in  = '';
            $out = '';

            if ($detail->status === 'checkin') {
                $balance += $detail->qty;
                $in = $detail->qty;
            } else {
                $balance -= $detail->qty;
                $out = $detail->qty;
            }

            $rows[] = [
                'no_req'  => $detail->no_req,
                'date'    => $detail->created_at->format('d/m/Y'),
                'user'    => optional($detail->order->user)->name ?? '-',
                'dept'    => optional($detail->order->department)->name ?? '-',
                'notes'   => $detail->order->notes ?? '-',
                'in'      => $in,
                'out'     => $out,
                'balance' => $balance,
            ];
        }

        return [
            'consumable'    => $consumable,
            'stockRows'     => $rows,
            'current_stock' => $balance,
            'totals'        => [
                'in'  => $totalIn,
                'out' => $totalOut,
            ],
        ];
    }

    /**
     * Print single consumable
     */
    public function print($id)
    {
        $data = $this->detailData($id);

        return Pdf::loadView('pdf.consumable-history', $data)
            ->setPaper('A4', 'portrait')
            ->stream("Consumable-History-{$data['consumable']->name}.pdf");
    }

    /**
     * Print selected consumables
     */
    public function printSelected(Request $request)
    {
        $ids = $request->input('consumable_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal 1 item.');
        }

        $items = collect($ids)->map(fn ($id) => $this->detailData($id));

        return Pdf::loadView('pdf.consumable-history-multi', compact('items'))
            ->setPaper('A4', 'portrait')
            ->stream('Consumable-Report-Selected.pdf');
    }

    /**
     * Export selected consumables to Excel
     */
    public function exportSelectedExcel(Request $request)
    {
        $ids = $request->input('consumable_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Pilih minimal 1 item.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $row         = 1;

        foreach ($ids as $index => $id) {
            $data       = $this->detailData($id);
            $consumable = $data['consumable'];

            // Title
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", 'Consumable Usage Report');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row += 2;

            // Item Info
            $sheet->setCellValue("A{$row}", 'Item Name');
            $sheet->setCellValue("B{$row}", $consumable->name);
            $row++;

            $sheet->setCellValue("A{$row}", 'Category');
            $sheet->setCellValue("B{$row}", $consumable->category->name ?? '-');
            $row++;

            $sheet->setCellValue("A{$row}", 'Current Stock');
            $sheet->setCellValue("B{$row}", $data['current_stock']);
            $row += 2;

            // Header
            $headers = ['No Req', 'Date', 'User', 'Department', 'Notes', 'In', 'Out', 'Balance'];
            $sheet->fromArray($headers, null, "A{$row}");

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E5E5'],
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            $sheet->freezePane("A" . ($row + 1));
            $row++;

            foreach ($data['stockRows'] as $r) {
                $sheet->fromArray([
                    $r['no_req'],
                    $r['date'],
                    $r['user'],
                    $r['dept'],
                    $r['notes'],
                    $r['in'],
                    $r['out'],
                    $r['balance'],
                ], null, "A{$row}");

                $sheet->getStyle("A{$row}:H{$row}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("E{$row}")->getAlignment()->setWrapText(true);
                $row++;
            }

            // Total
            $sheet->mergeCells("A{$row}:E{$row}");
            $sheet->setCellValue("A{$row}", 'TOTAL');
            $sheet->setCellValue("F{$row}", $data['totals']['in']);
            $sheet->setCellValue("G{$row}", $data['totals']['out']);

            $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:H{$row}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $row += 3;

            if ($index < count($ids) - 1) {
                $sheet->setBreak("A{$row}", Worksheet::BREAK_ROW);
            }
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'Consumable_Report_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $filename
        );
    }
}
