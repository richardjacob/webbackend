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
      <h1>Manage Driver <small>Driver</small></h1>
      <ol class="breadcrumb">
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a>
        </li>
        <li class="active">Manage Driver</li>
        <li>
          <a href="{{url(LOGIN_USER_TYPE.'/driver') }}">Driver</a>
        </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Manage Driver</h3>
              <div>
                <div class="col-md-8">
                  <div class="col-sm-4">&nbsp;</div>
                {!! Form::open(['url' => LOGIN_USER_TYPE.'/driver', 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}
                  <div class="col-sm-2">
                    <input type="text" name="driver_id" value="{{@$driver_id}}"  class="form-control" placeholder="Driver ID" autocomplete="off">
                  </div>

                  <div class="col-sm-2">
                    <input type="text" name="start_date" value="@if(isset($start_date)){{date('d-m-Y', strtotime(@$start_date))}}@endif"  class="form-control date" placeholder="Start Date" autocomplete="off">
                  </div>

                  <div class="col-sm-2">
                    <input type="text" name="end_date" value="@if(isset($end_date)){{date('d-m-Y', strtotime(@$end_date))}}@endif"  class="form-control date" placeholder="End Date" autocomplete="off">
                  </div>
                  <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary form_submit">
                      <i class="fa fa-search"></i> Search
                    </button>
                  </div>
                  
                {!! Form::close() !!}
                </div>

              @if((LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status == 'Active') || (LOGIN_USER_TYPE=='admin' && Auth::guard('admin')->user()->can('create_driver')))
                <div  class="col-md-4 text-right"><a class="btn btn-success" href="{{ url(LOGIN_USER_TYPE.'/add_driver') }}">Add Driver</a></div>
              @endif
              </div>
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
