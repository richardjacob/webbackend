@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
 <style>
  .container-border{
   border-left: 1px solid #D2D6DE;
   border-right: 1px solid #D2D6DE;
   border-bottom: 1px solid #D2D6DE;
   background-color: #FFF;
   padding:10px;
   color: #555
  }

  th{
    font-weight:normal; 
    text-align:center;
  }
  h3,h4{
    font-weight:bold;
    font-family: Verdana, Arial, Helvetica, sans-serif;
  }
  th,td,strong{
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size:11px;
  }

  .middle{
    vertical-align: middle !important;
  }

  .text-center{
    text-align:center !important;
  }
 
 
  .check{
    padding:0px 2px !important; 
    width:75px;
    text-align: right;
  }

  .fa-check{
    margin-right:10px;
  }
  .table td, .table th{
    border:1px solid #acacac !important;
  }
  .setExpireDate{
    cursor: pointer;
  }
  .setExpireDate:hover{
    color: blue;
  }

  .modal {
    text-align: center;
  }

  @media screen and (min-width: 768px) { 
    .modal:before {
      display: inline-block;
      vertical-align: middle;
      content: " ";
      height: 100%;
    }
  }

  .modal-dialog {
    display: inline-block;
    text-align: left;
    vertical-align: middle;
  }

    #suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
    #suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
    #suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
    #search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px}
</style>

@if($user = Auth::guard('admin')->user())@endif

