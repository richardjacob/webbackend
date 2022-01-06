@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="company_management">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Complain <small>Contact List</small></h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Complain</li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/contact_list') }}">Contact List</a></li>
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
                  {!! Form::open(['url' => LOGIN_USER_TYPE.'/contact_list', 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}
                  <div class="row">                    
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-2">
                      <label for="input_complain_by control-label">Contact category</label>
                      {!! Form::select('contact_for', array( 'Passenger' => 'Passenger', 'Driver' => 'Driver', 'Partner' => 'Partner', 'Complain' => 'Complain', 'Other' => 'Other'), @$contact_for ?? '', ['class' => 'form-control', 'id' => 'input_contact_for', 'placeholder' => 'Select']) !!}
                    </div>

                    <div class="col-md-2">
                      <label for="input_contact_name control-label">Name/ Mobile/ Email</label>
                      {!! Form::text('contact_name', @$driver_id, ['class' => 'form-control search-box', 'id' => 'input_contact_name', 'placeholder' => 'Name/ Mobile/ Email', 'autocomplete' => 'off']) !!}
                      <input type="hidden" id="search_contact" name="contact_id" value="{{ @$contact_id }}">
                      <div id="suggesstion-box"></div>
                    </div>
                    
                    <div class="col-md-2">
                      <label for="input_trip control-label">Status</label>
                      {!! Form::select('status', array( '0' => 'Pending', '1' => 'Completed', '2' => 'Processing'), @$status , ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                    </div>

                    <div class="col-md-2">
                        <label for="input_start_date control-label">Start Date</label>
                        <input type="text" name="start_date" value="@if(isset($start_date)){{date('d-m-Y', strtotime(@$start_date))}}@endif"  class="form-control date" placeholder="Start Date" autocomplete="off">
                    </div>
  
                    <div class="col-sm-2">
                        <label for="input_end_date control-label">End Date</label>
                        <input type="text" name="end_date" value="@if(isset($end_date)){{date('d-m-Y', strtotime(@$end_date))}}@endif"  class="form-control date" placeholder="End Date" autocomplete="off">
                    </div>

                    <div class="col-sm-1" style="padding-top:5px !important;"> <br />
                        <button type="submit" class="btn btn-primary form_submit">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div> 
              
                  {!! Form::close() !!}
                </div>
              
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body contact-list">
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
	$('#input_contact_name').on('keyup', function() {
	    var keyword = $(this).val();    
	    if(keyword.length >=3) suggestion_contact(keyword);
        if(keyword == '') $('#search_contact').val('');
	});

  

  function suggestion_contact(keyword){
    var url = "{{url('admin/ajax/suggestion_contact')}}?keywords="+keyword;
    $.ajax({
      type:'GET',
      url:url,
      beforeSend:function(){
        $("#input_contact_name").css("background","#eee");
      },
      success:function(data){
        $("#suggesstion-box").show();
        $("#suggesstion-box").html(data);
        $("#input_contact_name").css("background","#FFF");
      }	       
    });
  }

  function select_from_suggestion_for_contact(label, val){
    $("#suggesstion-box").hide();
    $("#input_contact_name").val(label);
    $("#search_contact").val(val);
  }

  $( ".date" ).datepicker({
     autoclose: true,
     todayHighlight: true,
     format: 'dd-mm-yyyy',
     endDate: '0'
  });
</script>
@endpush
