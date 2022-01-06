@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Manage Driver <small>Car Acquisition</small></h1>
      <ol class="breadcrumb">
        <li>
          <a href="dashboard"><i class="fa fa-dashboard"></i> Home</a>
        </li>
        <li class="active">Manage Driver</li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/car_acquisition') }}"> Car Acquisition</a>
        </li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                  <div class="col-lg-12">
                  {!! Form::open(['url' => LOGIN_USER_TYPE.'/car_acquisition', 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}

                    <div class="col-sm-2">
                      <select name="hub_id" id="hub_id" class="form-control">
                      	<option value="">All Hub</option>
                      	@foreach($hub_list as $hub)
                      	<option value="{{$hub->id}}" @if($hub->id == @$hub_id) selected="" @endif>{{$hub->name}}</option>
                      	@endforeach
                      </select>
                    </div>

                    <div class="col-sm-2">
                      <select name="employee_id" id="employee_id" class="form-control">
                        <option value="">All Employees</option>
                        @foreach($employee_list as $employee)
                        <option value="{{$employee->id}}" @if($employee->id == @$employee_id) selected="" @endif>{{$employee->employee_name}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-sm-2">
                      <input type="text" name="code" value="{{@$code}}"  class="form-control" placeholder="Referral Code" autocomplete="off">
                    </div>

                    <div class="col-sm-2">
                      <input type="text" name="start_date" value="{{date('d-m-Y', strtotime($start_date))}}"  class="form-control date" placeholder="Start Date" autocomplete="off">
                    </div>

                    <div class="col-sm-2">
                      <input type="text" name="end_date" value="{{date('d-m-Y', strtotime($end_date))}}"  class="form-control date" placeholder="End Date" autocomplete="off">
                    </div>
                    <div class="col-sm-1" style="padding:0px;">
                      <select name="per_page" class="form-control" style="padding-left:0px; padding-right:0px;">
                        <option value="">Per Page</option>
                        @foreach(array('10','20','50','100') as $p)
                          <option value="{{$p}}" @if($p == @$per_page) selected @endif>{{$p}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-sm-1">
                      <button type="submit" class="btn btn-primary form_submit">
                        <i class="fa fa-search"></i> Search
                      </button>
                    </div>
                  </div>

                  <div>
                    <div class="text-right" style="padding-right:5px;">                      
                       <button type="submit" onclick="print_page()" name="print" value="Print" class="btn btn-success form_submit">
                         <i class="fa fa-print"></i> Print
                       </button>
                    </div>
                  </div>
                  {!! Form::close() !!}
               </div>
               
               <!-- /.box-header -->
               <style type="text/css">
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
                #filter{
                    margin:10px;
                }
                #filter td:first-child{
                    padding-right:20px;
                }
                #filter td{
                    padding-right:20px;
                }
               </style>


              <div class="text-center">
                <h3>Alesha Ride Limited</h3>
              </div>
              <div class="text-center">
                <h4>Car Acquisition Report<h4>                      
              </div>

              <table id='filter'>
                @if(@$start_date !='')
                <tr>
                  <td>Date Range</td>
                  <td>:</td>
                  <td>
                    {{date("d F Y", strtotime($start_date))}}
                    @if(@$end_date !='') To {{date("d F Y", strtotime($end_date))}} @endif
                  </td>
               </tr>
               @endif

               @if(@$code !='')
                <tr>
                  <td>Referral Code</td>
                  <td>:</td>
                  <td>{{@$code}}</td>
                </tr>
               @endif

               @if(@$employee_name !='')
                <tr>
                  <td>Employee</td>
                  <td>:</td>
                  <td>{{@$employee_name}}</td>
                </tr>
               @endif

               @if(@$hub_name !='')
                <tr>
                  <td>Hub</td>
                  <td>:</td>
                  <td>{{$hub_name}}</td>
                </tr>
               @endif
              </table>
              
              @if(is_object($list) and count($list) > 0)
               <div class="box-body">                  
                  <div class="row">                    
                    <div class="text-left col-md-6">
                      Page {{$list->currentPage()}} of {{$list->lastPage()}}
                    </div>
                    <div class="text-right col-md-6">
                      Showings records from {{$list->firstItem()}} to {{$list->lastItem()}} of total {{$list->total()}}
                    </div>
                  </div>

                  <table class="table table-bordered">
                    <tr>
                      <th rowspan="2" class="middle">Sl#</th>
                      <th rowspan="2" class="middle">Driver ID</th>
                      <th rowspan="2" class="middle">Driver Name</th>
                      <th rowspan="2" class="middle">Driver's Number</th>
                      <th rowspan="2" class="middle">Car Number Plate No</th>
                      <th rowspan="2" class="middle">Camera</th>
                      <th colspan="3" class="middle">Personal Documents</th>
                      <th colspan="4" class="middle">Car Documents</th>
                      <th rowspan="2" class="middle" style="border-left : 1px solid #eee">Acquistioned By</th>
                      <th rowspan="2" class="middle">Status</th>
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
                      <tr>
                        <td class="middle">{{$list->firstItem() + $i}}</td>
                        <td class="middle">{{$d->id}}</td>
                        <td class="middle">
                        <?php 
                            $user = Auth::guard('admin')->user();
                          ?>
                          @if(@$user->can('view_driver_profile'))
                            <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->id)}}" target="_blank">{{$d->driver_name}}</a>
                          @else 
                            {{$d->driver_name}}
                          @endif  
                        </td>
                        <td class="middle">{{$d->mobile_number}}</td>
                        <td class="middle">{!! driver_vehicle($d->id) !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'camera', 'icon') !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'photo', 'icon') !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'driving_license', 'icon') !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'nid', 'icon') !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'registration_paper', 'icon') !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'tax_token', 'icon') !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'enlistment_certificate', 'icon') !!}</td>
                        <td class="text-center middle">{!! is_exist($d->id, 'fitness_certificate', 'icon') !!}</td>
                        <td class="middle">
                          {{$d->employee_name}} ({{$d->refaral_id}})<br />
                          {{@$d->employee_mobile_number}}
                        </td>       
                        <td>{{driver_status($d->id)}}</td>                 
                        <td>{{driver_last_remarks($d->id)}}</td>
                      </tr>
                    @endforeach
                  </table>
                  <div class="text-center">
                    {{ $list->appends(['hub_id' => @$hub_id, 'employee_id' => @$employee_id, 'code' => @$code, 'start_date' => @$start_date, 'end_date' => @$end_date])->links() }}
                  </div>
               </div>
              @else
                <div class="aler alert-danger text-center">No record found</div>
              @endif

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


$('#hub_id').on('change', function() {
  var hub_id = this.value;
  $.ajax({
    url: "{{url(LOGIN_USER_TYPE.'/hub_employee_ajax')}}",
    type:"POST",
    data:{
      id:hub_id,
      _token: "{{ csrf_token() }}"
    },
    success:function(response){
      if(response) {
        $('#employee_id').html(response);
      }
    },
  });
});

function print_page(){
  // var form = document.getElementById("frm");
  // form.setAttribute("target", "_blank");
  // form.submit();
}

</script>
@endpush
