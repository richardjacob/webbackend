@extends('admin.template')

@section('main')
<style type="text/css">
  .tooltip-inner {
    text-align: left;
    max-width: 250px;
    width: 250px;
    line-height: 150%;
  }


</style>


 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Manage Driver <small>Monitor And Camera</small></h1>

      <ol class="breadcrumb">
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}">
            <i class="fa fa-dashboard"></i> Home
          </a>
        </li>
        <li class="active">Manage Driver</li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/monitor_camera') }}">Monitor And Camera</a>
        </li>
        {{-- @if(@$driver_id !='')
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/view_drivers_remarks/'.@$driver_id) }}"> View</a>
        </li>
        @endif --}}
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

            <div class="box-header">
              <h3 class="box-title">Monitor And Camera</h3>
              @if((LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status == 'Active') || (LOGIN_USER_TYPE=='admin' && Auth::guard('admin')->user()->can('add_monitor_camera')))
                <div style="float:right;"><a class="btn btn-success" href="{{ url(LOGIN_USER_TYPE.'/add_monitor_camera') }}">Add Monitor And Camera</a></div>
              @endif
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              {!! $dataTable->table() !!}
            </div>
          </div>
        </div>
      </div>
    </section>
</div>
@endsection
@push('scripts')
<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush

<!--
order by followup_date asc !=0000-00-00 00:00:00
status pending/completed

-->