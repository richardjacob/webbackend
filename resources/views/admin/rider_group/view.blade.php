@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Rider <small>Rider Group</small></h1>
      <ol class="breadcrumb">
        <li>
          <a href="dashboard"><i class="fa fa-dashboard"></i> Home</a>
        </li>
        <li class="active">
          Rider
       </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/rider_group') }}"> Rider Group</a>
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

                  <div style="float:right;">
                    @if(Auth::guard('admin')->user()->can('add_rider_group'))
                    <a class="btn btn-success" id="add_rider_group">Add Group</a>
                    @endif
                </div><br><br>

                
                    {{-- <div class="col-sm-2">
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
               </div> --}}


               
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


              {{-- <div class="text-center">
                <h3>Alesha Ride Limited</h3>
              </div> --}}
              <div class="text-center">
                <h4>Rider Group List<h4>                      
              </div>

              
              
              @if(is_object($list) and count($list) > 0)
               <div class="box-body">

                  <table class="table table-bordered">
                    <tr>
                      <th >Sl#</th>
                      <th >Group Name</th>
                      <th >Cteated At</th>
                      <th >Action</th>
                    </tr>
                    @foreach($list as $sl => $i)
                      <tr>
                        <td>{{$sl+1}}</td>
                        <td>{{$i->name}}</td>
                        <td class="visibility: hidden"><input type="hidden" name="group_id"  value="$i->name"></td>
                        
                        <td>{{$i->created_at->format('d-M-Y')}}</td>
                        <td>
                          @if(Auth::guard('admin')->user()->can('add_rider_in_group'))
                              <a class="btn btn-xs btn-primary" href="{{ url(LOGIN_USER_TYPE.'/add_rider_in_group/'.$i->id)}}">
                                <i class="fa fa-plus"></i>
                                  Add Rider
                              </a>
                          @endif

                          @if(Auth::guard('admin')->user()->can('view_rider_group_list'))
                              <a class="btn btn-xs btn-success" href="{{ url(LOGIN_USER_TYPE.'/view_rider_group_list/'.$i->id)}}">
                                  View
                              </a>
                          @endif
                         {{--  @if(Auth::guard('admin')->user()->can('edit_rider_group_list'))
                              <a class="btn btn-xs btn-info" href="{{ url(LOGIN_USER_TYPE.'/edit_rider_group_list/'.$i->id)}}">
                                  Edit
                              </a>
                          @endif
                          @if(Auth::guard('admin')->user()->can('delete_rider_group_list'))
                              <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                  <input type="hidden" name="_method" value="DELETE">
                                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                  <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                              </form>
                          @endif --}}
                        </td>
                      </tr>
                    @endforeach 
                  </table>
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

<div class="modal fade" id="modalPartner" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h4 class="modal-title" id="driver_name">Add Rider Group</h4>
        </div>
  
        <div class="modal-body">
          <input type="hidden" name="sl" id="sl" value="">
          <input type="hidden" name="driver_id" id="driver_id" value="">
          <div>
            <div id="driver">
              <div class="row " style="border 1px solid #ddd;margin-top:20px;">
                <div class="col-md-2 text-right">Add Group</div>
                <div class="col-md-5" style="padding-right:0px; margin-right:0px;">              
                  <input type="text" name="name" id="name" placeholder="Group Name" class="form-control search-box"  autocomplete="off">
                </div>
  
                <div class="col-md-3 text-right" id="btn_add_new_partner" style="padding-right:50px;">
                  <button type="button" class="btn btn-info" id="add_group2">
                    <i class="fa fa-plus" ></i> Add Group
                  </button>
                </div>
              </div>
  
              <!-- <div class="row col-md-12 text-center">OR</div> -->
            </div>
          </div>
        </div>
      </div>      
    </div>
  </div>

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

$("#add_rider_group").click(function(){
    $('#modalPartner').modal('show');
});


$("#modalPartner #add_group2").click(function(){
    // $('#modalPartner').modal('show');

    var name = $("#modalPartner #name").val();
    $.ajax({
    url: "{{url(LOGIN_USER_TYPE.'/add_rider_group_submit')}}",
    type:"POST",
    data:{
      name:name,
      _token: "{{ csrf_token() }}"
    },
    success:function(response){
      if(response) {
        if(response == '1'){
          $('#modalPartner').modal('hide');
          location.reload();
        }else alert(response);
      }
    },
  });
});
</script>
@endpush


