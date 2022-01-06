@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Manage Fare <small>Manage Fare</small></h1>
    <ol class="breadcrumb">
      <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Manage Fare</li>
      <li><a href="{{ url('admin/manage_fare') }}"> Manage Fare</a></li>
      
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Manage Fare Management</h3>
            <div style="float:right;">
              <a class="btn btn-success" href="{{ url('admin/add_manage_fare') }}">Add Fare</a>
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
  <link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
  <script src="{{ url('js/dataTables.buttons.js') }}"></script>
  <script src="{{ url('js/buttons.server-side.js') }}"></script>
  {!! $dataTable->scripts() !!}
@endpush