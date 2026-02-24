<?php

namespace App\Models\Labels\Tapes\Brother;

class TZe_24mm_B extends TZe_24mm
{
    /*
     * ===== KONFIGURASI AMAN UNTUK 40x20 mm =====
     */
    private const BARCODE_MARGIN = 1.20;

    private const TITLE_SIZE   = 2.10;
    private const TITLE_MARGIN = 0.20;

    private const LABEL_SIZE   = 1.60;
    private const LABEL_MARGIN = 0.05;

    private const FIELD_SIZE   = 1.90;
    private const FIELD_MARGIN = 0.10;

    private const LOGO_MAX_WIDTH = 5.50;
    private const LOGO_MARGIN    = 0.60;
    private const TITLE_OFFSET_Y = -0.7;
    private const LOGO_OFFSET_Y = 0.8; 
    private const LOGO_OFFSET_X = 2; // mm, minus = geser ke kanan

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
    public function getSupportFields()    { return 4; } // field
    public function getSupportLogo()      { return true; }
    public function getSupportTitle()     { return true; }

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

            $barcodeTopY = $currentY;

            static::write2DBarcode(
                $pdf,
                $record->get('barcode2d')->content,
                $record->get('barcode2d')->type,
                $currentX,
                $barcodeTopY,
                $barcodeSize,
                $barcodeSize
            );

            // Geser area teks ke kanan QR
            $currentX    += $barcodeSize + self::BARCODE_MARGIN;
            $usableWidth -= $barcodeSize + self::BARCODE_MARGIN;

            // 🔥 TOP ALIGN dengan QR
            $currentY = $barcodeTopY + self::TITLE_OFFSET_Y;
        }

        /*
         * ===== TITLE =====
         */
        if ($record->has('title')) {
            static::writeText(
                $pdf,
                $record->get('title'),
                $currentX,
                $currentY,
                'freesans',
                'B',
                self::TITLE_SIZE,
                'L',
                $usableWidth,
                self::TITLE_SIZE * 0.9,
                true
            );
            $currentY += self::TITLE_SIZE + self::TITLE_MARGIN;
        }

        /*
         * ===== FIELDS (3 BARIS) =====
         */
        foreach ($record->get('fields')->take(4) as $field) {

            static::writeText(
                $pdf,
                $field['label'],
                $currentX,
                $currentY,
                'freesans',
                '',
                self::LABEL_SIZE,
                'L',
                $usableWidth,
                self::LABEL_SIZE * 0.85,
                true
            );

            $currentY += self::LABEL_SIZE + self::LABEL_MARGIN;

            static::writeText(
                $pdf,
                $field['value'],
                $currentX,
                $currentY,
                'freesans',
                'B',
                self::FIELD_SIZE,
                'L',
                $usableWidth,
                self::FIELD_SIZE * 0.9,
                true
            );

            $currentY += self::FIELD_SIZE + self::FIELD_MARGIN;
        }

        /*
         * ===== LOGO (HITAM PUTIH / GRAYSCALE) =====
         */
        if ($record->has('logo')) {
            static::writeImage(
                $pdf,
                $record->get('logo'),
                $pa->x2 - self::LOGO_MAX_WIDTH - self::LOGO_MARGIN + self::LOGO_OFFSET_X,
                $pa->y2 - (self::LOGO_MAX_WIDTH * 1.4) + self::LOGO_OFFSET_Y,
                self::LOGO_MAX_WIDTH,
                self::LOGO_MAX_WIDTH,
                'R',
                'B',
                300,
                true,
                false,
                 // 🔥 GRAYSCALE
            );
        }
    }
}