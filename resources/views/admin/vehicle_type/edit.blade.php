@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Manage Vehicles
		 <small>Edit  Vehicle types</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a></li>
			<li class="active"> Manage Vehicles </li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/vehicle_type') }}"> Vehicle types </a></li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/edit_vehicle_type/'.$result->id) }}"> Edit</a></li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Vehicle types Form</h3>
					</div>
					{!! Form::open(['url' => 'admin/edit_vehicle_type/'.$result->id, 'class' => 'form-horizontal','files' => true]) !!}
					<div class="box-body">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<div class="form-group">
							<label for="input_name" class="col-sm-3 control-label">Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('vehicle_name',$result->car_name, ['class' => 'form-control', 'id' => 'input_name', 'placeholder' => 'Name']) !!}
								<span class="text-danger">{{ $errors->first('vehicle_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_description" class="col-sm-3 control-label">Description</label>
							<div class="col-sm-6">
								{!! Form::textarea('description',$result->description, ['class' => 'form-control', 'id' => 'input_description', 'placeholder' => 'Description', 'rows' => 3]) !!}
								<span class="text-danger">{{ $errors->first('description') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_license_back" class="col-sm-3 control-label">Vehicle image<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::file('vehicle_image',  ['class' => 'form-control', 'id' => 'rc', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('vehicle_image') }}</span>
								<img src="{{ $result->vehicle_image }}" width="200" height="100" >
							</div>
						</div>
						<div class="form-group">
							<label for="input_license_back" class="col-sm-3 control-label">Vehicle Active image<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::file('active_image',  ['class' => 'form-control', 'id' => 'rc', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('active_image') }}</span>
								<img src="{{ $result->active_image }}" width="200" height="100" >
							</div>
						</div>
						<div class="form-group">
							<label for="input_pool" class="col-sm-3 control-label"> Is Pool <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('is_pool', array('No' => 'No', 'Yes' => 'Yes'),$result->is_pool, ['class' => 'form-control', 'id' => 'input_pool']) !!}
								<span class="text-danger">{{ $errors->first('is_pool') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'),$result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('status') }}</span>
							</div>
						</div>
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
