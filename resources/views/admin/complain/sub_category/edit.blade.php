@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Complain <small>Edit Sub Category</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Complain</li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/complain_sub_category') }}">Sub Category</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/edit_complain_sub_category/'.@$result->id) }}">Edit</a></li>
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
              <h3 class="box-title">Edit Sub Category Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => 'admin/edit_complain_sub_category/'.$result->id, 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>

                <div class="form-group">
                  <label for="input_complain_cat_id" class="col-sm-3 control-label">Category<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::select('complain_cat_id', $cat_list, $result->complain_cat_id, ['class' => 'form-control', 'id' => 'input_complain_cat_id', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('complain_cat_id') }}</span>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="input_sub_category" class="col-sm-3 control-label">Sub Category Name in English<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('sub_category', $result->sub_category, ['class' => 'form-control', 'id' => 'input_sub_category', 'placeholder' => 'Sub Category Name in English']) !!}
                    <span class="text-danger">{{ $errors->first('sub_category') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_sub_category_bn" class="col-sm-3 control-label">Sub Category Name in Bangla<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('sub_category_bn', $result->sub_category_bn, ['class' => 'form-control', 'id' => 'input_sub_category_bn', 'placeholder' => 'Sub Category Name in Bangla']) !!}
                    <span class="text-danger">{{ $errors->first('sub_category_bn') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::select('status', array( '1' => 'Active', '0' => 'Inactive'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
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