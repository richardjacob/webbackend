@extends('admin.template')
@section('main')
<div class="fees-wrap content-wrapper">
	<section class="content-header">
		<h1>Manage Fees</h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}">
					<i class="fa fa-dashboard"></i> Home
				</a>
			</li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/fees') }}">Fee</a></li>			
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12 col-sm-offset-0">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title"> Fees Form </h3>
					</div>
					{!! Form::open(['url' => 'admin/fees', 'class' => 'form-horizontal']) !!}
					<div class="box-body">
						<div class="form-group">
							<label for="input_service_fee" class="col-sm-3 control-label">Rider Service Fee</label>
							<div class="col-sm-7 col-md-5">
								<div class="input-group">
									{!! Form::text('access_fee', fees('access_fee'), ['class' => 'form-control', 'id' => 'input_service_fee', 'placeholder' => 'Rider Service Fee']) !!}
									{{-- <div class="input-group-addon" >%</div> --}}
									<span class="text-danger">{{ $errors->first('access_fee') }}</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="input_service_fee" class="col-sm-3 control-label">
								Driver Peak Fare
							</label>
							<div class="col-sm-7 col-md-5">
								<div class="input-group">
									{!! Form::text('driver_peak_fare', fees('driver_peak_fare'), ['class' => 'form-control', 'id' => 'input_driver_peak_fare', 'placeholder' => 'Driver Peak Fare']) !!}
									<div class="input-group-addon" >%</div>
									<span class="text-danger">{{ $errors->first('driver_peak_fare') }}</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="input_additional_rider_fare" class="col-sm-3 control-label">
								2nd Rider Amount <small>(For Pool Trips)</small>
							</label>
							<div class="col-sm-7 col-md-5">
								<div class="input-group">
									{!! Form::text('additional_rider_fare', fees('additional_rider_fare'), ['class' => 'form-control', 'id' => 'input_additional_rider_fare', 'placeholder' => 'Additional Rider Fare']) !!}
									<div class="input-group-addon" >%</div>
									<span class="text-danger">{{ $errors->first('additional_rider_fare') }}</span>
								</div>
							</div>
						</div>
						
						
						<div class="form-group">
							<label for="input_service_fee" class="col-sm-3 control-label">
								Driver Service Fee
							</label>
							<div class="col-sm-7 col-md-5">
								<div class="input-group">
									{!! Form::text('driver_service_fee', fees('driver_access_fee'), ['class' => 'form-control', 'id' => 'input_driver_service_fee', 'placeholder' => 'Driver Service Fee']) !!}
									<div class="input-group-addon" >%</div>
									<span class="text-danger">{{ $errors->first('driver_service_fee') }}</span>
								</div>
							</div>
						</div>
						
						<!--new added by Nishat 4-11-2021-->
						
						{{-- <div class="form-group">
							<label for="input_sticker_driver_service_fee" class="col-sm-3 control-label">
								With Sticker Driver Service Fee
							</label>
							<div class="col-sm-7 col-md-5">
								<div class="input-group">
									{!! Form::text('sticker_driver_service_fee', fees('sticker_driver_access_fee'), ['class' => 'form-control', 'id' => 'input_sticker_driver_service_fee', 'placeholder' => 'With Sticker Driver Service Fee']) !!}
									<div class="input-group-addon" >%</div>
									<span class="text-danger">{{ $errors->first('sticker_driver_service_fee') }}</span>
								</div>
							</div>
						</div> --}}
						
						<!--new added by Nishat End 4-11-2021-->
						
						
						<div class="form-group">
							<label for="input_additional_fee" class="col-sm-3 control-label">
								Apply Trip Additional Fee
							</label>
							<div class="col-sm-7 col-md-5">
								<div class="input-group">
									{!! Form::select('additional_fee', array_merge(['Yes' =>'Yes','No' =>'No']),fees('additional_fee'), ['class' => 'form-control', 'id' => 'input_additional_fee']) !!}
									<span class="text-danger">{{ $errors->first('additional_fee') }}</span>
								</div>
							</div>
						</div>
						
						<!--Changed By Nishat-->
						{{-- <div class="form-group">
							<label for="with_sticker_or_without" class="col-sm-3 control-label">
								With Sticker / Without Sticker
							</label>
							<div class="col-sm-7 col-md-5">
								<div class="input-group">
									{!! Form::select('with_sticker_or_without', array_merge(['Yes' =>'Yes','No' =>'No']),fees('with_sticker_or_without'), ['class' => 'form-control', 'id' => 'with_sticker_or_without']) !!}
									<span class="text-danger">{{ $errors->first('with_sticker_or_without') }}</span>
								</div>
							</div>
						</div> --}}
						
						<!--Changed By Nishat End-->
						
						
					</div>
					<div class="box-footer">
						<button type="reset" class="btn btn-default" name="cancel">Cancel</button>
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
@push('scripts')
<style type="text/css">
	.input-group-addon {
		background-color: #eee !important;
	}
</style>
@endpush
