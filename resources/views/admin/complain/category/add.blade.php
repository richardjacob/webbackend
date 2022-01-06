@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Complain <small>Add Category</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Complain</li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/complain_category') }}">Category</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/add_complain_category') }}">Add</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12 col-md-offset-0">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Add Category Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => 'admin/add_complain_category', 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>

                <div class="form-group">
                  <label for="input_category" class="col-sm-3 control-label">Category Name in English<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('category', '', ['class' => 'form-control', 'id' => 'input_category', 'placeholder' => 'Category Name in English']) !!}
                    <span class="text-danger">{{ $errors->first('category') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_category_bn" class="col-sm-3 control-label">Category Name in Bangla<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('category_bn', '', ['class' => 'form-control', 'id' => 'input_category_bn', 'placeholder' => 'Category Name in Bangla']) !!}
                    <span class="text-danger">{{ $errors->first('category_bn') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::select('status', array( '1' => 'Active', '0' => 'Inactive'), '', ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                  </div>
                </div>

              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
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