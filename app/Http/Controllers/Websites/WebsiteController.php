<?php

namespace App\Http\Controllers\Websites;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\Category;
use App\Models\Company;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Actionlog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\WebsiteFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
 
class WebsiteController extends Controller 
{
    public function index()
    {
        $this->authorize('view', Website::class);
        return view('website.index');
    }

    public function create()
    {
        return view('website.create', [
            'categories' => Category::orderBy('name')->pluck('name','id'),
            'companies' => Company::orderBy('name')->pluck('name','id'),
            'manufacturers' => Manufacturer::orderBy('name')->pluck('name','id'),
        ]);
    } 

    public function store(Request $request)
    {
        $request->validate([
            'manufacturer_id' =>  'required|exists:manufacturers,id',
            'category_id'      => 'required|exists:categories,id',
            'company_id'       => 'required|exists:companies,id',
            'decs'             => 'nullable|string|max:255',
            'id_subscribe'     => 'nullable|string|max:255',
            'name'             => 'required|string|max:25',
            'period_subscribe' => 'required|integer|min:0',
            'status'           => 'required|string',
            'pay_date'         => 'required|date',
            'price'            => 'nullable|numeric',
            'user_id'          => 'nullable|string|max:255',
        ]);

        $expired = Carbon::parse($request->pay_date)
                        ->addYears((int)$request->period_subscribe);

        $website = Website::create(array_merge(
            $request->only([
                'manufacturer_id','category_id','company_id',
                'decs','id_subscribe','name','period_subscribe','status','pay_date','price'
            ]),
                [
                    'expired_date' => $expired,
                    'user_id' => Auth::id()
                ]
        ));

        //LOG CREATE 
        ActionLog::create([
            'created_by'    => Auth::id(),
            'action_type'   => 'create',
            'target_id'     => $website->id,
            'target_type'   => Website::class,
            'note'          => 'Website Create: ' . 
                            $request->manufacturer_id . 
                            $request->category_id .
                            $request->manufacturer_id .
                            $request->company_id .
                            $request->decs .
                            $request->id_subscribe .
                            $request->name .
                            $request->period_subscribe .
                            $request->status .
                            $request->price ,
            'item_type'     => Website::class,
            'item_id'       => $website->id,
            'action_date'   => now(),
            'action_source' => 'gui',
            'remote_ip'     => $request->ip(),
            'user_agent'    => $request->header('User-Agent'),
        ]);

        return redirect()->route('website.index')
            ->with('success', 'Website created: ' . $website->decs);
    }

        public function edit(Website $website)
        {
            return view('website.edit', [
                'website' => $website, 
            // dd($website),
                'categories' => Category::orderBy('name')->pluck('name','id'),
                'companies' => Company::orderBy('name')->pluck('name','id'),
                'manufacturers' => Manufacturer::orderBy('name')->pluck('name','id'),
            ]);
        
        }

        public function update(Request $request, Website $website)
        {
            // =========================
            // VALIDASI
            // =========================
            $validated = $request->validate([
                'manufacturer_id'   => 'required|exists:manufacturers,id',
                'category_id'       => 'required|exists:categories,id',
                'company_id'        => 'required|exists:companies,id',
                'decs'              => 'nullable|string|max:255',
                'id_subscribe'      => 'nullable|string|max:255',
                'name'              => 'required|string|max:255',
                'period_subscribe'  => 'required|integer|min:0',
                'status'            => 'required|string',
                'pay_date'          => 'required|date',
                'price'             => 'nullable|numeric',

                // FILE
                'files'             => 'nullable|array',
                'files.*'           => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]);

            // =========================
            // UPDATE WEBSITE DATA
            // =========================
            $website->update([
                'manufacturer_id'  => $validated['manufacturer_id'],
                'category_id'      => $validated['category_id'],
                'company_id'       => $validated['company_id'],
                'decs'             => $validated['decs'] ?? null,
                'id_subscribe'     => $validated['id_subscribe'] ?? null,
                'name'             => $validated['name'],
                'period_subscribe' => $validated['period_subscribe'],
                'status'           => $validated['status'],
                'pay_date'         => $validated['pay_date'],
                'price'            => $validated['price'] ?? null,
                'user_id'          => Auth::id(),
            ]);

            // =========================
            // UPLOAD MULTIPLE FILE (SNIPE-IT STYLE)
            // =========================
            if ($request->hasFile('files')) {

                $disk = Storage::disk('public'); // 👉 public/uploads

                $basePath = 'website_files/'.$website->id;

                // pastikan folder ada
                $disk->makeDirectory($basePath);

                foreach ($request->file('files') as $file) {

                    if (!$file->isValid()) {
                        continue;
                    }

                    $originalName = $file->getClientOriginalName();
                    $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension    = $file->getClientOriginalExtension();

                    $fileName = $filenameOnly
                        . '_' . now()->format('YmdHis')
                        . '.' . $extension;

                    // SIMPAN FILE
                    $disk->putFileAs($basePath, $file, $fileName);

                    // SIMPAN DB
                    WebsiteFile::create([
                        'website_id'    => $website->id,
                        'original_name' => $originalName,
                        'file_name'     => $fileName,
                        'file_path'     => $basePath.'/'.$fileName,
                    ]);
                }
            }
            // =========================
            // LOG
            // =========================
            ActionLog::create([
                'created_by'    => Auth::id(),
                'action_type'   => 'update',
                'target_id'     => $website->id,
                'target_type'   => Website::class,
                'note'          => 'Website updated: '.$website->name,
                'item_type'     => Website::class,
                'item_id'       => $website->id,
                'action_date'   => now(),
                'action_source' => 'gui',
                'remote_ip'     => $request->ip(),
                'user_agent'    => $request->userAgent(),
            ]);

            return redirect()
                ->route('website.index')
                ->with('success', 'Data record updated');
        }

