@extends('layouts/default')

@section('title', 'Edit Consumable Order')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Edit Consumable Request — <medium>{{ $order->no_req }}</medium>
                </h3>
            </div>

            <form method="POST" action="{{ route('consumable.orders.update', $order->id) }}">
                @csrf
                @method('PUT')

                <div class="box-body">

                    {{-- HEADER SECTION --}}
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
                        <input type="text" class="form-control" value="{{ $order->department->name}}" readonly>
                     </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <input type="text" class="form-control" value="{{ ucfirst($order->status) }}" readonly>
                    </div>
                </div>
                <div class="form-group mb-4">
                    <label class="fw-bold">Notes</label>
                    <textarea name='notes' class="form-control" rows="2">{{ $order->notes}}</textarea>
                </div>

                    <hr>

                    {{-- DETAIL SECTION --}}
                    <h4>Order Details</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="order-details-table">
                            <thead>
                                <tr>
                                    <th style="width: 55%">Item</th>
                                    <th style="width: 20%">Qty</th>
                                    <th style="width: 10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $index => $detail)
                                    <tr>
                                        <td>
                                            <select name="details[{{ $index }}][consumable_id]" class="form-control" required>
                                                <option value="">-- Pilih Item --</option>
                                                @foreach($consumables as $c)
                                                    <option value="{{ $c->id }}" 
                                                        {{ $c->id == $detail->consumable_id ? 'selected' : '' }}>
                                                        {{ $c->name }} (Stok: {{ $c->qty }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="details[{{ $index }}][qty]" 
                                                   class="form-control" min="1" 
                                                   value="{{ $detail->qty }}" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="bi bi-dash-circle-fill" style="font-size:1.3rem;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-success btn-sm" id="add-row">
                        <i class="bi bi-plus-circle-fill"  style="font-size:1.3rem;"></i> Add Item
                    </button>

                </div>

                <div class="box-footer left-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-hdd-stack-fill" style="font-size:1.3rem"></i> Update Draft
                    </button>
                      <button type="submit" class="btn btn-success" name="action" value="submit">
                           <i class="bi bi-send-plus-fill" style="font-size:1.3rem;"></i> Submit
                    </button>
                    <a href="{{ route('consumable.orders.index') }}" class="btn btn-default">
                        <i class="bi bi-backspace-fill" style="font-size:1.3rem"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS Dynamic Row --}}
<script>
document.getElementById('add-row').addEventListener('click', function() {
    const table = document.querySelector('#order-details-table tbody');
    const rowCount = table.rows.length;
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <select name="details[${rowCount}][consumable_id]" class="form-control" required>
                <option value="">-- Pilih Item --</option>
                @foreach($consumables as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} (Stok: {{ $c->qty }})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="details[${rowCount}][qty]" class="form-control" min="1" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row">
                <i class="fa fa-minus"></i>
            </button>
        </td>
    `;
    table.appendChild(newRow);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        e.target.closest('tr').remove();
    }
});
</script>
@endsection