<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Manage Driver <small>Driver Status</small></h1>
      <ol class="breadcrumb">
        <li>
          <a href="dashboard"><i class="fa fa-dashboard"></i> Home</a>
        </li>
        <li class="active">Manage Driver</li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/driver_status') }}"> Driver Status</a>
        </li>
      </ol>
   </section>
   <!-- Main content -->
     
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
              <div class="col-md-12" style="padding-top:10px;">
                <ul class="nav nav-tabs">
                  @if(@$user->can('driver_status_all_documents'))
                    <li @if($type == "all_documents") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/all_documents')}}">
                        All Documents Submitted
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('driver_status_checked'))
                    <li @if($type == "checked") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/checked')}}">
                        Checked
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('driver_status_verified'))
                    <li @if($type == "verified") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/verified')}}">
                        Verified
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('driver_status_trained'))
                    <li @if($type == "trained") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/trained')}}">
                        Trained
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('driver_status_active'))
                    <li @if($type == "active") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/active')}}">
                        Active
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('driver_cum_owner'))
                    <li @if($type == "owner") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/owner')}}">
                      Driver Cum Owner
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('driver_status_partner'))
                    <li @if($type == "partner") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/partner')}}">
                      Partner
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('drivers_under_partner'))
                    <li @if($type == "drivers_under_partner") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/drivers_under_partner')}}">
                      Drivers under Partner
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('uncheck_owner_driver'))
                    <li @if($type == "uncheck_owner_driver") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/uncheck_owner_driver')}}">
                      Uncheck Owner / Driver
                      </a>
                    </li>
                  @endif

                  @if(@$user->can('driver_status_nid'))
                    <li @if($type == "nid") class="active" @endif>
                      <a href="{{url(LOGIN_USER_TYPE.'/driver_status/nid')}}">
                      NID Verification
                      </a>
                    </li>
                  @endif
                </ul>               
                   
                @if($list->firstItem() !='')                  
                  <div class="col-sm-12" style="padding:5px;">
                    <div class="text-right col-sm-12">
                    {!! Form::open(['url' => LOGIN_USER_TYPE.'/driver_status/'.$type, 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}

                      @if($type == "nid")                         
                        <div class="col-sm-3">
                          <select name="nid_verify" class="form-control">
                            <option value="1" @if($nid_verify == '1') selected @endif>Verified</option>
                            <option value="0" @if($nid_verify == '0') selected @endif>Unverified</option>
                            <option value="2" @if($nid_verify == '2') selected @endif>Invalid</option>
                            <option value="not_submitted" @if($nid_verify == 'not_submitted') selected @endif>Not Submitted</option>
                            <option value="checked_nid" @if($nid_verify == 'checked_nid') selected @endif>Checked From NID Server</option>
                            <option value="not_checked_nid" @if($nid_verify == 'not_checked_nid') selected @endif>Not Checked From NID Server</option>
                          </select>
                        </div>
                      @else
                        <div class="col-sm-3"></div>
                      @endif
                      
                      <div class="col-sm-2">
                        @if($type == "partner") 
                          <input type="text" name="company_id" value="{{@$company_id}}"  class="form-control" placeholder="Company ID" autocomplete="off">
                        @else
                          <input type="text" name="driver_id" value="{{@$driver_id}}"  class="form-control" placeholder="Driver ID" autocomplete="off">
                        @endif
                      </div>

                      <div class="col-sm-2">
                        <input type="text" name="start_date" value="@if(isset($start_date)){{date('d-m-Y', strtotime(@$start_date))}}@endif"  class="form-control date" placeholder="Start Date" autocomplete="off">
                      </div>

                      <div class="col-sm-2">
                        <input type="text" name="end_date" value="@if(isset($end_date)){{date('d-m-Y', strtotime(@$end_date))}}@endif"  class="form-control date" placeholder="End Date" autocomplete="off">
                      </div>

                      <div class="col-sm-1" style="padding:0px;">
                        <select name="per_page" class="form-control" style="padding-left:0px; padding-right:0px;">
                          <option value="">Per Page</option>
                          @foreach(array('10') as $p)
                            <option value="{{$p}}" @if($p == @$per_page) selected @endif>{{$p}}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-sm-1">
                        <button type="submit" class="btn btn-primary form_submit">
                          <i class="fa fa-search"></i> Search
                        </button>
                      </div>
                      @if(@$user->can('driver_status_'.$type.'_print')) 
                        <div class="text-right col-sm-1">
                          <a href="{{url(LOGIN_USER_TYPE.'/driver_status_print/'.$type)}}"  class="btn btn-success">
                            <i class="fa fa-print"></i> Print
                          </a>
                        </div>
                      @endif

                    </div> <!-- /.col-sm-12 -->
                    {!! Form::close() !!}   
                    
                  </div>


                  <div>   
                    <div class="text-left col-md-6">
                      Page {{$list->currentPage()}} of {{$list->lastPage()}}
                    </div>
                    <div class="text-right col-md-6">
                      Showings records from {{$list->firstItem()}} to {{$list->lastItem()}} of total {{$list->total()}}
                    </div>
                  </div>
                @endif
                
                @if($type == "all_documents") 
                  @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th rowspan="3" class="middle">Sl#</th>
                          <th rowspan="3" class="middle">Driver ID</th>
                          <th rowspan="3" class="middle">Driver Name</th>
                          <th rowspan="3" class="middle">Driver's Number</th>
                          <th rowspan="3" class="middle">Car Plate Number</th>
                          <th colspan="5" class="middle">Personal Documents (Checked)</th>
                          <th colspan="4" class="middle">Car Documents  (Checked)</th>
                          <th rowspan="3" class="middle" style="border-left : 1px solid #eee">Status</th>
                          @if(@$user->can('update_driver_info'))
                            <th rowspan="3" class="middle">Update</th>
                          @endif
                          @if(@$user->can('set_partner'))
                            <th rowspan="3" class="middle" style="width:200px;">Owner / Driver</th>
                          @endif
                          <th rowspan="3" class="middle">Remarks</th>
                        </tr>
                        <tr>
                          <th class="middle" colspan="3">Photo</th>
                          <th class="middle" rowspan="2">NID/ Passport</th>
                          <th class="middle" rowspan="2">Driver's License</th>

                          <th class="middle" rowspan="2">Registration</th>
                          <th class="middle" rowspan="2">Tax Token</th>
                          <th class="middle" rowspan="2">Enlistment</th>
                          <th class="middle" rowspan="2">Fitness Certificate</th>
                        </tr>
                        <tr>
                          <th class="middle">&nbsp;</th>
                          <th class="middle">NID</th>
                          <th class="middle">User</th>
                        </tr>
                      @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td>
                          <td class="middle">{{$d->user_id}}</td>
                          <td class="middle">
                            @if(@$user->can('view_driver_profile'))
                              <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->user_id)}}" target="_blank">{{$d->driver_name}}</a>
                            @else 
                              {{$d->driver_name}}
                            @endif
                          </td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle">
                            {!! driver_vehicle($d->user_id, 'link') !!}
                          </td>


              
                          <td class="text-center check middle" style="width:40px !important;padding-left:10px !important">
                            @if($d->photo !='')              
                                @if($d->photo_checked == '1')                              
                                    <i class="fa fa-check text-success uncheck" aria-hidden="true" title="Checked by {!! admin_user($d->checked_by) !!} at {!! $d->checked_time !!}" 
                                    data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="checked" data-sl="sl_{{$i}}" data-tab="profile_picture"></i>
                                @else
                                  <span>
                                    <input type="checkbox" name="profile_picture" class="update" data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="checked" data-sl="sl_{{$i}}" data-tab="profile_picture">
                                  </span>
                                @endif                              
                            @endif
                          </td>

                          <td class="text-center check middle nid_photo" style="width:18px !important;">
                            @if($d->nid_number !='')              
                              <a href="https://{{env('ADMIN_PANEL_SUB_DOMAIN')}}.{{env('DOMAIN')}}/images/nid_photo/{{$d->nid_number}}.png" target="_blank">
                                <img src="https://{{env('ADMIN_PANEL_SUB_DOMAIN')}}.{{env('DOMAIN')}}/images/nid_photo/{{$d->nid_number}}.png" width="18" height="18">
                              </a>
                            @endif
                          </td>
                          <td class="text-center check middle" style="width:18px !important;">
                            @if($d->photo !='')              
                              <a href="{{$d->photo}}" target="_blank">
                                <img src="{{$d->photo}}" width="18" height="18">
                              </a>
                            @endif
                          </td>

                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'nid', 'checked', $i) !!}</td>                          
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'driving_license', 'checked', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'registration_paper', 'checked', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'tax_token', 'checked', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'enlistment_certificate', 'checked', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'fitness_certificate', 'checked', $i) !!}</td>
                        
                          <td>{!! driver_status($d->user_id) !!}</td>    
                          
                          @if(@$user->can('update_driver_info'))
                          <td class="text-center middle update_td">
                            <span 
                              class="text-primary update_driver" 
                              role="button"
                              data-driver_id="{{$d->user_id}}"
                              data-first_name="{{$d->first_name}}"
                              data-last_name="{{$d->last_name}}"
                              data-email="{{$d->email}}"
                              data-address_line1="{{$d->address_line1}}"
                              data-address_line2="{{$d->address_line2}}"
                              data-city="{{$d->city}}"
                              data-state="{{$d->state}}"
                              data-postal_code="{{$d->postal_code}}"
                              data-nid_number="{{$d->nid_number}}"
                              data-driving_licence_number="{{$d->driving_licence_number}}"
                              data-passport_number="{{$d->passport_number}}"                              
                              data-document_type="{{documentType($d->user_id, 'nid')}}"
                              data-nid="{{document_link($d->user_id, 'nid')}}"
                              
                              data-photo="{{$d->photo}}"
                              data-sl="{{$i}}"                              
                              >                            
                              <i class="fa fa-edit fa-2x"></i> 
                            </span>
                          </td> 
                          @endif 
                          <td class="middle text-center set_partner_container">
                            @if(@$user->can('set_partner'))
                              @if($d->is_owner == '1')
                                Owner
                              @elseif($d->is_owner == '0')
                                Driver ({{$d->partner}})
                              @else
                              
                              <label class="col-sm-6 bg-success set_partner" style="padding:5px;" data-sl="{{$i}}" data-driver_id="{{$d->user_id}}" data-driver_name="{{$d->first_name}} {{$d->last_name}}" data-nid="{{document_link($d->user_id, 'nid')}}"  data-registration_paper="{{document_link($d->user_id, 'registration_paper')}}">
                                <input type="radio" name="is_owner" value="1" >Owner
                              </label>
                              <label class="col-sm-6 bg-primary set_partner" style="padding:5px;" data-sl="{{$i}}" data-driver_id="{{$d->user_id}}" data-driver_name="{{$d->first_name}} {{$d->last_name}}" data-nid="{{document_link($d->user_id, 'nid')}}"  data-registration_paper="{{document_link($d->user_id, 'registration_paper')}}">
                                <input type="radio" name="is_owner" value="0">Driver
                              </label>
                              @endif
                            @endif
                          </div>
                          <td class="middle text-center">
                            <?php
                              $add_remarks = (auth('admin')->user()->can('add_drivers_remarks')) ? '<a href="'.url(LOGIN_USER_TYPE.'/add_drivers_remarks/'.$d->user_id).'" data-toggle="tooltip" data-placement="top" title="'.driver_last_remarks($d->user_id).'" class="btn btn-xs btn-success" style="margin-bottom:5px;" target="_blank"><i class="fa fa-comment"></i></a>&nbsp;' : '';
                              echo $add_remarks;
                              ?>                              
                          </td>
                        </tr>
                      @endforeach
                      </table>
                    </div>
                    @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif


                @elseif($type == "checked") 
                  @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th rowspan="3" class="middle">Sl#</th>
                          <th rowspan="3" class="middle">Driver ID</th>
                          <th rowspan="3" class="middle">Driver Name</th>
                          <th rowspan="3" class="middle">Driver's Number</th>
                          <th rowspan="3" class="middle">Car Plate Number</th>
                          <th colspan="5" class="middle">Personal Documents (Checked)</th>
                          <th colspan="4" class="middle">Car Documents  (Checked)</th>
                          <th rowspan="3" class="middle" style="border-left : 1px solid #eee">Status</th>
                          @if(@$user->can('update_driver_info'))
                            <th rowspan="3" class="middle">Update</th>
                          @endif
                          @if(@$user->can('set_partner'))
                            <th rowspan="3" class="middle" style="width:200px;">Owner / Driver</th>
                          @endif
                          <th rowspan="3" class="middle">Remarks</th>
                          <th rowspan="3" class="middle">Checked by <br>and<br> Checked Time</th>
                        </tr>
                        <tr>
                          <th class="middle" colspan="3">Photo</th>
                          <th class="middle" rowspan="2">NID/ Passport</th>
                          <th class="middle" rowspan="2">Driver's License</th>

                          <th class="middle" rowspan="2">Registration</th>
                          <th class="middle" rowspan="2">Tax Token</th>
                          <th class="middle" rowspan="2">Enlistment</th>
                          <th class="middle" rowspan="2">Fitness Certificate</th>
                        </tr>
                        <tr>
                          <th class="middle">&nbsp;</th>
                          <th class="middle">NID</th>
                          <th class="middle">User</th>
                        </tr>
                        @foreach($list as $i => $d)
                          <tr id="sl_{{$i}}">
                            <td class="middle">{{$list->firstItem() + $i}}</td>
                            <td class="middle">{{$d->user_id}}</td>
                            <td class="middle">
                              @if(@$user->can('view_driver_profile'))
                                <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->user_id)}}" target="_blank">{{$d->driver_name}}</a>
                              @else 
                                {{$d->driver_name}}
                              @endif
                            </td>

                            <td class="middle">{{$d->mobile_number}}</td>
                            <td class="middle">{!! driver_vehicle($d->user_id, 'link') !!}</td>
                
                            <td class="text-center check middle" style="width:60px !important;padding-left:10px !important">
                              @if($d->photo !='')              
                                  @if($d->photo_verified == '1')                              
                                      <i class="fa fa-check text-success uncheck" aria-hidden="true" title="Verified by {!! admin_user($d->verified_by) !!} at {!! $d->verified_time !!}" 
                                      data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="verified" data-sl="sl_{{$i}}" data-tab="profile_picture"></i>
                                  @else
                                    <span>
                                      <input type="checkbox" name="profile_picture" class="update" data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="verified" data-sl="sl_{{$i}}" data-tab="profile_picture">
                                    </span>
                                    <span>
                                      <i class="fa fa-close text-danger uncheck" aria-hidden="true"  data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="checked" data-sl="sl_{{$i}}" data-tab="profile_picture"></i>
                                    </span>
                                  @endif
                                
                              @endif
                            </td>   

                            <td class="text-center check middle nid_photo" style="width:18px !important;">
                              @if($d->nid_number !='')              
                                <a href="https://{{env('ADMIN_PANEL_SUB_DOMAIN')}}.{{env('DOMAIN')}}/images/nid_photo/{{$d->nid_number}}.png" target="_blank">
                                  <img src="https://{{env('ADMIN_PANEL_SUB_DOMAIN')}}.{{env('DOMAIN')}}/images/nid_photo/{{$d->nid_number}}.png" width="18" height="18">
                                </a>
                              @endif
                            </td>
                            <td class="text-center check middle" style="width:18px !important;">
                              @if($d->photo !='')              
                                <a href="{{$d->photo}}" target="_blank">
                                  <img src="{{$d->photo}}" width="18" height="18">
                                </a>
                              @endif
                            </td>

                            <td class="text-center check middle">{!! document_info_details($d->user_id, 'nid', 'verified', $i) !!}</td>                            
                            <td class="text-center check middle">{!! document_info_details($d->user_id, 'driving_license', 'verified', $i) !!}</td>
                            <td class="text-center check middle">{!! document_info_details($d->user_id, 'registration_paper', 'verified', $i) !!}</td>
                            <td class="text-center check middle">{!! document_info_details($d->user_id, 'tax_token', 'verified', $i) !!}</td>
                            <td class="text-center check middle">{!! document_info_details($d->user_id, 'enlistment_certificate', 'verified', $i) !!}</td>
                            <td class="text-center check middle">{!! document_info_details($d->user_id, 'fitness_certificate', 'verified', $i) !!}</td>
                          
                            <td>{!! driver_status($d->user_id) !!}</td>  
                            @if(@$user->can('update_driver_info'))
                            <td class="text-center middle update_td">
                              <span 
                                class="text-primary update_driver" 
                                role="button"
                                data-driver_id="{{$d->user_id}}"
                                data-first_name="{{$d->first_name}}"
                                data-last_name="{{$d->last_name}}"
                                data-email="{{$d->email}}"
                                data-address_line1="{{$d->address_line1}}"
                                data-address_line2="{{$d->address_line2}}"
                                data-city="{{$d->city}}"
                                data-state="{{$d->state}}"
                                data-postal_code="{{$d->postal_code}}"
                                data-nid_number="{{$d->nid_number}}"
                                data-driving_licence_number="{{$d->driving_licence_number}}"
                                data-passport_number="{{$d->passport_number}}"                              
                                data-document_type="{{documentType($d->user_id, 'nid')}}"
                                data-nid="{{document_link($d->user_id, 'nid')}}"
                                data-photo="{{$d->photo}}"
                                data-sl="{{$i}}"
                                >                            
                                <i class="fa fa-edit fa-2x"></i> 
                              </span>
                            </td> 
                            <td class="middle text-center set_partner_container">
                              @if(@$user->can('set_partner'))
                                @if($d->is_owner == '1')
                                  Owner
                                @elseif($d->is_owner == '0')
                                  Driver ({{$d->partner}})
                                @else
                          
                                <label class="col-sm-6 bg-success set_partner" style="padding:5px;" data-sl="{{$i}}" data-driver_id="{{$d->user_id}}" data-driver_name="{{$d->first_name}} {{$d->last_name}}" data-nid="{{document_link($d->user_id, 'nid')}}"  data-registration_paper="{{document_link($d->user_id, 'registration_paper')}}">
                                  <input type="radio" name="is_owner" value="1" >Owner
                                </label>
                                <label class="col-sm-6 bg-primary set_partner" style="padding:5px;" data-sl="{{$i}}" data-driver_id="{{$d->user_id}}" data-driver_name="{{$d->first_name}} {{$d->last_name}}" data-nid="{{document_link($d->user_id, 'nid')}}"  data-registration_paper="{{document_link($d->user_id, 'registration_paper')}}">
                                  <input type="radio" name="is_owner" value="0">Driver
                                </label>
                                @endif
                              @endif
                            </div>
                            @endif                    
                            <td>{{driver_last_remarks($d->user_id)}}</td>
                            <td>
                              {{admin_user($d->last_checked_by)}}
                              ({{$d->checked_time}})
                            </td>
                          </tr>
                        @endforeach
                      </table>
                    </div>
                  @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif
                

                
                @elseif($type == "verified") 
                  @if($list->firstItem() !='') 
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th class="middle">Sl#</th>
                          <th class="middle">Driver ID</th>
                          <th class="middle">Driver Name</th>
                          <th class="middle">Driver's Number</th>
                          <th class="middle">Car Plate Number</th>
                          <th class="middle">Driver's License Number</th>
                          <th class="middle">NID</th>
                          <th class="middle" style="border-left : 1px solid #eee">Status</th>
                          <th class="middle">Remarks</th>
                          <th class="middle">Photo</th>
                          <th class="middle">Trained</th>
                          <th class="middle">Verified by and Verified Time</th>
                        </tr> 
                        
                        @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td>
                          <td class="middle">{{$d->user_id}}</td>
                          <td class="middle">
                            @if(@$user->can('view_driver_profile'))
                              <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->user_id)}}" target="_blank">{{$d->driver_name}}</a>
                            @else 
                              {{$d->driver_name}}
                            @endif
                          </td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle">{!! driver_vehicle($d->user_id, 'link') !!}</td>
                          <td class="middle">{{$d->driving_licence_number}}</td>
                          <td class="middle">{{$d->nid_number}}</td>
                          
                          <td>{!! driver_status($d->user_id) !!}</td>                     
                          <td>{{driver_last_remarks($d->user_id)}}</td>
                          <td class="text-center check middle">
                            @if($d->photo !='')                                          
                              <a href="{{$d->photo}}" target="_blank">
                                <img src="{{$d->photo}}" width="80" height="80">
                              </a>
                            @endif
                          </td>  
                          
                          <td class="middle text-center" >
                            <span>
                              <input type="checkbox" name="trained" class="update" data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="trained" data-sl="sl_{{$i}}" data-tab="users">
                            </span>
                          </td>
                          <td>
                            {{admin_user($d->last_verified_by)}}
                            ({{$d->verified_time}})
                          </td>
                          
                        </tr>
                        @endforeach

                      </table>
                    </div>
                  @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif


                
                @elseif($type == "trained") 
                    @if($list->firstItem() !='')
                      <div class="container-border">
                        <table class="table table-bordered">
                          <tr>
                            <th class="middle">Sl#</th>
                            <th class="middle">Driver ID</th>
                            <th class="middle">Driver Name</th>
                            <th class="middle">Driver's Number</th>
                            <th class="middle">Car Plate Number</th>
                            <th class="middle">Driver's License Number</th>
                            <th class="middle">NID</th>
                            <th class="middle" style="border-left : 1px solid #eee">Status</th>
                            <th class="middle">Remarks</th>
                            <th class="middle">Photo</th>
                            <th class="middle">Active</th>
                            <th class="middle">Trained by and Trained Time</th>
                          </tr> 
                          
                          @foreach($list as $i => $d)
                          <tr id="sl_{{$i}}">
                            <td class="middle">{{$list->firstItem() + $i}}</td>
                            <td class="middle">{{$d->user_id}}</td>
                            <td class="middle">                              
                              @if(@$user->can('view_driver_profile'))
                                <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->user_id)}}" target="_blank">{{$d->driver_name}}</a>
                              @else 
                                {{$d->driver_name}}
                              @endif
                            </td>
                            <td class="middle">{{$d->mobile_number}}</td>
                            <td class="middle">{!! driver_vehicle($d->user_id, 'link') !!}</td>
                            <td class="middle">{{$d->driving_licence_number}}</td>
                            <td class="middle">{{$d->nid_number}}</td>
                            
                            <td>{!! driver_status($d->user_id) !!}</td>                     
                            <td>{{driver_last_remarks($d->user_id)}}</td>
                            <td class="text-center check middle">
                              @if($d->photo !='')                                          
                                <a href="{{$d->photo}}" target="_blank">
                                  <img src="{{$d->photo}}" width="80" height="80">
                                </a>
                              @endif
                            </td>  
                            
                            <td class="middle text-center" >
                              <span>
                                <input type="checkbox" name="active" class="update" data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="active" data-sl="sl_{{$i}}" data-tab="users">
                              </span>
                            </td>
                            <td>
                              {{admin_user($d->last_trained_by)}}
                              ({{$d->trained_time}})
                            </td>
                            
                          </tr>
                          @endforeach

                        </table>
                      </div>
                    @else
                      <div class="text-center" style="padding:50px;">No Data Found</div>
                    @endif

                
                @elseif($type == "active") 
                    @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th class="middle">Sl#</th>
                          <th class="middle">Driver ID</th>
                          <th class="middle">Driver Name</th>
                          <th class="middle">Driver's Number</th>
                          <th class="middle">Car Plate Number</th>
                          <th class="middle">Driver's License Number</th>
                          <th class="middle">NID</th>
                          <th class="middle" style="border-left : 1px solid #eee">Status</th>
                          <th class="middle">Remarks</th>
                          <th class="middle">Photo</th>
                            <th class="middle">Active by and Activation Time</th>
                        </tr> 
                        
                        @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td>
                          <td class="middle">{{$d->user_id}}</td>
                          <td class="middle">
                            @if(@$user->can('view_driver_profile'))
                              <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->user_id)}}" target="_blank">{{$d->driver_name}}</a>
                            @else 
                              {{$d->driver_name}}
                            @endif
                          </td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle">{!! driver_vehicle($d->user_id, 'link') !!}</td>
                          <td class="middle">{{$d->driving_licence_number}}</td>
                          <td class="middle">{{$d->nid_number}}</td>
                          
                          <td>{!! driver_status($d->user_id) !!}</td>                     
                          <td>{{driver_last_remarks($d->user_id)}}</td>
                          <td class="text-center check middle">
                            @if($d->photo !='')                                          
                              <a href="{{$d->photo}}" target="_blank">
                                <img src="{{$d->photo}}" width="80" height="80">
                              </a>
                            @endif
                          </td> 
                          <td> 
                            {{admin_user($d->last_active_by)}}
                            ({{$d->active_time}})
                          </td>
                            
                          
                        </tr>
                        @endforeach

                      </table>
                    </div>
                    @else
                      <div class="text-center" style="padding:50px;">No Data Found</div>
                    @endif
                
                @elseif($type == "owner") 
                  @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th class="middle">Sl#</th>
                          <th class="middle">Owner/ Driver ID</th>
                          <th class="middle">Name</th>
                          <th class="middle">Email</th>
                          <th class="middle">Mobile Number</th>
                          <th class="middle">Address</th>
                          <th class="middle">City</th>
                          <th class="middle">State</th>
                          <th class="middle">Postal Code</th>
                          <th class="middle">Active Time</th>
                          <th class="middle">Status</th>
                          <th class="middle">Photo</th>
                        </tr> 

                        @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td>
                          <td class="middle">{{$d->id}}</td>
                          <td class="middle">
                            @if(@$user->can('view_driver_profile'))
                              <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->id)}}" target="_blank">{{$d->driver_name}}</a>
                            @else 
                              {{$d->driver_name}}
                            @endif
                          </td>
                          <td class="middle">{{$d->email}}</td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle">{{$d->address_line1}}</td>
                          <td class="middle">{{$d->city}}</td>
                          <td class="middle">{{$d->state}}</td>
                          <td class="middle text-center">{{$d->postal_code}}</td>
                          <td class="middle">{{$d->active_time}}</td>
                          <td class="middle">{!! driver_status($d->id) !!}</td>
                          <td class="middle text-center">
                            @if($d->photo !='') 
                              <a href="{{$d->photo}}" target="_blank">
                                <img src="{{$d->photo}}" width="18" height="18">
                              </a>                              
                            @endif
                          </td>
                        </tr> 
                        @endforeach
                        
                      </table>
                    </div>
                  @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif
                
                @elseif($type == "partner") 
                  @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th class="middle">Sl#</th>
                          <th class="middle">Partner ID</th>
                          <th class="middle">Name</th>
                          <th class="middle">Email</th>
                          <th class="middle">Mobile Number</th>
                          <th class="middle">VAT Number</th>
                          <th class="middle">Status</th>
                          <th class="middle">Address</th>
                          <th class="middle">City</th>
                          <th class="middle">State</th>
                          <th class="middle">Postal Code</th>
                          <th class="middle">Commission</th>
                          <th class="middle">Created at</th>
                          <th class="middle">Total Driver</th>
                        </tr> 

                        @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td>
                          <td class="middle">{{$d->company_id}}</td>
                          <td class="middle">{{$d->name}}</td>
                          <td class="middle">{{$d->email}}</td>
                          <td class="middle">0{{$d->mobile_number}}</td>
                          <td class="middle">{{$d->vat_number}}</td>
                          <td class="middle">{{$d->status}}</td>
                          <td class="middle">{{$d->address}}</td>
                          <td class="middle">{{$d->city}}</td>
                          <td class="middle">{{$d->state}}</td>
                          <td class="middle text-center">{{$d->postal_code}}</td>
                          <td class="middle text-center">{{$d->company_commission}}</td>
                          <td class="middle text-center">{{date("d-m-Y", strtotime($d->created_at))}}</td>
                          <td class="middle text-center"><a href="{{url(LOGIN_USER_TYPE.'/company_driver_list/'.$d->id)}}" target="_blank">{{total_company_driver($d->id)}}</a></td>
                        </tr> 
                        @endforeach
                        
                      </table>
                    </div>
                  @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif

                @elseif($type == "drivers_under_partner") 
                  @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th class="middle">Sl#</th>
                          <th class="middle">Partner</th>
                          <th class="middle">Driver Name</th>
                          <th class="middle">Driver ID</th>
                          <th class="middle">NID/ Passport Number</th>
                          <th class="middle">Email</th>
                          <th class="middle">Mobile Number</th>
                          <th class="middle">Status</th>
                          <th class="middle">Address</th>
                          <th class="middle">City</th>
                          <th class="middle">State</th>
                          <th class="middle">Postal Code</th>
                          <th class="middle">Created at</th>
                          <th class="middle">Active at</th>
                        </tr> 
                     
                        @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td> 
                          <td class="middle">
                            <a href="{{url(LOGIN_USER_TYPE.'/company_driver_list/'.$d->company_id)}}" target="_blank">
                              {{company_name($d->company_id)}} ({{total_company_driver($d->company_id)}})
                            </a>
                          </td>
                          <td><a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->id)}}" target="_blank">{{$d->driver_name}}</a></div>
                          <td class="middle">{{$d->id}}</td>
                          <td class="middle">
                            @if($d->nid_number !='') {{$d->nid_number}}
                            @elseif($d->passport_number !='') {{$d->passport_number}}
                            @endif
                          </td>

                          <td class="middle">{{$d->email}}</td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle">{{driver_status($d->id)}}</td>
                          <td class="middle">{{$d->address_line1}}</td>
                          <td class="middle">{{$d->city}}</td>
                          <td class="middle">{{$d->state}}</td>
                          <td class="middle text-center">{{$d->postal_code}}</td>
                          <td class="middle text-center">{{$d->created_at}}</td>
                          <td class="middle text-center">{{$d->active_time}}</td>
                        </tr> 
                        @endforeach
                      </table>
                    </div>
                  @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif

                @elseif($type == "uncheck_owner_driver") 
                  @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th class="middle">Sl#</th>
                          <th class="middle">Driver Name</th>
                          <th class="middle">Driver ID</th>
                          <th class="middle">NID/ Passport Number</th>
                          <th class="middle">Email</th>
                          <th class="middle">Mobile Number</th>
                          <th class="middle">Status</th>
                          <th class="middle">Address</th>
                          <th class="middle">City</th>
                          <th class="middle">State</th>
                          <th class="middle">Postal Code</th>
                          <th class="middle">Created at</th>
                          <th class="middle">Active at</th>
                          @if(@$user->can('set_partner'))
                            <th class="middle" style="width:200px;">Owner / Driver</th>
                          @endif
                        </tr> 
                     
                        @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td> 
                          <td><a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->id)}}" target="_blank">{{$d->driver_name}}</a></div>
                          <td class="middle">{{$d->id}}</td>
                          <td class="middle">
                            @if($d->nid_number !='') {{$d->nid_number}}
                            @elseif($d->passport_number !='') {{$d->passport_number}}
                            @endif
                          </td>
                          <td class="middle">{{$d->email}}</td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle">{{driver_status($d->id)}}</td>
                          <td class="middle">{{$d->address_line1}}</td>
                          <td class="middle">{{$d->city}}</td>
                          <td class="middle">{{$d->state}}</td>
                          <td class="middle text-center">{{$d->postal_code}}</td>
                          <td class="middle text-center">{{$d->created_at}}</td>
                          <td class="middle text-center">{{$d->active_time}}</td>
                          <td class="middle text-center set_partner_container">
                            @if(@$user->can('set_partner'))   
                              @if(document_link($d->id, 'nid') !='' AND document_link($d->id, 'registration_paper') !='')                           
                                <label class="col-sm-6 bg-success set_partner" style="padding:5px;" data-sl="{{$i}}" data-driver_id="{{$d->id}}" data-driver_name="{{$d->driver_name}}" data-nid="{{document_link($d->id, 'nid')}}" data-registration_paper="{{document_link($d->id, 'registration_paper')}}">
                                  <input type="radio" name="is_owner" value="1" >Owner
                                </label>
                                <label class="col-sm-6 bg-primary set_partner" style="padding:5px;" data-sl="{{$i}}" data-driver_id="{{$d->id}}" data-driver_name="{{$d->driver_name}}" data-nid="{{document_link($d->id, 'nid')}}"  data-registration_paper="{{document_link($d->id, 'registration_paper')}}">
                                  <input type="radio" name="is_owner" value="0">Driver
                                </label>
                              @endif
                            @endif
                          </div>
                        </tr> 
                        @endforeach
                       
                        
                      </table>
                    </div>
                  @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif

                @elseif($type == "nid") 
                  @if($list->firstItem() !='')
                    <div class="container-border">
                      <table class="table table-bordered">
                        <tr>
                          <th class="middle">Sl#</th>
                          <th class="middle">Driver Name</th>
                          <th class="middle">Driver ID</th>
                          <th class="middle">NID/ Passport Number</th>
                          <th class="middle">Email</th>
                          <th class="middle">Mobile Number</th>
                          <th class="middle">Created at</th>
                          <th class="middle">Status</th>
                          <th class="middle">NID Photo</th>
                          @if($nid_verify == '1')
                            <th class="middle">Verified by</th>
                            <th class="middle">Verified at</th>
                          @elseif($nid_verify == '2')
                            <th class="middle">Checked by</th>
                            <th class="middle">Checked at</th>
                          @endif

                          @if($nid_verify == '0' OR $nid_verify == '2' OR $nid_verify == 'checked_nid' OR $nid_verify == 'not_checked_nid')
                            <th class="middle">Verify</th>
                          @elseif($nid_verify == 'not_submitted')
                            <th class="middle">Upload NID</th>
                          @endif
                          <th class="middle">NID Status</th>
                        </tr> 
                     
                        @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td> 
                          <td><a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->id)}}" target="_blank">{{$d->driver_name}}</a></div>
                          <td class="middle">{{$d->id}}</td>
                          <td class="middle">
                            @if($d->nid_number !='') {{$d->nid_number}}
                            @elseif($d->passport_number !='') {{$d->passport_number}}
                            @endif                            
                          </td>
                          <td class="middle">{{$d->email}}</td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle text-center">{{$d->created_at}}</td>
                          <td class="middle">{{driver_status($d->id)}}</td>
                          <td class="middle text-center">                          
                            @if($d->nid_number !='') 
                              @if($file = public_path("images/nid_photo/".$d->nid_number.".png")) @endif
                              @if($nid_photo = "//".env('ADMIN_PANEL_SUB_DOMAIN').".".env('DOMAIN')."/images/nid_photo/".$d->nid_number.".png") @endif
                              @if (file_exists($file))                  
                              <a href="{{$nid_photo}}" target="_blank">
                                <img src="{{$nid_photo}}" width="50" height="50">
                              </a>
                              @endif
                            @endif
                          </td>

                          @if($nid_verify == '1')
                            <td class="middle">{{admin_user($d->verified_by)}}</td>
                            <td class="middle">{{$d->verified_time}}</td>
                          @elseif($nid_verify == '2')
                            <td class="middle">{{admin_user($d->checked_by)}}</td>
                            <td class="middle">{{$d->checked_time}}</td>
                          @endif


                          @if($nid_verify == '0' OR $nid_verify == '2' OR $nid_verify == 'checked_nid' OR $nid_verify == 'not_checked_nid')
                            <td class="middle text-center">
                              <span 
                                  class="text-primary verify_nid" 
                                  role="button"
                                  data-driver_id="{{$d->id}}"
                                  data-driver_document_id="{{@$d->driver_document_id}}"
                                  data-driver_name="{{$d->driver_name}}"
                                  data-nid_number="{{$d->nid_number}}"
                                  data-document="{{@$d->document}}"
                                  data-photo="{{@$d->photo}}"
                                  data-sl="{{$i}}"
                                >                            
                                <i class="fa fa-refresh fa-2x text-primary"></i> 
                            </td>
                          @elseif($nid_verify == 'not_submitted')
                            <th class="middle">
                              @if(@$user->can('update_driver')) 
                                <a href="{{ url(LOGIN_USER_TYPE.'/edit_driver/'.$d->id) }}" target="_blank">Upload NID</a>
                              @endif
                            </th>
                          @endif

                          <td class="middle">
                            @if(isset($d->nid_status))
                              @if($d->nid_status == '0') Pending
                              @elseif($d->nid_status == '1') Verified
                              @elseif($d->nid_status == '2') Invalid
                              @endif
                            @else
                              Not Submitted
                            @endif
                          </td>
                        </tr> 
                        @endforeach                      
                        
                      </table>
                    </div>
                  @else
                    <div class="text-center" style="padding:50px;">No Data Found</div>
                  @endif
                  
                @endif  


                
                
                <div class="text-center">
                  {{$list->appends(request()->input())->links()}}
                </div>
              </div>        
            </div>
         </div>
      </div>
   </section>   
