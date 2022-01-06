@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
 <style> 
  #suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
  #suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
  #suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
  #search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px}
</style>

<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Logs <small>Audit Log</small></h1>
      <ol class="breadcrumb">
         <li>
            <a href="dashboard">
               <i class="fa fa-dashboard"></i> Home
            </a>
         </li>
         <li class="active">Logs</li>
         <li class="active">
            <a href="{{url(LOGIN_USER_TYPE.'/audit_log')}}">Audit Log</a>
         </li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">

            <div class="box-header">
              <div class="col-lg-5"></div>
                  <div class="col-lg-7">
                  {!! Form::open(['url' => LOGIN_USER_TYPE.'/audit_log', 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}

            
                    <div class="col-sm-4">
                      <select name="user_type" id="user_type" class="form-control">
                        <option value="Driver">Driver</option>
                        <option value="Rider">Rider</option>
                        {{-- @foreach($employee_list as $employee)
                        <option value="{{$employee->id}}" @if($employee->id == @$employee_id) selected="" @endif>{{$employee->employee_name}}</option>
                        @endforeach --}}
                      </select>
                    </div>

                    <div class="col-sm-4">
                      <input type="text" name="code" id="keyword" value="{{@$code}}"  class="form-control" placeholder="Search User" autocomplete="off">
                      <input type="hidden" id="search_user" name="search_user" value="">
                      <div id="suggesstion-box"></div>

                    </div>
                    <div class="col-sm-3">
                      {!! Form::select('vehicle_id', array('' => 'Select Vehicle'), '', ['class' => 'form-control', 'id' => 'input_vehicle']) !!}
                    </div>
                    <div class="col-sm-1" style="padding-left: 0px">
                      <button type="submit" class="btn btn-primary form_submit">
                        <i class="fa fa-search"></i> Search
                      </button>
                    </div>
                  </div>
                  {!! Form::close() !!}
               </div>

               <div class="box-header">
                  <h3 class="box-title">Audit Log</h3>
               </div>
               <!-- /.box-header -->
               <div class="box-body">
                  {!! $dataTable->table() !!}
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	$('#keyword').on('keyup', function() {
	    var keyword = $(this).val();
      var user_type = $('#user_type').val();
	    if(keyword.length >=3) suggestion(keyword,user_type);
	});



	function suggestion(keyword,user_type){
		var url = "{{url('admin/ajax/suggestion')}}?keywords="+keyword+"&user_type="+user_type;
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
    $("#search_user").val(val);

    $('#input_vehicle').find('option:not(:first)').remove();
    var option_array = option.split("|");

    for(var i = 0; i<option_array.length; i++){
    	var option_array_data = option_array[i].split("_");

    	$('#input_vehicle').append($('<option>', {
		    value: option_array_data[0],
		    text: option_array_data[1]
		}));
    }

	



  }

</script>

<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush
