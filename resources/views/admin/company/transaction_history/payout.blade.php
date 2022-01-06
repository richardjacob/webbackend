@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Payout</h1>  
      <ol class="breadcrumb">
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
        </li>
        @if(LOGIN_USER_TYPE == 'admin')        
        <li class="active">
          <a href="{{ url(LOGIN_USER_TYPE.'/company') }}"> Manage Company </a>
        </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/company/transaction_history/payout/'.$company_id) }}"> Payout</a>
        </li>
        @else        
        <li class="active">
          <a href="{{ url(LOGIN_USER_TYPE.'/transaction_history') }}" style="pointer-events: none;"> Transaction History </a>
        </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/transaction_history/payout') }}"> Payout</a>
        </li>
        @endif
      </ol>
    </section>    


    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
                <div class="col-sm-4">&nbsp;</div>
                @if(LOGIN_USER_TYPE == 'admin')
                  @if($url = LOGIN_USER_TYPE.'/company/transaction_history/payout/'.$company_id) @endif
                @elseif(LOGIN_USER_TYPE == 'company')
                  @if($url = LOGIN_USER_TYPE.'/transaction_history/payout') @endif
                @endif

                {!! Form::open(['url' => $url, 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}
                
                <!-- <div class="col-sm-2">
                  <input type="text" name="driver_id" value="{{@$driver_id}}"  class="form-control" placeholder="Driver ID" autocomplete="off">
                </div> -->

                <div class="col-sm-2">
                  <input type="text" name="start_date" value="@if(isset($start_date)){{date('d-m-Y', strtotime(@$start_date))}}@endif"  class="form-control date" placeholder="Start Date" autocomplete="off">
                </div>

                <div class="col-sm-2">
                  <input type="text" name="end_date" value="@if(isset($end_date)){{date('d-m-Y', strtotime(@$end_date))}}@endif"  class="form-control date" placeholder="End Date" autocomplete="off">
                </div>
                <div class="col-sm-2">
                  <button type="submit" class="btn btn-primary form_submit">
                    <i class="fa fa-search"></i> Search
                  </button>
                </div>  
                <div class="col-sm-4">&nbsp;</div>                
                {!! Form::close() !!}
            </div>
            <!-- /.box-header -->


            <div class="box-body companey-list">{!! $dataTable->table() !!}</div>
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
</script>

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
@endpush