</div>


<div class="modal fade" id="modalExpireDate" role="dialog">
    <div class="modal-dialog"> 
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h4 class="modal-title" id="doc_name"></h4>
        </div>
        <div class="modal-body">
          <div>
            <div>Expire Date</div>
            <div style="margin-top:10px;margin-bottom:10px;">
              <input type="hidden" name="driver_doc_id" id="driver_doc_id" value="">
              <input type="text" name="exp_date"  class="form-control date" id="exp_date" placeholder="Expire Date" autocomplete="off">
            </div>
            <div class="text-right">
              <button type="button" class="btn btn-primary form_submit">
                <i class="fa fa-paper-plane"></i> Update
              </button>
            </div>
          </div>
        </div>
      </div>      
    </div>
</div>

<div class="modal fade" id="modalUpdateDriver" role="dialog">
    <div class="modal-dialog"> 
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h4 class="modal-title" id="driver_name">Update Driver's Information</h4>
        </div>
        <div class="modal-body" style="margin-top:10px;">
            <div class="text-center">
              <div class="col-sm-6">              
                <a href="" target="_blank" id="photo_link">
                  <img src="" style="width:180px;height:240px; border:1px solid #ddd; padding:1px;" class="border-success" id="photo" alt="">
                </a>
                <label>Driver uploaded Photo</label>
              </div>
              <div class="col-sm-6">
                <a href="" target="_blank" id="nid_photo_link">
                  <img src="" style="width:180px;height:240px; border:1px solid #ddd; padding:1px;" class="border-success" id="nid_photo" alt="">
                </a>
                <label>NID Server Photo/ Passport</label>
              </div>
            </div>

            <input type="hidden" name="sl" id="sl" value="">
            <input type="hidden" name="driver_id" id="driver_id" value="">
            
            <div class="row col-md-12" style="margin-top:20px !important">
              <div class="col-sm-4">Driver Name :</div>
              <div class="col-sm-4">              
                <input type="text" name="first_name" id="first_name" placeholder="First Name" class="form-control"  autocomplete="off">
              </div>
              <div class="col-sm-4">              
                <input type="text" name="last_name" id="last_name" placeholder="Last Name" class="form-control"  autocomplete="off">
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">NID Number :</div>
              <div class="col-sm-7">              
                <input type="text" name="nid_number" id="nid_number" placeholder="NID Number" class="form-control" maxlength="17" minlength="10" autocomplete="off" onkeypress="validate(event)">
              </div>
              <div class="col-sm-1" style="padding-left:2px;">
                <i class="fa fa-refresh fa-2x text-primary" role="button" id="check_nid"></i>
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">Passport Number :</div>
              <div class="col-sm-8">              
                <input type="text" name="passport_number" id="passport_number" placeholder="Passport Number" class="form-control" autocomplete="off">
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">Driver's License Number :</div>
              <div class="col-sm-8">              
                <input type="text" name="driving_licence_number" id="driving_licence_number" placeholder="Driver's License Number" class="form-control" autocomplete="off">
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">Email :</div>
              <div class="col-sm-8">              
                <input type="text" name="email" id="email" placeholder="Email" class="form-control" autocomplete="off">
              </div>
            </div>


            <div class="row col-md-12">
              <div class="col-sm-4">Address Line 1 :</div>
              <div class="col-sm-8">              
                <input type="text" name="address_line1" id="address_line1" placeholder="Address Line 1" class="form-control" autocomplete="off">
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">Address Line 2 :</div>
              <div class="col-sm-8">              
                <input type="text" name="address_line2" id="address_line2" placeholder="Address Line 2" class="form-control" autocomplete="off">
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">City :</div>
              <div class="col-sm-8">              
                <input type="text" name="city" id="city" placeholder="City" class="form-control" autocomplete="off">
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">State :</div>
              <div class="col-sm-8">              
                <input type="text" name="state" id="state" placeholder="State" class="form-control" autocomplete="off">
              </div>
            </div>

            <div class="row col-md-12">
              <div class="col-sm-4">Postal Code :</div>
              <div class="col-sm-8">              
                <input type="text" name="postal_code" id="postal_code" placeholder="Postal Code" class="form-control" autocomplete="off">
              </div>
            </div>        

            <div class="text-right">
              <button type="button" class="btn btn-primary form_submit">
                <i class="fa fa-paper-plane"></i> Update
              </button>
            </div>

          </div>
        </div>
      </div>      
    </div>
