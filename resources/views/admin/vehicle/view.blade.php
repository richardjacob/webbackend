@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Manage Vehicles <small>Vehicles</small></h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a></li>
      <li class="active"> Manage Vehicles </li>
      <li><a href="{{ url(LOGIN_USER_TYPE.'/vehicle') }}"> Vehicles </a></li>
      </ol>
    </section>
    <style type="text/css">
      .vertical-middle{
        vertical-align: middle !important;
      }
    </style>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Manage Vehicles</h3>
                <div class="row">
                  <div class="col-sm-10">
                    {!! Form::open(['url' => LOGIN_USER_TYPE.'/vehicle', 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}
                    <div class="col-sm-12" style="margin-top: 10px;  border:1px solid #ddd; padding:0px;">

                      <div class="col-sm-1 bg-gray" style="padding:16px 0px 16px 5px;">Filter By</div>

                      <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12" style="padding-bottom:5px">Vehicle Type</div>
                          <label class="col-sm-6" style="font-weight:normal">
                            <input type="radio" name="vehicle_type" value="Regular" @if(@$vehicle_type == 'Regular') checked="" @endif>Regular
                          </label>
                          <label class="col-sm-6"  style="font-weight:normal">
                            <input type="radio" name="vehicle_type" value="Premier"  @if(@$vehicle_type == 'Premier') checked="" @endif>Premier
                          </label>
                        </div>
                      </div>

                      <div class="col-sm-2" style="padding-top:8px;">
                        <input type="text" name="year" value="{{@$year}}" class="form-control" id="year" placeholder="Year" autocomplete="off">
                      </div>

                      <div class="col-sm-2" style="padding-top:8px;padding-left:0;">                        
                        <input type="text" name="vehicle_number" value="{{@$vehicle_number}}" class="form-control" placeholder="Vehicle Number" autocomplete="off">
                      </div>

                      <div class="col-sm-2" style="padding-top:8px;padding-left:0;">                        
                        <input type="text" name="user_id" value="{{@$user_id}}" class="form-control" placeholder="Driver ID" autocomplete="off">
                      </div>

                      <div class="col-sm-2" style="padding-top:8px;">                        
                        <button class="btn btn-primary">
                          <i class="fa fa-search"></i> Search
                        </button>
                      </div>

                    </div>
                    </form>
                  </div>
                  
                  @if(LOGIN_USER_TYPE!='company' || Auth::guard('company')->user()->status == 'Active')
                    <div class="col-sm-2 text-right" ><a class="btn btn-success" href="{{ url(LOGIN_USER_TYPE.'/add_vehicle') }}">Add Vehicles </a></div>
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

<script>
$("#year").datepicker({
    format: "yyyy",
    viewMode: "years", 
    minViewMode: "years",
    autoclose : true,
    startDate: '2000',
    endDate: '<?php echo date('Y'); ?>'
});
</script>

<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}

<script type="text/javascript">
  function change_vehicle_type(id, vehicle_type){
    $.ajax({
      url: "{{url('admin/ajax/change_vehicle_type')}}",
      type:"POST",
      data:{
        id:id,
        vehicle_type:vehicle_type,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data == 'Success') {
          $('#vehicle_type'+id).html(vehicle_type);
          $('#change_by'+id).html('<i class="fa fa-check text-success"></i>');

          $('#change_by'+id).removeClass('btn');
          $('#change_by'+id).removeClass('btn-primary');
          $('#change_by'+id).removeClass('btn-success');
          $('#change_by'+id).removeClass('btn-xs');
        }
        else{
          alert(data);
        }
      }
    });
  }
</script>
@endpush