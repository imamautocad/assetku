@extends('layouts/default')

@section('title')
Detail Pemakaian - {{ $consumable->name }}
@parent
@stop

@section('header')
<section class="content-header">
    <h1><i class="fa fa-eye"></i> Detail Pemakaian: {{ $consumable->name }}</h1>
</section>
@stop

@section('content')
<div class="box box-default">
    <div class="box-body">
          {{-- ALERT WARNING STOCK HABIS --}}
        @if($consumable->qty == 0)
            <div class="alert alert-danger" style="font-size:14px;">
                <i class="fa fa-exclamation-triangle"></i>
                <b>Warning!</b> Real Stock for item <b>{{ $consumable->name }}</b> has <b>0</b> .
               <b> please add stock...!!! </b>
            </div>
        @endif
        <h3><b>Item Information</b></h3>
<table class="table table-bordered">
    <tr>
        <th>Item Name</th><td>{{ $consumable->name }}</td>
    </tr>
    <tr>
        <th>Category</th><td>{{ $consumable->category->name ?? '-' }}</td>
    </tr>
    {{-- <tr>
        <th>Stok Awal</th><td>{{ $stok_awal }}</td>
    </tr> --}}
    <tr>
        <th>Real / Current Stock</th><td>{{ $current_stock }}</td>
    </tr>
</table>

<h4>History Usage Consumable</h4>

<table class="table table-bordered table-striped text-left">
    <thead>
        <tr>
            <th width="140">No Request</th>
            <th width="120">Req/Input Date</th>
            <th>Name</th>
            <th>Department</th>
            <th>Notes</th> 
            <th>Checkin</th>
            <th>Checkout</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stockRows as $row)
            <tr>
                <td>{{$row['no_req']}}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['user'] }}</td>
                <td>{{ $row['dept'] }}</td>
                <td class="text-left">{{ $row['notes'] }}</td>
                <td>{{ $row['in'] !== '' ? $row['in'] : '' }}</td>
                <td>{{ $row['out'] !== '' ? $row['out'] : '' }}</td>
                <td><strong>{{ $row['balance'] }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Tidak ada riwayat stok</td>
            </tr>
        @endforelse
    </tbody>

    @if(!empty($stockRows))
    <tfoot>
        <tr>
            <th colspan="4" class="text-center">Total</th>
            <th>{{ $totals['in'] }}</th>
            <th>{{ $totals['out'] }}</th>
            <th></th>
        </tr>
    </tfoot>
    @endif
</table>
        <div class="box-footer">
            <a href="{{ route('reports.consumables.index') }}" class="btn btn-default btn-sm">
                <i class="bi bi-backspace-fill" style="font-size:1.3rem"></i> Back
            </a>
             <a href="{{ route('reports.consumables.print', $consumable->id) }}" 
                class="btn btn-primary btn-sm" target="_blank">
                <i class="bi bi-printer-fill" style="font-size:1.3rem"></i> Print
             </a>
        </div>
        
    </div>
</div>
@stop
 