     public function show(Website $website)
    {
        $website->load('files');
        return view('website.show', [
            'website' => $website, 
           // dd($website),
            'categories' => Category::orderBy('name')->pluck('name','id'),
            'companies' => Company::orderBy('name')->pluck('name','id'),
            'manufacturers' => Manufacturer::orderBy('name')->pluck('name','id'),
        ]);
       
    }

    public function destroy(Website $website)
    {

        //$website->delete();
        DB::table('websites')
            ->where ('id',$website->id)
            ->update([
                'deleted_at' => Carbon::now('Asia/Jakarta'),
                'user_id' =>Auth::id()
            ]);
         return redirect()->route('website.index')
         ->with('success', 'Data record deleted: ' . $website->decs . ' - ' . $website->company->name);
    }

    public function renew(Request $request, Website $website)
        {
            $request->validate([
                'pay_date' => 'required|date',
            ]);

            $payDate = Carbon::parse($request->pay_date);

            $website->update([
                'pay_date'     => $payDate,
                'expired_date' => $payDate->copy()->addYears($website->period_subscribe),
                'user_id'      => Auth::id(),
            ]);

            // LOG RENEW
            ActionLog::create([
                'created_by'    => Auth::id(),
                'action_type'   => 'renew',
                'target_id'     => $website->id,
                'target_type'   => Website::class,
                'note'          => 'Website renewed: ' . $website->name,
                'item_type'     => Website::class,
                'item_id'       => $website->id,
                'action_date'   => now(),
                'action_source' => 'gui',
                'remote_ip'     => $request->ip(),
                'user_agent'    => $request->header('User-Agent'),
            ]);

            return redirect()->back()
                ->with('success', 'Website berhasil diperpanjang.');
        }
    
    // public function downloadFile(WebsiteFile $file)
    // {
    //     // Optional authorization
    //     // $this->authorize('view', $file->website);

    //     if (!Storage::disk('public')->exists($file->file_path)) {
    //         abort(404, 'File not found');
    //     }

    //     return Storage::disk('public')->download(
    //         $file->file_path,
    //         $file->original_name
    //     );
    // }

    public function openFile(WebsiteFile $file)
    {
        // Optional security
        // $this->authorize('view', $file->website);

        $disk = Storage::disk('public');

        if (!$disk->exists($file->file_path)) {
            abort(404, 'File not found');
        }

        $fullPath = $disk->path($file->file_path);

        return response()->file($fullPath, [
            'Content-Disposition' => 'inline; filename="'.$file->original_name.'"',
        ]);
    }
    public function deleteFile(WebsiteFile $file)
    {
        // Optional security
        // $this->authorize('update', $file->website);
        //dd('DELETE FILE', $file->id, $file->file_path);
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        ActionLog::create([
            'created_by'    => Auth::id(),
            'action_type'   => 'delete_file',
            'target_id'     => $file->id,
            'target_type'   => WebsiteFile::class,
            'note'          => 'Delete website file: ' . $file->original_name,
            'item_type'     => Website::class,
            'item_id'       => $file->website_id,
            'action_date'   => now(),
            'action_source' => 'gui',
            'remote_ip'     => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);

        return back()->with('success', 'File berhasil dihapus');
    }
    public function bulkDeleteFiles(Request $request)
    {
        $request->validate([
            'file_ids'   => 'required|array',
            'file_ids.*' => 'exists:website_files,id',
        ]);

        $files = WebsiteFile::whereIn('id', $request->file_ids)->get();

        foreach ($files as $file) {

            // HAPUS FILE FISIK
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // HAPUS RECORD DB
            $file->delete();

            // LOG
            ActionLog::create([
                'created_by'    => Auth::id(),
                'action_type'   => 'delete_file_bulk',
                'target_id'     => $file->id,
                'target_type'   => WebsiteFile::class,
                'note'          => 'Bulk delete website file: '.$file->original_name,
                'item_type'     => Website::class,
                'item_id'       => $file->website_id,
                'action_date'   => now(),
                'action_source' => 'gui',
                'remote_ip'     => request()->ip(),
                'user_agent'    => request()->userAgent(),
            ]);
        }

        return back()->with('success', 'File terpilih berhasil dihapus');
    }

}
 