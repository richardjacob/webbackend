<title>{{ trans('messages.driver_dashboard.edit') }} {{ trans('messages.driver_dashboard.vehicle_details') }}</title>
@extends('template_driver_dashboard')
@section('main')
<style>
  fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 10!important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
  }
  legend.scheduler-border {
    width:inherit; /* Or auto */
    padding:0 10px; /* To give a bit of padding on the left and right */
    border-bottom:none;
}
</style>

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;" ng-controller="vehicle_details">
	<div class="page-lead separated--bottom  text--center text--uppercase">
		<h1 class="flush-h1 flush">{{ trans('messages.driver_dashboard.edit') }} {{ trans('messages.driver_dashboard.vehicle_details') }}</h1>
	</div>

	{!! Form::open(['url' => 'update_vehicle', 'class' => '','id'=>'vehicle_form','files' => true]) !!}
	<div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 25px 0px 15px;">
		<div class="col-lg-12 form-group">
			<label >{{ trans('messages.driver_dashboard.vehicle_make') }}</label>
			{!! Form::select('vehicle_make_id',$make, $result->vehicle_make_id, ['class' => 'form-control', 'id' => 'vehicle_make', 'placeholder' => trans('messages.driver_dashboard.select') ]) !!}
			<span class="text-danger">{{ $errors->first('vehicle_make_id') }}</span>
		</div>
		<div class="col-lg-12 form-group vehicle_model">
			<label>{{ trans('messages.driver_dashboard.vehicle_model') }}</label>
			{!! Form::select('vehicle_model_id',$model, $result->vehicle_model_id, ['class' => 'form-control', 'id' => 'vehicle_model', 'placeholder' => trans('messages.driver_dashboard.select') ]) !!}
			<span class="text-danger">{{ $errors->first('vehicle_model_id') }}</span>
		</div>
		<div class="col-lg-12 form-group">
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
			<label>{{ trans('messages.driver_dashboard.vehicle_number') }} ({{$vehicle_number}})</label>
			<div>
		        <div class="col-sm-3" style="padding-left:0">
		            <select name="city" class="form-control">
		              <option value="">মেট্রো নির্বাচন করুন</option>
		              @foreach($city as $val)
		              <option value="{{$val->city}}" @if($val->city == @$city_data) selected="" @endif>{{$val->city}}</option>
		              @endforeach
		          </select>
		        </div>
		        <div class="col-sm-2" style="padding-left:0">
		            <select name="reg_letter" class="form-control">
		              <option value="">বর্ণ</option>
		              @foreach($letter as $val)
		              <option value="{{$val->reg_letter}}" @if($val->reg_letter == @$letter_data) selected="" @endif>{{$val->reg_letter}}</option>
		              @endforeach
		          </select>
		        </div>
		        <div class="col-sm-2" style="padding-left:0">
		            <select name="vehicle_class" class="form-control">
		              <option value="">গাড়ির ক্লাস</option>
		              @foreach($class as $val)
		              <option value="{{$val->vehicle_class}}" 
		              	@if($val->vehicle_class == $class_data || $val->id == $class_data) selected="" @endif>{{$val->vehicle_class}}</option>
		              @endforeach
		          </select>
		        </div>
		        <div class="col-sm-1 text-center" style="padding:5px 0 0 0;width:5px;">-</div>

		        <div class="col-sm-4">		        	
		          {!! Form::text('vehicle_number', @$number_data, ['class' => 'form-control', 'id' => 'vehicle_number', 'maxlength' => '4', 'onkeyup' => 'validate(event)', 'placeholder' => trans('messages.driver_dashboard.vehicle_number') ]) !!}
		        </div>
		    </div>			
			<span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
		</div>




		<div class="col-lg-12 form-group">
			<label>{{ trans('messages.driver_dashboard.vehicle_color') }}</label>
			{!! Form::text('color',$result->color, ['class' => 'form-control', 'id' => 'color', 'placeholder' => trans('messages.driver_dashboard.vehicle_color') ]) !!}
			<span class="text-danger">{{ $errors->first('color') }}</span>
		</div>
		<div class="col-lg-12 form-group">
			<label>{{ trans('messages.driver_dashboard.vehicle_year') }}</label>
			{!! Form::text('year',$result->year, ['class' => 'form-control', 'id' => 'year', 'placeholder' => trans('messages.driver_dashboard.vehicle_year'),'autocomplete'=>'off']) !!}
			<span class="text-danger">{{ $errors->first('year') }}</span>
		</div>
		<div class="col-lg-12 form-group">
			<label>{{ trans('messages.driver_dashboard.vehicle_type') }}</label>
			<div class="cls_vehicle">
				@php $vehicle_types = explode(',', $result->vehicle_id); @endphp

				@foreach($vehicle_type as $type)
				<li class="col-lg-6 col-md-12 col-12">
					<input type="checkbox"  name="vehicle_type[]" class="form-check-input vehicle_type" value="{{ $type->id }}" 
					{{ in_array($type->id,$vehicle_types) ? "checked" : "" }} /> {{ $type->car_name }}
				</li>
				@endforeach
				<span class="text-danger">{{ $errors->first('vehicle_type') }}</span>
			</div>
		</div>

		<div class="col-lg-12 form-group">
			<label>{{trans('messages.driver_dashboard.handicap')}} {{trans('messages.ride.accessibility')}} {{trans('messages.driver_dashboard.available')}}</label>
			<div>
				{{ Form::radio('handicap', '1', in_array('2',$options) ? true:false, ['class'=>'form-check-input']) }} 
				{{ trans('messages.driver_dashboard.yes') }}
				{{ Form::radio('handicap', '0', !in_array('2',$options) ? true:false, ['class'=>'form-check-input']) }} 
				{{ trans('messages.driver_dashboard.no') }}
			</div>
			<div class="text-danger">{{ $errors->first('handicap') }}</div>
		</div>

		<div class="col-lg-12 form-group">
			<label>{{trans('messages.driver_dashboard.child_seat')}} {{trans('messages.ride.accessibility')}} {{trans('messages.driver_dashboard.available')}}</label>
			<div>
				{{ Form::radio('child_seat', '1', in_array('3',$options) ? true:false, ['class'=>'form-check-input']) }} 
				{{ trans('messages.driver_dashboard.yes') }}
				{{ Form::radio('child_seat', '0', !in_array('3',$options) ? true:false, ['class'=>'form-check-input']) }} 
				{{ trans('messages.driver_dashboard.no') }}
			</div>
			<div class="text-danger">{{ $errors->first('child_seat') }}</div>
		</div>

		@if($result->user->gender=='2')
		<div class="col-lg-12 form-group">
			<label>{{trans('messages.driver_dashboard.request_from')}}</label>
			<div>
				{{ Form::radio('request_from', '1', in_array('1',$options) ? true:false, ['class'=>'form-check-input']) }} 
				{{ trans('messages.profile.female') }}
				{{ Form::radio('request_from', '0', !in_array('1',$options) ? true:false, ['class'=>'form-check-input']) }} 
				{{ trans('messages.driver_dashboard.both') }}
			</div>
			<div class="text-danger">{{ $errors->first('request_from') }}</div>
		</div>
		@else
		{{ Form::hidden('request_from', '0') }}
		@endif


		@foreach($vehicle_documents as $document)
			@if($document->document_name == 'Registration Paper')
          	<fieldset class="scheduler-border">
          	<legend class="scheduler-border">{{$document->document_name}}</legend>
          		<div class="col-lg-12 form-group" >
	                <div class="col-lg-6" style="padding-left:0">
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
						<div class="col-lg-12 form-group" style="padding:0px;">
							<input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" value="{{$document->expired_date}}">
							<span class="text-danger"> 
								{{ $errors->first('expired_date_'.$document->id) }}
							</span>		  		
						</div>
						@endif
	                </div>

	                <div class="col-lg-6 text-right">
	                	@php $image = ($document->document !='') ? $document->document : url('images/driver_doc.png'); @endphp
						<div class="license-img">
							<a href="{{$image}}" target="_blank">
								<img style="width:200px;height:200px;" src="{{$image}}">
							</a>
						</div>	
	                </div>
	             </div> 
      		</fieldset>
	        @else 
	        <fieldset class="scheduler-border">
          	<legend class="scheduler-border">{{$document->document_name}}</legend>	  	 <div class="col-lg-12 form-group" >
	                <div class="col-lg-6" style="padding-left:0">
	                  	<div>
							<input type="file" name="{{$document->doc_name}}" class="form-control">
							<span class="text-danger">
								{{ $errors->first($document->doc_name) }} 
							</span>	
						</div>
					@if($document->expiry_required == '1')
					<div class="text-right" style="padding-top:10px;">Expiry Date</div>
					<div class="col-lg-12 form-group" style="padding:0px;">
						<input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" value="{{$document->expired_date}}">
						<span class="text-danger"> 
							{{ $errors->first('expired_date_'.$document->id) }}
						</span>		  		
					</div>
					@endif
				</div>
				<div class="col-lg-6  text-right">
					@php $image = ($document->document !='') ? $document->document : url('images/driver_doc.png'); @endphp
					<div class="license-img">
						<a href="{{$image}}" target="_blank">
							<img style="width:200px;height:200px;" src="{{$image}}">
						</a>
					</div>	
				</div>
      		</fieldset>
			@endif
		@endforeach
	</div>

	<input type="hidden" name="vehicle_id" value="{{$result->id ?? ''}}">
	<div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom:0px !important;">
		<button style="padding: 0px 30px !important;
		font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue" id="update_btn">{{trans('messages.user.update')}}</button>
	</div>
	{{ Form::close() }}
</div>
</div>
</div>
</div>
</main>
<style type="text/css">
	.form-check-input[type="radio"]
	{
		margin:4px 0 0 !important;
		vertical-align: bottom;
		margin-left: 10px !important;
	}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="{{ url('admin_assets/plugins/datepicker/bootstrap-datepicker3.css') }}">
<script>
	$("#year").datepicker({
		format: "yyyy",
		viewMode: "years", 
		minViewMode: "years",
		autoclose : true,
		startDate: '1950',
		endDate: '<?php echo date('Y'); ?>'
	});

	$( document ).ready(function() {
	    getBanglaNum();
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
    getBanglaNum();
  }



  function getBanglaNum() {
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
