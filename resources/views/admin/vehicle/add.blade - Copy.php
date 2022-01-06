@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="vehicle_management">
	<section class="content-header" ng-init='vehicle_id=0'>
		<h1>
		Add Vehicles
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/vehicle') }}">Vehicles</a></li>
			<li class="active">Add</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-10 col-sm-offset-1 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Add Vehicles Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/add_vehicle', 'class' => 'form-horizontal','files' => true,'id'=>'vehicle_form']) !!}
					{!! Form::hidden('user_country_code', '', ['id' => 'user_country_code']) !!}
					{!! Form::hidden('user_gender', '', ['id' => 'user_gender']) !!}
					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>
						@if (LOGIN_USER_TYPE!='company')
							<div class="form-group">
								<label for="input_company" class="col-sm-3 control-label">Company Name <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::select('company_name', $company, '', ['class'=>'form-control', 'id'=>'input_company_name', 'placeholder'=>'Select', 'ng-model'=>'company_name', 'ng-change'=>'get_driver()']) !!}
									<span class="text-danger">{{ $errors->first('company_name') }}</span>
								</div>
							</div>
						@else
							<span ng-init='company_name="{{Auth::guard("company")->user()->id}}";vehcileIdList=[];get_driver()'></span>
						@endif
						<div class="form-group">
							<label for="input_company" class="col-sm-3 control-label">Driver Name <em class="text-danger">*</em></label>
							<div class="col-sm-6" ng-init="selectedDriver = '';">
								<span class="loading" id="driver_loading" style="display: none;padding-left: 50%"><img src="{{ url('images/loader.gif') }}" style="width: 25px;height: 25px; "><br></span>
								<select class='form-control' name="driver_name" id="input_driver_name" ng-model="selectedDriver" ng-change="updateVehicleType()" ng-cloak>
									<option value="">Select</option>
									<option ng-repeat="driver in drivers" value="@{{driver.id}}">@{{driver.first_name}} @{{driver.last_name}} - @{{driver.id}} </option>
								</select>
								<span class="text-danger" id="driver-error">{{ $errors->first('driver_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), '', ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('status') }}</span>
							</div>
						</div>
						<div class="form-group">
			              <label for="input_status" class="col-sm-3 control-label">Make <em class="text-danger">*</em></label>
			              <div class="col-sm-6">
			                {!! Form::select('vehicle_make_id',$make, '', ['class' => 'form-control', 'id' => 'vehicle_make', 'placeholder' => 'Select']) !!}
			                <span class="text-danger">{{ $errors->first('vehicle_make_id') }}</span>
			              </div>
			            </div>
			            <div class="form-group">
			              <label for="input_status" class="col-sm-3 control-label">Model <em class="text-danger">*</em></label>
			              <div class="col-sm-6">
			              	<span class="loading" id="model_loading" style="display: none;padding-left: 50%"><img src="{{ url('images/loader.gif') }}" style="width: 25px;height: 25px; "><br></span>
			                {!! Form::select('vehicle_model_id',array(), '', ['class' => 'form-control', 'id' => 'vehicle_model', 'placeholder' => 'Select']) !!}
			                <span class="text-danger">{{ $errors->first('vehicle_make_id') }}</span>
			              </div>
			            </div>
						<div class="form-group cls_vehicle">
							<label for="vehicle_type" class="col-sm-3 control-label">Vehicle Type <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check">
								@foreach($car_type as $type)
								<li class="col-lg-12">
									<input type="checkbox" name="vehicle_type[]" id="vehicle_type" class="form-check-input vehicle_type" value="{{ $type->id }}" data-error-placement="container" data-error-container="#vehicle_type_error"/> <span>{{ $type->car_name }}</span>
								</li>
								@endforeach
								</br></br>
								<div class="text-danger" id="vehicle_type_error"></div>
								<span class="text-danger">{{ $errors->first('vehicle_type') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="default" class="col-sm-3 control-label">Default <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('default', '1', false, ['class' => 'form-check-input default', 'id' => 'default_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#default_error']) }} Yes
								{{ Form::radio('default', '0', false, ['class' => 'form-check-input default', 'id' => 'default_no', 'data-error-placement'=>'container', 'data-error-container'=>'#default_error']) }} No
								</br>
								<div class="text-danger" id="default_error"></div>
								<span style="color:gray;font-style: italic;"> * If the driver has only one vehicle with active status, it will be automatically selected as default.</span>
							</div>
						</div>
						
						<!-- <div class="form-group">
							<label for="sticker_mode" class="col-sm-3 control-label">Sticker Mode <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('sticker_mode', 'Yes', false, ['class' => 'form-check-input sticker_mode', 'id' => 'sticker_mode_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#sticker_mode_error', 'required' => 'required']) }} Yes
								{{ Form::radio('sticker_mode', 'No', false, ['class' => 'form-check-input sticker_mode', 'id' => 'sticker_mode_no', 'data-error-placement'=>'container', 'data-error-container'=>'#sticker_mode_error']) }} No
								</br>
								<div class="text-danger" id="sticker_mode_error">{{ $errors->first('sticker_mode') }}</div>
							</div>
						</div> -->
						
						
						<!-- <div class="form-group">
							<label for="handicap" class="col-sm-3 control-label">Handicap Accessibility Available <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('handicap', '1', false, ['class' => 'form-check-input handicap', 'id' => 'handicap_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#handicap_error']) }} Yes
								{{ Form::radio('handicap', '0', false, ['class' => 'form-check-input handicap', 'id' => 'handicap_no', 'data-error-placement'=>'container', 'data-error-container'=>'#handicap_error']) }} No
								</br>
								<div class="text-danger" id="handicap_error"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="child_seat" class="col-sm-3 control-label">Child Seat Accessibility Available <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('child_seat', '1', false, ['class' => 'form-check-input child_seat', 'id' => 'child_seat_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#child_seat_error']) }} Yes
								{{ Form::radio('child_seat', '0', false, ['class' => 'form-check-input child_seat', 'id' => 'child_seat_no', 'data-error-placement'=>'container', 'data-error-container'=>'#child_seat_error']) }} No
								</br>
								<div class="text-danger" id="child_seat_error"></div>
							</div>
						</div> -->


						<div class="form-group">
							<label for="request_from" class="col-sm-3 control-label">Request From <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('request_from', '1', false, ['class' => 'form-check-input request_from', 'id' => 'request_from_female', 'data-error-placement'=>'container', 'data-error-container'=>'#request_from_error']) }} Female
								{{ Form::radio('request_from', '0', false, ['class' => 'form-check-input request_from', 'id' => 'request_from_both', 'data-error-placement'=>'container', 'data-error-container'=>'#request_from_error']) }} Both
								</br>
								<div class="text-danger" id="request_from_error"></div>
								<span style="color:gray;font-style: italic;"> * If the driver is male, it will be automatically selected as both.</span>
							</div>
						</div>
						<div class="form-group">
							<label for="vehicle_number" class="col-sm-3 control-label">Vehicle Number <em class="text-danger">*</em></label>
							<div>
						        <div class="col-sm-2" style="padding-left:0">
						            <select name="city" class="form-control">
						              <option value="">মেট্রো নির্বাচন করুন</option>
						              @foreach($city as $val)
						              <option value="{{$val->city}}">{{$val->city}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1" style="padding-left:0">
						            <select name="reg_letter" class="form-control">
						              <option value="">বর্ণ</option>
						              @foreach($letter as $val)
						              <option value="{{$val->reg_letter}}">{{$val->reg_letter}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1" style="padding-left:0">
						            <select name="vehicle_class" class="form-control">
						              <option value="">ক্লাস</option>
						              @foreach($class as $val)
						              <option value="{{$val->vehicle_class}}">{{$val->vehicle_class}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1 text-center" style="padding:5px 0 0 0;width:5px;">-</div>
								<div class="col-sm-2">
									{!! Form::text('vehicle_number','', ['class' => 'form-control', 'id' => 'vehicle_number', 'placeholder' => 'Vehicle Number', 'onkeyup' => 'validate(event)', 'maxlength' => '4']) !!}
									<span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="color" class="col-sm-3 control-label">Color <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('color','', ['class' => 'form-control', 'id' => 'color', 'placeholder' => 'Color']) !!}
								<span class="text-danger">{{ $errors->first('color') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="year" class="col-sm-3 control-label">Year <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('year','', ['class' => 'form-control', 'id' => 'year', 'placeholder' => 'Year','autocomplete' =>'off']) !!}
								<span class="text-danger">{{ $errors->first('year') }}</span>
							</div>
						</div>
						<input type="hidden" id="vehicle_id" value="">
						<p ng-init="vehicle_doc='';errors = {{json_encode($errors->getMessages())}};"></p>
						<span class="loading" id="document_loading" style="padding-left: 80%" ng-if="(vehicle_doc=='' && selectedDriver!='')"></span>
						<div class="form-group" ng-repeat="doc in vehicle_doc" ng-cloak ng-if="vehicle_doc">
							<div class="form-group">
							<label class="col-sm-3 control-label">@{{doc.document_name}} <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<input type="file" name="file_@{{doc.id}}" class="form-control document_file">
								<span class="text-danger">@{{ errors['file_'+doc.id][0] }}</span>
							</div>
							</div>
							<div class="form-group">
							<label class="col-sm-3 control-label" ng-if="doc.expiry_required =='1'">Expire Date <em class="text-danger">*</em></label>
							<div class="col-sm-6" ng-if="doc.expiry_required =='1'">
								<input type="text" min="{{ date('Y-m-d') }}" name="expired_date_@{{doc.id}}" class="form-control document_expired" placeholder="Expire date" autocomplete="off">
								<span class="text-danger">@{{ errors['expired_date_'+doc.id][0] }}</span>
							</div>
							</div>
							<div class="form-group">
							<label class="col-sm-3 control-label">@{{doc.document_name}} Status <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class ='form-control' name='@{{doc.doc_name}}_status'>
									<option value="0" ng-selected="doc.status==0">Pending</option>
									<option value="1" ng-selected="doc.status==1">Approved</option>
									<option value="2" ng-selected="doc.status==2">Rejected</option>
								</select>
							</div>
						</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit"> Submit </button>
						<a href="{{url(LOGIN_USER_TYPE.'/vehicle')}}"><span class="btn btn-default pull-left">Cancel</span></a>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script> -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
<script>

$("#year").datepicker({
    format: "yyyy",
    viewMode: "years", 
    minViewMode: "years",
    autoclose : true,
    startDate: '1950',
    endDate: '<?php echo date('Y'); ?>'
});
function validate(evt) {
    var theEvent = evt || window.event;

    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
    // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /[0-9]|\./;
    if( !regex.test(key) ) {
      theEvent.returnValue = false;
      if(theEvent.preventDefault) theEvent.preventDefault();
    }
    getBanglaNum(evt);
  }



  function getBanglaNum(event) {
    var vehicle_number = $('#vehicle_number').val();

    var myArr = vehicle_number.split("");
    var unicode_text = "";
    var unicode_char = "";
    var char='',i;

    for (i = 0; i < myArr.length; i++) {
      char = myArr[i];
      switch(char) {
        case '0': unicode_char='০'; break;
        case '1': unicode_char='১'; break;
        case '2': unicode_char='২'; break;
        case '3': unicode_char='৩'; break;
        case '4': unicode_char='৪'; break;
        case '5': unicode_char='৫'; break;
        case '6': unicode_char='৬'; break;
        case '7': unicode_char='৭'; break;
        case '8': unicode_char='৮'; break;
        case '9': unicode_char='৯'; break;

        case '০': unicode_char='০'; break;
        case '১': unicode_char='১'; break;
        case '২': unicode_char='২'; break;
        case '৩': unicode_char='৩'; break;
        case '৪': unicode_char='৪'; break;
        case '৫': unicode_char='৫'; break;
        case '৬': unicode_char='৬'; break;
        case '৭': unicode_char='৭'; break;
        case '৮': unicode_char='৮'; break;
        case '৯': unicode_char='৯'; break;
        default: unicode_char = ''; break;
      } 
      unicode_text+=unicode_char;
    }    

    $('#vehicle_number').val(unicode_text);
  }
</script>
@endsection
