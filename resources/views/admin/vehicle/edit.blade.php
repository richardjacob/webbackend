@extends('admin.template')
@section('main')
<style>
  fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin:0 100px;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
  }
  legend.scheduler-border {
    width:inherit; /* Or auto */
    padding:0 10px; /* To give a bit of padding on the left and right */
    border-bottom:none;
}
</style>
<div class="content-wrapper" ng-controller="vehicle_management">
	<section class="content-header">
		<h1>Manage Vehicles <small>Edit Vehicles</small></h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a></li>
			<li class="active"> Manage Vehicles </li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/vehicle') }}"> Vehicles </a></li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/edit_vehicle/'.$result->id) }}"> Edit</a></li>
		</ol>
	</section>
	<section class="content" ng-init='vehicle_id="{{$result->id}}"'>
		<div class="row">
			<div class="col-md-12 col-sm-offset-0 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Vehicles Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/edit_vehicle/'.$result->id, 'class' => 'form-horizontal vehicle_form','files' => true,'id'=>'vehicle_form']) !!}
					{!! Form::hidden('user_country_code', @$result->user->country->phone_code, ['id' => 'user_country_code']) !!}
					{!! Form::hidden('user_gender', $result->user->gender, ['id' => 'user_gender']) !!}
					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>

						<div class="form-group">
							<label for="input_company" class="col-sm-3 control-label">Driver Name <em class="text-danger">*</em></label>
							<div class="col-sm-6" ng-init='driver_name = "{{$result->user_id}}";selectedDriver={{ $result->user_id }}'>
								{!! Form::hidden('driver_name', $result->user_id) !!}
								{!! Form::text('driver', $result->user->first_name.' '.$result->user->last_name.' - '.$result->user->id, ['class'=>'form-control', 'id'=>'driver', 'readonly'=>'true']) !!}
							</div>
						</div>
						@if (LOGIN_USER_TYPE!='company')
							<div class="form-group" ng-init='company_name = "{{$result->company_id}}"'>
								<label for="input_company" class="col-sm-3 control-label">Company Name <em class="text-danger">*</em></label>
								<div class="col-sm-6" ng-init='get_driver()'>
									{!! Form::text('company_name', $company->name, ['class'=>'form-control', 'id'=>'input_company_name', 'readonly'=>'true']) !!}
								</div>
							</div>

							<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('status') }}</span>
							</div>
						</div> 
						@else
							<span ng-init='company_name="{{Auth::guard("company")->user()->id}}";get_driver()'></span>
						@endif
						

						

						<div class="form-group">
			              <label for="input_status" class="col-sm-3 control-label">Make <em class="text-danger">*</em></label>
			              <div class="col-sm-6">
			                {!! Form::select('vehicle_make_id',$make, $result->vehicle_make_id, ['class' => 'form-control', 'id' => 'vehicle_make', 'placeholder' => 'Select']) !!}
			                <span class="text-danger">{{ $errors->first('vehicle_make_id') }}</span>
			              </div>
			            </div>
			            <div class="form-group">
			              <label for="input_status" class="col-sm-3 control-label">Model <em class="text-danger">*</em></label>
			              <div class="col-sm-6" ng-init='vehicle_model_id="{{ $result->vehicle_model_id }}";'>
			              	<span class="loading" id="model_loading" style="display: none;padding-left: 50%"></span>
			                {!! Form::select('vehicle_model_id', array(), '', ['class'=>'form-control', 'id'=>'vehicle_model', 'placeholder'=>'Select']) !!}
			                <span class="text-danger">{{ $errors->first('vehicle_make_id') }}</span>
			              </div>
			            </div>
						<div class="form-group cls_vehicle">
							<label for="vehicle_type" class="col-sm-3 control-label">Vehicle Type <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check">
								@php $vehicle_types = explode(',', $result->vehicle_id); @endphp
								@foreach($car_type as $type)
								<li class="col-lg-12">
									<input type="radio" name="vehicle_type[]" id="vehicle_type" class="form-check-input vehicle_type" value="{{ $type->id }}" data-error-placement="container" data-error-container="#vehicle_type_error" {{ in_array($type->id,$vehicle_types) ? 'checked' : '' }}/> <span> {{ $type->car_name }}</span>
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
								{{ Form::radio('default', '1', $result->default_type=='1' ? true:false, ['class' => 'form-check-input default', 'id' => 'default_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#default_error']) }} Yes
								{{ Form::radio('default', '0', $result->default_type=='0' ? true:false, ['class' => 'form-check-input default', 'id' => 'default_no', 'data-error-placement'=>'container', 'data-error-container'=>'#default_error']) }} No
								</br>
								<div class="text-danger" id="default_error"></div>
								<span style="color:gray;font-style: italic;"> * If the driver has only one vehicle with active status, it will be automatically selected as default.</span>
							</div>
						</div>
						
