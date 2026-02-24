@extends('layouts/default')

{{-- Page title --}}
@section('title') 
{{ trans('general.detail_report') }} 
@parent
@stop

{{-- Page content --}}
    @section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-body">
                    <div class="table-responsive">
                        <table
                        data-cookie-id-table="licensesReport"
                        data-pagination="true"
                        data-id-table="licensesReport" 
                        data-search="true"
                        data-side-pagination="client"
                        data-show-columns="true"
                        data-show-export="true"
                        data-show-refresh="true"
                        data-sort-order="asc"
                        id="licensesReport"
                        class="table table-striped snipe-table"
                        data-export-options='{
                    "fileName": "Detail-Report-{{ date('Y-m-d') }}",
                    "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                    }'>
                    <thead>
                        <tr role="row">
                            <th class="col-sm-1">Company</th>
                            <th class="col-sm-1">Email</th>
                            <th class="col-sm-1">Model</th>
                            <th class="col-sm-1">CPU</th>
                            <th class="col-sm-1">RAM</th>
                            <th class="col-sm-1">SN</th>
                            <th class="col-sm-1">Purchase_Date</th>
                            <th class="col-sm-1">Since_Date</th>
                            <th class="col-sm-1">Purchase_Cost</th>
                            <th class="col-sm-1">Order_Number</th>
                            <th class="col-sm-1">License/Accessories</th>
                            {{-- <th class="col-sm-1">{{ trans('admin/licenses/form.license_key') }}</th>
                            <th class="col-sm-1">{{ trans('admin/licenses/form.seats') }}</th>
                            <th class="col-sm-1">{{ trans('admin/licenses/form.remaining_seats') }}</th>
                            <th class="col-sm-1">{{ trans('admin/licenses/form.expiration') }}</th>
                            <th class="col-sm-1">{{ trans('general.purchase_date') }}</th>
                            <th class="col-sm-1 text-right" class="col-sm-1">{{ trans('general.purchase_cost') }}</th>
                            <th class="col-sm-1">{{ trans('general.depreciation') }}</th>
                            <th class="col-sm-1 text-right">{{ trans('admin/hardware/table.book_value') }}</th>
                            <th class="col-sm-1 text-right">{{ trans('admin/hardware/table.diff') }}</th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($ReportDetail as $DetailReport)
                        <tr>
                            <td>{{ is_null($DetailReport->company) ? '' : $DetailReport->company }}</td>
                            <td>{{ is_null($DetailReport->email) ? '' : $DetailReport->email }}</td>
                            <td>{{ is_null($DetailReport->Model) ? '' : $DetailReport->Model }}</td>
                            <td>{{ is_null($DetailReport->cpu) ? '' : $DetailReport->cpu }}</td>
                            <td>{{ is_null($DetailReport->ram) ? '' : $DetailReport->ram }}</td>
                            <td>{{ is_null($DetailReport->SN) ? '' : $DetailReport->SN }}</td>
                            <td>{{ is_null($DetailReport->purchase_date) ? '' : $DetailReport->purchase_date }}</td>
                            <td>{{ is_null($DetailReport->since_date) ? '' : $DetailReport->since_date }}</td>
                            <td>{{ is_null($DetailReport->purchase_cost) ? '' : $DetailReport->purchase_cost }}</td>
                            <td>{{ is_null($DetailReport->order_number) ? '' : $DetailReport->order_number }}</td>
                            <td>{{ is_null($DetailReport->asset_use) ? '' : $DetailReport->asset_use }}</td>
                            {{-- <td>{{ $DetailReport->email }}</td>
                            <td>{{ $DetailReport->model }}</td>
                            <td>{{ $DetailReport->cpu }}</td>
                            <td>{{ $DetailReport->ram }}</td>
                            <td>{{ $DetailReport->sn }}</td>
                            <td>{{ $DetailReport->asset_use }}</td> --}}
                            {{-- <td>{{ $license->name }}</td>
                            <td>
                                @can('viewKeys', $license)
                                    {{ $license->serial }}
                                @else
                                    ------------
                                @endcan
                            </td>
                            <td>{{ $license->seats }}</td>
                            <td>{{ $license->remaincount() }}</td>
                            <td>{{ $license->expiration_date }}</td>
                            <td>{{ $license->purchase_date }}</td>
                            <td class="text-right">
                                {{ $snipeSettings->default_currency }}{{ Helper::formatCurrencyOutput($license->purchase_cost) }}
                            </td>
                            <td>
                                {{ ($license->depreciation) ? e($license->depreciation->name).' ('.$license->depreciation->months.' '.trans('general.months').')' : ''  }}
                            </td>
                            <td class="text-right">
                                {{ $snipeSettings->default_currency }}{{ Helper::formatCurrencyOutput($license->getDepreciatedValue()) }}
                            </td>
                            <td class="text-right">
                                -{{ $snipeSettings->default_currency }}{{ Helper::formatCurrencyOutput(($license->purchase_cost - $license->getDepreciatedValue())) }}
                            </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                                
                        
                    </div> <!-- /.table-responsive-->
                </div>
            </div>
        </div>
    </div>

@stop
@section('moar_scripts')
    @include ('partials.bootstrap-table')
@stop
