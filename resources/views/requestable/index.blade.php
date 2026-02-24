@extends('layouts/default')

@section('title')
Requestable Consumables
@parent
@stop

@section('header_right')
    <a href="{{ route('requestable.create') }}" {{$snipeSettings->shortcuts_enabled == 1 ? "accesskey=n" : ''}} 
       class="btn btn-primary pull-right">Create Request</a>
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-body">
            <table
                data-columns="{{ \App\Presenters\RequestableConsumablePresenter::dataTableLayout() }}"
                data-cookie-id-table="requestableTable"
                data-pagination="true"
                data-id-table="requestableTable"
                data-search-highlight="true"
                data-search="true"
                data-show-print="true"
                data-side-pagination="server"
                data-show-columns="true"
                data-show-fullscreen="true"
                data-show-export="true"
                data-show-refresh="true"
                data-show-footer="true"
                data-sort-order="asc"
                id="requestableTable"
                class="table table-striped snipe-table"
                data-url="{{ route('api.requestable.index') }}"
                data-export-options='{
                    "fileName": "export-requestable-{{ date('Y-m-d') }}",
                    "ignoreColumn": ["actions","notes"]
                }'>
          </table>
      </div>
    </div>
  </div>
</div>
@stop

@section('moar_scripts')
@include ('partials.bootstrap-table')
@stop