</div>



<div class="modal fade" id="modalVerifyNid" role="dialog">
    <div class="modal-dialog"> 
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h4 class="modal-title">Verify NID for <span  id="driver_name"></span></h4>
        </div>
        <div class="modal-body">
            <div class="col-sm-12 text-center" style="margin-bottom:5px;">
              <a href="" target="_blank" id="nid_link">
                <img src="" style="width:100%" id="doc"> <!-- max-height: 240px; -->
              </a>
            </div>
            <div class="text-center">
              <div class="col-sm-6">              
                <a href="" target="_blank" id="photo_link">
                  <img src="" style="width:180px;height:220px; border:1px solid #ddd; padding:1px;" class="border-success" id="photo" alt="">
                </a>
                <label>Driver Uploaded Photo</label>
              </div>
              <div class="col-sm-6">
                <a href="" target="_blank" id="nid_photo_link">
                  <img src="" style="width:180px;height:220px; border:1px solid #ddd; padding:1px;" class="border-success" id="nid_photo" alt="">
                </a>
                <label>NID Server Photo/ Passport</label>
              </div>
            </div>

            <input type="hidden" name="sl" id="sl" value="">
            <input type="hidden" name="driver_id" id="driver_id" value="">
            <input type="hidden" name="driver_document_id" id="driver_document_id" value="">

            <div class="row col-md-12" style="padding-top:5px;padding-bottom:5px;">
              <div class="col-sm-5">NID Number :</div>
              <div class="col-sm-7">              
                <input type="text" name="nid_number" id="nid_number" placeholder="NID Number" class="form-control" maxlength="17" minlength="10" autocomplete="off" onkeypress="validate(event)">
              </div>
            </div>

            <div class="text-right">
              <button type="button" class="btn btn-primary btn-lg" id="checkNid">
              <i class="fa fa-refresh"></i> Get NID Server Info
              </button>

              <button type="button" class="btn btn-success btn-lg verify_invalid" id="Verified">
                <i class="fa fa-paper-plane"></i> Verified
              </button>

              <button type="button" class="btn btn-warning btn-lg verify_invalid" id="Invalid">
                <i class="fa fa-close"></i> Invalid
              </button>
            </div>

          </div>
        </div>
      </div>      
    </div>
