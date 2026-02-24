@extends('layouts/default')

{{-- Page title --}}
@section('title')
Consumable Orders
@parent
@stop

@section('header_right')
  <a href="{{ route('consumable.orders.create') }}" {{$snipeSettings->shortcuts_enabled == 1 ? "accesskey=n" : ''}} class="btn btn-primary pull-right">
    <i class="fa fa-plus"></i> Create Request
  </a>
@stop

@section('content')
<div class="row"> 
  <div class="col-md-12"> 
    <div class="box box-default">
      <div class="box-body">

        {{-- <div id="toolbar" class="mb-2">
            <div class="row">
                <div class="col-md-6">
                    <input id="q" name="search" class="form-control" type="text" placeholder="Search Request No / Status / Notes">
                </div>
                <div class="col-md-6 text-right">
                    <a id="exportBtn" class="btn btn-success">Export</a>
                </div>
            </div> 
        </div> --}}

        <table
            id="consumableOrdersTable"
            data-toggle="table"
            data-url="{{ route('api.consumable.orders.index') }}"
            data-query-params="queryParams"
            data-pagination="true"
            data-side-pagination="server"
            data-page-size="10"
            data-search="true"
            data-search-highlight="true"
            data-show-refresh="true"
            data-show-columns="true"
            data-show-export="true"
            data-show-fullscreen="true"
            data-show-print="true"
            data-toolbar="#toolbar"
            data-cookie-id-table="consumableOrdersTable"
            class="table table-striped snipe-table">

            <thead>
                <tr>
                    <th data-field="id" data-formatter="indexFormatter">#</th>
                    <th data-field="no_req" data-sortable="true">Request No</th>
                    <th data-field="user" data-sortable="true">User</th>
                    <th data-field="department" data-sortable="true">Department</th>
                    <th data-field="status" data-sortable="true">Status</th>
                    <th data-field="created_at" data-sortable="true">Requested Date</th>
                    <th data-field="notes">Notes</th>
                    <th data-field="actions" data-formatter="actionFormatter" data-align="center">Action</th>
                </tr>
            </thead>
        </table>

      </div>
    </div>
  </div>
</div>
@stop

@section('moar_scripts')
<script>
    // Attach search box to bootstrap-table query
    // function queryParams(params) {
    //     // read search input
    //     var q = document.getElementById('q').value;
    //     // pass through bootstrap-table params + custom search
    //     return {
    //         limit: params.limit,
    //         offset: params.offset,
    //         sort: params.sort,
    //         order: params.order,
    //         search: q
    //     };
    // }

    // numbering since bootstrap-table returns rows without index
    function indexFormatter(value, row, index) {
        // returns index from bootstrap-table (client index)
        // but we prefer show serial number across pages:
        var pageSize = $('#consumableOrdersTable').bootstrapTable('getOptions').pageSize;
        var pageNumber = $('#consumableOrdersTable').bootstrapTable('getOptions').pageNumber;
        return (pageNumber - 1) * pageSize + (index + 1);
    }

    // action buttons
    function actionFormatter(value, row) {
        var html = '';
        // View (always available)
        html += '<a href="{{ url("consumable-orders") }}/' + row.id + '" class="btn btn-xs btn-info" title="View"><i class="bi bi-eye-fill"></i></a> ';

        // Edit: only if API sets can_edit = true
        if (row.can_edit) {
            html += '<a href="{{ url("consumable-orders") }}/' + row.id + '/edit" class="btn btn-xs btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a> ';
        } 

        // Print: show when status submitted (case-insensitive check)
        if (row.status && row.status.toLowerCase() === 'submitted') {
            html += '<a target="_blank" href="{{ url("consumable-orders/print") }}/' + row.id + '" class="btn btn-xs btn-primary" title="Print"><i class="bi bi-printer-fill""></i></a>';
        }

        return html;
    }

    // Wire search input to table refresh
    // document.addEventListener('DOMContentLoaded', function () {
    //     var searchInput = document.getElementById('q');
    //     var timer = null;
    //     searchInput.addEventListener('input', function () {
    //         clearTimeout(timer);
    //         timer = setTimeout(function () {
    //             $('#consumableOrdersTable').bootstrapTable('refresh', {pageNumber: 1});
    //         }, 350);
    //     });

    //     // Export button: uses bootstrap-table export plugin if available
    //     document.getElementById('exportBtn').addEventListener('click', function () {
    //         $('#consumableOrdersTable').bootstrapTable('export', {
    //             type: 'csv',
    //             fileName: 'consumable-orders-{{ date("Ymd_His") }}'
    //         });
    //     });
    // });
</script>

@include('partials.bootstrap-table', ['exportFile' => 'consumable-orders-export', 'search' => true, 'showFooter' => false])
@stop
