@extends('layouts/default')

@section('title')
    Add Website Subscription
@parent
@stop

@section('header')
<section class="content-header">
    <h1><i class="fa fa-plus"></i> Add Website Subscription</h1>
</section>
@stop

@section('content')

<div class="box box-default">
<form action="{{ route('website.store') }}" method="POST">
    @csrf
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
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control select2" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Company --}}
                <div class="form-group">
                    <label>Company</label>
                    <select name="company_id" class="form-control select2" required>
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ID Subscribe --}}
                <div class="form-group">
                    <label>ID Subscribe</label>
                    <input type="text" name="id_subscribe" class="form-control">
                </div>

                {{-- Name Domain/Hosting --}}
                <div class="form-group">
                    <label>Name Domain/Hosting</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-md-6">

                {{-- Period --}}
                <div class="form-group">
                    <label>Period Subscribe (Years)</label>
                    <input type="number" name="period_subscribe" id="period_subscribe" class="form-control" value="1" required>
                </div>

                {{-- Pay Date --}}
                <div class="form-group">
                    <label>Pay Date</label>
                    <input type="date" name="pay_date" id="pay_date" class="form-control" required>
                </div>

                {{-- Expired Date --}}
                <div class="form-group">
                    <label>Expired Date</label>
                    <input type="date" name="expired_date" id="expired_date" class="form-control" readonly>
                </div>

                {{-- Price --}}
                {{-- <div class="form-group">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div> --}}
                {{-- Price --}}
                <div class="form-group">
                    <label>Price</label>

                    {{-- DISPLAY (Rupiah) --}}
                    <input type="text"
                        id="price_display"
                        class="form-control"
                        placeholder="Rp. 0"
                        autocomplete="off">

                    {{-- VALUE ASLI (NUMERIC UNTUK BACKEND) --}}
                    <input type="hidden"
                        name="price"
                        id="price">
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

            </div>

        </div>

        {{-- Description --}}
        <div class="form-group">
            <label>Description</label>
            <textarea type="text" name="decs" class="form-control input-sm" rows="2" placeholder="Description......"></textarea>
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

    <div class="box-footer text-left">
        <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
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

        // Jika format dd/mm/yyyy → ubah ke yyyy-mm-dd
        if (payDate.includes('/')) {
            let parts = payDate.split('/');
            payDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
            $('#pay_date').val(payDate);
        }

        let dt = new Date(payDate);
        dt.setFullYear(dt.getFullYear() + parseInt(period));

        // Format ke yyyy-mm-dd
        let yyyy = dt.getFullYear();
        let mm   = String(dt.getMonth() + 1).padStart(2, '0');
        let dd   = String(dt.getDate()).padStart(2, '0');

        $('#expired_date').val(`${yyyy}-${mm}-${dd}`);
    }

    // Trigger hitung otomatis
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
</script>
@stop