</div>

<div class="modal fade" id="modalPartner" role="dialog">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"></button>
        <h4 class="modal-title" id="driver_name">Driver Name</h4>
      </div>

      <div class="modal-body">
        <input type="hidden" name="sl" id="sl" value="">
        <input type="hidden" name="driver_id" id="driver_id" value="">

        <div>
            <!-- <div class="col-sm-6">
              <img src="" style="width:100%;border:1px solid #ddd; padding:1px;" class="border-success" id="nid" alt=""><br />
              <label>NID</label>
            </div>

            <div class="col-sm-6">
              <img src="" style="width:100%;border:1px solid #ddd; padding:1px;" class="border-success" id="registration_paper" alt=""><br />
              <label>Registration Paper</label>
            </div> -->

            <div class="col-sm-12">
              <img src="" style="width:100%;border:1px solid #ddd; padding:1px;" class="border-success" id="nid" alt=""><br />
              <label>NID / Passport</label>
            </div>

            <div class="col-sm-12">
              <img src="" style="width:100%;border:1px solid #ddd; padding:1px;" class="border-success" id="registration_paper" alt=""><br />
              <label>Registration Paper</label>
            </div>

          <div id="partner">
            <div class="text-right" style="padding:20px;">
              <button type="button" class="btn btn-primary confirm_partner">
                <i class="fa fa-plus"></i> Confirm Owner
              </button>
            </div>
          </div>

          <div id="driver">
            <div class="row " style="border 1px solid #ddd;margin-top:20px;">
              <div class="col-md-2 text-right">Existing Partner</div>
              <div class="col-md-5" style="padding-right:0px; margin-right:0px;">              
                <input type="text" name="keyword" id="keyword" placeholder="Partner Name/ Mobile Number" class="form-control search-box"  autocomplete="off">
                <input type="hidden" id="partner_id" name="partner_id">
                <span class="text-danger"></span>
                <div id="suggesstion-box"></div>
              </div>

              <div class="col-md-2" style="padding-left:2px; margin-left:0px;">              
                <button type="button" class="btn btn-primary" id="assign_partner">
                  <i class="fa fa-check"></i> Assign to Partner
                </button>
              </div>

              <div class="col-md-3 text-right" id="btn_add_new_partner" style="padding-right:50px;">
                <button type="button" class="btn btn-info">
                  <i class="fa fa-plus"></i> Add New Partner
                </button>
              </div>
            </div>

            <!-- <div class="row col-md-12 text-center">OR</div> -->

            <div style="border 1px solid #ddd" id="new_partner_content">
              <div class="row col-md-12" style="margin-top:20px !important">
                <div class="col-sm-2 text-right">Partner Name *</div>
                <div class="col-sm-4">              
                  <input type="text" name="name" id="name" placeholder="Partner Name" class="form-control"  autocomplete="off">
                </div>

                <div class="col-sm-2 text-right">Mobile Number *</div>
                <div class="col-sm-4">              
                  <input type="text" name="mobile_number" id="mobile_number" placeholder="Mobile Number" class="form-control"  autocomplete="off">
                </div>
              </div>

              <div class="row col-md-12">
                <div class="col-sm-2 text-right">Email</div>
                <div class="col-sm-4">              
                  <input type="text" name="email" id="email" placeholder="Email" class="form-control"  autocomplete="off">
                </div>

                <div class="col-sm-2 text-right">VAT Number</div>
                <div class="col-sm-4">              
                  <input type="text" name="vat_number" id="vat_number" placeholder="VAT Number" class="form-control"  autocomplete="off">
                </div>
              </div>
            
              <div class="row col-md-12">
                <div class="col-sm-2 text-right">Address</div>
                <div class="col-sm-4">              
                  <textarea name="address" id="address" placeholder="Address" class="form-control"  autocomplete="off" col="4"></textarea>
                </div>

                <div class="col-sm-2 text-right">City</div>
                <div class="col-sm-4">              
                  <input type="text" name="city" id="city" placeholder="City" class="form-control"  autocomplete="off">
                </div>
              </div>            

              <div class="row col-md-12">
                <div class="col-sm-2 text-right">State</div>
                <div class="col-sm-4">              
                  <input type="text" name="state" id="state" placeholder="State" class="form-control"  autocomplete="off">
                </div>

                <div class="col-sm-2 text-right">Postal Code</div>
                <div class="col-sm-4">              
                  <input type="text" name="postal_code" id="postal_code" placeholder="Postal Code" class="form-control"  autocomplete="off">
                </div>
              </div>           

              <div class="row col-md-12">
                <div class="col-sm-2 text-right">Commission</div>
                <div class="col-sm-4"> 
                  <div class="input-group"> 
                    <input class="form-control" placeholder="Company Commission" name="company_commission" id="company_commission" type="text" value="0" autocomplete="off">
                    <div class="input-group-addon" style="background-color:#eee;width:50px;">%</div>
                  </div>              
                </div>

                <div class="col-sm-2 text-right">Status</div>
                <div class="col-sm-4">              
                {!! Form::select('status', array('Pending' => 'Pending', 'Active' => 'Active', 'Inactive' => 'Inactive'), old('status'), ['class' => 'form-control', 'id' => 'status', 'placeholder' => 'Select']) !!}
                </div>
              </div>                       

              <div class="text-right" style="padding-right:40px;margin-top:20px !important;">
                <button type="button" class="btn btn-primary add_partner">
                  <i class="fa fa-plus"></i> Add Partner
                </button>
              </div>
            </div>

          </div>

          



          

        </div>
      </div>
    </div>      
  </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
