@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="driver_management">
  <section class="content-header">
    <h1>Manage Support <small>Edit Support</small></h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ url(LOGIN_USER_TYPE.'/support') }}">Support</a></li>
      <li><a href="{{ url(LOGIN_USER_TYPE.'/edit_support/'.@$result->id) }}">Edit</a></li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12 col-sm-offset-0 ne_ed">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Support Form</h3>
          </div>
          {!! Form::open(['url' => LOGIN_USER_TYPE.'/edit_support/'.$result->id, 'class' => 'form-horizontal','files' => true]) !!}
            <div class="box-body ed_bld">
            <span class="text-danger">(*)Fields are Mandatory</span>
            
            <div class="form-group">
              <label for="input_first_name" class="col-sm-3 control-label">Name<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::text('name', $result->name, ['class' => 'form-control', 'id' => 'input_name', 'placeholder' => 'Name',$editable]) !!}
                <span class="text-danger">{{ $errors->first('name') }}</span>
              </div>
            </div>

            <div class="form-group">
              @if($result->id == 1)
                <label for="input_first_name" class="col-sm-3 control-label">Number<em class="text-danger">*</em></label>
              @else
                <label for="input_first_name" class="col-sm-3 control-label">Link<em class="text-danger">*</em></label>
              @endif
              <div class="col-sm-6">
                {!! Form::text('link', $result->link, ['class' => 'form-control', 'id' => 'input_link', 'placeholder' => 'link']) !!}
                @if($result->id == 1)
                <small>Note* : Please fill it with the country code.(Ex-911234567890).</small>
                @endif
                <span class="text-danger">{{ $errors->first('link') }}</span>
              </div>
            </div>

            <div class="form-group">
              <label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                <span class="text-danger">{{ $errors->first('status') }}</span>
              </div>
            </div>
         
          <div class="form-group">
              <label for="input_image" class="col-sm-3 control-label">Image <em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::file('image', ['class' => 'form-control', 'id' => 'input_image', 'accept' => 'image/*']) !!}
                <span class="text-danger">{{ $errors->first('image') }}</span>
                <br>
                <img style="width:100px" src="{{url('images/support/'.$result->image) }}">
              </div>
            </div>
          </div>
            <div class="box-footer">
            <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
            <button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
          </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
