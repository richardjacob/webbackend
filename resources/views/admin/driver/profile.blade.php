<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
		table, th, td {
		border: 1px solid black;
		border-collapse: collapse;
		text-align: center;
		}
		th, td {
		padding: 15px;
		}
		.bg-primary{
		background-color:#71DBDB !important;
		color:#fff !important;
		}
		.border-primary{
		border-color:#71DBDB !important;
		}

	</style>
  </head>
  <body>
    
    <div class="d-flex justify-content-center">
		<div class="card"  style="width:70% !important">
			<div class="card-body">

			
				<!-- Basic Information Start -->
				<div class="row pt-0 pb-2">
					<div class="col-md-10">
						<div class=" row">
							<div class="col-2 mb-1">Name :</div> 
							<div class="col-4 mb-1"><strong>{{$data->first_name}} {{$data->last_name}}</strong></div>

							<div class="col-2 mb-1">Mobile :</div> 
							<div class="col-4 mb-1">0{{$data->mobile_number}}</div>
                            
                            @if($data->email !='')
							<div class="col-2 mb-1">Email :</div> 
							<div class="col-4 mb-1">{{$data->email}}</div>
                            @endif
                            
                            @if($data->company_id !='1')
							<div class="col-2 mb-1">Company :</div> 
							<div class="col-4 mb-1">{{company_name($data->company_id)}}</div>	
                            @endif	
                            
							<div class="col-2 mb-1">Referral Code :</div> 
							<div class="col-4 mb-1">{{$data->referral_code}}</div>

                            <div class="col-2 mb-1">Gender :</div> 
							<div class="col-4 mb-1">
                                @if($data->gender == '1') Male
                                @elseif($data->gender == '2') Female
                                @else Other
                                @endif
                            </div>

                            @if($data->nid_number !='')
							<div class="col-2 mb-1">NID :</div> 
							<div class="col-4 mb-1">{{$data->nid_number}}</div>
                            @endif

                            @if($data->driving_licence_number !='')
                            <div class="col-2 mb-1">Driving Licence no. :</div> 
							<div class="col-4 mb-1">{{$data->driving_licence_number}}</div>
                            @endif

                            @if(@$address->address_line1 !='')
                            <div class="col-2 mb-1">Address Line 1 :</div> 
							<div class="col-4 mb-1">{{@$address->address_line1}}</div>
                            @endif

                            @if(@$address->address_line2 !='')
                            <div class="col-2 mb-1">Address Line 2 :</div> 
							<div class="col-4 mb-1">{{@$address->address_line2}}</div>
                            @endif

                            @if(@$address->city !='')
                            <div class="col-2 mb-1">City :</div> 
							<div class="col-4 mb-1">{{@$address->city}}</div>
                            @endif

                            @if(@$address->state !='')
                            <div class="col-2 mb-1">State :</div> 
							<div class="col-4 mb-1">{{$address->state}}</div>
                            @endif

                            @if(@$address->postal_code !='')
                            <div class="col-2 mb-1">Postal code :</div> 
							<div class="col-4 mb-1">{{@$address->postal_code}}</div>
                            @endif

                            <div class="col-2 mb-1">Country :</div> 
							<div class="col-4 mb-1">{{$country}}</div>
                            
                            
                            <div class="col-2 mb-1">Checked :</div> 
							<div class="col-4 mb-1">
                                @if($data->checked =='1')
                                    Checked by {{admin_user($data->checked_by)}}
                                @else
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                @endif
                            </div>

                            <div class="col-2 mb-1">Verified :</div> 
							<div class="col-4 mb-1">
                                @if($data->verified =='1')
                                    Verified by {{admin_user($data->verified_by)}}
                                @else
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                @endif
                            </div>

                            <div class="col-2 mb-1">Trained :</div> 
							<div class="col-4 mb-1">
                                @if($data->trained =='1')
                                    Trained by {{admin_user($data->trained_by)}}
                                @else
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                @endif
                            </div>

                            <div class="col-2 mb-1">Active :</div> 
							<div class="col-4 mb-1">
                                @if($data->active =='1')
                                    Active by {{admin_user($data->active_by)}}
                                @else
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                @endif
                            </div>

                            <div class="col-2 mb-1">Status :</div> 
							<div class="col-4 mb-1">{{driver_status($data->id)}}</div>
                            
                            

						</div>
					</div>
                    <div class="col-md-2 text-right">
                        <a href="{{$profile_picture}}" target="_blank">
                            <img src="{{$profile_picture}}" alt="{{$data->first_name}} {{$data->last_name}}" style="width:100%;">
                        </a>
                    </div>
				</div>
				
				
				<div class="border border-primary row pb-1">
					<div class="col-12 bg-primary p-2">
						<h5>Driver documents</h5>
					</div>
                    @foreach($driver_doc as $doc)                        
                    <div class="col-md-6 pt-3">
                        <a href="{{$doc->document}}" target="_blank">
                            <img src="{{$doc->document}}"  style="width:100%;height:300px">
                        </a><br />
                        <div class="row">
                            <div class="col-sm-6">{{doc_name_from_url($doc->document)}}</div>

                            <div class="col-sm-6 text-right">
                                @if($doc->expired_date !='')
                                    Expire : {{date("d F Y",strtotime($doc->expired_date))}}
                                @endif
                            </div>

                            <div class="col-sm-6">
                                @if($doc->checked =='1') 
                                    Checked by {{admin_user($doc->checked_by)}}
                                @else
                                    Not Checked
                                @endif
                            </div>

                            <div class="col-sm-6 text-right">
                                @if($doc->verified =='1') 
                                    Verified by {{admin_user($doc->checked_by)}}
                                @else
                                    Not Verified
                                @endif
                            </div>

                        </div>
                    </div>
                    @endforeach
				</div>

                <div class="border border-primary row pb-5">
					<div class="col-12 bg-primary p-2">
						<h5>Vehicle documents</h5>
					</div>
                    @foreach($vehicle_doc as $doc)                        
                    <div class="col-md-6 pt-3">
                        <a href="{{$doc->document}}" target="_blank">
                            <img src="{{$doc->document}}"  style="width:100%;height:300px">
                        </a><br />
                        <div class="row">
                            <div class="col-sm-6">{{doc_name_from_url($doc->document)}}</div>

                            <div class="col-sm-6 text-right">
                                @if($doc->expired_date !='')
                                    Expire : {{date("d F Y",strtotime($doc->expired_date))}}
                                @endif
                            </div>

                            <div class="col-sm-6">
                                @if($doc->checked =='1') 
                                    Checked by {{admin_user($doc->checked_by)}}
                                @else
                                    Not Checked
                                @endif
                            </div>

                            <div class="col-sm-6 text-right">
                                @if($doc->verified =='1') 
                                    Verified by {{admin_user($doc->checked_by)}}
                                @else
                                    Not Verified
                                @endif
                            </div>

                        </div>
                    </div>
                    @endforeach
				</div>
				

				

				
			</div>
		</div>
	</div>          

  </body>
</html>



            