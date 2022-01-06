@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Payout Preference
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><a href="{{ url(LOGIN_USER_TYPE.'/payout_preference') }}">Payout Preference</a></li>
      <li class="active"><a href="{{ url(LOGIN_USER_TYPE.'/edit_payout_preference/'.$result->id) }}">Edit</a></li>
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
            <h3 class="box-title">Update Payout Preference</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          {!! Form::open(['url'=>'company/edit_payout_preference/'.$result->id, 'class'=>'form-horizontal', 'files'=>true, 'id'=>'edit_payout_preference']) !!}
          <div class="box-body ed_bld">
            @if($errors->any())          
              <h4 class="alert alert-danger">{{$errors->first()}}</h4>
            @endif
            
            <span class="text-danger">(*)Fields are Mandatory</span>
            <div class="form-group">
              <label for="input_payout_method" class="col-sm-3 control-label">Payout Method <em class="text-danger">*</em></label>
              <div class="col-sm-6">
                <select class='form-control' id='payout_method' name='payout_method'>
                  <option value=""> Select </option>
                  @foreach($payment_gateway as $pg)
                  <option value="{{@$pg}}" @if($pg == $result->payout_method) Selected='Selected' @endif>
                    @if($pg == 'banktransfer') Bank Transfer
                    @else {{ucwords($pg)}}
                    @endif
                  </option>
                  @endforeach
                </select>
                <span class="text-danger">{{ $errors->first('payout_method') }}</span>
              </div>
            </div>
            
            <div id="mb">
              <div class="form-group">
                <label for="input_account_number_mb" class="col-sm-3 control-label">Mobile Banking Account Number <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                  {!! Form::text('account_number_mb', old('account_number_mb') ?? $result->account_number, ['class' => 'form-control', 'id' => 'input_account_number_mb', 'placeholder' => 'Account Number']) !!}
                  <span class="text-danger">{{ $errors->first('account_number_mb') }}</span>
                </div>
              </div>

              <div class="form-group">
                <label for="input_default" class="col-sm-3 control-label">Default <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                    @foreach($defaults as $key => $default)
                    <label class="col-sm-6" style="padding-top:8px;">
                      <input type="radio" name="default_mb" value="{{$key}}" @if($key == $result->default) checked="checked" @endif> {{$default}}
                    </label>
                    @endforeach
                  </select>
                  <span class="text-danger">{{ $errors->first('default') }}</span>
                </div>
              </div>

            </div>

            <div id="banktransfer">
              <div class="form-group">
                <label for="input_account_number" class="col-sm-3 control-label">Account Number <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                  {!! Form::text('account_number', old('account_number') ?? $result->account_number, ['class' => 'form-control', 'id' => 'input_account_number', 'placeholder' => 'Account Number']) !!}
                  <span class="text-danger">{{ $errors->first('account_number') }}</span>
                </div>
              </div>
             
              <div class="form-group">
                <label for="input_holder_name" class="col-sm-3 control-label">Account Holder Name <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                  {!! Form::text('holder_name', old('holder_name') ?? $result->holder_name, ['class' => 'form-control', 'id' => 'input_holder_name', 'placeholder' => 'Account Holder Name']) !!}
                  <span class="text-danger">{{ $errors->first('holder_name') }}</span>
                </div>
              </div>

              <div class="form-group">
                <label for="input_account_type" class="col-sm-3 control-label">Account Type <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                  <select class='form-control' id='account_type' name='account_type'>
                    <option value=""> Select </option>
                    @foreach($account_type as $at)
                    <option value="{{@$at}}" @if($at == $result->account_type) Selected='Selected' @endif >{{$at}}</option>
                    @endforeach
                    {!! Form::hidden('country_id', old('country_id'), array('id'=>'country_id')) !!}
                  </select>
                  <span class="text-danger">{{ $errors->first('account_type') }}</span>
                </div>
              </div>
              
              <div class="form-group">
                <label for="input_holder_type" class="col-sm-3 control-label">Holder Type <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                  <select class='form-control' id='holder_type' name='holder_type'>
                    <option value=""> Select </option>
                    @foreach($holder_type as $ht)
                    <option value="{{@$ht}}" @if($ht == $result->holder_type) Selected='Selected' @endif >{{$ht}}</option>
                    @endforeach
                    {!! Form::hidden('country_id', old('country_id'), array('id'=>'country_id')) !!}
                  </select>
                  <span class="text-danger">{{ $errors->first('holder_type') }}</span>
                </div>
              </div>
              
              <div class="form-group">
                <label for="input_bank_name" class="col-sm-3 control-label">Bank Name <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                  {!! Form::text('bank_name', old('bank_name') ?? $result->bank_name, ['class' => 'form-control', 'id' => 'input_bank_name', 'placeholder' => 'Bank Name']) !!}
                  <span class="text-danger">{{ $errors->first('bank_name') }}</span>
                </div>
              </div>
             
              <div class="form-group">
                <label for="input_branch_name" class="col-sm-3 control-label">Branch Name <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                  {!! Form::text('branch_name', old('branch_name') ?? $result->branch_name, ['class' => 'form-control', 'id' => 'input_branch_name', 'placeholder' => 'Branch Name']) !!}
                  <span class="text-danger">{{ $errors->first('branch_name') }}</span>
                </div>
              </div>
              
              <div class="form-group">
                <label for="input_routing_number" class="col-sm-3 control-label">Routing Number</label>
                <div class="col-sm-6">
                  {!! Form::text('routing_number', old('routing_number') ?? $result->routing_number, ['class' => 'form-control', 'id' => 'input_routing_number', 'placeholder' => 'Routing Number']) !!}
                  <span class="text-danger">{{ $errors->first('routing_number') }}</span>
                </div>
              </div>
              
              <div class="form-group">
                <label for="input_default" class="col-sm-3 control-label">Default <em class="text-danger">*</em></label>
                <div class="col-sm-6">
                    @foreach($defaults as $key => $default)
                    <label class="col-sm-6" style="padding-top:8px;">
                      <input type="radio" name="default" value="{{@$key}}" @if($key == $result->default) checked="checked" @endif > {{$default}}
                    </label>
                    @endforeach
                  </select>
                  <span class="text-danger">{{ $errors->first('default') }}</span>
                </div>
              </div>

            </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right" name="submit" value="Update">Update</button>
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
@endsection
@push('scripts')
<script>
  @if($result->payout_method != '')
    @if($result->payout_method =='banktransfer')
      $("#banktransfer").show();
      $("#mb").hide();
    @else
      $("#banktransfer").hide();
      $("#mb").show();
    @endif
  @else 
    $("#banktransfer").hide();
    $("#mb").hide();
  @endif


  
  $("#payout_method").change(function(){
    var payout_method =  $( "#payout_method option:selected" ).val();
    if(payout_method == 'banktransfer'){
      $("#mb").hide();
      $("#banktransfer").show();
    }else{
      $("#mb").show();
      $("#banktransfer").hide();
    }
  });


  var datepicker_format = 'dd-mm-yy';
  $('#license_exp_date').datepicker({ 'dateFormat': datepicker_format, maxDate: new Date()});
  $(function () {
    $("#yearDate").datepicker({
      changeMonth: true,
      changeYear: true,
      yearRange: '1950:' + new Date().getFullYear().toString(),
      dateFormat: datepicker_format,
    });
    $('.ui-datepicker').addClass('notranslate');
  });
  $('#insurance_exp_date').datepicker({ 'dateFormat': datepicker_format, maxDate: new Date()});
  $(function () {
    $("#yearDate").datepicker({
      changeMonth: true,
      changeYear: true,
      yearRange: '1950:' + new Date().getFullYear().toString(),
      dateFormat: datepicker_format,
    });
    $('.ui-datepicker').addClass('notranslate');
  });
</script>
@endpush
