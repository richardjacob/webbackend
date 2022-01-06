@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1>Manage Driver <small>OTP</small></h1>
		<ol class="breadcrumb">
			<li>
			<a href="dashboard"><i class="fa fa-dashboard"></i> Home</a>
			</li>
			<li class="active">Manage Driver</li>
			<li>
			<a href="{{ url(LOGIN_USER_TYPE.'/otp') }}"> Otp</a>
			</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Search OTP</h3>
					</div>
					@if(isset($err))
						<div class="alert alert-danger">{{$err}}</div>
					@endif
					{!! Form::open(['url' => 'admin/otp', 'class' => 'form-horizontal']) !!}
					<div class="box-body">
						<span class="text-danger">(*)Fields are Mandatory</span>
						 
						<div class="box-body">
							<div class="form-group">
								<label for="input_payout_methods" class="col-sm-3 control-label"> Mobile Number <em class="text-danger">*</em> </label>
								<div class="col-sm-6">
									<input type="text" name="mobile_number" value="{{@$mobile_number}}" class="form-control">
								</div>
							</div>
					</div>
					
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="Search">Search</button>
					</div>
					{!! Form::close() !!}

					@if(isset($data) AND is_object($data))
						<table class="table table-bordered">
							<tr>
								<th>Mobile Number</th>
								<th>OTP</th>
								<th>Date Time</th>
							</tr>
							<tr>
								<td>{{$data->mobile_number}}</td>
								<td>			
									@if(strtotime(now()) - strtotime($data->created_at) > 600)
									OTP Expired
									@else
									{{$data->otp}}
									@endif
								</td>
								<td>{{date('d-m-Y, h:i:s A',strtotime($data->created_at))}}</td>
							</tr>
						</table>
					@endif
					
				</div>
			</div>			
		</div>

		
	</section>
</div>
@endsection