@extends('admin.template')
@section('main')
<style>
#suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
#suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
#suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
#search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px}

</style>

<div class="content-wrapper">
	<section class="content-header">
		<h1>Manage Vehicles <small>View Vehicle</small></h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/vehicle') }}"> Manage Vehicles </a>
			</li>
			<li class="active">
				<a href="{{ url(LOGIN_USER_TYPE.'/view_vehicle/'.$vehicle->id) }}"> View </a>
			</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">{{$vehicle->vehicle_number}}</h3>
					</div>					
					<div class="box-body">

						<!-- Vehicle Information -->
						<div class="col-md-12" ><label>Vehicle Information</label></div>
							<div class="row">
								<div class="col-md-6">
									<div class="col-md-4">Vehicle Number :</div> 
									<div class="col-md-8 text-primary">{{$vehicle->vehicle_number}}</div>

									<div class="col-md-4">Vehicle Name :</div> 
									<div class="col-md-8">{{$vehicle->vehicle_name}}</div>

									<div class="col-md-4">Model :</div> 
									<div class="col-md-8">{{$vehicle_model}}</div>

									<div class="col-md-4">Make :</div> 
									<div class="col-md-8">{{$vehicle_make}}</div>

									<div class="col-md-4">Vehicle Type :</div> 
									<div class="col-md-8">{{$vehicle->vehicle_type}}</div>

									<div class="col-md-4">Year :</div> 
									<div class="col-md-8">{{$vehicle->year}}</div>

									<div class="col-md-4">Color :</div> 
									<div class="col-md-8">{{$vehicle->color}}</div>

									<div class="col-md-4">Status :</div> 
									<div class="col-md-8">{{$vehicle->status}}</div>

									<div class="col-md-4">Default :</div> 
									<div class="col-md-8">
										@if($vehicle->default_type == '1')
											Yes
										@else
											No
										@endif
									</div>
								</div>
								<div class="col-md-6">
									Registration Paper<br>
									<a href="{{$registration_paper}}" target="_blank">
										<img src="{{$registration_paper}}" style="width:100%">
									</a>
								</div>	
							</div>						

						<!-- Basic Information -->
						<div class="col-md-12" style="margin-top:15px;"><label>Driver's Basic Information</label></div>
							<div class="col-md-2">Driver Name :</div>
							<div class="col-md-4 text-primary">{{$driver->first_name.' '.$driver->last_name}}</div>

							<div class="col-md-2">Mobile :</div>
							<div class="col-md-4">{{$driver->mobile_number}}</div>
							@if($driver->email !='')
								<div class="col-md-2">Email :</div> 
								<div class="col-md-4">{{$driver->email}}</div>
		          			@endif

							@if($driver->nid_number !='')
									<div class="col-md-2">NID :</div> 
									<div class="col-md-4">{{$driver->nid_number}}</div>
		          			@endif

		          			<div class="col-md-2">Referral Code :</div> 
							<div class="col-md-4">{{$driver->referral_code}}</div>

							@if($driver->driving_licence_number !='')
					            <div class="col-md-2">Driving Licence no. :</div> 
								<div class="col-md-4">{{$driver->driving_licence_number}}</div>
					        @endif

				        	<div class="col-md-2">Status :</div> 
							<div class="col-md-4">{{driver_status($driver->id)}}</div>

							<div class="col-md-2">Joinining Date :</div> 
							<div class="col-md-4">{{date("d M Y", strtotime($driver->created_at))}}</div>

							@if($driver->active_time !='')
							<div class="col-md-2">Activation Date :</div> 
							<div class="col-md-4">{{date("d M Y", strtotime($driver->active_time))}}</div>
							@endif

						<!-- Company Information -->
						<div class="col-md-12" style="margin-top:15px;"><label>Company Information</label></div>
							<div class="col-md-2">Company :</div> 
							<div class="col-md-4 text-primary">{{$company->name}}</div>

							<div class="col-md-2">Mobile :</div> 
							<div class="col-md-4">0{{$company->mobile_number}}</div>

							<div class="col-md-2">Status :</div> 
							<div class="col-md-4">{{$company->status}}</div>

							<div class="col-md-2">Address :</div> 
							<div class="col-md-4">{{$company->address}}</div>						
				</div>
			</div>
		</div>
	</section>
</div>

@endsection
