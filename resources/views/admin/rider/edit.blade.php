@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="destination_admin">
	<section class="content-header">
		<h1> Rider <small>Edit Rider </small></h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a></li>
			<li class="active">Rider</li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/rider') }}">Manage Rider </a></li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/edit_rider/'.$result->id) }}"> Edit </a></li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Rider Form</h3>
					</div>
					{!! Form::open(['url' => 'admin/edit_rider/'.$result->id, 'class' => 'form-horizontal']) !!}
					<div class="box-body">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<div class="form-group">
							<label for="input_first_name" class="col-sm-3 control-label">First Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('first_name', $result->first_name, ['class' => 'form-control', 'id' => 'input_first_name', 'placeholder' => 'First Name']) !!}
								<span class="text-danger">{{ $errors->first('first_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_last_name" class="col-sm-3 control-label">Last Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('last_name', $result->last_name, ['class' => 'form-control', 'id' => 'input_last_name', 'placeholder' => 'Last Name']) !!}
								<span class="text-danger">{{ $errors->first('last_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_email" class="col-sm-3 control-label">Email</label>
							<div class="col-sm-6">
								{!! Form::text('email', $result->email, ['class' => 'form-control', 'id' => 'input_email', 'placeholder' => 'Email']) !!}
								<span class="text-danger">{{ $errors->first('email') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="is_email_valid" class="col-sm-3 control-label">Is email Valid ?</label>
							<div class="col-sm-6">
								{{ Form::radio('is_email_valid', '1', $result->getOriginal('is_email_valid')=='1' ? true:false, ['class' => 'form-check-input is_email_valid', 'id'=>'email_valid']) }}
								<label for="email_valid" style="font-weight: normal !important;">Yes</label>
								{{ Form::radio('is_email_valid', '0', $result->getOriginal('is_email_valid')=='0' ? true:false, ['class' => 'form-check-input is_email_valid', 'id'=>'email_invalid']) }}
								<label for="email_invalid" style="font-weight: normal !important;">No</label>

								<div class="text-danger">{{ $errors->first('is_email_valid') }}</div>
							</div>
						</div>

						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Password</label>
							<div class="col-sm-6">
								{!! Form::text('password', '', ['class' => 'form-control', 'id' => 'input_password', 'placeholder' => 'Password']) !!}
								<span class="text-danger">{{ $errors->first('password') }}</span>
							</div>
						</div>
						{!! Form::hidden('user_type','Rider', ['class' => 'form-control', 'id' => 'user_type', 'placeholder' => 'Select']) !!}
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Country Code<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class='form-control' id = 'input_country_code' name='country_code' >
									<option value="880">Bangladesh </option>
									{{-- @foreach($country_code_option as $country_code)
									<option value="{{@$country_code->phone_code}}" {{ ($country_code->id == $result->country_id) ? 'Selected' : ''}} data-id="{{ $country_code->id }}">{{$country_code->long_name}}</option>
									@endforeach --}}
									{{-- {!! Form::hidden('country_id', $result->country_id, array('id'=>'country_id')) !!} --}}
									{!! Form::hidden('country_id', "18") !!}
								</select>
								<span class="text-danger">{{ $errors->first('country_code') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="gender" class="col-sm-3 control-label">Gender <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{{ Form::radio('gender', '1', $result->getOriginal('gender')=='1' ? true:false, ['class' => 'form-check-input gender', 'id'=>'g_male']) }}
								<label for="g_male" style="font-weight: normal !important;">Male</label>
								{{ Form::radio('gender', '2', $result->getOriginal('gender')=='2' ? true:false, ['class' => 'form-check-input gender', 'id'=>'g_female']) }}
								<label for="g_female" style="font-weight: normal !important;">Female</label>
								
								
								<!--New Added By Nishat Start-->
								{{ Form::radio('gender', '3', $result->getOriginal('gender')=='3' ? true:false, ['class' => 'form-check-input gender', 'id'=>'g_other']) }}
                                <label for="g_other" style="font-weight: normal !important;">Other</label>
                                <!--New Added By Nishat End-->

								<div class="text-danger">{{ $errors->first('gender') }}</div>
							</div>
						</div>
						<div class="form-group">
							<label for="mobile_number" class="col-sm-3 control-label">Mobile Number <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('mobile_number', old('mobile_number',$result->mobile_number), ['class' => 'form-control', 'maxlength'=>'11', 'id' => 'mobile_number', 'placeholder' => 'Mobile Number']) !!}
								<span class="text-danger">{{ $errors->first('mobile_number') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Home Location</label>
							<div class="col-sm-6">
								<div class="autocomplete-input-container">
									<div class="autocomplete-input">
										{!! Form::text('home_location', @$location->home, ['class' => 'form-control', 'id' => 'input_home_location', 'placeholder' => 'Home Location','autocomplete' => 'off']) !!}
									</div>
									<ul class="autocomplete-results home-autocomplete-results">
									</ul>
								</div>
								<span class="text-danger">{{ $errors->first('home_location') }}</span>
							</div>
						</div>
						{!! Form::hidden('home_latitude',@$location->home_latitude, ['class' => 'form-control', 'id' => 'home_latitude', 'placeholder' => 'Select']) !!}
						{!! Form::hidden('home_longitude',@$location->home_longitude, ['class' => 'form-control', 'id' => 'home_longitude', 'placeholder' => 'Select']) !!}
						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Work Location</label>
							<div class="col-sm-6">
								<div class="autocomplete-input-container">
									<div class="autocomplete-input">
										{!! Form::text('work_location', @$location->work, ['class' => 'form-control', 'id' => 'input_work_location', 'placeholder' => 'Work Location','autocomplete' => 'off']) !!}
									</div>
									<ul class="autocomplete-results work-autocomplete-results">
									</ul>
								</div>
								<span class="text-danger">{{ $errors->first('work_location') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('status') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="input_is_our_employee" class="col-sm-3 control-label">Is Our Employee ?<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('is_our_employee', array('0' => 'No', '1' => 'Yes'), $result->is_our_employee, ['class' => 'form-control', 'id' => 'input_is_our_employee', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('is_our_employee') }}</span>
							</div>
						</div>


						{!! Form::hidden('work_latitude',@$location->work_latitude, ['class' => 'form-control', 'id' => 'work_latitude', 'placeholder' => 'Select']) !!}
						{!! Form::hidden('work_longitude',@$location->work_longitude, ['class' => 'form-control', 'id' => 'work_longitude', 'placeholder' => 'Select']) !!}
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
