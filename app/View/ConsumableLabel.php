<?php

namespace App\View;

use App\Models\Consumable;
use App\Models\Labels\Label as LabelModel;
use App\Models\Labels\Sheet;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Macroable;
use TCPDF;

class ConsumableLabel implements View
{
    use Macroable { __call as macroCall; }

    protected const NAME = 'consumable-label';

    /**
     * Passed data container
     */
    protected Collection $data;

    /**
     * TCPDF output destination
     */
    private string $destination = 'I';

    public function __construct()
    {
        $this->data = new Collection();
    }

    /**
     * Render Consumable Labels PDF
     */
    public function render(callable $callback = null)
    {
        $settings    = $this->data->get('settings');
        $consumables = $this->data->get('consumables');
        $offset      = $this->data->get('offset', 0);

        if (!$consumables instanceof Collection || $consumables->isEmpty()) {
            abort(500, 'Consumables collection is missing or empty');
        }

        /**
         * Load label template
         */
        $template = LabelModel::find($settings->label2_template);

        if ($template === null) {
            return redirect()
                ->route('settings.labels.index')
                ->with('error', trans('admin/settings/message.labels.null_template'));
        }

        $template->validate();

        /**
         * Init PDF
         */
        $pdf = new TCPDF(
            $template->getOrientation(),
            $template->getUnit(),
            [
                0 => $template->getWidth(),
                1 => $template->getHeight(),
                'Rotate' => $template->getRotation(),
            ]
        );

        // PDF defaults
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(0, 0, null, true);
        $pdf->SetCellMargins(0, 0, 0, 0);
        $pdf->SetCellPaddings(0, 0, 0, 0);
        $pdf->setCreator('Snipe-IT');
        $pdf->SetTitle('Consumable Barcode Labels');
        $pdf->SetSubject('Consumable Barcode Labels');

        $template->preparePDF($pdf);

        /**
         * Prepare label data
         * VALUE ONLY — NO TITLE — NO LABEL
         */
        $data = $consumables->map(function (Consumable $consumable) use ($template, $settings) {

            $row = new Collection();

            // Identity (optional but safe)
            $row->put('id', $consumable->id);

            /**
             * Global logo only (consumable has no company)
             */
            if ($template->getSupportLogo() && !empty($settings->label_logo)) {
                $row->put(
                    'logo',
                    Storage::disk('public')->path('/' . ltrim($settings->label_logo, '/'))
                );
            }

            /**
             * 2D Barcode (QR)
             */
            if ($template->getSupport2DBarcode() && !empty($settings->label2_2d_type)) {
                $row->put('barcode2d', (object)[
                    'type'    => $settings->label2_2d_type,
                    'content' => route('consumables.show', $consumable),
                ]);
            }

            /**
             * VALUE ONLY FIELDS
             */
            $fields = collect([
                [
                    'label' => '',
                    'value' => optional($consumable->company)->name,
                ],
                [
                    'label' => '',
                    'value' => $consumable->name,
                ],
                [
                    'label' => '',
                    'value' => $consumable->item_no,
                ],
                [
                    'label' => '',
                    'value' => optional($consumable->category)->name,
                ],
            ])->filter(fn ($f) => !empty($f['value']));

            $row->put(
                'fields',
                $fields->take($template->getSupportFields())
            );

            return $row;
        });

        /**
         * Sheet offset support
         */
        if ($template instanceof Sheet) {
            $template->setLabelIndexOffset($offset);
        }

        /**
         * Render all labels
         */
        $template->writeAll($pdf, $data);

        $filename = $consumables->count() > 1
            ? 'consumables-labels.pdf'
            : 'consumable-' . $consumables->first()->id . '.pdf';

        $pdf->Output($filename, $this->destination);
    }

    /**
     * Attach data
     */
    public function with($key, $value = null)
    {
        $this->data->put($key, $value);
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function name()
    {
        return self::NAME;
    }

    public function getName()
    {
        return self::NAME;
    }
}
