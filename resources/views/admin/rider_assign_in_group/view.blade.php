@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Rider <small>Add Rider in <b>{{$rider_group_name->name}}</b> Group</small></h1>
      <ol class="breadcrumb">
        <li>
          <a href="dashboard"><i class="fa fa-dashboard"></i> Home</a>
        </li>
        <li class="active">Rider</li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/rider_group') }}"> Rider Group</a>
        </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/add_rider_in_group/'.$rider_group_id) }}"> Add</a>
        </li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                 {{-- <div class="col-lg-2"></div> --}}

                  <div class="col-md-12">
                  {!! Form::open(['url' => LOGIN_USER_TYPE.'/add_rider_in_group/'.$rider_group_id, 'class' => 'form-horizontal', 'method' => 'POST', 'id' => 'frm']) !!}
                  <br><br>
                  <div class="col-sm-11">
                    <textarea type="text" name="keyword" value="" style="width:100%;" class="form-control" placeholder="Rider Mobile / Id" autocomplete="off"></textarea>
                    <input type="hidden" name="rider_group_id" value="{{$rider_group_id}}">
                  </div>
                  <div class="col-sm-1 pull-right">
                      <button type="submit" class="btn btn-primary form_submit" style="margin-top: 33%;">
                        <i class="fa fa-search"></i> Search
                      </button>
                  </div>
                  {!! Form::close() !!}
                  </div>

                 {{-- <div class="col-lg-2"></div> --}}
               </div>
               
               {{-- @foreach($list as $l) --}}
               {!! Form::open(['url' => LOGIN_USER_TYPE.'/rider_group', 'class' => 'form-horizontal', 'method' => 'POST', 'id' => 'frm']) !!}
               <input type="hidden" name="rider_group_id" value="{{$rider_group_id}}">
              
               @if(isset($list))
            <table class="table table-bordered">
              <tr>
                <th class="middle"><b>Sl#</b></th>
                {{-- <th class="middle"><b>Check</b></th> --}}
                <th class="middle"><b>Check All<input type="checkbox" id="checkAll" name="checkAll" @if(@$checkAll == 'All') checked="" @endif value="All"></b></th>
                <th class="middle"><b>Photo</b></th>
                <th class="middle"><b>Rider Name</b></th>
                <th class="middle"><b>Rider's Number</b></th>
                <th class="middle"><b>Rider ID</b></th>
              </tr> 
              
              @foreach($list as $i => $d)
              <tr id="sl_{{$i}}">
                <td class="middle text-center">{{ $i+1}}</td>

                <td class="text-center check middle" style="width:40px !important;padding-left:10px !important">
                  
                          <input type="checkbox" name="rider_id[]" value="{{$d->id}}" class="chk" >
                        
                </td>

                {{-- <td class="middle">{{$d->src}}</td>  --}}
                <td class="text-center check middle">
                  @if($d->src !='')                                          
                    <a href="{{$d->src}}" target="_blank">
                      <img src="{{$d->src}}" width="80" height="80">
                    </a>
                  @endif
                </td> 
                <td class="middle text-center">{{$d->first_name}}</td>
                <td class="middle text-center">{{$d->mobile_number}}</td> 
                <td class="middle text-center">{{$d->id}}</td>
                {{-- <td class="middle">
                  @if(@$user->can('view_driver_profile'))
                    <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->user_id)}}" target="_blank">{{$d->driver_name}}</a>
                  @else 
                    {{$d->driver_name}}
                  @endif
                </td> --}}
                
              </tr>
              @endforeach
              <button type="submit" class="btn btn-info pull-right" style="margin-right: 4%;" name="submit" value="submit">Submit</button>
              @endif
              <br>
              <br>
              <br>
              <br>
            </table>
            {!! Form::close() !!}
            {{-- @endforeach --}}


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

                .table-bordered > thead > tr > th, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td, .table-bordered > tfoot > tr > td {
                  border: 1px solid #1a1818 !important;
                }
               </style>
               
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

$("#checkAll").change(function () {
  $("input:checkbox").prop('checked', $(this).prop("checked"));
});

$(".chk").change(function () { 

  var uncheque = "";

  $('.chk').each(function () {
    if(!this.checked) uncheque ='1';
  });

  if(uncheque != "") $("#checkAll").prop('checked', false);
  else $("#checkAll").prop('checked', true);

}); 
</script>
@endpush


