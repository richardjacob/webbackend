@extends('admin.template')

@section('main')
<style> 
    #suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
    #suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
    #suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
    #search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px;}
</style>


 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Manage Statements  <small>Drivers Statements </small>
    </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Manage Statements</li>
      <li><a href="{{ url(LOGIN_USER_TYPE.'/statements/driver') }}">Driver Statements</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
 
          <div class="box">
            <div class="box-header">
              <div class="col-sm-4">
                <h3 class="box-title">Drivers Statements</h3>  
              </div>   
              <div class="col-sm-8 text-right">
                {!! Form::open(['url' => 'admin/statements/driver', 'class' => 'form-horizontal', 'method' => 'GET']) !!}
                  <div class="col-sm-4" style="padding-right:0">
                    <input type="text" id="input_email" class="form-control search-box" placeholder = "Enter Driver Name or Number" >
                    <input type="hidden" id="search_user" name="driver_id">
                    <span class="text-danger">{{ $errors->first('driver_id') }}</span>
                     <div id="suggesstion-box"></div>
                  </div>

                  <div class="col-sm-3" style="padding-right:0">
                    <input type="text" max="{{date('Y-m-d')}}" name="start_date" class="form-control date" placeholder="Start Date" autocomplete="off">
                  </div>

                  <div class="col-sm-3"  style="padding-right:0">
                    <input type="text" max="{{date('Y-m-d')}}" name="end_date" class="form-control date" placeholder="End Date" autocomplete="off">
                  </div>

                  <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                  </button>
                  </div>
                  {!! Form::close() !!}
              </div> 


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
  $('#input_email').on('keyup', function() {
      var keyword = $(this).val();
      if(keyword.length >=3) suggestion(keyword);
  });


  function suggestion(keyword){
    var url = "{{url('admin/monitor_camera_suggestion')}}?keywords="+keyword;

      $.ajax({
        type:'GET',
        url:url,
        beforeSend:function(){
          $("#input_email").css("background","#eee");
        },
        success:function(data){
          $("#suggesstion-box").show();
          $("#suggesstion-box").html(data);
          $("#input_email").css("background","#FFF");
        }        
      });
  }

  function select_from_suggestion(label, val, option){
    $("#input_email").val(label);
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


$( ".date" ).datepicker({
     autoclose: true,
     todayHighlight: true,
     format: 'dd-mm-yyyy' 
});



</script>
<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
{!! $dataTable->scripts() !!}
@endpush

