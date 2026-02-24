<?php
namespace App\Http\Controllers\Assets;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Actionlog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class AssetBeritaAcaraController extends Controller
{
    /**
     * ===============================
     * SERAH TERIMA
     * ===============================
     */
    public function printSerahTerima(Asset $asset)
    {
        abort_if(!$asset->assigned_to, 404);
        Carbon::setLocale('id'); 
        $user = User::findOrFail($asset->assigned_to);
        return Pdf::loadView(
            'pdf.assets.berita-acara-serah-terima',
            compact('asset','user')
        )->setPaper('a4')
         ->stream('BAST_Penyerahan'.'_'.$user->name.'.pdf');
    }

    public function printSerahTerimaBroken(Asset $asset)
    {
        //dd($asset->status_id);
        abort_if($asset->status_id != 4, 404);
        Carbon::setLocale('id'); 
        $user = User::findOrFail(454);
        return Pdf::loadView(
            'pdf.assets.berita-acara-serah-broken',
            compact('asset','user')
        )->setPaper('a4')
         ->stream('BAST_Broken'.'_'.$asset->asset_tag.'.pdf');
    }

    /**
     * ===============================
     * PENGEMBALIAN (FINAL)
     * ===============================
     */
    public function printPengembalian(Asset $asset)
    {
        Carbon::setLocale('id');

        // 1. Ambil LOG CHECKIN TERAKHIR asset
        $checkinLog = Actionlog::where('item_type', Asset::class)
            ->where('item_id', $asset->id)
            ->where('action_type', 'checkin from')
            ->latest('created_at')
            ->firstOrFail();

        // 2. User yang mengembalikan
        $user = User::findOrFail($checkinLog->target_id);

        // 3. Reload asset relasi
        $asset->load(['company', 'model.manufacturer']);

        // 4. Generate VERIFY URL (INI YANG KURANG)
        $verifyUrl = $this->generateVerifyUrl($checkinLog, 'pengembalian');

        return Pdf::loadView(
            'pdf.assets.berita-acara-pengembalian',
            [
                'asset'       => $asset,
                'checkinLog'  => $checkinLog,
                'checkinUser' => $user,
                'verifyUrl'   => $verifyUrl, // ✅ WAJIB
            ]
        )->setPaper('a4')
        ->stream('BAST_Pengembalian_'.$user->name.'.pdf');
    }

    private function generateVerifyUrl(Actionlog $log, string $type = 'pengembalian'): string
    {
        $ts = now()->format('Y-m-d H:i:s');

        $sig = hash_hmac(
            'sha256',
            implode('|', [
                $log->id,
                $type,
                $ts,
                $log->company_id,
            ]),
            config('app.key')
        );

        return route('bast.verify', $log->id) . '?' . http_build_query([
            'type' => $type,
            'ts'   => $ts,
            'sig'  => $sig,
        ]);
    }

    public function verify(Request $request, Actionlog $log)
    {
        // abort_if(!$request->hasValidSignature(), 403);

        // abort_if(
        //     $log->item_type !== Asset::class ||
        //     $log->action_type !== 'checkin from',
        //     404
        // );

        // $asset = Asset::with(['company', 'model.manufacturer'])
        //     ->findOrFail($log->item_id);

        // $user = User::find($log->target_id);

        // return view('bast.verify', [
        //     'log'   => $log,
        //     'asset' => $asset,
        //     'user'  => $user,
        // ]);
        $type = $request->query('type');
        $ts   = $request->query('ts');
        $sig  = $request->query('sig');

        abort_if(!$type || !$ts || !$sig, 404);

        /**
         * 🔒 1. HARD LOCK (SEKALI SAJA)
         */
        if ($log->accept_signature === 'verified') {
            return response()
                ->view('bast.403-verify', [], 403);
        }

        /**
         * ⏰ 2. EXPIRED (30 hari)
         */
        if (Carbon::parse($ts)->diffInDays(now()) > 30) {
            return response()
                ->view('bast.403-verify', [], 403);
        }

        /**
         * 🔐 3. VALIDASI SIGNATURE
         */
        $expected = hash_hmac(
            'sha256',
            implode('|', [
                $log->id, 
                $type,
                $ts,
                $log->company_id,
            ]),
            config('app.key')
        );

        if (!hash_equals($expected, $sig)) {
            return response()
                ->view('bast.403-verify', [], 403);
        }

        /**
         * ✅ 4. UPDATE LOG (INI SEKARANG PASTI TERSIMPAN)
         */
        $log->forceFill([
            'accept_signature' => 'verified',
            'remote_ip'        => $request->ip(),
            'action_date'      => now(),
        ])->save();

        /**
         * 5. DATA UNTUK VIEW
         */
        $asset = $log->item;
        $user  = $log->target;

        return view('bast.verify', compact(
            'log',
            'asset',
            'user',
            'type'
        ));
    }
 
    public function bastQr(Actionlog $log)
    {
        $url = $this->generateVerifyUrl($log);

        return response(
            QrCode::format('svg')
                ->size(160)
                ->margin(1)
                ->generate($url),
            200,
            ['Content-Type' => 'image/svg+xml']
        );
    }
}

