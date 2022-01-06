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
      <h1>Manage Driver <small>Driver's Remarks</small></h1>

      <ol class="breadcrumb">
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}">
            <i class="fa fa-dashboard"></i> Home
          </a>
        </li>
        <li class="active">Manage Driver</li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/drivers_remarks') }}"> Driver's Remarks</a>
        </li>
        @if(@$driver_id !='')
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/view_drivers_remarks/'.@$driver_id) }}"> View</a>
        </li>
        @endif
      </ol>
    </section>

   
        

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

            <div class="box-header">
              <div class="col-sm-4">
                <h3 class="box-title">Driver's Remarks</h3>  
              </div>

             <div class="col-sm-8 text-right">
              {!! Form::open(['url' => LOGIN_USER_TYPE.'/drivers_remarks/'.$status, 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}
                <div class="col-sm-2">
                  <input type="text" name="driver_id" value="{{@$driver_id}}"  class="form-control" placeholder="Driver ID" autocomplete="off">
                </div>

                <div class="col-sm-2">
                  <input type="text" name="start_date" value="@if(isset($start_date)){{date('d-m-Y', strtotime(@$start_date))}}@endif"  class="form-control date" placeholder="Start Date" autocomplete="off">
                </div>

                <div class="col-sm-2">
                  <input type="text" name="end_date" value="@if(isset($end_date)){{date('d-m-Y', strtotime(@$end_date))}}@endif"  class="form-control date" placeholder="End Date" autocomplete="off">
                </div>
                <div class="col-sm-1">
                  <button type="submit" class="btn btn-primary form_submit">
                    <i class="fa fa-search"></i> Search
                  </button>
                </div>
              {!! Form::close() !!}
              <div class="col-sm-5 text-right">
                <a href="{{ url(LOGIN_USER_TYPE.'/drivers_remarks/0') }}" class="btn @if(@$status == '0')btn-success @else btn-default @endif">Inprocessing</a> &nbsp; 
                <a href="{{ url(LOGIN_USER_TYPE.'/drivers_remarks/1') }}" class="btn @if(@$status == '1')btn-primary @else btn-default @endif">Completed</a>
              </div>  

            </div>

            @if(
              Route::current()->uri() == 'admin/view_drivers_remarks/{id}' || 
              Route::current()->uri() == 'company/view_drivers_remarks/{id}' ||
              Route::current()->uri() == 'hub/view_drivers_remarks/{id}'
            )
            <div class="col-xs-12 text-right">
              <div>Driver Name : {{@$name}}</div>
              <div>Mobile : {{@$mobile}}</div>
              <div>Status : {{@$status}}</div>
            </div>
            @endif

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
<script type="text/javascript">
$( ".date" ).datepicker({
     autoclose: true,
     todayHighlight: true,
     format: 'dd-mm-yyyy' 
});
</script>

<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush

<!--
order by followup_date asc !=0000-00-00 00:00:00
status pending/completed

-->