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

#suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
#suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
#suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
#search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px}

</style>

<div class="content-wrapper" ng-controller="vehicle_management">
	<section class="content-header" ng-init='vehicle_id=0'>
		<h1>Manage Vehicles <small>Add Vehicles</small></h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a></li>
			<li class="active">Manage Vehicles </li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/vehicle') }}"> Vehicles </a></li>
			<li class="active"><a href="{{ url(LOGIN_USER_TYPE.'/add_vehicle/') }}"> Add</a></li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0 ne_ed">
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
									{!! Form::text('company_name_suggestion', '', ['class' => 'form-control search-box', 'id' => 'input_company_name', 'data-id' => 'search-box', 'placeholder' => 'Enter Company Name or Mobile Number OR Company ID', 'autocomplete' => 'off']) !!}
									<input type="hidden" id="company_id" name="company_name">
									<span class="text-danger">{{ $errors->first('company_name') }}</span>
									<div id="suggesstion_box_company"></div>
								</div>
							</div>
							<div class="form-group">
								<label for="input_driver" class="col-sm-3 control-label">Driver Name <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('driver_name', '', ['class' => 'form-control search-box', 'id' => 'input_driver_name', 'data-id' => 'search-box', 'placeholder' => 'Enter Driver Name or Mobile Number OR Driver ID', 'autocomplete' => 'off']) !!}
									<input type="hidden" id="driver_name" name="driver_name">
									<span class="text-danger">{{ $errors->first('driver_name') }}</span>
									<div id="suggestion_company_driver"></div>
								</div>
							</div>

							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), '', ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
									<span class="text-danger">{{ $errors->first('status') }}</span>
								</div>
							</div>
						@else							
							<div class="form-group">
								<label for="input_driver_name" class="col-sm-3 control-label">Driver Name <em class="text-danger">*</em></label>
									
								<div class="col-sm-6">
									<select class='form-control' name="driver_name">
										<option value="">Select</option>
										@foreach($company_driver as $driver)
										<option value="{{$driver->id}}">{{$driver->driver_name}} - {{$driver->id}} </option>
										@endforeach
									</select>
								</div>
							</div>


						@endif

					
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
						        <div class="col-sm-2">
						            <select name="city" class="form-control">
						              <option value="">Metro<!-- মেট্রো নির্বাচন করুন--></option>
						              @foreach($city as $val)
						              <option value="{{$val->city}}">{{$val->city_en}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1" style="padding-left:0">
						            <select name="reg_letter" class="form-control">
						              <option value="">Letter<!--বর্ণ--></option>
						              @foreach($letter as $val)
						              <option value="{{$val->reg_letter}}">{{$val->reg_letter_en}} &nbsp; {{$val->reg_letter}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1" style="padding-left:0">
						            <select name="vehicle_class" class="form-control">
						              <option value="">Class<!--ক্লাস--></option>
						              @foreach($class as $val)
						              <option value="{{$val->vehicle_class}}">{{$val->vehicle_class_en}}</option>
						              @endforeach
						          </select>
						        </div>
						        <div class="col-sm-1 text-center" style="padding:5px 0 0 0;width:5px;">-</div>
								<div class="col-sm-2">
									{!! Form::text('vehicle_number','', ['class' => 'form-control', 'id' => 'vehicle_number', 'placeholder' => 'Vehicle Number', 'onkeyup' => 'getBanglaNum()', 'maxlength' => '4']) !!}
								</div>
							</div>
							<span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
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

						@foreach($vehicle_documents as $k => $document)
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
						                @if($document->expire_on_date == 'Yes')
						                <div class="text-right" style="padding-top:10px;">Expiry Date</div>
										<div class="col-lg-12 form-group" style="padding-left:15px;padding-right:0px;">
											<input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" value="{{old('expired_date_'.$document->id)}}" class="form-control">
											<span class="text-danger"> 
												{{ $errors->first('expired_date_'.$document->id) }}
											</span>		  		
										</div>
										@endif	

										<div class="text-right" style="padding-top:22px;">Status</div>
										<div class="col-lg-12 form-group" style="padding:0;margin-left:0px;">
											<select class ='form-control' name='{{$document->doc_name}}_status'>
												<option value="0" @if(old($document->doc_name.'_status') == '0') selected="" @endif>Pending</option>
												<option value="1" @if(old($document->doc_name.'_status') == '1') selected="" @endif>Approved</option>
												<option value="2" @if(old($document->doc_name.'_status') == '2') selected="" @endif>Rejected</option>
											</select>
										</div>
					                </div>



					                <div class="col-lg-6 text-right">
					                	@php $img = url('images/driver_doc.png'); @endphp
										<div class="license-img">
											<a href="{{$img}}" target="_blank">
												<img style="width: 200px;height: 100px;object-fit: cover;" src="{{$img}}">
											</a>
										</div>
					                </div>

					                

					             </div> 
				      		</fieldset>
					        @else 
					        <fieldset class="scheduler-border">
				          	<legend class="scheduler-border">{{$document->document_name}}</legend>	  	
								<div class="col-lg-12 form-group">
									<input type="file" name="{{$document->doc_name}}" class="form-control">
									<span class="text-danger">
										{{ $errors->first($document->doc_name) }} 
									</span>
									@php $img = url('images/driver_doc.png'); @endphp
									<div class="license-img">
										<a href="{{$img}}" target="_blank">
											<img style="width: 200px;height: 100px;object-fit: cover;" src="{{$img}}">
										</a>
									</div>		  		
								</div>		  	
								@if($document->expire_on_date == 'Yes')
								<div class="col-lg-12 form-group">
									<input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" value="{{old('expired_date_'.$document->id)}}" class="form-control">
									<span class="text-danger"> 
										{{ $errors->first('expired_date_'.$document->id) }}
									</span>		  		
								</div>
								@endif

								<div class="text-right" style="padding-top:22px;">Status</div>
			                  	<div class="col-lg-12 form-group" style="padding:0;margin-left:0px;">
									<select class ='form-control' name='{{$document->doc_name}}_status'>
										<option value="0" @if(old($document->doc_name.'_status') == '0') selected="" @endif>Pending</option>
										<option value="1" @if(old($document->doc_name.'_status') == '1') selected="" @endif>Approved</option>
										<option value="2" @if(old($document->doc_name.'_status') == '2') selected="" @endif>Rejected</option>
									</select>
								</div>
				      		</fieldset>
							@endif
						@endforeach
						
						<?php /*
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
						</div> */?>
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

	getBanglaNum();

	function getBanglaNum(){
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



$('#input_company_name').on('keyup', function() {
	var keyword = $(this).val();
	if(keyword.length >=3) suggestion_company(keyword);
});



function suggestion_company(keyword){
	var url = "{{url('admin/ajax/suggestion_company')}}";
	$.ajax({
		type:'POST',
		url:url,
		data:{
			keywords:keyword,
			_token: "{{ csrf_token() }}"
		},
		beforeSend:function(){
		$("#input_company_name").css("background","#eee");
		},
		success:function(data){
		$("#suggesstion_box_company").show();
		$("#suggesstion_box_company").html(data);
		$("#input_company_name").css("background","#FFF");
		}	       
	});
}

function select_from_company_suggestion(label, val){
    $("#input_company_name").val(label);
    $("#suggesstion_box_company").hide();
    $("#company_id").val(val);
}

$('#input_driver_name').on('keyup', function() {
	var company_id = $("#company_id").val();
	var keyword = $(this).val();
	
	if(keyword.length >=3) {
		if(company_id !=''){
			suggestion_company_driver(company_id, keyword);
		}else{
			alert("Company name is required.");
		}
	}
});


function suggestion_company_driver(company_id, keyword){
	var url = "{{url('admin/ajax/suggestion_company_driver')}}";
	$.ajax({
		type:'POST',
		url:url,
		data:{
			company_id: company_id,
			keywords:keyword,
			_token: "{{ csrf_token() }}"
		},
		beforeSend:function(){
			$("#input_driver_name").css("background","#eee");
		},
		success:function(data){
			$("#suggestion_company_driver").show();
			$("#suggestion_company_driver").html(data);
			$("#input_driver_name").css("background","#FFF");
		}	       
	});
}

function select_from_suggestion_company_driver(label, val){
    $("#input_driver_name").val(label);
    $("#suggestion_company_driver").hide();
    $("#driver_name").val(val);
}
	
</script>
@endsection
