@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="company_management">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Complain <small>Complain List</small></h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Complain</li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/complain_list') }}">Complain List</a></li>
      </ol>
    </section>

  <style> 
    #suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
    #suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
    #suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
    #search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px}
  </style>
  

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Filter</h3>

              <div>
                <div class="col-md-12">
                  {!! Form::open(['url' => LOGIN_USER_TYPE.'/complain_list', 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}
                  <div class="row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-2">
                      <label for="input_complain_by control-label">Complain by</label>
                      {!! Form::select('complain_by', array( 'Rider' => 'Rider', 'Driver' => 'Driver'), @$complain_by ?? 'Rider', ['class' => 'form-control', 'id' => 'input_complain_by', 'placeholder' => 'Select']) !!}
                    </div>

                    <div class="col-md-2">
                      <label for="input_driver control-label">Driver</label>
                      {!! Form::text('driver_name', @$driver_id, ['class' => 'form-control search-box', 'id' => 'input_driver', 'data-user_type' => 'Driver', 'placeholder' => 'Enter Driver Name/ Email/ ID', 'autocomplete' => 'off']) !!}
                      <input type="hidden" id="search_driver" name="driver_id" value="{{ @$driver_id }}">
                      <div id="suggesstion-box"></div>
                    </div>

                    <div class="col-md-2">
                      <label for="input_rider control-label">Rider</label>
                      {!! Form::text('rider_name', @$rider_id, ['class' => 'form-control search-box', 'id' => 'input_rider', 'data-user_type' => 'Rider', 'placeholder' => 'Enter Rider Name/ Email/ ID', 'autocomplete' => 'off']) !!}
                      <input type="hidden" id="search_rider" name="rider_id" value="{{ @$rider_id }}">
                      <div id="suggesstion-box"></div>
                    </div>

                    <div class="col-md-2">
                      <label for="input_trip control-label">Trip ID</label>
                      {!! Form::text('trip_id', @$trip_id, ['class' => 'form-control', 'placeholder' => 'Enter Trip ID', 'autocomplete' => 'off', 'onkeypress' => 'validate(event)']) !!}
                    </div>

                    <div class="col-md-2">
                      <label for="input_trip control-label">Status</label>
                      {!! Form::select('status', array( '0' => 'Pending', '1' => 'Completed', '2' => 'Processing'), @$status , ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                    </div>
                    <div class="col-md-1">&nbsp;</div>
                  </div>

                  <div class="row" style="margin-top:10px;">
                    <div class="col-md-1">&nbsp;</div>

                    <div class="col-md-2">
                      <label for="input_cat_id control-label">Category</label>
                      {!! Form::select('cat_id', $cat_list, @$cat_id, ['class' => 'form-control', 'id' => 'input_cat_id', 'placeholder' => 'Select']) !!}
                    </div>

                    <div class="col-md-2">
                      <label for="input_sub_cat_id control-label">Sub Category</label>
                      {!! Form::select('sub_cat_id', $sub_cat_list, @$sub_cat_id, ['class' => 'form-control', 'id' => 'input_sub_cat_id', 'placeholder' => 'Select']) !!}
                    </div>

                    <div class="col-md-2">
                      <label for="input_start_date control-label">Start Date</label>
                      <input type="text" name="start_date" value="@if(isset($start_date)){{date('d-m-Y', strtotime(@$start_date))}}@endif"  class="form-control date" placeholder="Start Date" autocomplete="off">
                    </div>

                    <div class="col-sm-2">
                      <label for="input_end_date control-label">End Date</label>
                      <input type="text" name="end_date" value="@if(isset($end_date)){{date('d-m-Y', strtotime(@$end_date))}}@endif"  class="form-control date" placeholder="End Date" autocomplete="off">
                    </div>
                    
                    <div class="col-sm-2" style="padding-top:5px !important;"> <br />
                      <button type="submit" class="btn btn-primary form_submit">
                        <i class="fa fa-search"></i> Search
                      </button>
                    </div>   
                    <div class="col-md-1">&nbsp;</div>               
                  {!! Form::close() !!}
                </div>
              
                @if(Auth::guard('admin')->user()->can('add_complain'))
                  <div  class="col-md-2 text-right"><a class="btn btn-success" href="{{ url('admin/add_complain') }}">Add Complain</a></div>
                @endif
              
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body companey-list">
{!! $dataTable->table() !!}
</div>
</div>
</div>
</div>
</section>
</div>
@endsection
@push('scripts')
<style type="text/css">
  .company_driver_list {
    width: 300px;
    overflow-wrap: break-word;
  }
</style>
<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}


<script type="text/javascript">
	$('#input_driver').on('keyup', function() {
	    var keyword = $(this).val();
      var user_type = $(this).data('user_type');
      var search_field_id = $(this).attr('id');
      var search_value_id = $(this).closest('input').next().attr('id');    
	    if(keyword.length >=3) suggestion_for_user(user_type, search_field_id, search_value_id, keyword);
      if(keyword == '') $('#'+search_value_id).val('');
	});

  $('#input_rider').on('keyup', function() {
	    var keyword = $(this).val();
      var user_type = $(this).data('user_type');
      var search_field_id = $(this).attr('id');
      var search_value_id = $(this).closest('input').next().attr('id');    
	    if(keyword.length >=3) suggestion_for_user(user_type, search_field_id, search_value_id, keyword);
      if(keyword == '') $('#'+search_value_id).val('');
	});

  $('#input_cat_id').on('change', function() {
	    var cat_id = $(this).val();
      var url = "{{url('admin/ajax/complain_sub_category_option')}}?cat_id="+cat_id;

      $.ajax({
        type:'GET',
        url:url,
        success:function(data){
             $("#input_sub_cat_id").html(data);
        }	       
      });
      
	});

  function suggestion_for_user(user_type, search_field_id, search_value_id,keyword){
    var url = "{{url('admin/ajax/suggestion_for_user')}}?user_type="+user_type+"&search_field_id="+search_field_id+"&search_value_id="+search_value_id+"&keywords="+keyword;
    $.ajax({
      type:'GET',
      url:url,
      beforeSend:function(){
        $("#"+search_field_id).css("background","#eee");
      },
      success:function(data){
        $("#suggesstion-box").show();
        $("#suggesstion-box").html(data);
        $("#"+search_field_id).css("background","#FFF");
      }	       
    });
  }

  function select_from_suggestion_for_user(label, val, search_field_id, search_value_id){
    $("#"+search_field_id).val(label);
    $("#suggesstion-box").hide();
    $("#"+search_value_id).val(val);
  }

  $( ".date" ).datepicker({
     autoclose: true,
     todayHighlight: true,
     format: 'dd-mm-yyyy',
     endDate: '0'
  });
</script>
@endpush
