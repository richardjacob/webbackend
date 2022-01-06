@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="hub_employee">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Manage Driver <small>Edit Monitor & Camera</small></h1>
    <ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li class="active">Manage Driver</li>
      <li>
    		<a href="{{url(LOGIN_USER_TYPE.'/monitor_camera')}}">Monitor And Camera</a>
  		</li>
      <li>
        <a href="{{ url(LOGIN_USER_TYPE.'/edit_monitor_camera/'.$result->id) }}"> Edit</a>
      </li>

		</ol>
  </section>
<style> 
    #suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
    #suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
    #suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
    #search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px}
</style>


  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!-- right column -->
      <div class="col-md-12">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Add Monitor & Camera Form</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          {!! Form::open(['url' => 'admin/edit_monitor_camera/'.$result->id, 'class' => 'form-horizontal']) !!}
          <div class="box-body">
            <span class="text-danger">(*)Fields are Mandatory</span>

            <div class="form-group" id="email_textbox">
              <label for="inpu_driver_name" class="col-sm-3 control-label">Driver Name<em class="text-danger">*</em></label>
              <div class="col-sm-6" ng-init='driver_name = "{{$result->driver_id}}";selectedDriver={{ $result->driver_id }}'>

				{!! Form::text('driver', $result->user->first_name.' '.$result->user->last_name.' - '.$result->user->mobile_number, ['class'=>'form-control', 'id'=>'driver', 'readonly'=>'true']) !!}

                <input type="hidden" id="search_user" name="driver_id" value={{$result->driver_id}}>
                <span class="text-danger">{{ $errors->first('driver_id') }}</span>
                 <div id="suggesstion-box"></div>
              </div>
            </div>

            <div class="form-group">
              <label for="input_vehicle" class="col-sm-3 control-label">Vehicle<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::select('vehicle_id', $vehicle, @$result->vehicle, ['class' => 'form-control', 'id' => 'input_vehicle']) !!}
                <span class="text-danger">{{ $errors->first('vehicle_id') }}</span>
              </div>
            </div>

            {{-- {{$vehicle->vehicle_name}} --}}

            <div class="form-group">
              <label for="input_monitor_sim" class="col-sm-3 control-label">Monitor Sim Number<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::text('monitor_sim', $result->monitor_sim, ['class' => 'form-control', 'id' => 'monitor_sim', 'placeholder' => 'Monitor Sim Number']) !!}
                <span class="text-danger">{{ $errors->first('monitor_sim') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_monitor_imei" class="col-sm-3 control-label">Monitor IMEI<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::text('monitor_imei', $result->monitor_imei, ['class' => 'form-control', 'id' => 'monitor_imei', 'placeholder' => 'Monitor IMEI']) !!}
                <span class="text-danger">{{ $errors->first('monitor_imei') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_monitor_ip" class="col-sm-3 control-label">Monitor IP</label>
              <div class="col-sm-6">
                {!! Form::text('monitor_ip',$result->monitor_ip, ['class' => 'form-control', 'id' => 'monitor_ip', 'placeholder' => 'Monitor IP']) !!}
                <span class="text-danger">{{ $errors->first('monitor_ip') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_monitor_status" class="col-sm-3 control-label">Monitor Status<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::select('monitor_status', array('Active' => 'Active', 'Inactive' => 'Inactive', 'Problem' => 'Problem', 'Not Connected' => 'Not Connected'), $result->monitor_status, ['class' => 'form-control', 'id' => 'monitor_status', 'placeholder' => 'Monitor Status']) !!}
                <span class="text-danger">{{ $errors->first('monitor_status') }}</span>
              </div>
            </div>

             <div class="form-group">
              <label for="input_camera_sim" class="col-sm-3 control-label">Camera Sim Number<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::text('camera_sim',  $result->camera_sim, ['class' => 'form-control', 'id' => 'camera_sim', 'placeholder' => 'Camera Sim Number']) !!}
                <span class="text-danger">{{ $errors->first('camera_sim') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_camera_imei" class="col-sm-3 control-label">Camera IMEI<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::text('camera_imei', $result->camera_imei, ['class' => 'form-control', 'id' => 'camera_imei', 'placeholder' => 'Camera IMEI']) !!}
                <span class="text-danger">{{ $errors->first('camera_imei') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_camera_ip" class="col-sm-3 control-label">Camera IP</label>
              <div class="col-sm-6">
                {!! Form::text('camera_ip', $result->camera_ip, ['class' => 'form-control', 'id' => 'camera_ip', 'placeholder' => 'Camera IP']) !!}
                <span class="text-danger">{{ $errors->first('camera_ip') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_camera_status" class="col-sm-3 control-label">Camera Status<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::select('camera_status', array('Active' => 'Active', 'Inactive' => 'Inactive', 'Problem' => 'Problem', 'Not Connected' => 'Not Connected'), $result->camera_status, ['class' => 'form-control', 'id' => 'camera_status', 'placeholder' => 'Camera Status']) !!}
                <span class="text-danger">{{ $errors->first('camera_status') }}</span>
              </div>
            </div>

          <!-- /.box-body -->
          <div class="box-footer">
            <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
            <button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
          </div>
          <!-- /.box-footer -->
          {!! Form::close() !!}
        </div>
        <!-- /.box -->
      </div>
      <!--/.col (right) -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@stop

@push('scripts')
<script type="text/javascript">
  $("#txtEditor").Editor(); 
  $('.Editor-editor').html($('#answer').val());
</script>



<script type="text/javascript">
	$('#input_email').on('keyup', function() {
	    var keyword = $(this).val();
	    if(keyword.length >=3) suggestion(keyword);
	});



	function suggestion(keyword){
		var url = "{{url('admin/monitor_camera_suggestion')}}?keywords="+keyword;
	    $.ajax({
	      type:'GET',
	      url:url,
	      beforeSend:function(){
	        $("#input_email").css("background","#eee");
	      },
	      success:function(data){
	        $("#suggesstion-box").show();
	        $("#suggesstion-box").html(data);
	        $("#input_email").css("background","#FFF");
	      }	       
	    });
  }

  function select_from_suggestion(label, val, option){
    $("#input_email").val(label);
    $("#suggesstion-box").hide();
    $("#search_user").val(val);

    $('#input_vehicle').find('option:not(:first)').remove();
    var option_array = option.split("|");

    for(var i = 0; i<option_array.length; i++){
    	var option_array_data = option_array[i].split("_");

    	$('#input_vehicle').append($('<option>', {
		    value: option_array_data[0],
		    text: option_array_data[1]
		}));
    }

	



  }

</script>
@endpush