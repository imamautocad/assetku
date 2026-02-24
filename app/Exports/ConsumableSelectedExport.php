<?php

namespace App\Exports;

use App\Models\Consumable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsumableSelectedExport implements FromArray, WithHeadings, WithStyles
{
    protected array $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function headings(): array
    {
        return [
            'Item Name',
            'Category',
            'Date',
            'User',
            'Department',
            'Notes',
            'In',
            'Out',
            'Balance',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->ids as $id) {
            $consumable = Consumable::with('category')->find($id);
            if (!$consumable) continue;

            $balance = $consumable->qty;

            // Opening Balance
            $rows[] = [
                $consumable->name,
                $consumable->category->name ?? '-',
                '',
                'Opening Balance',
                '',
                '',
                '',
                '',
                $balance,
            ];

            foreach ($consumable->orderDetails()->with('order.user.department')->get() as $detail) {

                $in = $out = '';

                if ($detail->status === 'checkin') {
                    $balance += $detail->qty;
                    $in = $detail->qty;
                }

                if ($detail->status === 'checkout') {
                    $balance -= $detail->qty;
                    $out = $detail->qty;
                }

                $rows[] = [
                    $consumable->name,
                    $consumable->category->name ?? '-',
                    $detail->created_at->format('d/m/Y'),
                    $detail->order->user->name ?? '',
                    $detail->order->user->department->name ?? '',
                    $detail->order->notes ?? '',
                    $in,
                    $out,
                    $balance,
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Notes auto wrap (seperti PDF)
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        return [];
    }
}
