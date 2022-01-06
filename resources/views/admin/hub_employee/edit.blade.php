@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="hub_employee">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Hub Management <small>Edit Hub Employee</small></h1>
      <ol class="breadcrumb">
          <li><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Hub Management</li>
          <li><a href="{{ url(LOGIN_USER_TYPE.'/manage_employee/') }}">Manage Employee</a></li>
          <li><a href="{{ url(LOGIN_USER_TYPE.'/edit_hub_employee/'.$result->id) }}"> Edit</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Hub Employee Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => 'admin/edit_hub_employee/'.$result->id, 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>


                <div class="form-group">
                    <label for="input_employee_name" class="col-sm-3 control-label">Employee Name<em class="text-danger">*</em></label>
                    <div class="col-sm-6">
                      {!! Form::text('employee_name', $result->employee_name, ['class' => 'form-control', 'id' => 'input_employee_name', 'placeholder' => 'Employee Name']) !!}
                      <span class="text-danger">{{ $errors->first('employee_name') }}</span>
                    </div>
                  </div>

                <div class="form-group">
                    <label for="input_email" class="col-sm-3 control-label">Email<em class="text-danger">*</em></label>
                    <div class="col-sm-6">
                      {!! Form::text('email', $result->email, ['class' => 'form-control', 'id' => 'input_email', 'placeholder' => 'email']) !!}
                      <span class="text-danger">{{ $errors->first('email') }}</span>
                    </div>
                  </div>


                <div class="form-group">
              <label for="input_hub_name" class="col-sm-3 control-label">Hub Name<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::select('hub_id', $hub, $result->hub_id, ['class' => 'form-control', 'id' => 'input_hub_id', 'placeholder' =>'Select', 'ng-change' => 'change_hub(hub_id)', 'ng-model' => 'hub_name']) !!}
                <span class="text-danger">{{ $errors->first('hub_id') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_password" class="col-sm-3 control-label">Password</label>
              <div class="col-sm-6">
                {!! Form::password('password',array ('class' => 'form-control', 'id' => 'input_password', 'placeholder' => 'Password')) !!}
                <span class="text-danger">{{ $errors->first('password') }}</span>
              </div>
            </div>
            
            {{-- <div class="col-sm-6">
                {!! Form::select('company_name_view', $company, $result->company_id, ['class' => 'form-control', 'id' => 'input_company_name', 'placeholder' => 'Select','disabled']) !!}
                {!! Form::hidden('company_name', $result->company_id) !!}
                <span class="text-danger">{{ $errors->first('company_name') }}</span>
              </div> --}}

              <div class="form-group">
                  <label for="input_role" class="col-sm-3 control-label">Role<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::select('role_id', $role->pluck('name', 'id'), $result->role_id, ['class' => 'form-control', 'id' => 'input_category_id', 'placeholder' => 'Select', 'ng-change' => 'change_role(role_id)', 'ng-model' => 'role_id', 'ng-init' => 'role_id = '.$result->role_id]) !!}
                     <span class="text-danger">{{ $errors->first('role') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_employee_name" class="col-sm-3 control-label">Mobile Number<em class="text-danger">*</em></label>
                  <div class="col-sm-6">

                   {!! Form::text('mobile_number', $result->mobile_number, ['class' => 'form-control', 'id' => 'input_mobile_number', 'placeholder' => 'Mobile Number']) !!}

                    <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
                 <button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
              </div>
              <!-- /.box-footer -->
            {!! Form::close() !!}
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
@stop

@push('scripts')
<script type="text/javascript">
$("#txtEditor").Editor(); 
$('.Editor-editor').html($('#answer').val());
</script>
@endpush