$( ".date" ).datepicker({
     autoclose: true,
     todayHighlight: true,
     format: 'dd-mm-yyyy' 
});

$(".uncheck").click(function () {
  var sl = $(this).data('sl');
  var id = $(this).data('id');
  var user_id = $(this).data('user_id');
  var col = $(this).data('col');
  var tab = $(this).data('tab');
  var this_parent = $(this).parent(); 

  // if(col == "checked") col = "all_documents";
  // else if(col == "verified") col = "checked";
  // else if(col == "trained") col = "verified";
  // else if(col == "active") col = "trained";
  
    $.ajax({
      url: "{{url('admin/ajax/driver_status_uncheck')}}",
      type:"POST",
      data:{
        id:id,
        user_id:user_id,
        col:col,
        tab:tab,
        sl:sl,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
          var response_array = data.split('|');
          if(response_array.length == 2) $('#'+sl).hide();
          this_parent.html(response_array[0]);
        }
      },
    });

}); 

$(".setExpireDate").click(function () { 
  var id = $(this).data('id');
  var exp_date = $(this).data('date');
  var doc_name = $(this).data('doc_name');    

  $('#modalExpireDate #doc_name').html(doc_name);
  $('#modalExpireDate #driver_doc_id').val(id);
  $('#modalExpireDate #exp_date').val(exp_date);
  $('#modalExpireDate').modal('show');
});

