@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="company_management">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Manage Payout Preference</h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"><a href="{{ url(LOGIN_USER_TYPE.'/payout_preference') }}">Payout Preference</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Payout Preference</h3>
                <div style="float:right;"><a class="btn btn-success" href="{{ url('company/add_payout_preference') }}">Add Payout Preference</a></div>
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
@endpush
