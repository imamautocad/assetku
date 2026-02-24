@extends('layouts/default')

@section('title')
    {{ trans('general.report_consumable') }}
    @parent
@stop

@section('header')
    <section class="content-header">
        <h1><i class="fa fa-bar-chart"></i> Report Consumable Used</h1>
    </section>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-body">
                
                {{-- TOP ACTION BAR --}}
                <div class="row" style="margin-bottom:15px;">
                    <div class="col-md-6">
                        <div class="action-bar">
                            {{-- Gunakan atribut form="bulkForm" agar button bisa berada di luar tag form --}}
                            <button type="submit" form="bulkForm" formaction="{{ route('reports.consumables.printSelected') }}" class="btn btn-success" onclick="return validatePrint();">
                                <i class="fa fa-print"></i> Print
                            </button>
                            <button type="submit" form="bulkForm" formaction="{{ route('reports.consumables.exportExcel') }}" class="btn btn-primary" onclick="return validatePrint();">
                                <i class="fa fa-file-excel-o"></i> Export
                            </button>
                            <span id="selectedCount" class="selected-badge" style="display:none"> Selected 0 </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="form-inline">
                            <input type="text" id="searchItem" class="form-control" placeholder="Search item name..." style="max-width:300px; display:inline-block;">
                        </div>
                    </div>
                </div>

                {{-- MAIN FORM --}}
                <form method="POST" id="bulkForm" target="_blank">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="consumableTable">
                            <thead>
                                <tr>
                                    <th width="60" class="text-center">
                                        <label class="switch small">
                                            <input type="checkbox" id="checkAll">
                                            <span class="slider"></span>
                                        </label>
                                    </th>
                                    <th>No</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>First Stock</th>
                                    <th>Stock In</th>
                                    <th>Stock Out</th>
                                    <th>Real Stock</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $index => $item)
                                    <tr>
                                        <td class="text-center">
                                            <label class="switch small">
                                                <input type="checkbox" name="consumable_ids[]" value="{{ $item->id }}" class="item-checkbox">
                                                <span class="slider"></span>
                                            </label>
                                        </td>
                                        {{-- Penomoran otomatis menyesuaikan pagination --}}
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="item-name">{{ $item->name }}</td>
                                        <td>{{ $item->category_name ?? '-' }}</td>
                                        <td>{{ number_format($item->first_stock) }}</td>
                                        <td>{{ number_format($item->stock_in) }}</td>
                                        <td>{{ number_format($item->used_stock) }}</td>
                                        <td><strong>{{ number_format($item->stok_akhir) }}</strong></td>
                                        <td>
                                            <a href="{{ route('reports.consumables.show', ['consumable_id' => $item->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                {{-- BOTTOM CONTROLS (PAGINATION & PER PAGE) --}}
                <div class="row" style="margin-top:10px;">
                    <div class="col-sm-6">
                        <form method="GET" action="{{ url()->current() }}" class="form-inline">
                            <label style="font-weight:normal;"> 
                                Show 
                                <select name="per_page" onchange="this.form.submit()" class="form-control input-sm" style="width:80px; margin:0 5px;">
                                    @foreach([10,25,50,100] as $size)
                                        <option value="{{ $size }}" {{ request('per_page', 20) == $size ? 'selected' : '' }}>{{ $size }}</option>
                                    @endforeach
                                </select> 
                                entries
                            </label>
                            {{-- Mempertahankan query search jika ada --}}
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                        </form>
                    </div>
                    <div class="col-sm-6 text-right">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                </div>

            </div> {{-- end box-body --}}
        </div> {{-- end box --}}
    </div>
</div>
@stop

@push('css')
<style>
    .selected-badge { background-color: #00c0ef; color: #fff; font-size: 12px; font-weight: 600; padding: 6px 10px; border-radius: 4px; white-space: nowrap; }
    .switch { position: relative; display: inline-block; width: 34px; height: 18px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: 0.25s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: 0.25s; border-radius: 50%; }
    .switch input:checked + .slider { background-color: #3c8dbc; }
    .switch input:checked + .slider:before { transform: translateX(16px); }
    .switch.small { width: 28px; height: 14px; }
    .switch.small .slider:before { width: 10px; height: 10px; }
    .switch.small input:checked + .slider:before { transform: translateX(14px); }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const searchBox = document.getElementById('searchItem');
        const countLabel = document.getElementById('selectedCount');

        function updateCount() {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            if (checkedCount > 0) {
                countLabel.style.display = 'inline-block';
                countLabel.textContent = `Selected: ${checkedCount}`;
            } else {
                countLabel.style.display = 'none';
            }
        }

        // Toggle All
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
            updateCount();
        });

        // Individual Checkbox
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('item-checkbox')) {
                const visible = [...document.querySelectorAll('.item-checkbox')]
                    .filter(cb => cb.closest('tr').style.display !== 'none');
                const checked = visible.filter(cb => cb.checked);
                checkAll.checked = visible.length > 0 && visible.length === checked.length;
                updateCount();
            }
        });

        // Live Search (Client Side)
        searchBox.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase();
            document.querySelectorAll('#consumableTable tbody tr').forEach(row => {
                const nameCell = row.querySelector('.item-name');
                if (nameCell) {
                    const name = nameCell.textContent.toLowerCase();
                    row.style.display = name.includes(keyword) ? '' : 'none';
                }
            });
            checkAll.checked = false;
            updateCount();
        });

        updateCount();
    });

    function validatePrint() {
        const checked = document.querySelectorAll('.item-checkbox:checked');
        if (checked.length === 0) {
            alert('Silakan pilih minimal 1 item.');
            return false;
        }
        return true;
    }
</script>
@endpush
