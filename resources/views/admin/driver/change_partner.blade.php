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
		<h1> Manage Driver <small>Change Partner </small></h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/driver') }}"> Manage Driver </a>
			</li>
			<li class="active">
				<a href="{{ url(LOGIN_USER_TYPE.'/driver/change_partner/'.$data->id) }}"> Change Partner </a>
			</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">{{$data->first_name.' '.$data->last_name}}</h3>
					</div>

					<!-- Basic Information Start -->
					<div class="box-body">
						<div class="col-md-12"><label>Driver's Basic Information</label></div>

						<div class="col-md-2">Mobile :</div>
						<div class="col-md-4">{{$data->mobile_number}}</div>
						@if($data->email !='')
							<div class="col-md-2">Email :</div> 
							<div class="col-md-4">{{$data->email}}</div>
	          			@endif

						@if($data->nid_number !='')
								<div class="col-md-2">NID :</div> 
								<div class="col-md-4">{{$data->nid_number}}</div>
	          			@endif

	          			<div class="col-md-2">Referral Code :</div> 
						<div class="col-md-4">{{$data->referral_code}}</div>

						@if($data->driving_licence_number !='')
	            			<div class="col-md-2">Driving Licence no. :</div> 
							<div class="col-md-4">{{$data->driving_licence_number}}</div>
	          			@endif

	          			<div class="col-md-2">Status :</div> 
						<div class="col-md-4">{{driver_status($data->id)}}</div>

						<div class="col-md-2">Joinining Date :</div> 
						<div class="col-md-4">{{date("d M Y", strtotime($data->created_at))}}</div>

						@if($data->active_time !='')
						<div class="col-md-2">Activation Date :</div> 
						<div class="col-md-4">{{date("d M Y", strtotime($data->active_time))}}</div>
						@endif

						<!-- Vehicle Information -->
						<div class="col-md-12" ><label>Vehicle Information</label></div>
						@foreach($vehicles as $vehicle)
							<div class="row" style="margin-top:10px;border-bottom:1px solid #ECF0F5 !important;">
								<div class="col-md-6">
									<div class="col-md-4">Vehicle Number :</div> 
									<div class="col-md-8 text-primary">{{$vehicle->vehicle_number}}</div>

									<div class="col-md-4">Vehicle Name :</div> 
									<div class="col-md-8">{{$vehicle->vehicle_name}}</div>

								
									<div class="col-md-4">Model :</div> 
									<div class="col-md-8">{{vehicle_model($vehicle->vehicle_model_id)}}</div>

									<div class="col-md-4">Make :</div> 
									<div class="col-md-8">{{vehicle_make($vehicle->vehicle_make_id)}}</div>
									
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
									<a href="{{registration_paper($vehicle->id)}}" target="_blank">
										<img src="{{registration_paper($vehicle->id)}}" style="height:140px;">
									</a>
								</div>	
							</div>						
						@endforeach

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

						<!-- Dues Information -->
						<div class="col-md-12" style="margin-top:15px;"><label>Dues Information</label></div>
							
							<div class="col-md-2">Owe :</div> 
							<div class="col-md-10">{{$owe_amount}}</div>

							<div class="col-md-2">Payout :</div> 
							<div class="col-md-10">{{$payout_amount}}</div>

							<div class="col-md-2">Bonus :</div> 
							<div class="col-md-10">{{$bonus_due_amount}}</div>
	        	
	        <div class="col-md-8" style="margin-top:25px;margin-bottom:10px;">
	        	<div style="margin-bottom:10px;">
	        		<label>Assign Driver to new Partner</label>	  
	        	</div>

	        	{!! Form::open(['url' => LOGIN_USER_TYPE.'/change_partner/'.$data->id, 'class' => 'form-horizontal','id' => 'changePartnerForm']) !!}
	        		@csrf
	        		<input type="hidden" name="user_id" value="{{$data->id}}">
		        	
		        	<div class="col-sm-4 text-right">New Partner *</div>
		        	<div class="col-sm-8">
								{!! Form::text('company_name_suggestion', '', ['class' => 'form-control search-box', 'id' => 'input_company_name', 'data-id' => 'search-box', 'placeholder' => 'Enter Partner Name or Mobile Number OR Partner ID', 'autocomplete' => 'off', 'onkeyup' => 'suggestion_company()']) !!}
								<span class="text-danger">{{ $errors->first('company_id') }}</span>

								<input type="hidden" id="company_id" name="company_id" value="{{old('company_id')}}">
								<span class="text-danger">{{ $errors->first('company_name') }}</span>
								<div id="suggesstion_box_company"></div>
							</div>

							<div class="col-sm-12 text-warning">By changing driver to new partner, If any dues with the current partner that will be transfer to new partner.</div>

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

	        <div class="col-md-4">&nbsp;</div>    
	                            
	                            
						

					
					
					
				</div>
			</div>
		</div>
	</section>
</div>
<script type="text/javascript">
	function suggestion_company(){
	var	keyword = $('#input_company_name').val();
	var url = "{{url('admin/ajax/suggestion_company')}}";

	if(keyword == '' ) $("#company_id").val('');

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
</script>
@endsection
