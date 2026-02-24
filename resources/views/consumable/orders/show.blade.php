@extends('layouts/default')

@section('title', 'Detail Request Consumable')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Request #{{ $order->no_req }}</h3>
            </div>
            <div class="box-body">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label>Tanggal Request</label>
                        <input type="text" class="form-control" value="{{ $order->created_at->format('d-m-Y') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" value="{{ ($order->user->first_name ?? '-') . ' ' . ($order->user->last_name ?? '-') }}"readonly>
                    </div>
                    <div class="col-md-3"> 
                        <label>Department</label>
                        <input type="text" class="form-control" value="{{ $order->department->name ?? '-' }}" readonly>
                     </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <input type="text" class="form-control" value="{{ ucfirst($order->status) }}" readonly>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label class="fw-bold">Notes</label>
                    <textarea class="form-control" rows="2" readonly>{{ $order->notes ?? '-' }}</textarea>
                </div>
                <h4>Daftar Item</h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Kategori</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->details as $i => $detail)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ $detail->consumable->name ?? '-' }}</td>
                            <td>{{ $detail->category->name ?? '-' }}</td>
                            <td>{{ $detail->qty }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="box-footer left-right">
                    <a href="{{ route('consumable.orders.index') }}" class="btn btn-default btn-sm">
                       <i class="bi bi-backspace-fill" style="font-size:1.3rem"></i> Back
                    </a>
                    @if($order->status === 'Draft')
                    <a href="{{ route('consumable.orders.edit', $order->id) }}" class="btn btn-default btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
