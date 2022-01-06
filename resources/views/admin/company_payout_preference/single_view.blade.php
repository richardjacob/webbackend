@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" ng-controller="company_management" ng-init="login_user_type = '{{ LOGIN_USER_TYPE }}'; company_doc=''; errors = {{ json_encode($errors->getMessages()) }};">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Payout Preference
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ url(LOGIN_USER_TYPE.'/payout_preference') }}">Payout Preference</a></li>
      <li class="active"><a href="{{ url(LOGIN_USER_TYPE.'/view_payout_preference/'.$data->id) }}">View</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!-- right column -->
      <div class="col-md-8 col-sm-offset-2 ne_ed">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Payout Preference</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <div class="box-body ed_bld">
            <table class="table table-bordered">
                <tr>
                    <td class="col-sm-3">Payout Method</td>
                    <td class="col-sm-9">
                        @if($data->payout_method == "banktransfer") Bank Transfer
                        @else {{ucwords($data->payout_method)}}
                        @endif 
                    </td>
                </tr>
                <tr>
                    <td>Account Number</td>
                    <td>{{$data->account_number}}</td>
                </tr>
                @if($data->payout_method == "banktransfer")
                <tr>
                    <td>Account Holder Name</td>
                    <td>{{$data->holder_name}}</td>
                </tr>
                <tr>
                    <td>Account Type</td>
                    <td>{{$data->account_type}}</td>
                </tr>
                <tr>
                    <td>Holder Type</td>
                    <td>{{$data->holder_type}}</td>
                </tr>
                <tr>
                    <td>Bank Name</td>
                    <td>{{$data->bank_name}}</td>
                </tr>
                <tr>
                    <td>Branch Name</td>
                    <td>{{$data->branch_name}}</td>
                </tr>
                <tr>
                    <td>Routing Number</td>
                    <td>{{$data->routing_number}}</td>
                </tr>
                @endif
                <tr>
                    <td>Default</td>
                    <td>
                        @if($data->default == "yes") <i class="fa fa-check text-success" aria-hidden="true"></i>
                        @else <i class="fa fa-times text-danger" aria-hidden="true"></i>
                        @endif
                    </td>
                </tr>
            </table>
            
            
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!--/.col (right) -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
  
</div>
<!-- /.content-wrapper -->
@endsection