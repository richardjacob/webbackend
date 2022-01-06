@extends('admin.template')
@section('main')
<link rel="stylesheet" href="{{ url('admin_assets/plugins/datetimepicker/jquery.datetimepicker.css') }}">
<div class="content-wrapper" ng-controller="driver_management" ng-init="login_user_type = '{{ LOGIN_USER_TYPE }}'; driver_doc = ''; errors = {{ json_encode($errors->getMessages()) }};">
	<section class="content-header">
		<h1>Manage Driver <small>Edit Driver Remarks</small></h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li class="active">DManage river</li>
	        <li>
	          <a href="{{ url(LOGIN_USER_TYPE.'/drivers_remarks') }}"> Driver's Remarks</a>
	        </li>
	        <li>
	          <a href="{{ url(LOGIN_USER_TYPE.'/edit_drivers_remarks/'.$result->id) }}"> Edit</a>
	        </li>

		</ol>
	</section>
	
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Driver Remarks Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/edit_drivers_remarks/'.$result->id, 'class' => 'form-horizontal','files' => true, 'novalidate']) !!}
					
					{{ Form::hidden('driver_id', $result->driver_id, array('id'=>'driver_id')) }}

					{{ Form::hidden('driver_remarks_id', $result->id, array('id'=>'driver_remarks_id')) }}

					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>
						
						<div class="form-group">
							<label for="Conversation" class="col-sm-3 control-label">Conversation<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('conversation', $result->conversation, ['class' => 'form-control', 'id' => 'conversation', 'placeholder' => 'Conversation']) !!}
								<span class="text-danger">{{ $errors->first('conversation') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="conversation_date" class="col-sm-3 control-label">Conversation Date <em class="text-danger">*</em></label>
							<div class="col-sm-6"> 	
								{!! Form::text('conversation_date', $result->conversation_date, ['class' => 'form-control', 'id' => 'conversation_date', 'placeholder' => 'Conversation Date']) !!}
								<span class="text-danger">{{ $errors->first(' 	conversation_date') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="followup_date" class="col-sm-3 control-label">Followup Date</label>
							<div class="col-sm-6">
								{!! Form::text('followup_date', $result->followup_date, ['class' => 'form-control', 'id' => 'followup_date', 'placeholder' => 'Followup Date']) !!}
								<span class="text-danger">{{ $errors->first('followup_date') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="remarks" class="col-sm-3 control-label">Remarks</label>
							<div class="col-sm-6">
								{!! Form::textarea('remarks', $result->remarks, ['class' => 'form-control', 'style' => 'height:100px;', 'id' => 'remarks', 'placeholder' => 'Remarks']) !!}
								<span class="text-danger">{{ $errors->first('remarks') }}</span>
							</div>
						</div>

						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class ='form-control' name='status'>
									<option value="0" 
									@if($result->remarks_status == '0') selected='' @endif>Inprocessing</option>
									<option value="1" @if($result->remarks_status == '1') selected='' @endif>Completed</option>
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
											@if($data->value == @$result->processing_status) selected="" @endif
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
