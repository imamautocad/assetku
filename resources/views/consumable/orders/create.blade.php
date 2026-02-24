@extends('layouts/default')

@section('title')
    Create Consumable Request
    @parent
@stop

@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">

            {{-- Box Container --}}
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                </div>

                <form action="{{ route('consumable.orders.store') }}" method="POST" id="consumableForm">
                    @csrf
                    <div class="box-body">

                        {{-- Header Information --}}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>No. Request</label>
                                    <input type="text" class="form-control input-sm" name="no_req" value="{{ $noReq }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="text" class="form-control input-sm" value="{{ now()->format('d/m/Y') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" class="form-control input-sm" value="{{ $user->first_name }} {{ $user->last_name }}" readonly>
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Departement</label>
                                    <input type="text" class="form-control input-sm" value="{{ $user->department->name ?? '-' }}" readonly>
                                    <input type="hidden" name="department_id" value="{{ $user->department_id }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control input-sm" rows="2" placeholder="Notes......"></textarea>
                        </div>

                        <hr>

                        {{-- Detail Items --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detailTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50%">Nama Item</th>
                                        <th style="width: 15%">Qty</th>
                                        <th style="width: 10%" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="detailBody">
                                    <tr>
                                        <td>
                                            <select name="items[0][consumable_id]" class="form-control input-sm select2" required>
                                                <option value="">-- Choose Item --</option>
                                                @foreach ($consumables as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->name }} (Stok: {{ $item->qty }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][qty]" class="form-control input-sm text-center" min="1" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm addRow"><i class="bi bi-plus-circle-fill"  style="font-size:1.3rem;"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div> {{-- box-body --}}

                    <div class="box-footer">
                        <button type="submit" name="action" value="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send-plus-fill" style="font-size:1.3rem;"></i> Submit
                        </button>
                        <button type="submit" name="action" value="draft" class="btn btn-warning btn-sm">
                            <i class="bi bi-hdd-stack-fill" style="font-size:1.3rem"></i> Save Draft
                        </button>
                        <a href="{{ route('consumable.orders.index') }}" class="btn btn-default btn-sm">
                            <i class="bi bi-backspace-fill" style="font-size:1.3rem"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

{{-- Dynamic Row Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;

    document.querySelector('#detailTable').addEventListener('click', function(e) {
        if (e.target.closest('.addRow')) {
            const tbody = document.querySelector('#detailBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][consumable_id]" class="form-control input-sm select2" required>
                        <option value="">-- Pilih Item --</option>
                        @foreach ($consumables as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (Stok: {{ $item->qty }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="items[${rowIndex}][qty]" class="form-control input-sm text-center" min="1" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm removeRow"><i class="bi bi-dash-circle-fill" style="font-size:1.3rem;"></i></button>
                </td>
            `;
            tbody.appendChild(newRow);
            $('.select2').select2();
            rowIndex++;
        }

        if (e.target.closest('.removeRow')) {
            e.target.closest('tr').remove();
        }
    });

    document.querySelector('#consumableForm').addEventListener('submit', function(e) {
        let valid = true;
        document.querySelectorAll('#detailBody tr').forEach(row => {
            const item = row.querySelector('select').value;
            const qty = row.querySelector('input[type="number"]').value;
            if (!item || qty <= 0) valid = false;
        });

        if (!valid) {
            e.preventDefault();
            alert('⚠️ Pastikan semua item dan quantity sudah diisi dengan benar.');
        }
    });
});
</script>
@stop
