@extends('admin.template')
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

<div class="content-wrapper" ng-controller="driver_management" ng-init="login_user_type = '{{ LOGIN_USER_TYPE }}'; driver_doc = {{ $driver_doc }}; errors = {{ json_encode($errors->getMessages()) }};">
	<section class="content-header">
		<h1>Manage Driver <small> Driver</small></h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li class="active">Manage Driver</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/driver') }}"> Driver </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/add_driver') }}">Add </a>
			</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Add Driver Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/add_driver', 'class' => 'form-horizontal','files' => true]) !!}
					{{ Form::hidden('user_id', '', array('id'=>'user_id')) }}
					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<div class="form-group">
							<label for="input_first_name" class="col-sm-3 control-label">First Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('first_name',  old('first_name'), ['class' => 'form-control', 'id' => 'input_first_name', 'placeholder' => 'First Name']) !!}
								<span class="text-danger">{{ $errors->first('first_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_last_name" class="col-sm-3 control-label">Last Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('last_name', old('last_name'), ['class' => 'form-control', 'id' => 'input_last_name', 'placeholder' => 'Last Name']) !!}
								<span class="text-danger">{{ $errors->first('last_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_email" class="col-sm-3 control-label">Email</label>
							<div class="col-sm-6">
								{!! Form::text('email', old('email'), ['class' => 'form-control', 'id' => 'input_email', 'placeholder' => 'Email']) !!}
								<span class="text-danger">{{ $errors->first('email') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Password<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('password', '', ['class' => 'form-control', 'id' => 'input_password', 'placeholder' => 'Password']) !!}
								<span class="text-danger">{{ $errors->first('password') }}</span>
							</div>
						</div>
						{!! Form::hidden('user_type','Driver', ['class' => 'form-control', 'id' => 'user_type', 'placeholder' => 'Select']) !!}
						<div class="form-group">
							<label for="input_country_code" class="col-sm-3 control-label">Country Code<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class ='form-control' id = 'input_country_code' name='country_code'>
									<option value="880"> Bangladesh </option>
									{{-- @foreach($country_code_option as $country_code)
									<option value="{{@$country_code->phone_code}}" {{ ($country_code->id == old('country_id')) ? 'Selected' : ''}} data-id="{{ $country_code->id }}">{{$country_code->long_name}}</option>
									@endforeach--}}
									{!! Form::hidden('country_id', "18") !!} 
								</select>
								<span class="text-danger">{{ $errors->first('country_code') }}</span>
							</div>
						</div>

						{{-- <div class="form-group">
							<label for="input_country_code" class="col-sm-3 control-label">Country Code<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class ='form-control' id = 'input_country_code' name='country_code'>
									<option value=""> Select </option>
									@foreach($country_code_option as $country_code)
									<option value="{{@$country_code->phone_code}}" {{ ($country_code->id == old('country_id')) ? 'Selected' : ''}} data-id="{{ $country_code->id }}">{{$country_code->long_name}}</option>
									@endforeach
									{!! Form::hidden('country_id', old('country_id'), array('id'=>'country_id')) !!}
								</select>
								<span class="text-danger">{{ $errors->first('country_code') }}</span>
							</div>
						</div> --}}

						<div class="form-group">
							<label for="gender" class="col-sm-3 control-label">Gender <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{{ Form::radio('gender', '1', '', ['class' => 'form-check-input gender', 'id'=>'g_male']) }}
								<label for="g_male" style="font-weight: normal !important;">Male</label>
								{{ Form::radio('gender', '2', '', ['class' => 'form-check-input gender', 'id'=>'g_female']) }}
								<label for="g_female" style="font-weight: normal !important;">Female</label>
								<!--New Added By Nishat Start-->
								{{ Form::radio('gender', '3', '', ['class' => 'form-check-input gender', 'id'=>'g_other']) }}
                                <label for="g_other" style="font-weight: normal !important;">Other</label>
                                <!--New Added By Nishat End-->
								<div class="text-danger">{{ $errors->first('gender') }}</div>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Mobile Number <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('mobile_number', old('mobile_number'), ['class' => 'form-control', 'maxlength'=>'11', 'id' => 'mobile_number', 'placeholder' => 'Mobile Number']) !!}
								<span class="text-danger">{{ $errors->first('mobile_number') }}</span>
							</div>
						</div>
						@if (LOGIN_USER_TYPE!='company')
						<div class="form-group">
							<label for="input_company" class="col-sm-3 control-label">Company Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('company_name', $company, old('company_name'), ['class' => 'form-control', 'id' => 'input_company_name', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('company_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('status', 'Car_details', ['class' => 'form-control', 'id' => 'input_status', 'readonly']) !!}
							</div>
						</div>
						@endif

						<div class="form-group">
							<label for="input_address_line1" class="col-sm-3 control-label">Address Line 1 </label>
							<div class="col-sm-6">
								{!! Form::text('address_line1', old('address_line1'), ['class' => 'form-control', 'id' => 'input_address_line1', 'placeholder' => 'Address Line 1']) !!}
								<span class="text-danger">{{ $errors->first('address_line1') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_address_line2" class="col-sm-3 control-label">Address Line 2 </label>
							<div class="col-sm-6">
								{!! Form::text('address_line2', old('address_line2'), ['class' => 'form-control', 'id' => 'input_address_line2', 'placeholder' => 'Address Line 2']) !!}
								<span class="text-danger">{{ $errors->first('address_line2') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_city" class="col-sm-3 control-label">City </label>
							<div class="col-sm-6">
								{!! Form::text('city', old('city'), ['class' => 'form-control', 'id' => 'input_city', 'placeholder' => 'City']) !!}
								<span class="text-danger">{{ $errors->first('city') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_state" class="col-sm-3 control-label">State</label>
							<div class="col-sm-6">
								{!! Form::text('state', old('state'), ['class' => 'form-control', 'id' => 'input_state', 'placeholder' => 'State']) !!}
								<span class="text-danger">{{ $errors->first('state') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_postal_code" class="col-sm-3 control-label">Postal Code </label>
							<div class="col-sm-6">
								{!! Form::text('postal_code', old('postal_code'), ['class' => 'form-control', 'id' => 'input_postal_code', 'placeholder' => 'Postal Code']) !!}
								<span class="text-danger">{{ $errors->first('postal_code') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="input_nid_number" class="col-sm-3 control-label">NID Number </label>
							<div class="col-sm-5">
								{!! Form::text('nid_number', '', ['class' => 'form-control', 'id' => 'nid_number', 'placeholder' => 'NID Number']) !!}
								<span class="text-danger">{{ $errors->first('nid_number') }}</span>
							</div>
							<div class="col-sm-1" style="padding-left:2px;">
								<i class="fa fa-refresh fa-2x text-primary" role="button" id="driver_check_nid" data-id="-"></i>
							</div>
						</div>

						<div class="form-group">
							<label for="input_passport_number" class="col-sm-3 control-label">Passport Number </label>
							<div class="col-sm-6">
								{!! Form::text('passport_number', old('passport_number'), ['class' => 'form-control', 'id' => 'input_passport_number', 'placeholder' => 'Passport Number']) !!}
								<span class="text-danger">{{ $errors->first('passport_number') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_driving_licence_number" class="col-sm-3 control-label">Driving Licence Number</label>
							<div class="col-sm-6">
								{!! Form::text('driving_licence_number', old('driving_licence_number'), ['class' => 'form-control', 'id' => 'input_driving_licence_number', 'placeholder' => 'Driving Licence Number']) !!}
								<span class="text-danger">{{ $errors->first('driving_licence_number') }}</span>
							</div>
						</div>

						<div class="form-group" style="border: 1px solid #ddd; height: auto; margin-left:2%; padding: 10px 0px; margin-right:10%">
							<label for="profile_picture" class="col-sm-3 control-label">Profile Picture</label>
							<div class="col-sm-6" style="margin-bottom:10px;">
								<input type="file" name="profile_picture">
								<br>
								<img src="{{prifile_src_replace(@$result->id)}}" alt="Profile Picture" height="200px" width="200px">

								<div>
									<span class="text-danger">{{ $errors->has('profile_picture') ? $errors->first('profile_picture'):'' }}</span>	
								</div>
							</div>
						</div>




						@foreach($driver_doc as $k=> $document)   
				          @if($document->document_name == 'Driving License' || 
				            $document->document_name == 'NID Or Passport' ||
				            $document->document_name == 'NID')
				            <!-- Both Design Here -->
				            <fieldset class="scheduler-border">
				              <legend class="scheduler-border">{{$document->document_name}}</legend>

				              <div class="col-lg-12 form-group" >
				                <div class="col-lg-6" style="padding-left:0">

				                	@if($document->document_name == 'Driving License')
						                <div>
											<div class="text-right">Smart Card/ Slip</div>
											<div>
												{!! Form::select($document->doc_name.'_document_type', $card_slip_array, '', ['class' => 'form-control', 'id' => 'document_type', 'placeholder' => 'Select']) !!}
												<span class="text-danger">{{ $errors->first($document->doc_name.'_document_type') }}</span>
											</div>
										</div>
					                
					                @elseif($document->document_name == 'NID Or Passport' || $document->document_name == 'NID')
						                <div>
											<div class="text-right">NID/ Passport</div>
											<div>
												{!! Form::select($document->doc_name.'_document_type', $nid_passport_array, '', ['class' => 'form-control', 'id' => 'document_type', 'placeholder' => 'Select']) !!}
												<span class="text-danger">{{ $errors->first($document->doc_name.'_document_type') }}</span>
											</div>
										</div>
					                @endif

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
				                      {{ $errors->first($document->doc_name.'_back') }} 
				                    </span>
				                  </div>

				                  @if($document->expire_on_date == 'Yes')
					                  <div class="text-right" style="padding-top:10px;">Expiry Date</div>
					                  <div class="col-lg-12 form-group" style="padding:0;margin-left:0px;">
					                    <input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" value="{{old('expired_date_'.$document->id)}}">

					                    <span class="text-danger"> 
					                      {{ $errors->first('expired_date_'.$document->id) }}
					                    </span>         
					                  </div>
				                  @endif


								  @if (LOGIN_USER_TYPE!='company')
				                  	<div class="text-right" style="padding-top:10px;">Status</div>
				                  	<div class="col-lg-12 form-group" style="padding:0;margin-left:0px;">
										<select class ='form-control' name='{{$document->doc_name}}_status'>
											<option value="0" @if(old($document->doc_name.'_status') == '0') selected="" @endif>Pending</option>
											<option value="1" @if(old($document->doc_name.'_status') == '1') selected="" @endif>Approved</option>
											<option value="2" @if(old($document->doc_name.'_status') == '2') selected="" @endif>Rejected</option>
										</select>
									</div>
								  @endif

				                </div>

				                <div class="col-lg-6 text-right">			                	
				                
				                 @php $image = url('images/driver_doc.png'); @endphp
				                  <div class="license-img">
				                    <a href="{{@$image}}" target="_blank">
				                      <img style="width: 200px;height: 100px;object-fit: cover;" src="{{@$image}}">
				                    </a>
				                  </div>   
				                </div>


				              </div>
				            </fieldset>
				          @else
				          	<fieldset class="scheduler-border">
				              	<legend class="scheduler-border">
				              		<label>{{$document->document_name}}</label>
				              	</legend>
					            <div class="col-lg-12 form-group" style="padding:0;margin-left:0px;">
					              <input type="file" name="{{$document->doc_name}}" class="form-control">
					              <span class="text-danger">
					                {{ $errors->first($document->doc_name) }} 
					              </span>
					              	
					                @php $image = url('images/driver_doc.png'); @endphp

					              <div class="license-img">
					              <a href="{{@$image}}" target="_blank">
					                <img style="width: 200px;height: 100px;object-fit: cover;" src="{{@$image}}">
					              </a>
					              </div>          
					            </div>   
					            @if($document->expire_on_date == 'Yes')
						            <div class="col-lg-12 form-group" style="padding:0;margin-left:0px;">
						              <input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" value="{{old('expired_date_'.$document->id)}}">
						              <span class="text-danger"> 
						                {{ $errors->first('expired_date_'.$document->id) }}
						              </span>         
						            </div>
					            @endif

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
						<span class="loading" id="document_loading" style="padding-left: 80%;display: inline-block;height: 50px;" ng-if="driver_doc==''"></span>
												
						<div class="form-group" ng-repeat="doc in driver_doc" ng-cloak ng-if="driver_doc" >
						<div style="border: 1px solid #ddd; padding: 10px 0px; height:140px; margin-left:10%; margin-right:10%">
								<label class="col-sm-3 control-label">@{{doc.document_name}} <em class="text-danger">*</em></label>
								<div class="col-sm-6" style="margin-bottom:5px;">
									<input type="file" name="file_@{{doc.id}}" class="form-control">
									<span class="text-danger">@{{ errors['file_'+doc.id][0] }}</span>
								</div>
								
								<label class="col-sm-3 control-label" ng-if="doc.expiry_required=='1'"><!-- @{{doc.document_name}} -->  Expire Date <em class="text-danger">*</em></label>
								<div class="col-sm-6" ng-if="doc.expiry_required=='1'">
									<input type="text" min="{{ date('Y-m-d') }}" name="expired_date_@{{doc.id}}" class="form-control document_expired" placeholder="Expire date" autocomplete="off" readonly>
									<span class="text-danger">@{{ errors['expired_date_'+doc.id][0] }}</span>
								</div>
								<label class="col-sm-3 control-label"> <!-- @{{doc.document_name}} -->  Status<em class="text-danger">*</em></label>
								<div class="col-sm-6">
									<select class ='form-control' name='@{{doc.doc_name}}_status'>
										<option value="0" ng-selected="doc.status==0">Pending</option>
										<option value="1" ng-selected="doc.status==1">Approved</option>
										<option value="2" ng-selected="doc.status==2">Rejected</option>
									</select>
								</div>
							
								</div>
						</div> */ ?>
					
						<!-- @if(LOGIN_USER_TYPE!='company' || Auth::guard('company')->user()->id != 1)
						<span class="bank_detail">
							<div class="form-group"  style="margin-top:10px;">
								<label for="input_status" class="col-sm-3 control-label">Account Holder Name <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('account_holder_name', old('account_holder_name'), ['class' => 'form-control', 'id' => 'account_holder_name', 'placeholder' => 'Account Holder Name']) !!}
									<span class="text-danger">{{ $errors->first('account_holder_name') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Account Number <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('account_number', old('account_number'), ['class' => 'form-control', 'id' => 'account_number', 'placeholder' => 'Account Number']) !!}
									<span class="text-danger">{{ $errors->first('account_number') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Name of Bank <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bank_name', old('bank_name'), ['class' => 'form-control', 'id' => 'bank_name', 'placeholder' => 'Name of Bank']) !!}
									<span class="text-danger">{{ $errors->first('bank_name') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Bank Location <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bank_location', old('bank_location'), ['class' => 'form-control', 'id' => 'bank_location', 'placeholder' => 'Bank Location']) !!}
									<span class="text-danger">{{ $errors->first('bank_location') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">BIC/SWIFT Code <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bank_code', old('bank_code'), ['class' => 'form-control', 'id' => 'bank_code', 'placeholder' => 'BIC/SWIFT Code']) !!}
									<span class="text-danger">{{ $errors->first('bank_code') }}</span>
								</div>
							</div>
						</span>
						@endif -->
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
						<button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