<!-- 						
						<div class="form-group">
							<label for="sticker_mode" class="col-sm-3 control-label">Sticker Mode <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('sticker_mode', 'Yes', $result->sticker_mode=='Yes' ? true:false, ['class' => 'form-check-input sticker_mode', 'id' => 'sticker_mode_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#sticker_mode_error', 'required' => 'required']) }} Yes
								{{ Form::radio('sticker_mode', 'No', $result->sticker_mode=='No' ? true:false, ['class' => 'form-check-input sticker_mode', 'id' => 'sticker_mode_no', 'data-error-placement'=>'container', 'data-error-container'=>'#sticker_mode_error']) }} No
								</br>
								<div class="text-danger" id="sticker_mode_error"></div>
							</div>
						</div> -->
						
						
						<!-- <div class="form-group">
							<label for="handicap" class="col-sm-3 control-label">Handicap Accessibility Available <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('handicap', '1', in_array('2',$options) ? true:false, ['class' => 'form-check-input handicap', 'id' => 'handicap_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#handicap_error']) }} Yes
								{{ Form::radio('handicap', '0', !in_array('2',$options) ? true:false, ['class' => 'form-check-input handicap', 'id' => 'handicap_no', 'data-error-placement'=>'container', 'data-error-container'=>'#handicap_error']) }} No
								</br>
								<div class="text-danger" id="handicap_error"></div>
							</div>
						</div>
						<div class="form-group">
							<label for="child_seat" class="col-sm-3 control-label">Child Seat Accessibility Available <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('child_seat', '1', in_array('3',$options) ? true:false, ['class' => 'form-check-input child_seat', 'id' => 'child_seat_yes', 'data-error-placement'=>'container', 'data-error-container'=>'#child_seat_error']) }} Yes
								{{ Form::radio('child_seat', '0', !in_array('3',$options) ? true:false, ['class' => 'form-check-input child_seat', 'id' => 'child_seat_no', 'data-error-placement'=>'container', 'data-error-container'=>'#child_seat_error']) }} No
								</br>
								<div class="text-danger" id="child_seat_error"></div>
							</div>
						</div> -->


						<div class="form-group">
							<label for="request_from" class="col-sm-3 control-label">Request From <em class="text-danger">*</em></label>
							<div class="col-sm-6 form-check" style="padding-top: 6px;">
								{{ Form::radio('request_from', '1', in_array('1',$options) ? true:false, ['class' => 'form-check-input request_from', 'id' => 'request_from_female', 'data-error-placement'=>'container', 'data-error-container'=>'#request_from_error']) }} Female
								{{ Form::radio('request_from', '0', !in_array('1',$options) ? true:false, ['class' => 'form-check-input request_from', 'id' => 'request_from_both', 'data-error-placement'=>'container', 'data-error-container'=>'#request_from_error']) }} Both
								</br>
								<div class="text-danger" id="request_from_error"></div>
								<span style="color:gray;font-style: italic;"> * If the driver is male, it will be automatically selected as both.</span>
							</div>
						</div>

						<?php
							$vehicle_number = $result->vehicle_number;
							$vehicle_number_array = explode(' ', str_replace('-', ' ', $vehicle_number));
				        	$vehicle_element_size = count($vehicle_number_array);
				        	if($vehicle_element_size == '5'){
				        		$city_data = @$vehicle_number_array[0].' '.@$vehicle_number_array[1];
					        	$letter_data = @$vehicle_number_array[2];
					        	$class_data = @$vehicle_number_array[3];
					        	$number_data = @$vehicle_number_array[4];
				        	}else{
				        		$city_data = @$vehicle_number_array[0];
					        	$letter_data = @$vehicle_number_array[1];
					        	$class_data = @$vehicle_number_array[2];
					        	$number_data = @$vehicle_number_array[3];
				        	}
				        ?>
				        
						<div class="form-group">
							<label for="vehicle_number" class="col-sm-3 control-label">Vehicle Number <em class="text-danger">*</em></label>

							<div>
								<div class="col-sm-2">
						            <select name="city" class="form-control">
										<option value="">Metro<!-- মেট্রো নির্বাচন করুন--></option>
						              @foreach($city as $val)
						              <option value="{{$val->city}}" @if($val->city == @$city_data) selected="" @endif>{{$val->city_en}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1" style="padding-left:0">
						            <select name="reg_letter" class="form-control">
									<option value="">Letter<!--বর্ণ--></option>
						              @foreach($letter as $val)
						              <option value="{{$val->reg_letter}}" @if($val->reg_letter == @$letter_data) selected="" @endif>{{$val->reg_letter_en}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1" style="padding-left:0">
						            <select name="vehicle_class" class="form-control">
									<option value="">Class<!--ক্লাস--></option>
						              @foreach($class as $val)
						              <option value="{{$val->vehicle_class}}" 
						              	@if($val->vehicle_class == $class_data || $val->id == $class_data) selected="" @endif>{{$val->vehicle_class_en}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1 text-center" style="padding:5px 0 0 0;width:5px;">-</div>
						        <div class="col-sm-2">
									{!! Form::text('vehicle_number',@$number_data, ['class' => 'form-control', 'id' => 'vehicle_number', 'maxlength' => '4', 'onkeyup' => 'getBanglaNum()', 'placeholder' => 'Vehicle Number']) !!}
								</div>
								<span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
						    </div>

							

						</div>
						<div class="form-group">
							<label for="color" class="col-sm-3 control-label">Color <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('color',@$result->color, ['class' => 'form-control', 'id' => 'color', 'placeholder' => 'Color']) !!}
								<span class="text-danger">{{ $errors->first('color') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="year" class="col-sm-3 control-label">Year <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('year',@$result->year, ['class' => 'form-control', 'id' => 'year', 'placeholder' => 'Year']) !!}
								<span class="text-danger">{{ $errors->first('year') }}</span>
							</div>
						</div>
						<input type="hidden" id="vehicle_id" value="{{$result->id}}">
						<p ng-init='vehicle_doc="";errors={{json_encode($errors->getMessages())}};'></p>


						<?php /*
						<span class="loading" id="document_loading" style="padding-left: 80%" ng-if="vehicle_doc==''"></span>
						<div class="form-group" ng-repeat="doc in vehicle_doc" ng-cloak ng-if="vehicle_doc">
							<div class="form-group">
								<label class="col-sm-3 control-label"> @{{doc.document_name}} <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									<input type="file" name="file_@{{doc.id}}" class="form-control">
									<span class="text-danger">@{{ errors['file_'+doc.id][0] }}</span>								
									<div class="license-img" ng-if="doc.document">
										<a href="@{{doc.document}}" target="_blank">
											<img style="width: 200px;height: 100px;object-fit: contain;" ng-src="@{{doc.document}}">
										</a>
									</div>
									<div class="license-img" ng-if="!doc.document">
										<img style="width: 100px;height: 100px;object-fit: contain;" src="{{ url('images/driver_doc.png')}}">
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-sm-3 control-label" ng-if="doc.expiry_required=='1'">Expire Date <em class="text-danger">*</em></label>
								<div class="col-sm-6" ng-if="doc.expiry_required=='1'">
									<input type="text" name="expired_date_@{{doc.id}}" min="{{ date('Y-m-d') }}" value="@{{doc.expired_date}}" class="form-control document_expired" placeholder="Expire date" autocomplete="off">
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
						</div> */ ?>

						
						@foreach($vehicle_documents as $k => $document)
							@if($doc_exist = 0)@endif

							@foreach($user_documents as $ud)
			                	@if($document->id == $ud->document_id) 
			                		@if($image = $ud->document)@endif
			                		@if($status = $ud->status)@endif
			                		@if($expired_date = $ud->expired_date)@endif
									@if($doc_exist = '1')@endif
			                	@endif								
			                @endforeach

							@if($document->document_name == 'Registration Paper')
				          	<fieldset class="scheduler-border">
				          	<legend class="scheduler-border">{{$document->document_name}}</legend>
				          		<div class="col-lg-12 form-group" >
					                <div class="col-lg-6" style="padding-right:0px;">
					                  	<div>
					                    	<div class="text-right">Front Side</div>
					                    	<input type="file" name="{{$document->doc_name}}" class="form-control">
					                    	<span class="text-danger">
					                      		{{ $errors->first($document->doc_name) }} 
					                    	</span>
					                  	</div>
					                
						                <div style="padding-top:10px;">
						                    <div class="text-right">Back Side</div>
						                    <input type="file" name="{{$document->doc_name}}_back" class="form-control">
						                    <span class="text-danger">
						                      {{ $errors->first($document->doc_name.'_back')}} 
						                    </span>
						                </div>

						                @if($document->expiry_required == '1')
						                <div class="text-right" style="padding-top:10px;">Expiry Date</div>
										<div class="col-lg-12 form-group" style="padding-left:15px;padding-right:0px;">
											<input type="date"  name="expired_date_{{$document->id}}" class="form-control" value="@if($doc_exist == '1'){{@$expired_date}}@endif">
											<span class="text-danger"> 
												{{ $errors->first('expired_date_'.$document->id) }}
											</span>	
											<!-- min="{{ date('Y-m-d') }}" -->	  		
										</div>
										@endif

										<div class="text-right" style="padding-top:22px;">Status</div>
					                  	<div class="col-lg-12 form-group" style="padding:0;margin-left:0px;">
											<select class ='form-control' name='{{$document->doc_name}}_status'>
												<option value="0" @if($doc_exist == '1' AND @$status == '0') selected="" @endif>Pending</option>
												<option value="1" @if($doc_exist == '1' AND @$status == '1') selected="" @endif>Approved</option>
												<option value="2" @if($doc_exist == '1' AND @$status == '2') selected="" @endif>Rejected</option>
											</select>
										</div>

					                </div>

					                <div class="col-lg-6 text-right">
					                	<div class="col-lg-12 form-group">
											@if($doc_exist == 1)
						                	@php $img = @$image ? $image : url('images/driver_doc.png'); @endphp
											<div class="license-img">
												<a href="{{@$img}}" target="_blank">
													<img style="width: 200px;object-fit: cover;" src="{{@$img}}">
												</a>
											</div>
											@endif
										</div>											
					                </div>
					             </div> 
				      		</fieldset>
					        @else 
					        <fieldset class="scheduler-border">
				          	<legend class="scheduler-border">{{$document->document_name}}</legend>	
				          		<div class="col-lg-6">	
									<div>
										<input type="file" name="{{$document->doc_name}}" class="form-control">
										<span class="text-danger">
											{{ $errors->first($document->doc_name) }} 
										</span>	  		
									</div>

									@if($document->expiry_required == '1')
									<div style="margin-top:20px;">
										<input type="date" @if($document->doc_name !='enlistment_certificate')min="{{ date('Y-m-d') }}@endif" name="expired_date_{{$document->id}}" class="form-control" value="@if($doc_exist == '1'){{@$expired_date}}@endif">
										<span class="text-danger"> 
											{{ $errors->first('expired_date_'.$document->id) }}
										</span>		  		
									</div>
									@endif

									<div class="text-right">Status</div>
					                  	<div>
										<select class ='form-control' name='{{$document->doc_name}}_status'>
											<option value="0" @if($doc_exist == '1' AND @$status == '0') selected="" @endif>Pending</option>
											<option value="1" @if($doc_exist == '1' AND @$status == '1') selected="" @endif>Approved</option>
											<option value="2" @if($doc_exist == '1' AND @$status == '2') selected="" @endif>Rejected</option>
										</select>
									</div>
								</div>

								<div class="col-lg-6" style="padding-left:0">  	
									<div class="col-lg-12 form-group">
										@if($doc_exist == '1')
										@php $img = @$image ? $image : url('images/driver_doc.png'); @endphp
										<div class="license-img text-center">
											<a href="{{@$img}}" target="_blank">
												<img style="width: 200px;object-fit: cover;" src="{{@$img}}">
											</a>
										</div>	
										@endif	  		
									</div>
								</div>
				      		</fieldset>
							@endif
						@endforeach
						
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>
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

	getBanglaNum();

	
  function getBanglaNum() {
    var vehicle_number = $('#vehicle_number').val();

    var myArr = vehicle_number.split("");
    var unicode_text = "";
    var unicode_char = "";
    var char='',i;

    for (i = 0; i < myArr.length; i++) {
      char = myArr[i];
      switch(char) {
        /*case '0': unicode_char='০'; break;
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
        default: unicode_char = ''; break;*/


		case '০': unicode_char='0'; break;
        case '১': unicode_char='1'; break;
        case '২': unicode_char='2'; break;
        case '৩': unicode_char='3'; break;
        case '৪': unicode_char='4'; break;
        case '৫': unicode_char='5'; break;
        case '৬': unicode_char='6'; break;
        case '৭': unicode_char='7'; break;
        case '৮': unicode_char='8'; break;
        case '৯': unicode_char='9'; break;

		case '0': unicode_char='0'; break;
		case '1': unicode_char='1'; break;
		case '2': unicode_char='2'; break;
		case '3': unicode_char='3'; break;
		case '4': unicode_char='4'; break;
		case '5': unicode_char='5'; break;
		case '6': unicode_char='6'; break;
		case '7': unicode_char='7'; break;
		case '8': unicode_char='8'; break;
		case '9': unicode_char='9'; break;
		default: unicode_char = ''; break;
      } 
      unicode_text+=unicode_char;
    }    

    $('#vehicle_number').val(unicode_text);
  }
</script>
@endsection
