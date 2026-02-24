@extends('layouts/default')

@section('title')
Website
@parent
@stop

@section('header_right')
  <a href="{{ route('website.create') }}" class="btn btn-primary pull-right">
    <i class="fa fa-plus"></i> Create Website
  </a>
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-default"> 
      <div class="box-body">
        {{-- CSS agar Description auto wrap --}}
        <style>
            /* Target kolom Description berdasarkan nama field dari bootstrap-table */
            td[data-field="decs"] {
                white-space: normal !important;
                word-break: break-word !important;
                max-width: 100px; /* sesuaikan jika ingin lebih kecil */
            } 
        </style>
        <table 
            data-columns="{{ \App\Presenters\WebsitePresenter::dataTableLayout() }}"
            data-cookie-id-table="websiteTable"
            data-pagination="true" 
            data-id-table="websiteTable" 
            data-search="true"
            data-search-highlight="true"
            data-show-print="true"
            data-side-pagination="server"
            data-show-columns="true"
            data-show-export="true"
            data-show-fullscreen="true"
            data-show-footer="true" 
            data-show-refresh="true" 
            data-sort-order="asc"
            data-sort-name="id"
            class="table table-striped snipe-table"
            data-url="{{ route('api.website.index') }}" 
        >
      </table>
  
      </div>
      <!-- MODAL RENEW -->
      <div class="modal fade" id="renewModal" tabindex="-1">
          <div class="modal-dialog">
              <form method="POST" id="renewForm">
                  @csrf
                  @method('PUT')

                  <div class="modal-content">
                      <div class="modal-header">
                          <h4 class="modal-title">Konfirmasi Pembayaran</h4>
                      </div>

                      <div class="modal-body">
                          <div class="form-group">
                              <label>Pay Date</label>
                              <input type="date"
                                    name="pay_date"
                                    class="form-control"
                                    required>
                          </div>
                      </div>

                      <div class="modal-footer">
                          <button class="btn btn-success">
                              Simpan & Perpanjang
                          </button>
                          <button class="btn btn-default" data-dismiss="modal">
                              Batal
                          </button>
                      </div>
                  </div>
              </form>
          </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('moar_scripts')
<script>
    function queryParams(params) {
        // pass through bootstrap-table params to API
        return { 
            limit: params.limit,
            offset: params.offset,
            sort: params.sort,
            order: params.order,
            search: params.search,
            // add any custom filters here if needed, e.g. category_id
            // category_id: $('#filter-category').val()
        };
    }

    function indexFormatter(value, row, index) {
        var opts = $('#websiteTable').bootstrapTable('getOptions');
        return (opts.pageNumber - 1) * opts.pageSize + (index + 1);
    }

    // Render actions returned by API (API already returns rendered HTML)
    function websiteActionFormatter(value, row) {
        return value || '';
    }

    // Optional: auto refresh when filters change
    // $(document).on('change', '#filter-category', function () {
    //     $('#websiteTable').bootstrapTable('refresh', {pageNumber: 1});
    // });
    $(document).on('click', '.btn-renew', function () {
        const id = $(this).data('id');

        const actionUrl = '{{ route("website.renew", ":id") }}'
            .replace(':id', id);

        $('#renewForm').attr('action', actionUrl);

        const today = new Date().toISOString().split('T')[0];
        $('#renewForm input[name="pay_date"]').val(today);

        $('#renewModal').modal('show');
    });
</script>

@include('partials.bootstrap-table', ['exportFile' => 'website-export', 'search' => true, 'showFooter' => false])
@stop
