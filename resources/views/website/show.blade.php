@extends('layouts/default')

@section('title')
    Detail Website Subscription
@parent
@stop

@section('header')
<section class="content-header">
    <h1><i class="fa fa-eye"></i> Detail Website Subscription</h1>
</section>
@stop

@section('content')

<style>
/* Styling checkbox agar kecil dan rapi */
.checkbox_list,
#checkAll {
    width: 14px;
    height: 14px;
    cursor: pointer;
    transform: scale(0.9);   /* perkecil sedikit */
    margin: 2px;             /* beri jarak agar rapi */
    vertical-align: middle;  /* sejajarkan dengan teks */
}
</style>

<div class="box box-default">
    <div class="box-body">

        <div class="row">
            {{-- LEFT --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label>Manufacturer</label>
                    <input class="form-control" value="{{ $website->manufacturer->name ?? '-' }}" disabled>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input class="form-control" value="{{ $website->category->name ?? '-' }}" disabled>
                </div>
                <div class="form-group">
                    <label>Company</label>
                    <input class="form-control" value="{{ $website->company->name ?? '-' }}" disabled>
                </div>
                <div class="form-group">
                    <label>ID Subscribe</label>
                    <input class="form-control" value="{{ $website->id_subscribe }}" disabled>
                </div>
                <div class="form-group">
                    <label>Name Domain / Hosting</label>
                    <input class="form-control" value="{{ $website->name }}" disabled>
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label>Period (Years)</label>
                    <input class="form-control" value="{{ $website->period_subscribe }}" disabled>
                </div>
                <div class="form-group">
                    <label>Pay Date</label>
                    <input class="form-control" value="{{ $website->pay_date?->format('d M Y') }}" disabled>
                </div> 
                <div class="form-group">
                    <label>Expired Date</label>
                    <input class="form-control" value="{{ $website->expired_date?->format('d M Y') }}" disabled>
                </div>
                {{-- <div class="form-group">
                    <label>Price</label>
                    <input class="form-control" value="{{ number_format($website->price, 2) }}" disabled>
                </div> --}}
                <div class="form-group">
                    <label>Price</label>
                    <input class="form-control"
                        value="Rp. {{ number_format($website->price, 2, ',', '.') }}"
                        disabled>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <input class="form-control" value="{{ $website->status }}" disabled>
                </div>
            </div>
        </div>

        {{-- DESCRIPTION --}}
        <div class="form-group">
            <label>Description</label>
            <input class="form-control" value="{{ $website->decs }}" disabled>
        </div>

        {{-- FILES --}}
        @if($website->files->count())
        <hr>
        <h4><i class="fa fa-paperclip"></i> Documents</h4>

        <form action="{{ route('website.file.bulk-delete') }}"
              method="POST"
              onsubmit="return confirm('Hapus semua file yang dipilih?')">

            @csrf
            @method('DELETE')

            <div class="text-left">
                <button class="btn btn-danger btn-sm" title="Delete Selected" disabled>
                    <i class="fa fa-trash"></i> Delete Selected
                </button>
            </div>    

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th>File</th>
                        <th width="120">Size</th>
                        <th width="180">Uploaded</th>
                        <th width="200" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($website->files as $file)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="file_ids[]"
                                   value="{{ $file->id }}"
                                   class="checkbox_list" style="width:14px; height:14px; margin:2px; vertical-align:middle;">
                        </td>
                        <td>{{ $file->original_name }}</td>
                        <td>{{ $file->file_size_kb }} KB</td>
                        <td>{{ $file->created_at->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('website.file.open', $file->id) }}"
                               target="_blank"
                               class="btn btn-xs btn-primary" title="Open">
                                <i class="fa fa-eye"></i>
                            </a>

                            <form action="{{ route('website.file.delete', $file->id) }}"
                                  method="POST"
                                  style="display:inline;"
                                  onsubmit="return confirm('Hapus file ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-xs btn-danger" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
        @endif
    </div>

    <div class="box-footer">
        <a href="{{ route('website.index') }}" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
</div>

@stop

@section('moar_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const deleteBtn = document.querySelector('.btn-danger');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.checkbox_list').forEach(cb => {
                cb.checked = checkAll.checked;
            });
            toggleDeleteButton();
        });
    }

    document.querySelectorAll('.checkbox_list').forEach(cb => {
        cb.addEventListener('change', toggleDeleteButton);
    });

    function toggleDeleteButton() {
        const anyChecked = document.querySelector('.checkbox_list:checked');
        deleteBtn.disabled = !anyChecked;
    }
});
    
</script>
@stop