@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
       <h1>
        Manage Admin
        <small>Edit Role</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Manage Admin</li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/roles') }}">Roles & Permissions</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/edit_role/'.$result->id) }}">Edit</a></li>

      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12 col-sm-offset-0">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Role Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => 'admin/edit_role/'.$result->id, 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="input_name" class="col-sm-2 control-label">Name<em class="text-danger">*</em></label>

                  <div class="col-sm-10">
                    {!! Form::text('name', $result->name, ['class' => 'form-control', 'id' => 'input_name', 'placeholder' => 'Name']) !!}
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_display_name" class="col-sm-2 control-label">Display Name<em class="text-danger">*</em></label>
                  <div class="col-sm-10">
                    {!! Form::text('display_name', $result->display_name, ['class' => 'form-control', 'id' => 'input_display_name', 'placeholder' => 'Display Name']) !!}
                    <span class="text-danger">{{ $errors->first('display_name') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_description" class="col-sm-2 control-label">Description<em class="text-danger">*</em></label>
                  <div class="col-sm-10">
                    {!! Form::textarea('description', $result->description, ['class' => 'form-control', 'id' => 'input_description', 'placeholder' => 'Description', 'rows' => 3]) !!}
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label">Permissions</label>
                  <div class="col-sm-10">
                  <ul style="display: inline-block;list-style-type: none;padding:0; margin:0;">
                     @if ($errors->has('permission'))
                        <span class="text-danger">
                            {{ $errors->first('permission') }}
                        </span>
                        @foreach($permissions as $row)
                          <li class="checkbox" style="display: inline-block; min-width: 180px;">
                            <label>
                              <input type="checkbox" class="permission_check" name="permission[]" value="{{ $row->id }}"> 
                              {{ $row->display_name }}
                            </label>                      
                          </li>                    
                        @endforeach
                    @else 
                      @if($menu = "") @endif
                      @foreach($permissions as $row)
                        @if($menu !=  $row->menu_name)
                          <div class="text-bold" style="padding-top:15px;">{{ucwords(str_replace('_',' ',$row->menu_name))}}</div>
                        @endif
                        <li class="checkbox" style="display: inline-block; min-width: 180px;">
                          <label>
                            <input type="checkbox" class="permission_check" name="permission[]" value="{{ $row->id }}" {{ in_array($row->id, $stored_permissions) ? 'checked' : '' }}> 
                            {{ $row->display_name }}
                          </label>
                        </li>
                        @if($menu = $row->menu_name) @endif
                      @endforeach
                    @endif
                  </ul>
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
function permission_changes()
{
  if(!$('input[value="3"]').is(":checked")  && !$('input[value="4"]').is(":checked") && !$('input[value="5"]').is(":checked")){
    $("input[value='2']").removeAttr("disabled");
  }
  else{
    $("input[value='2']").prop('checked', true).attr("disabled","disabled");
  }
  if(!$('input[value="19"]').is(":checked") && !$('input[value="20"]').is(":checked") && !$('input[value="21"]').is(":checked")){
    $("input[value='18']").removeAttr("disabled");
  }
  else{
    $("input[value='18']").prop('checked', true).attr("disabled","disabled");
  }
  if(!$('input[value="42"]').is(":checked") && !$('input[value="43"]').is(":checked") && !$('input[value="44"]').is(":checked")){
    $("input[value='41']").removeAttr("disabled");
  }
  else{
    $("input[value='41']").prop('checked', true).attr("disabled","disabled");
  }
}
$('.permission_check').click(function(){
  // permission_changes();
});
// permission_changes();
</script>
@endpush