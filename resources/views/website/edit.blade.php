@extends('layouts/default')

@section('title')
    Edit Website Subscription
@parent
@stop

@section('header')
<section class="content-header">
    <h1><i class="fa fa-edit"></i> Edit Website Subscription</h1>
</section>
@stop

@section('content')

<style>
.checkbox_list,
#checkAll {
    width: 14px;
    height: 14px;
    cursor: pointer;
    transform: scale(0.9);
    margin: 2px;
    vertical-align: middle;
}
</style>

<div class="box box-default">
<form action="{{ route('website.update', $website->id) }}" method="POST"  enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="box-body">
        <div class="row">

            {{-- LEFT COLUMN --}}
            <div class="col-md-6">

                {{-- Manufacturer --}}
                <div class="form-group"> 
                    <label>Manufacturer</label>
                    <select name="manufacturer_id" class="form-control select2" required>
                        <option value="">-- Select Manufacturer --</option>
                        @foreach($manufacturers as $id => $name)
                            <option value="{{ $id }}" {{ $website->manufacturer_id == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div> 

                {{-- Category --}}
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control select2" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ $website->category_id == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Company --}}
                <div class="form-group">
                    <label>Company</label>
                    <select name="company_id" class="form-control select2" required>
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" {{ $website->company_id == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ID Subscribe --}}
                <div class="form-group">
                    <label>ID Subscribe</label>
                    <input type="text" name="id_subscribe" class="form-control" value="{{ $website->id_subscribe }}">
                </div>
                {{--Name Domain/Hosting--}}
                <div class="form-group">
                    <label>Name Domain/Hosting</label>
                    <input type="text" name="name" class="form-control" value="{{ $website->name }}" required >
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-md-6">

                {{-- Period --}}
                <div class="form-group">
                    <label>Period Subscribe (Years)</label required>
                    <input type="number" name="period_subscribe" id="period_subscribe" 
                           class="form-control" value="{{ $website->period_subscribe }}" required>
                </div>

                {{-- Pay Date --}}
                <div class="form-group">
                    <label>Pay Date</label>
                    <input type="date" name="pay_date" id="pay_date" class="form-control"  required
                     value="{{ $website->pay_date ? $website->pay_date->format('Y-m-d') : '' }}">
                </div>

                {{-- Expired Date --}}
                <div class="form-group">
                    <label>Expired Date</label>
                    <input type="date" name="expired_date" id="expired_date" class="form-control"  readonly
                    value="{{ $website->expired_date ? $website->expired_date->format('Y-m-d') : '' }}">
                </div>

                {{-- Price --}}
                {{-- <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required
                           value="{{ $website->price }}">
                </div>  --}}
                <div class="form-group">
                    <label>Price</label>

                    <input type="text"
                        id="price_display"
                        class="form-control"
                        autocomplete="off">

                    <input type="hidden"
                        name="price"
                        id="price"
                        value="{{ old('price', (int) $website->price) }}">
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Active" {{ $website->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $website->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

            </div>

        </div>

        {{-- Description --}}
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="decs" class="form-control" value="{{ $website->decs }}">
        </div>
        {{-- File Upload --}}
        <div class="form-group">
            <label>Upload Documents</label>
            <input type="file"
                name="files[]"
                class="form-control"
                multiple>
            <small class="text-muted">
                Multiple File Upload (PDF, JPG, PNG, DOCX)
            </small>
        </div>

    </div>

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
                            class="checkbox_list">
                    </td>
                    <td>{{ $file->original_name }}</td>
                    <td>{{ $file->file_size_kb }} KB</td>
                    <td>{{ $file->created_at->format('d M Y H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('website.file.open', $file->id) }}"
                        target="_blank"
                        class="btn btn-xs btn-primary"
                        title="Open">
                            <i class="fa fa-eye"></i>
                        </a>

                        <form action="{{ route('website.file.delete', $file->id) }}"
                            method="POST"
                            style="display:inline"
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

    <div class="box-footer text-left">
        <button class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
        <a href="{{ route('website.index') }}" class="btn btn-default">Cancel</a>
    </div>

</form>
</div>
@stop

@section('moar_scripts')
<script>
$(document).ready(function () {

    function calculateExpired() {

        let payDate = $('#pay_date').val();
        let period  = $('#period_subscribe').val();

        if (!payDate || !period) return;

        if (payDate.includes('/')) {
            let parts = payDate.split('/');
            payDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
            $('#pay_date').val(payDate);
        }

        let dt = new Date(payDate);
        dt.setFullYear(dt.getFullYear() + parseInt(period));

        let yyyy = dt.getFullYear();
        let mm   = String(dt.getMonth() + 1).padStart(2, '0');
        let dd   = String(dt.getDate()).padStart(2, '0');

        $('#expired_date').val(`${yyyy}-${mm}-${dd}`);
    }

    // Trigger otomatis saat edit form dibuka
    calculateExpired();

    // Trigger saat user mengubah
    $('#pay_date').on('change', calculateExpired);
    $('#period_subscribe').on('keyup change', calculateExpired);

});
function formatRupiah(angka) {
    let number_string = angka.replace(/[^,\d]/g, '').toString();
    let split = number_string.split(',');
    let sisa  = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1].substr(0, 2) : rupiah;

    return rupiah ? 'Rp. ' + rupiah : '';
}

document.addEventListener('DOMContentLoaded', function () {
    const display = document.getElementById('price_display');
    const hidden  = document.getElementById('price');

    if (!display || !hidden) return;

    // Saat load (edit/view)
    if (hidden.value) {
        display.value = formatRupiah(hidden.value + ',00');
    }

    display.addEventListener('keyup', function () {
        display.value = formatRupiah(this.value);

        // simpan numeric murni ke hidden input
        hidden.value = this.value.replace(/[^0-9]/g, '');
    });

    // sebelum submit pastikan hidden terisi
    display.closest('form').addEventListener('submit', function () {
        hidden.value = display.value.replace(/[^0-9]/g, '');
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const deleteBtn = document.querySelector('.btn-danger');

    if (!checkAll || !deleteBtn) return;

    checkAll.addEventListener('change', function () {
        document.querySelectorAll('.checkbox_list').forEach(cb => {
            cb.checked = checkAll.checked;
        });
        toggleDeleteButton();
    });

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
