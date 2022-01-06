@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Contact <small>Add Movement</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Complain</li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/contact_list') }}">Contact List</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/add_contact_status/'.$result->id) }}">Add Movement</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-6">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Add Resolve Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => 'admin/movement_contact/'.$result->id, 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
              
              <div class="form-group">
                <label for="input_complain" class="col-sm-3 control-label">Contact</label>
                <div class="col-sm-9" style="margin-top:8px;">{{ $result->complain_content }}</div>
              </div>

                <div class="form-group">
                  <label for="input_process_by" class="col-sm-3 control-label">Resolve/ Processing by<em class="text-danger">*</em></label>
                  <div class="col-sm-9">
                    {!! Form::text('process_by', '', ['class' => 'form-control', 'id' => 'input_process_by', 'placeholder' => 'Resolve/ Processing By']) !!}
                    <span class="text-danger">{{ $errors->first('process_by') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_process" class="col-sm-3 control-label">Processing in Details<em class="text-danger">*</em></label>
                  <div class="col-sm-9">
                    {!! Form::textarea('process', '', ['class' => 'form-control', 'rows' => '6', 'id' => 'input_process', 'placeholder' => 'Processing in Details']) !!}
                    <span class="text-danger">{{ $errors->first('process') }}</span>
                  </div>
                </div>

                <div class="form-group">
                    <label for="input_remarks" class="col-sm-3 control-label">Remarks<em class="text-danger">*</em></label>
                    <div class="col-sm-9">
                      {!! Form::textarea('remarks', '', ['class' => 'form-control', 'rows' => '6', 'id' => 'input_remarks', 'placeholder' => 'Remarks']) !!}
                      <span class="text-danger">{{ $errors->first('remarks') }}</span>
                    </div>
                  </div>

                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
                  <div class="col-sm-9">
                    {!! Form::select('status', array( '1' => 'Completed', '2' => 'Processing'), '', ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
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

        <div class="col-md-6">
            <!-- Horizontal Form -->
            <div class="box box-success">
              <div class="box-header with-border">
                
                  <div class="col-sm-11"> 
                    <h3 class="box-title">Last Processing  </h3>
                  </div> 
                  <div class="col-sm-1 text-right">   
                    @if(auth('admin')->user()->can('tracking_movement_contact'))             
                    <a href="{{ url('admin/tracking_movement_contact'.$result->id)}}" class="btn btn-xs btn-success" title="Tracking">
                      <i class="glyphicon glyphicon-flash"></i>
                    </a>
                    @endif
                  </div>
                
              </div>
              <!-- /.box-header -->
              <!-- form start -->
            @if(is_object($last_record))
                {!! Form::open(['url' => 'admin/edit_movement_contact', 'class' => 'form-horizontal']) !!}
                <div class="box-body">
                <span class="text-danger">(*)Fields are Mandatory</span>
                <input type="hidden" name="contact_movement_id" value="{{ $last_record->id }}">
              
                <div class="form-group">
                  <label for="input_complain" class="col-sm-3 control-label">Complain</label>
                  <div class="col-sm-9" style="margin-top:8px;">{{ $result->complain_content }}</div>
                </div>
  
                  <div class="form-group">
                    <label for="input_process_by" class="col-sm-3 control-label">Resolve/ Processing by<em class="text-danger">*</em></label>
                    <div class="col-sm-9">
                      {!! Form::text('process_by', $last_record->process_by, ['class' => 'form-control', 'id' => 'input_process_by', 'placeholder' => 'Resolve/ Processing By']) !!}
                      <span class="text-danger">{{ $errors->first('process_by') }}</span>
                    </div>
                  </div>
  
                  <div class="form-group">
                    <label for="input_process" class="col-sm-3 control-label">Processing in Details<em class="text-danger">*</em></label>
                    <div class="col-sm-9">
                      {!! Form::textarea('process', $last_record->process, ['class' => 'form-control', 'rows' => '6', 'id' => 'input_process', 'placeholder' => 'Processing in Details']) !!}
                      <span class="text-danger">{{ $errors->first('process') }}</span>
                    </div>
                  </div>
  
                  <div class="form-group">
                      <label for="input_remarks" class="col-sm-3 control-label">Remarks<em class="text-danger">*</em></label>
                      <div class="col-sm-9">
                        {!! Form::textarea('remarks', $last_record->remarks, ['class' => 'form-control', 'rows' => '6', 'id' => 'input_remarks', 'placeholder' => 'Remarks']) !!}
                        <span class="text-danger">{{ $errors->first('remarks') }}</span>
                      </div>
                    </div>
  
                  <div class="form-group">
                    <label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>
                    <div class="col-sm-9">
                      {!! Form::select('status', array( '1' => 'Completed', '2' => 'Processing'), $last_record->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                      <span class="text-danger">{{ $errors->first('status') }}</span>
                    </div>
                  </div>

                <div class="form-group">
                    <label for="entry_by" class="col-sm-2 control-label">Created By :</label>
                    <div class="col-sm-4" style="margin-top:8px;">{{ admin_user($last_record->entry_by_id) }}</div>
                    
                    <label for="created_at" class="col-sm-2 control-label">Created at :</label>
                    <div class="col-sm-4" style="margin-top:8px;">{{ date("d-m-Y, h:i a", strtotime($last_record->created_at)) }}</div>
                </div> 
               
                @if(strtotime($last_record->created_at) != strtotime($last_record->updated_at))
                <div class="form-group">
                    <label for="entry_by" class="col-sm-2 control-label">Updated By :</label>
                    <div class="col-sm-4" style="margin-top:8px;">{{ admin_user($last_record->updated_by_id) }}</div>

                    <label for="updated_at" class="col-sm-2 control-label">Updated at :</label>
                    <div class="col-sm-4" style="margin-top:8px;">{{ date("d-m-Y, h:i a", strtotime($last_record->updated_at)) }}</div>
                </div>
                @endif

  
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  <button type="submit" class="btn btn-success pull-right" name="submit" value="update">Update</button>
                </div>
                <!-- /.box-footer -->
              {!! Form::close() !!}
            @endif
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