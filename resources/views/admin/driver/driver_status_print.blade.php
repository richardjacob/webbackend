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
    width:55px;
    text-align: right;
  }

  .fa-check{
    margin-right:10px;
  }
  .table td, .table th{
    border:1px solid #acacac !important;
  }
 </style>


<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Manage  Driver <small>Driver Status</small></h1>
      <ol class="breadcrumb">
        <li>
          <a href="dashboard"><i class="fa fa-dashboard"></i> Home</a>
        </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/driver') }}">Manage  Driver </a>
        </li>
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
                  <li @if($type == "all_documents") class="active" @endif>
                    <a href="{{url(LOGIN_USER_TYPE.'/driver_status/all_documents')}}">
                      All Documents Submitted
                    </a>
                  </li>
                  <li @if($type == "checked") class="active" @endif>
                    <a href="{{url(LOGIN_USER_TYPE.'/driver_status/checked')}}">
                      Checked
                    </a>
                  </li>
                  <li @if($type == "verified") class="active" @endif>
                    <a href="{{url(LOGIN_USER_TYPE.'/driver_status/verified')}}">
                      Verified
                    </a>
                  </vi>
                  <li @if($type == "trained") class="active" @endif>
                    <a href="{{url(LOGIN_USER_TYPE.'/driver_status/trained')}}">
                      Trained
                    </a>
                  </li>
                  <li @if($type == "active") class="active" @endif>
                    <a href="{{url(LOGIN_USER_TYPE.'/driver_status/active')}}">
                      Active
                    </a>
                  </li>
                </ul>
                   
                @if($list->firstItem() !='')                                 
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
                  <div class="container-border">
                    <table class="table table-bordered">
                      <tr>
                        <th rowspan="2" class="middle">Sl#</th>
                        <th rowspan="2" class="middle">Driver ID</th>
                        <th rowspan="2" class="middle">Driver Name</th>
                        <th rowspan="2" class="middle">Driver's Number</th>
                        <th rowspan="2" class="middle">Car Plate Number</th>
                        <th colspan="3" class="middle">Personal Documents (Checked)</th>
                        <th colspan="4" class="middle">Car Documents  (Checked)</th>
                        <th rowspan="2" class="middle" style="border-left : 1px solid #eee">Status</th>
                        <th rowspan="2" class="middle">Remarks</th>
                      </tr>
                      <tr>
                        <th class="middle">Photo</th>
                        <th class="middle">Driver's License</th>
                        <th class="middle">NID/ Passport</th>

                        <th class="middle">Registration</th>
                        <th class="middle">Tax Token</th>
                        <th class="middle">Enlistment</th>
                        <th class="middle">Fitness Certificate</th>
                      </tr>
                    @foreach($list as $i => $d)
                      <tr id="sl_{{$i}}">
                        <td class="middle">{{$list->firstItem() + $i}}</td>
                        <td class="middle">{{$d->user_id}}</td>
                        <td class="middle">{{$d->driver_name}}</td>
                        <td class="middle">{{$d->mobile_number}}</td>
                        <td class="middle">{!! driver_vehicle($d->user_id) !!}</td>
             
                        <td class="text-center check middle">
                          @if($d->photo !='')              
                              @if($d->photo_checked == '1')                              
                                  <i class="fa fa-check text-success" aria-hidden="true" title="Checked by {!! admin_user($d->checked_by) !!} at {!! $d->checked_time !!}"></i>
                                  <a href="{{$d->photo}}" target="_blank">
                                    <img src="{{$d->photo}}" width="18" height="18">
                                  </a>
                              @else
                                <span>
                                  <input type="checkbox" name="profile_picture" class="update" data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="checked" data-sl="sl_{{$i}}" data-tab="profile_picture">
                                </span>
                                <a href="{{$d->photo}}" target="_blank">
                                  <img src="{{$d->photo}}" width="18" height="18">
                                </a>
                              @endif
                            
                          @endif
                        </td>

                        <td class="text-center check middle">{!! document_info_details($d->user_id, 'driving_license', 'checked', $i) !!}</td>
                        <td class="text-center check middle">{!! document_info_details($d->user_id, 'nid', 'checked', $i) !!}</td>
                        <td class="text-center check middle">{!! document_info_details($d->user_id, 'registration_paper', 'checked', $i) !!}</td>
                        <td class="text-center check middle">{!! document_info_details($d->user_id, 'tax_token', 'checked', $i) !!}</td>
                        <td class="text-center check middle">{!! document_info_details($d->user_id, 'enlistment_certificate', 'checked', $i) !!}</td>
                        <td class="text-center check middle">{!! document_info_details($d->user_id, 'fitness_certificate', 'checked', $i) !!}</td>
                      
                        <td>{!! driver_status($d->user_id) !!}</td>                     
                        <td>
                          <?php
                            $add_remarks = (auth('admin')->user()->can('add_drivers_remarks')) ? '<a href="'.url(LOGIN_USER_TYPE.'/add_drivers_remarks/'.$d->user_id).'" class="btn btn-xs btn-success" style="margin-bottom:5px;" target="_blank"><i class="fa fa-comment"></i></a>&nbsp;' : '';
                            echo $add_remarks;
                            ?>
                            {{driver_last_remarks($d->user_id)}}
                        </td>
                      </tr>
                    @endforeach
                    </table>
                  </div>
                
                @elseif($type == "checked") 
                  <div class="container-border">
                    <table class="table table-bordered">
                      <tr>
                        <th rowspan="2" class="middle">Sl#</th>
                        <th rowspan="2" class="middle">Driver ID</th>
                        <th rowspan="2" class="middle">Driver Name</th>
                        <th rowspan="2" class="middle">Driver's Number</th>
                        <th rowspan="2" class="middle">Car Plate Number</th>
                        <th colspan="3" class="middle">Personal Documents (Verified)</th>
                        <th colspan="4" class="middle">Car Documents  (Verified)</th>
                        <th rowspan="2" class="middle" style="border-left : 1px solid #eee">Status</th>
                        <th rowspan="2" class="middle">Remarks</th>
                      </tr>
                      <tr>
                        <th class="middle">Photo</th>
                        <th class="middle">Driver's License</th>
                        <th class="middle">NID</th>

                        <th class="middle">Registration</th>
                        <th class="middle">Tax Token</th>
                        <th class="middle">Enlistment</th>
                        <th class="middle">Fitness Certificate</th>
                      </tr>
                      @foreach($list as $i => $d)
                        <tr id="sl_{{$i}}">
                          <td class="middle">{{$list->firstItem() + $i}}</td>
                          <td class="middle">{{$d->user_id}}</td>
                          <td class="middle">{{$d->driver_name}}</td>
                          <td class="middle">{{$d->mobile_number}}</td>
                          <td class="middle">{!! driver_vehicle($d->user_id) !!}</td>
              
                          <td class="text-center check middle">
                            @if($d->photo !='')              
                                @if($d->photo_verified == '1')                              
                                    <i class="fa fa-check text-success" aria-hidden="true" title="Verified by {!! admin_user($d->verified_by) !!} at {!! $d->verified_time !!}"></i>
                                    <a href="{{$d->photo}}" target="_blank">
                                      <img src="{{$d->photo}}" width="18" height="18">
                                    </a>
                                @else
                                  <span>
                                    <input type="checkbox" name="profile_picture" class="update" data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="verified" data-sl="sl_{{$i}}" data-tab="profile_picture">
                                  </span>
                                  <a href="{{$d->photo}}" target="_blank">
                                    <img src="{{$d->photo}}" width="18" height="18">
                                  </a>
                                @endif
                              
                            @endif
                          </td>

                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'driving_license', 'verified', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'nid', 'verified', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'registration_paper', 'verified', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'tax_token', 'verified', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'enlistment_certificate', 'verified', $i) !!}</td>
                          <td class="text-center check middle">{!! document_info_details($d->user_id, 'fitness_certificate', 'verified', $i) !!}</td>
                        
                          <td>{!! driver_status($d->user_id) !!}</td>                     
                          <td>{{driver_last_remarks($d->user_id)}}</td>
                        </tr>
                      @endforeach
                    </table>
                  </div>
                
                @elseif($type == "verified") 
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
                      </tr> 
                      
                      @foreach($list as $i => $d)
                      <tr id="sl_{{$i}}">
                        <td class="middle">{{$list->firstItem() + $i}}</td>
                        <td class="middle">{{$d->user_id}}</td>
                        <td class="middle">{{$d->driver_name}}</td>
                        <td class="middle">{{$d->mobile_number}}</td>
                        <td class="middle">{!! driver_vehicle($d->user_id) !!}</td>
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
                        
                      </tr>
                      @endforeach

                    </table>
                  </div>

                  @elseif($type == "trained") 
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
                      </tr> 
                      
                      @foreach($list as $i => $d)
                      <tr id="sl_{{$i}}">
                        <td class="middle">{{$list->firstItem() + $i}}</td>
                        <td class="middle">{{$d->user_id}}</td>
                        <td class="middle">{{$d->driver_name}}</td>
                        <td class="middle">{{$d->mobile_number}}</td>
                        <td class="middle">{!! driver_vehicle($d->user_id) !!}</td>
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
                            <input type="checkbox" name="trained" class="update" data-id="{{$d->user_id}}"  data-user_id="{{$d->user_id}}" data-col="active" data-sl="sl_{{$i}}" data-tab="users">
                          </span>
                        </td>
                        
                      </tr>
                      @endforeach

                    </table>
                  </div>

                  @elseif($type == "active") 
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
                      </tr> 
                      
                      @foreach($list as $i => $d)
                      <tr id="sl_{{$i}}">
                        <td class="middle">{{$list->firstItem() + $i}}</td>
                        <td class="middle">{{$d->user_id}}</td>
                        <td class="middle">{{$d->driver_name}}</td>
                        <td class="middle">{{$d->mobile_number}}</td>
                        <td class="middle">{!! driver_vehicle($d->user_id) !!}</td>
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
                        
                      </tr>
                      @endforeach

                    </table>
                  </div>
                
                  @endif

                
                <div class="text-center">
                    {{ $list->links() }}
                  </div>
              </div>



            

        
            </div>
         </div>
      </div>
   </section>
   
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$( ".date" ).datepicker({
     autoclose: true,
     todayHighlight: true,
     format: 'dd-mm-yyyy' 
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

</script>
@endpush
