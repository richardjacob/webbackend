@extends('admin.template')
@section('main')
<link rel="stylesheet" href="{{ url('admin_assets/plugins/datetimepicker/jquery.datetimepicker.css') }}">
<div class="content-wrapper" ng-controller="driver_management" ng-init="login_user_type = '{{ LOGIN_USER_TYPE }}'; errors = {{ json_encode($errors->getMessages()) }};">
	<section class="content-header">
		<h1>Manage Driver <small>Add Driver's Remarks</small> </h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
	        <li class="active">Manage Driver</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/drivers_remarks') }}"> Driver's Remarks </a>
			</li>
	        <li>
	          <a href="{{ url(LOGIN_USER_TYPE.'/add_drivers_remarks/'.@$driver_id) }}"> Add</a>
	        </li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Add Driver's Remarks Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/add_drivers_remarks', 'class' => 'form-horizontal','files' => true]) !!}
					{{ Form::hidden('driver_id', @$driver_id, array('id'=>'driver_id')) }}
					
					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>
						
						<div class="form-group">
							<label for="conversation" class="col-sm-3 control-label">Conversation <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::textarea('conversation', old('conversation'), ['class' => 'form-control', 'style' => 'height:100px;', 'id' => 'conversation', 'placeholder' => 'Conversation' ]) !!}
								<span class="text-danger">{{ $errors->first('conversation') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="conversation_date" class="col-sm-3 control-label">Conversation Date <em class="text-danger">*</em></label>
							<div class="col-sm-6"> 	
								{!! Form::text('conversation_date', old('conversation_date'), ['class' => 'form-control', 'id' => 'conversation_date', 'placeholder' => 'Conversation Date']) !!}
								<span class="text-danger">{{ $errors->first(' 	conversation_date') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="followup_date" class="col-sm-3 control-label">Followup Date</label>
							<div class="col-sm-6">
								{!! Form::text('followup_date', old('followup_date'), ['class' => 'form-control', 'id' => 'followup_date', 'placeholder' => 'Followup Date']) !!}
								<span class="text-danger">{{ $errors->first('followup_date') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="remarks" class="col-sm-3 control-label">Remarks</label>
							<div class="col-sm-6">
								{!! Form::textarea('remarks', old('remarks'), ['class' => 'form-control', 'style' => 'height:100px;', 'id' => 'remarks', 'placeholder' => 'Remarks']) !!}
								<span class="text-danger">{{ $errors->first('remarks') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class ='form-control' name='status'>
										<option value="0" ng-selected="doc.status==0">Inprocessing</option>
										<option value="1" ng-selected="doc.status==1">Completed</option>
									</select>
							</div>
						</div>

						<div class="form-group">
							<label for="processing_status" class="col-sm-3 control-label">Processing Status</label>
							<div class="col-sm-6">
								<select class ='form-control' name='processing_status'>
									<option value="">Select</option>
									@foreach($processing_status_list as $data)
										<option value="{{$data->value}}" 
											@if($data->value == @$processing_status) selected="" @endif
										>{{$data->name_en}}</option>
									@endforeach
								</select>
							</div>
						</div>

						



						
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
						<span onclick="window.history.back();" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</span>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
