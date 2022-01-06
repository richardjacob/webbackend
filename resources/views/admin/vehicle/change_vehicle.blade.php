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
		<h1>Manage Vehicles <small>Change Vehicle</small></h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/vehicle') }}"> Manage Vehicles </a>
			</li>
			<li class="active">
				<a href="{{ url(LOGIN_USER_TYPE.'/vehicle/change_vehicle/'.$vehicle->id) }}"> Change Vehicle </a>
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
			        

				    	
				        <div class="col-md-8" style="margin-top:25px;margin-bottom:10px;">
				        	<div style="margin-bottom:10px;">
				        		<label>Assign vehicle to new driver</label>	  
				        	</div>

				        	{!! Form::open(['url' => LOGIN_USER_TYPE.'/vehicle/change_vehicle/'.$vehicle->id, 'class' => 'form-horizontal']) !!}

				        		@csrf
				        		<input type="hidden" name="vehicle_id" value="{{$vehicle->id}}">
					        	
					        	<div class="col-sm-4 text-right">New Driver *</div>
					        	<div class="col-sm-8">
									{!! Form::text('driver_name', old('driver_name'), ['class' => 'form-control search-box', 'id' => 'input_driver_name', 'autocomplete' => 'off', 'onkeyup' => 'suggestion_driver()', 'placeholder' => 'Enter Driver Name or Mobile Number Or ID']) !!}

									<input type="hidden" id="driver_id" name="driver_id" value="{{old('driver_id')}}">
									<span class="text-danger">{{ $errors->first('driver_id') }}</span>
									<div id="suggesstion_box_driver"></div>
								</div>

								<div class="col-sm-9" style="margin-top:10px">
									<label>
										<input type="checkbox" id="confirm" name="confirm" value="1"
										@if(old('confirm') !='') checked=""  @endif
										>* I agree to change partner.	
									</label>		
									<span class="text-danger">{{ $errors->first('confirm') }}</span>					
								</div>

								<div class="col-sm-3 text-right" style="padding-top:10px;">
									<button class="btn btn-primary" type="submit" id="submit">
										<i class="fa fa-exchange" aria-hidden="true"></i> Change Partner
									</button>
								</div>
							{!! Form::close() !!}
				        </div>  
   
		                            	
					
					
				</div>
			</div>
		</div>
	</section>
</div>
<script type="text/javascript">
	function suggestion_driver(){
		var	keyword = $('#input_driver_name').val();
		var url = "{{url('admin/ajax/suggestion_company_driver')}}";
		if(keyword == '' ) $("#driver_id").val('');

		$.ajax({
			type:'POST',
			url:url,
			data:{				
				@if(LOGIN_USER_TYPE == 'company')
					company_id:{{Auth::guard('company')->user()->id}},
				@endif
				keywords:keyword,
				_token: "{{ csrf_token() }}"
			},
			beforeSend:function(){
				$("#input_driver_name").css("background","#eee");
			},
			success:function(data){
				$("#suggesstion_box_driver").show();
				$("#suggesstion_box_driver").html(data);
				$("#input_driver_name").css("background","#FFF");
			}	       
		});
	}

function select_from_suggestion_company_driver(label, val){
    $("#input_driver_name").val(label);
    $("#suggesstion_box_driver").hide();
    $("#driver_id").val(val);
}
</script>
@endsection
