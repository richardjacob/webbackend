@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Driver List</h1>
      <ol class="breadcrumb">
         <li><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
         <li class="active">Acqusition List</li>
      </ol>
   </section>


    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header with-border">
              <h2 class="col-xs-6 box-title"><strong>{{@company_name($id)}}</strong></h2>
              <div class="col-xs-6">
                {!! Form::open(['url' => LOGIN_USER_TYPE.'/company_driver_list/'.@$id, 'class' => 'form-horizontal', 'method' => 'GET']) !!}

   

                  
                  <div class="col-sm-3" style="padding-right:0">
                    <input type="text" name="driver_id" value="{{@$driver_id}}"  class="form-control" placeholder="Driver ID" autocomplete="off">
                  </div>
                  <div class="col-sm-3" style="padding-right:0">
                    <input type="text" name="start_date"value="@if(@$start_date !=''){{date('d-m-Y', strtotime(@$start_date))}}@endif"  class="form-control date" placeholder="Start Date" autocomplete="off">
                  </div>

                  <div class="col-sm-3"  style="padding-right:0">
                    <input type="text" name="end_date" value="@if(@$end_date !=''){{date('d-m-Y', strtotime(@$end_date))}}@endif"  class="form-control date" placeholder="End Date" autocomplete="off">
                  </div>

                  <div class="col-sm-3">
                    <button type="submit" class="btn btn-primary">
                       <i class="fa fa-search"></i> Search
                     </button>
                  </div>
                  {!! Form::close() !!}
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
