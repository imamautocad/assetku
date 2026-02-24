<?php

namespace App\Models\Labels\Tapes\Brother;

class TZe_24mm_Consumable extends TZe_24mm
{
    /*
     * ===== KONFIGURASI =====
     */
    private const BARCODE_MARGIN = 1.20;

    private const FIELD_SIZE   = 2.10;
    private const FIELD_MARGIN = 0.35;

    private const LOGO_MAX_WIDTH = 5.50;
    private const LOGO_MARGIN    = 0.60;

    /*
     * ===== UKURAN LABEL =====
     */
    public function getUnit()  { return 'mm'; }
    public function getWidth() { return 40.0; }

    /*
     * ===== SUPPORT =====
     */
    public function getSupportAssetTag()  { return false; }
    public function getSupport1DBarcode() { return false; }
    public function getSupport2DBarcode() { return true; }
    public function getSupportFields()    { return 4; }
    public function getSupportLogo()      { return false; }
    public function getSupportTitle()     { return false; } // 🔥 TITLE MATI

    public function preparePDF($pdf) {}

    public function write($pdf, $record)
    {
        $pa = $this->getPrintableArea();

        $currentX = $pa->x1 - 1.2;
        $currentY = $pa->y1;

        $usableWidth  = $pa->w;
        $usableHeight = $pa->h;

        /*
         * ===== QR CODE (KIRI) =====
         */
        if ($record->has('barcode2d')) {

            $barcodeSize = min(
                $usableHeight - 0.6,
                14.5
            );

            static::write2DBarcode(
                $pdf,
                $record->get('barcode2d')->content,
                $record->get('barcode2d')->type,
                $currentX,
                $currentY,
                $barcodeSize,
                $barcodeSize
            );

            // Geser teks ke kanan QR
            $currentX    += $barcodeSize + self::BARCODE_MARGIN;
            $usableWidth -= $barcodeSize + self::BARCODE_MARGIN;
        }

        /*
         * ===== FIELDS (VALUE ONLY) =====
         */
        foreach ($record->get('fields')->take(4) as $field) {

            if (empty($field['value'])) {
                continue;
            }

            static::writeText(
                $pdf,
                $field['value'],          // 🔥 HANYA VALUE
                $currentX,
                $currentY,
                'freesans',
                'B',
                self::FIELD_SIZE,
                'L',
                $usableWidth,
                self::FIELD_SIZE * 0.95,
                true
            );

            $currentY += self::FIELD_SIZE + self::FIELD_MARGIN;
        }
    }
}
