@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>All Rider list in <b>{{$rider_group_name->name}}</b> Group</h1>
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

                 {{-- <div class="col-lg-2"></div>

                  <div class="col-md-12">
                  {!! Form::open(['url' => LOGIN_USER_TYPE.'/add_rider_in_group/'.$rider_group_id, 'class' => 'form-horizontal', 'method' => 'POST', 'id' => 'frm']) !!}
                  <br><br>
                  <div class="col-sm-6"></div>
                  <div class="col-sm-5" >
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

                 <div class="col-lg-2"></div> --}}


                 <div class="text-center">
                <h4>All Rider in {{$rider_group_name->name}} Group<h4>                      
              </div>

               </div>
               
               {{-- @foreach($list as $l) --}}
               {!! Form::open(['url' => LOGIN_USER_TYPE.'/rider_group', 'class' => 'form-horizontal', 'method' => 'POST', 'id' => 'frm']) !!}
               <input type="hidden" name="rider_group_id" value="{{$rider_group_id}}">
              
               @if(isset($list))
            <table class="table table-bordered">
              <tr >
                <th class="middle"><b>Sl#</b></th>
                
                <th class="middle"><b>Check</b></th>
                <th class="middle"><b>Photo</b></th>
                <th class="middle"><b>Rider Name</b></th>
                <th class="middle"><b>Rider's Number</b></th>
                <th class="middle"><b>Rider ID</b></th>
              </tr> 
              
              @foreach($list as $i => $d)
              <tr id="sl_{{$i}}">
                <td class="middle text-center">{{ $i+1}}</td>

                {{-- <td class="text-center check middle" style="width:40px !important;padding-left:10px !important">
                  
                      <input type="checkbox" name="rider_id[]" value="{{$d->id}}">
                        
                </td> --}}



                <td class="text-center check middle text-center" style="width:60px !important;padding-left:10px !important">
                  {{-- <span>
                    <input type="checkbox" name="profile_picture" class="update" data-id="10549" data-user_id="10549" data-col="verified" data-sl="sl_0" data-tab="profile_picture">
                  </span> --}}
                  <span>
                    {{-- <i class="fa fa-close text-danger fa-2x remove_group_id" aria-hidden="true" data-id="10549" data-user_id="10549" data-sl="sl_{{$i}}" data-tab="profile_picture" style="cursor: pointer;"></i> --}}
                    <i class="fa fa-close text-danger fa-2x remove_group_id" aria-hidden="true" data-id="{{$d->id}}" data-sl="sl_{{$i}}" style="cursor: pointer;"></i>
                  </span>
                </td>



                {{-- <td class="middle">{{$d->src}}</td>  --}}
                <td class="text-center check middle text-center">
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
              {{-- <button type="submit" class="btn btn-info pull-right" style="margin-right: 4%;" name="submit" value="submit">Submit</button> --}}
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


$(".remove_group_id").click(function(){

  // alert('hi');
    var id = $(this).data('id');
    var sl = $(this).data('sl');

    // $('#'+ sl).hide();
    // var user_id = "Volvo";

    $.ajax({
    url: "{{url(LOGIN_USER_TYPE.'/remove_rider_from_group')}}",
    type:"POST",
    data:{
      id:id,
      _token: "{{ csrf_token() }}"
    },
    success:function(response){
      if(response) {
        if(response == '1'){
          $('#'+ sl).hide();
        }else alert(response);
      }
    },
  });
});

</script>
@endpush


