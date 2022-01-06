@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Manage Payouts <small>
    @if(@$driver_balance !='')
      Driver Balance Payouts
    @else
      Driver Payouts
    @endif</small></h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Manage Payout</li>
      
      @if(@$driver_balance !='')
      <li>
        <a href="{{ url('admin/payout/driver_balance') }}"> Driver Balance Payouts</a>
      </li>
      
      @else
        <li>
          <a href="{{ url('admin/payout/overall') }}"> Driver Payouts</a>
        </li>

        @if(@$type !='' AND @$id !='')
        <li>
          <a href="{{ url('admin/'.@$type.'/'.@$id) }}"> Weekly Payouts</a>
        </li>
        @endif

        @if(@$per_day !='')
        <li>
          <a href="{{@$request_url}}"> {{@$per_day}}</a>
        </li>
        @endif

        @if(@$per_week !='')
        <li>
          <a href="{{@$request_url}}"> {{@$per_week}}</a>
        </li>
        @endif
      @endif

     

      

    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            {{-- <h3 class="box-title">Manage Driver {{ $sub_title }}</h3> --}}
            <h3 class="box-title">Driver Balance Payouts</h3>
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
  <link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
  <script src="{{ url('js/dataTables.buttons.js') }}"></script>
  <script src="{{ url('js/buttons.server-side.js') }}"></script>
  {!! $dataTable->scripts() !!}
@endpush