$("#modalExpireDate .form_submit").click(function () { 
  var driver_doc_id = $('#modalExpireDate #driver_doc_id').val();
  var exp_date = $('#modalExpireDate #exp_date').val();
  
  if(driver_doc_id !='' && exp_date !=''){
    $.ajax({
      url: "{{url('admin/ajax/driver_expired_date_update')}}",
      type:"POST",
      data:{
        id:driver_doc_id,
        exp_date:exp_date,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
         $('#exp_'+driver_doc_id).html('');
        $('#modalExpireDate').modal('hide');
        }
      },
    });
  }

});

$(".update").change(function () { 
  var sl = $(this).data('sl');
  var id = $(this).data('id');
  var user_id = $(this).data('user_id');
  var col = $(this).data('col');
  var tab = $(this).data('tab');
  var this_parent = $(this).parent();  

  if(this.checked){
    $.ajax({
      url: "{{url('admin/ajax/driver_status_update')}}",
      type:"POST",
      data:{
        id:id,
        user_id:user_id,
        col:col,
        tab:tab,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
          var response_array = data.split('|');
          if(response_array.length == 2) $('#'+sl).hide();
          this_parent.html(response_array[0]);
        }
      },
    });
  }
});

$(".update_driver").click(function () {   
  var driver_id = $(this).data('driver_id');
  var driver_name = $(this).data('first_name')+" "+$(this).data('last_name');    
  var first_name = $(this).data('first_name');        
  var last_name = $(this).data('last_name');        
  var email = $(this).data('email');         
  var address_line1 = $(this).data('address_line1');     
  var address_line2 = $(this).data('address_line2');     
  var city = $(this).data('city');   
  var state = $(this).data('state');   
  var postal_code = $(this).data('postal_code');   
  var nid_number = $(this).data('nid_number');   
  var passport_number = $(this).data('passport_number'); 
  var document_type = $(this).data('document_type'); 
  var nid = $(this).data('nid'); 

  
  
  
  var driving_licence_number = $(this).data('driving_licence_number');
  var photo = $(this).data('photo');  
  var sl = $(this).data('sl');
  var nid_photo = "{{url('images/nid_photo')}}/"+nid_number+".png";


  $('#modalUpdateDriver #check_nid').addClass('text-primary');
  $('#modalUpdateDriver #check_nid').addClass('fa-refresh');
  $('#modalUpdateDriver #check_nid').removeClass('text-success'); 
  $('#modalUpdateDriver #check_nid').removeClass('fa-check');           
  $('#modalUpdateDriver #nid_number').removeAttr('disabled');

  $('#modalUpdateDriver #driver_name').html(driver_name);
  $('#modalUpdateDriver #driver_id').val(driver_id);
  $('#modalUpdateDriver #first_name').val(first_name);
  $('#modalUpdateDriver #last_name').val(last_name);
  $('#modalUpdateDriver #email').val(email);
  $('#modalUpdateDriver #address_line1').val(address_line1);
  $('#modalUpdateDriver #address_line2').val(address_line2);
  $('#modalUpdateDriver #city').val(city);
  $('#modalUpdateDriver #state').val(state);
  $('#modalUpdateDriver #postal_code').val(postal_code);
  $('#modalUpdateDriver #nid_number').val(nid_number);
  $('#modalUpdateDriver #passport_number').val(passport_number);
  $('#modalUpdateDriver #driving_licence_number').val(driving_licence_number);
  
  $('#modalUpdateDriver #photo_link').attr("href", photo);
  $('#modalUpdateDriver #photo').attr("src", photo);

  if(document_type == 'passport'){
    if(nid !=''){
      $('#modalUpdateDriver #nid_photo').attr("src", nid);
      $('#modalUpdateDriver #nid_photo_link').attr("href", nid);    
    }    
  }else{
    if(nid_photo !=''){
      $('#modalUpdateDriver #nid_photo').attr("src", nid_photo);
              
        $.ajax({
          type: 'HEAD',url: nid_photo,crossDomain: true,success: function () {
            $('#modalUpdateDriver #nid_photo_link').attr("href", nid_photo); 
          },
          error: function () {
            $('#modalUpdateDriver #nid_photo_link').attr("href", ""); 
            return false;
            }
        });      
    }   

  }
  
  
  $('#modalUpdateDriver #sl').val(sl);
  $('#modalUpdateDriver').modal('show');
});

$(".verify_nid").click(function () { 
  $('#modalVerifyNid #driver_name').html('');
  $('#modalVerifyNid #driver_id').val('');
  $('#modalVerifyNid #nid_number').val('');
  $('#modalVerifyNid #driver_document_id').val('');
  
  $('#modalVerifyNid #photo_link').attr("href", "");
  $('#modalVerifyNid #photo').attr("src", "");

  $('#modalVerifyNid #nid_link').attr("href", "");
  $('#modalVerifyNid #doc').attr("src", "");

  var driver_id = $(this).data('driver_id');
  var driver_document_id = $(this).data('driver_document_id');
  var driver_name = $(this).data('driver_name');    
  var nid_number = $(this).data('nid_number');     
  var doc = $(this).data('document');  
  var photo = $(this).data('photo');        
  var sl = $(this).data('sl');  
  var nid_photo = "{{url('images/nid_photo')}}/"+nid_number+".png";


  $('#modalVerifyNid #check_nid').addClass('text-primary');
  $('#modalVerifyNid #check_nid').addClass('fa-refresh');
  $('#modalVerifyNid #check_nid').removeClass('text-success'); 
  $('#modalVerifyNid #check_nid').removeClass('fa-check');           
  $('#modalVerifyNid #nid_number').removeAttr('disabled');

  $('#modalVerifyNid #driver_name').html(driver_name);
  $('#modalVerifyNid #driver_id').val(driver_id);
  $('#modalVerifyNid #nid_number').val(nid_number);
  $('#modalVerifyNid #driver_document_id').val(driver_document_id);
  
  $('#modalVerifyNid #photo_link').attr("href", photo);
  $('#modalVerifyNid #photo').attr("src", photo);

  $('#modalVerifyNid #nid_link').attr("href", doc);
  $('#modalVerifyNid #doc').attr("src", doc);
  
  

  if(nid_photo !=''){
    $('#modalUpdateDriver #nid_photo').attr("src", nid_photo);
    $('#modalUpdateDriver #photo_link').attr("src", nid_photo);

    $('#modalVerifyNid #nid_photo').attr("src", nid_photo);
    $('#modalVerifyNid #photo_link').attr("src", nid_photo);
            
    $.ajax({
      type: 'HEAD',url: nid_photo,crossDomain: true,success: function () {
        $('#modalVerifyNid #nid_photo_link').attr("href", nid_photo); 
      },
      error: function () {
        $('#modalVerifyNid #nid_photo_link').attr("href", ""); 
        return false;
        }
    });
  }
  
  
  $('#modalVerifyNid #sl').val(sl);
  $('#modalVerifyNid').modal('show');
});

$("#modalUpdateDriver #check_nid").click(function () {   
    var nid_number =  $('#modalUpdateDriver #nid_number').val();
    var user_id =  $('#modalUpdateDriver #driver_id').val();
    var sl =  $('#modalUpdateDriver #sl').val(); 

    if(nid_number.length == 10 || nid_number.length == 13 || nid_number.length == 17){
      
      $.ajax({
        url: "{{url('admin/ajax/verify_nid')}}",
        type:"POST",
        data:{
          nid:nid_number,
          user_id:user_id,          
          _token: "{{ csrf_token() }}"
        },
        success:function(data){
          if(data == 'Success') {
            $('#modalUpdateDriver #check_nid').removeClass('text-primary');
            $('#modalUpdateDriver #check_nid').removeClass('fa-refresh');
            $('#modalUpdateDriver #check_nid').addClass('fa-check');
            $('#modalUpdateDriver #check_nid').addClass('text-success');            
            $('#modalUpdateDriver #nid_number').attr('disabled', 'disabled'); 
                      
            var img = "{{'//'.env('ADMIN_PANEL_SUB_DOMAIN').'.'.env('DOMAIN').'/images/nid_photo'}}/"+nid_number+".png";
            var nid_photo = '<a href="'+img+'" target="_blank">';
            nid_photo+='<img src="'+img+'" width="18" height="18"></a>';
            
            $('#modalUpdateDriver #nid_photo').attr("src", img);

            $('#sl_'+sl+' .nid_photo').html(nid_photo);      
                  
          }else{
            if(data == '') alert("NID Server Error. Please try later. ");
		        else alert(data);
          }
        },
      });
    }else{
      alert("NID Number must be 10, 13 or 17 digits.");
    }
});

