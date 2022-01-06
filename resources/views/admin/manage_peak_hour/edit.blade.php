@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Manage Fare <small>Edit Peak Hour</small></h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a>
      </li>
      <li>
        <a>Manage fare</a>
      </li>
      <li>
        <a href="{{ url('admin/manage_peak_hour') }}"> Manage Peak Hour</a>
      </li>
      <li>
        <a href="{{ url('admin/edit_peak_hour/'.$result->id) }}">Edit</a>
      </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content" ng-controller='manage_peak_fare'>
    <div class="row" ng-cloak>
      <!-- right column -->
      <div class="col-md-12 col-sm-offset-0">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Peak Hour</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          {!! Form::open(['url' => 'admin/edit_peak_hour/'.$result->id, 'class' => 'form-horizontal','id' => 'edit_peak_hour']) !!}
          <div class="box-body">
            <span class="text-danger">(*)Fields are Mandatory</span>
            <div class="form-group">
              <label for="day_name" class="col-sm-4 control-label">
                Day Name
              </label>
              <div class="col-sm-6" style="padding-top:5px;">{{$result->day_name}}</div>
            </div>
            <div class="form-group">
              <label for="type" class="col-sm-4 control-label">
                Type
              </label>
              <div class="col-sm-6" style="padding-top:5px;">
                @if($result->type == '1') Morning
                @else Evening
                @endif
                </div>
            </div>


            <div class="form-group">
              <label for="input_start_time" class="col-sm-4 control-label">
                Start Time <em class="text-danger">*</em>
              </label>
              <div class="col-sm-6" ng-init="start_time='{{ old('start_time','') }}'">
                {!! Form::select('start_time', $times, $result->start_time, ['class' => 'form-control', 'id' => 'input_start_time','placeholder' => 'Start Time']) !!}
                <span class="text-danger">{{ $errors->first('start_time') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_end_time" class="col-sm-4 control-label">
                End Time <em class="text-danger">*</em>
              </label>
              <div class="col-sm-6" ng-init="end_time='{{ old('end_time','') }}'">
                {!! Form::select('end_time', $times, $result->end_time, ['class' => 'form-control', 'id' => 'input_end_time','placeholder' => 'End Time']) !!}
                <span class="text-danger">{{ $errors->first('end_time') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_status" class="col-sm-4 control-label">
                Status <em class="text-danger">*</em>
              </label>
              <div class="col-sm-6" ng-init="status='{{ old('status','') }}'">
                {!! Form::select('status', $status, $result->status, ['class' => 'form-control', 'id' => 'input_status','placeholder' => 'Select Status']) !!}
                <span class="text-danger">{{ $errors->first('status') }}</span>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <button type="submit" class="btn btn-info pull-right manage_fare_submit" name="submit" value="submit">Submit</button>
            <a href="{{ url('admin/manage_peak_hour') }}" class="btn btn-default pull-left" name="cancel" value="Cancel">
              Cancel
            </a>
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