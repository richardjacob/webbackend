@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="help">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Hub Management <small>Edit Hub</small></h1>
      <ol class="breadcrumb">
          <li><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Hub Management</li>
          <li><a href="{{ url(LOGIN_USER_TYPE.'/manage_hub/') }}">Manage Hub</a></li>
          <li><a href="{{ url(LOGIN_USER_TYPE.'/edit_hub/'.$result->id) }}"> Edit</a></li>

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
              <h3 class="box-title">Hub Edit Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => 'admin/edit_hub/'.$result->id, 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>

                

                <div class="form-group">
                  <label for="input_question" class="col-sm-3 control-label">Hub Name<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('name', $result->name, ['class' => 'form-control', 'id' => 'input_name', 'placeholder' => 'Name']) !!}
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_address" class="col-sm-3 control-label">Address<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('address', $result->address, ['class' => 'form-control', 'id' => 'input_address', 'placeholder' => 'Address']) !!}
                    <span class="text-danger">{{ $errors->first('address') }}</span>
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