$("#modalVerifyNid #checkNid").click(function () {   
    var nid_number =  $('#modalVerifyNid #nid_number').val();
    var user_id =  $('#modalVerifyNid #driver_id').val();
    var sl =  $('#modalVerifyNid #sl').val(); 
    

    if(nid_number.length == 10 || nid_number.length == 13 || nid_number.length == 17){
      
      $.ajax({
        url: "{{url('admin/ajax/verify_nid')}}",
        type:"POST",
        data:{
          nid:nid_number,
          user_id:user_id,          
          _token: "{{ csrf_token() }}"
        },
        success:function(data){
          if(data == 'Success') {
            $('#modalVerifyNid #check_nid').removeClass('text-primary');
            $('#modalVerifyNid #check_nid').removeClass('fa-refresh');
            $('#modalVerifyNid #check_nid').addClass('fa-check');
            $('#modalVerifyNid #check_nid').addClass('text-success');            
            $('#modalVerifyNid #nid_number').attr('disabled', 'disabled'); 
                      
            var img = "{{'//'.env('ADMIN_PANEL_SUB_DOMAIN').'.'.env('DOMAIN').'/images/nid_photo'}}/"+nid_number+".png";
            var nid_photo = '<a href="'+img+'" target="_blank">';
            nid_photo+='<img src="'+img+'" width="18" height="18"></a>';
            
            $('#modalVerifyNid #nid_photo').attr("src", img);

            $('#sl_'+sl+' .nid_photo').html(nid_photo);      
                  
          }else{
            if(data == '') alert("NID Server Error. Please try later. ");
		        else alert(data);
          }
        },
      });
    }else{
      alert("NID Number must be 10, 13 or 17 digits.");
    }
});

$("#modalUpdateDriver .form_submit").click(function () {
  var driver_id = $('#modalUpdateDriver #driver_id').val();  
  var first_name = $("#modalUpdateDriver #first_name").val();
  var last_name = $("#modalUpdateDriver #last_name").val();        
  var email = $("#modalUpdateDriver #email").val();      
  var address_line1 = $("#modalUpdateDriver #address_line1").val();   
  var address_line2 = $("#modalUpdateDriver #address_line2").val();    
  var city = $("#modalUpdateDriver #city").val();   
  var state = $("#modalUpdateDriver #state").val();  
  var postal_code = $("#modalUpdateDriver #postal_code").val();  
  var passport_number = $("#modalUpdateDriver #passport_number").val();  
  var driving_licence_number = $("#modalUpdateDriver #driving_licence_number").val();
  var sl = $("#modalUpdateDriver #sl").val();
 
    $.ajax({
      url: "{{url('admin/ajax/update_driver_info')}}",
      type:"POST",
      data:{
        driver_id:driver_id,
        first_name:first_name,
        last_name:last_name,
        email:email,
        address_line1:address_line1,
        address_line2:address_line2,
        city:city,
        state:state,
        postal_code:postal_code,
        passport_number:passport_number,
        driving_licence_number:driving_licence_number,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
          $('#sl_'+sl+' .update_td').html('<i class="fa fa-2x fa-check text-success"></i>');          
          $('#modalUpdateDriver').modal('hide');
        }
      },
    });
}); 


$("#modalVerifyNid .verify_invalid").click(function () {
  var driver_id = $('#modalVerifyNid #driver_id').val(); 
  var sl = $("#modalVerifyNid #sl").val();
  var driver_document_id = $("#modalVerifyNid #driver_document_id").val();
  var action = $(this).attr('id');

    $.ajax({
      url: "{{url('admin/ajax/verify_invalid')}}",
      type:"POST",
      data:{
        driver_id:driver_id,
        driver_document_id:driver_document_id,
        action:action,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
          if(data == "1"){
            $('#sl_'+sl+' .verify_nid').html('<i class="fa fa-2x fa-check text-success"></i>');          
            $('#modalVerifyNid').modal('hide');
          }else{
            alert(data);
          }
        }
      },
    });
    
}); 


$(".set_partner").change(function(){
  var is_owner = $("input[type='radio']:checked").val();
  var driver_id = $(this).data('driver_id');
  var nid = $(this).data('nid');
  var registration_paper = $(this).data('registration_paper');
  var driver_name = $(this).data('driver_name');
  var sl = $(this).data('sl');
  
  $('#modalPartner').modal('show');

  if(is_owner == '1'){
    $('#modalPartner #partner').show();
    $('#modalPartner #driver').hide();
  }
  else if(is_owner == '0'){
    $('#modalPartner #driver').show();
    $('#modalPartner #partner').hide();
  }
  
  $('#modalPartner #sl').val(sl);
  $('#modalPartner #driver_id').val(driver_id);
  $('#modalPartner #driver_name').html(driver_name);

  $('#modalPartner #nid').attr("src", nid);
  $('#modalPartner #registration_paper').attr("src", registration_paper);

  
  $('#modalPartner #new_partner_content').hide();
   
  

});

$("#modalPartner #btn_add_new_partner").click(function(){
  $('#modalPartner #new_partner_content').show();
  $("#suggesstion-box").hide();
  $("#partner_id").val('');
});

$("#modalPartner .confirm_partner").click(function(){
  var sl = $("#modalPartner #sl").val();
  var driver_id = $("#modalPartner #driver_id").val();

  $.ajax({
      url: "{{url('admin/ajax/set_partner')}}",
      type:"POST",
      data:{
        is_owner:'1',
        driver_id:driver_id,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
          if(data == '1'){
            $('#sl_'+sl+' .set_partner_container').html('<i class="fa fa-2x fa-check text-success"></i>');     
            $('#modalPartner').modal('hide');
          }else{
            alert('Information did not save. Please try later.');
          }          
        }
      },
    });
});

$("#modalPartner .add_partner").click(function(){
  var sl = $("#modalPartner #sl").val();
  var driver_id = $("#modalPartner #driver_id").val();
  var name = $("#modalPartner #name").val();
  var mobile_number = $("#modalPartner #mobile_number").val();
  var email = $("#modalPartner #email").val();
  var vat_number = $("#modalPartner #vat_number").val();
  var address = $("#modalPartner #address").val();
  var city = $("#modalPartner #city").val();
  var state = $("#modalPartner #state").val();
  var status = $("#modalPartner #status").val();
  var postal_code = $("#modalPartner #postal_code").val();
  var company_commission = $("#modalPartner #company_commission").val();

  if(name.trim() !='' && mobile_number.trim() !=''){
    $.ajax({
      url: "{{url('admin/ajax/set_partner')}}",
      type:"POST",
      data:{
        is_owner:'0',
        driver_id:driver_id,
        name:name,
        mobile_number:mobile_number,
        email:email,
        vat_number:vat_number,
        address:address,
        city:city,
        state:state,
        status:status,
        postal_code:postal_code,
        company_commission:company_commission,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
          if(data == '1'){
            $('#sl_'+sl+' .set_partner_container').html('<i class="fa fa-2x fa-check text-success"></i>');     
            $('#modalPartner').modal('hide');
          }else{
            alert(data);
          }          
        }
      },
    });

  }
  else{
    alert('Partner Name and Mobile Number are required.');
  }

});

$("#modalPartner #assign_partner").click(function(){
  var sl = $("#modalPartner #sl").val();
  var partner_id = $("#modalPartner #partner_id").val();
  var driver_id = $("#modalPartner #driver_id").val();

  if(partner_id !=''){
    $.ajax({
      url: "{{url('admin/ajax/set_partner')}}",
      type:"POST",
      data:{
        is_owner:'0',
        driver_id:driver_id,
        partner_id:partner_id,
        _token: "{{ csrf_token() }}"
      },
      success:function(data){
        if(data) {
          $("#modalPartner #keyword").val('');
          $("#modalPartner #partner_id").val('');          

          if(data == '1'){
            $('#sl_'+sl+' .set_partner_container').html('<i class="fa fa-2x fa-check text-success"></i>');     
            $('#modalPartner').modal('hide');
          }else{
            alert('Information did not save. Please try later.');
          }          
        }
      },
    });
  }
  else{
    alert("Partner not selected");
  }
});


$('#modalPartner #keyword').on('keyup', function() {
    var keyword = $(this).val();
    if(keyword.length >=3) suggestion(keyword);
});

function suggestion(keyword){
  var url = "{{url('admin/ajax/suggestion_partner')}}?keywords="+keyword;
    
    $.ajax({
      type:'GET',
      url:url,
      beforeSend:function(){
        $("#keyword").css("background","#eee");
      },
      success:function(data){
        $("#suggesstion-box").show();
        $("#suggesstion-box").html(data);
        $("#keyword").css("background","#FFF");
      }	       
    });
}

function select_from_suggestion(label, val, option){
  $("#keyword").val(label);
  $("#suggesstion-box").hide();
  $("#partner_id").val(val);
}



</script>
@endpush
