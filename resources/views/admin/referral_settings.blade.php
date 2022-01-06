@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<link rel="stylesheet" href="{{ url('admin_assets/plugins/datetimepicker/jquery.datetimepicker.css') }}">
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header" style="border-bottom:1px solid #ccc;margin-bottom:10px;">
    <h1> {{ $main_title }} </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ url('admin/referral_settings') }}"> {{ $main_title }} </a></li>
    </ol>
  </section>
<?php /*
  <!-- Driver Signup Bonus Previous-1  -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"> Driver Signup Bonus</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        
        @if($user_type = 'DriverSignupBonus_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}

        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_signup['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_signup['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_signup['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$driver_signup['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div>


          
          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'currency_code',$driver_signup['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Bonus Amount
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $driver_signup['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Bonus Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_signup['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_signup['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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
  */ ?>

  <!-- Driver Joining Bonus  -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"> Driver Joining Bonus (Now)</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        
        @if($user_type = 'DriverJoiningBonus_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}

        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_joining_bonus['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_joining_bonus['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_joining_bonus['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$driver_joining_bonus['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'number_of_trips') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'number_of_days'}}" class="col-sm-4 control-label">Within Days *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_days', old($user_type.'number_of_days',$driver_joining_bonus['number_of_days']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_days', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'number_of_days') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'payment_after_days'}}" class="col-sm-4 control-label">Payment After Days *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'payment_after_days', old($user_type.'payment_after_days',$driver_joining_bonus['payment_after_days']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_days', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'payment_after_days') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'allow_same_user'}}" class="col-sm-4 control-label">Allow Same User *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'allow_same_user', $yes_no, old($user_type.'allow_same_user', $driver_joining_bonus['allow_same_user']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'allow_same_user']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'allow_same_user') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'trip_distance'}}" class="col-sm-4 control-label">Trip Distance *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'trip_distance', old($user_type.'trip_distance',$driver_joining_bonus['trip_distance']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'trip_distance', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'trip_distance') }}</span>
            </div>
          </div>


          
          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'currency_code',$driver_joining_bonus['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Bonus Amount
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $driver_joining_bonus['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Bonus Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_joining_bonus['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_joining_bonus['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first(@$user_type.'terms_condition') }}</span>
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


  
  
 <?php /*
  <!-- Driver Referral Settings -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Driver Referral Bonus</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'Driver_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}
        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Referral *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_referral['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_referral['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_referral['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$driver_referral['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div> 

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'driver_currency',$driver_referral['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Amount for Trips
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $driver_referral['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Amount for Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'who_get_bonus'}}" class="col-sm-4 control-label">Who Get Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'who_get_bonus', $beneficiary, old($user_type.'who_get_bonus',$driver_referral['who_get_bonus']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'who_get_bonus']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'who_get_bonus') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_referral['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_referral['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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

  */ ?>



  <!-- DriverReferralBonus -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Driver Referral Bonus</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'DriverReferralBonus_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}
        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Referral *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_referral_bonus['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_referral_bonus['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_referral_bonus['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$driver_referral_bonus['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div> 

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_days'}}" class="col-sm-4 control-label">Within Days *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_days', old($user_type.'number_of_days',$driver_joining_bonus['number_of_days']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_days', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_days') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'payment_after_days'}}" class="col-sm-4 control-label">Payment After Days *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'payment_after_days', old($user_type.'payment_after_days',$driver_joining_bonus['payment_after_days']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_days', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'payment_after_days') }}</span>
            </div>
          </div>

          

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'driver_currency',$driver_referral_bonus['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Amount for Trips
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $driver_referral_bonus['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Amount for Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'who_get_bonus'}}" class="col-sm-4 control-label">Who Get Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'who_get_bonus', $who_get_bonus_array, old($user_type.'who_get_bonus',$driver_referral_bonus['who_get_bonus']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'who_get_bonus']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'who_get_bonus') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_referral_bonus['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_referral_bonus['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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


  <!-- Driver Trip Bonus -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"> Driver Weekly (Trip) Bonus</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'DriverTripBonus_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}

        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_trip_bonus['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_trip_bonus['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_trip_bonus['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label" title="Lowest to Highest" style="padding-top:20px;">First Stage *</label>
            <div class="col-sm-2">  
              Minimum Trips           
              {!! Form::text($user_type.'first_stage_min_trips', old($user_type.'first_stage_min_trips',$driver_trip_bonus['first_stage_min_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'first_stage_min_trips', 'placeholder' => 'Minimum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>

            <div class="col-sm-2">  
              Maximum Trips           
              {!! Form::text($user_type.'first_stage_max_trips', old($user_type.'first_stage_max_trips',$driver_trip_bonus['first_stage_max_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'first_stage_max_trips', 'placeholder' => 'Maximum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>

            <div class="col-sm-1">  
              Amount         
              {!! Form::text($user_type.'first_stage_amount', old($user_type.'first_stage_amount',$driver_trip_bonus['first_stage_amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'first_stage_amount', 'placeholder' => 'Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label" title="Lowest to Highest" style="padding-top:20px;">Second Stage *</label>
            <div class="col-sm-2">  
              Minimum Trips           
              {!! Form::text($user_type.'second_stage_min_trips', old($user_type.'second_stage_min_trips',$driver_trip_bonus['second_stage_min_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'second_stage_min_trips', 'placeholder' => 'Minimum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>

            <div class="col-sm-2">  
              Maximum Trips           
              {!! Form::text($user_type.'second_stage_max_trips', old($user_type.'second_stage_max_trips',$driver_trip_bonus['second_stage_max_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'second_stage_max_trips', 'placeholder' => 'Maximum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>

            <div class="col-sm-1">  
              Amount         
              {!! Form::text($user_type.'second_stage_amount', old($user_type.'second_stage_amount',$driver_trip_bonus['second_stage_amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'second_stage_amount', 'placeholder' => 'Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label" title="Lowest to Highest" style="padding-top:20px;">Third Stage *</label>
            <div class="col-sm-2">  
              Minimum Trips           
              {!! Form::text($user_type.'third_stage_min_trips', old($user_type.'third_stage_min_trips',$driver_trip_bonus['third_stage_min_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'third_stage_min_trips', 'placeholder' => 'Minimum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>

            <div class="col-sm-2">  
              Maximum Trips           
              {!! Form::text($user_type.'third_stage_max_trips', old($user_type.'third_stage_max_trips',$driver_trip_bonus['third_stage_max_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'third_stage_max_trips', 'placeholder' => 'Maximum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>

            <div class="col-sm-1">  
              Amount         
              {!! Form::text($user_type.'third_stage_amount', old($user_type.'third_stage_amount',$driver_trip_bonus['third_stage_amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'third_stage_amount', 'placeholder' => 'Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'allow_same_user'}}" class="col-sm-4 control-label">Bonus Start after month *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'bonus_start_after_month', $months, old($user_type.'bonus_start_after_month', $driver_trip_bonus['bonus_start_after_month']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'bonus_start_after_month']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'bonus_start_after_month') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'allow_same_user'}}" class="col-sm-4 control-label">Allow Same User *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'allow_same_user', $yes_no, old($user_type.'allow_same_user', $driver_trip_bonus['allow_same_user']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'allow_same_user']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'allow_same_user') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'trip_distance'}}" class="col-sm-4 control-label">Trip Distance *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'trip_distance', old($user_type.'trip_distance',$driver_trip_bonus['trip_distance']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'trip_distance', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ @$errors->first(@$user_type.'trip_distance') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'number_of_days'}}" class="col-sm-4 control-label">Within Days *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_days', old($user_type.'number_of_days',$driver_trip_bonus['number_of_days']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_days', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_days') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'currency_code',$driver_trip_bonus['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_trip_bonus['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'day_name'}}" class="col-sm-4 control-label">Bonus Day *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'day_name', $day_name, old($user_type.'day_name', $driver_trip_bonus['day_name']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'day_name']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'day_name') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_trip_bonus['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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

  <!-- Driver Online  Bonus -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"> Driver Daily (Online) Bonus</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'DriverOnlineBonus_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}

        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_online_bonus['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_online_bonus['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_online_bonus['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'peak_hour'}}" class="col-sm-4 control-label">Minimum Online Peak Hours *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'peak_hour', old($user_type.'peak_hour', $driver_online_bonus['peak_hour']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'peak_hour', 'placeholder' => 'Minimum Online Peak Hours', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'peak_hour') }}</span>
            </div>
          </div>

          

          <div class="form-group">
            <label for="input_{{$user_type.'trip_complete_percent'}}" class="col-sm-4 control-label">Trips Completion Percentage *</label>
            <div class="col-sm-4">
              {!! Form::text($user_type.'trip_complete_percent', old($user_type.'trip_complete_percent', $driver_online_bonus['trip_complete_percent']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'trip_complete_percent', 'placeholder' => 'Trips Completion Percentage', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'trip_complete_percent') }}</span>
            </div>
            <div class="col-sm-1" style="width:20px !important">%</div>
          </div>
       
          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'currency_code',$driver_online_bonus['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>
 
          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Bonus Amount
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $driver_online_bonus['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Bonus Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_online_bonus['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_online_bonus['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:90px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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
 
  <?php /*
  <!-- Driver Online  Bonus -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"> Driver Daily (Online) Bonus</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'DriverOnlineBonus_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}

        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_online_bonus['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_online_bonus['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_online_bonus['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'peak_hour'}}" class="col-sm-4 control-label">Minimum Online Peak Hours *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'peak_hour', old($user_type.'peak_hour', $driver_online_bonus['peak_hour']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'peak_hour', 'placeholder' => 'Minimum Online Peak Hours', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'peak_hour') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'min_hour'}}" class="col-sm-4 control-label">Minimum Online Hours *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'min_hour', old($user_type.'min_hour', $driver_online_bonus['min_hour']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'min_hour', 'placeholder' => 'Minimum Online Hours', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'min_hour') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'min_trip'}}" class="col-sm-4 control-label">Minimum Trips *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'min_trip', old($user_type.'min_trip', $driver_online_bonus['min_trip']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'min_trip', 'placeholder' => 'Minimum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'min_trip') }}</span>
            </div>
          </div>
       
          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'currency_code',$driver_online_bonus['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>
 
          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Bonus Amount
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $driver_online_bonus['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Bonus Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_online_bonus['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_online_bonus['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:90px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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
  
  <!-- Rider Referral Settings -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Rider Referral </h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'Rider_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}
        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Referral *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $rider_referral['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$rider_referral['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$rider_referral['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$rider_referral['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div> 

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'driver_currency',$rider_referral['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Amount for Trips
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $rider_referral['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Amount for Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'who_get_bonus'}}" class="col-sm-4 control-label">Who Get Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'who_get_bonus', $beneficiary, old($user_type.'who_get_bonus',$rider_referral['who_get_bonus']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'who_get_bonus']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'who_get_bonus') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $rider_referral['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $rider_referral['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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

  <!-- Rider Cashback Offer1 Settings -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Rider Cashback Offer - 1</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'RiderCashback1_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}
        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Offer *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $rider_cashback1['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$rider_cashback1['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$rider_cashback1['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$rider_cashback1['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div> 

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'driver_currency',$rider_cashback1['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Amount for Trips
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $rider_cashback1['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Amount for Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Adjustable *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $adjustable, old($user_type.'withdrawal_method', $rider_cashback1['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $rider_cashback1['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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

  <!-- Rider Cashback Offer1 Settings -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Rider Cashback Offer - 2</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'RiderCashback2_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}
        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Offer *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $rider_cashback2['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$rider_cashback2['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$rider_cashback2['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$rider_cashback2['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div> 

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'driver_currency',$rider_cashback2['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Amount for Trips
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $rider_cashback2['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Amount for Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Adjustable *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $adjustable, old($user_type.'withdrawal_method', $rider_cashback2['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $rider_cashback2['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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
*/ ?>
   <!-- Rider First Discount Offer -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Rider Welcome 300 Offer</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'RiderDiscountOffer1_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}
        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Offer *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $rider_discount_offer1['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$rider_discount_offer1['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$rider_discount_offer1['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'offer_for'}}" class="col-sm-4 control-label">Offer for *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'offer_for', $offer_for, old($user_type.'offer_for',$rider_discount_offer1['offer_for']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'offer_for']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'offer_for') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'currency_code',$rider_discount_offer1['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'rate_fixed'}}" class="col-sm-4 control-label">Rate/ Fixed Amount *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'rate_fixed', $rate_fixed, old($user_type.'rate_fixed',$rider_discount_offer1['rate_fixed']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'rate_fixed']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'rate_fixed') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Amount for Trips
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $rider_discount_offer1['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'max_trip'}}" class="col-sm-4 control-label">
              Maximum Trips
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'max_trip', old($user_type.'max_trip', $rider_discount_offer1['max_trip']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'max_trip', 'placeholder' => 'Maximum Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'max_trip') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $rider_discount_offer1['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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

  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<?php
/*
  <!-- Driver Trip Bonus -->
  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"> Driver Weekly (Trip) Bonus</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => $update_url, 'class' => 'form-horizontal', 'method'=> 'POST']) !!}
        @if($user_type = 'DriverTripBonus_')@endif
        {!! Form::hidden('user_type', substr($user_type, 0, -1)) !!}

        <div class="box-body">
          <div class="form-group">
            <label for="input_{{$user_type.'apply_referral'}}" class="col-sm-4 control-label">Apply Bonus *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'apply_referral', $yes_no, old($user_type.'apply_referral', $driver_trip_bonus['apply_referral']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'apply_referral']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'apply_referral') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'start_time'}}" class="col-sm-4 control-label">Start Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'start_time', old($user_type.'start_time',$driver_trip_bonus['start_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'start_time', 'placeholder' => 'Start Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'start_time') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'end_time'}}" class="col-sm-4 control-label">End Time *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'end_time', old($user_type.'end_time',$driver_trip_bonus['end_time']), ['class' => 'form-control date_time', 'autocomplete' => 'off', 'id' => 'input_'.$user_type.'end_time', 'placeholder' => 'End Time']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'end_time') }}</span>
            </div>
          </div>


          <div class="form-group">
            <label for="input_{{$user_type.'number_of_trips'}}" class="col-sm-4 control-label">Number Of Trips *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_trips', old($user_type.'number_of_trips',$driver_trip_bonus['number_of_trips']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_trips', 'placeholder' => 'Number Of Trips', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_trips') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'number_of_days'}}" class="col-sm-4 control-label">Within Days *</label>
            <div class="col-sm-5">              
              {!! Form::text($user_type.'number_of_days', old($user_type.'number_of_days',$driver_trip_bonus['number_of_days']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'number_of_days', 'placeholder' => 'Within Days', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'number_of_days') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'currency_code'}}" class="col-sm-4 control-label">Currency Code *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'currency_code', $currency, old($user_type.'currency_code',$driver_trip_bonus['currency_code']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'currency_code']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'currency_code') }}</span>
            </div>
          </div>
 
          <div class="form-group">
            <label for="input_{{$user_type.'amount'}}" class="col-sm-4 control-label">
              Bonus Amount
            *</label>
            <div class="col-sm-5">
              {!! Form::text($user_type.'amount', old($user_type.'amount', $driver_trip_bonus['amount']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'amount', 'placeholder' => 'Bonus Amount', 'onkeypress' => 'validate(event)']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'amount') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'withdrawal_method'}}" class="col-sm-4 control-label">Withdrawal Method *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'withdrawal_method', $withdrawal_method, old($user_type.'withdrawal_method', $driver_trip_bonus['withdrawal_method']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'withdrawal_method']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'withdrawal_method') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'day_name'}}" class="col-sm-4 control-label">Bonus Day *</label>
            <div class="col-sm-5">
              {!! Form::select($user_type.'day_name', $day_name, old($user_type.'day_name', $driver_trip_bonus['day_name']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'day_name']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'day_name') }}</span>
            </div>
          </div>

          <div class="form-group">
            <label for="input_{{$user_type.'terms_condition'}}" class="col-sm-4 control-label">
              Terms & Conditions
            *</label>
            <div class="col-sm-5">
              {!! Form::textarea($user_type.'terms_condition', old($user_type.'terms_condition', $driver_trip_bonus['terms_condition']), ['class' => 'form-control', 'id' => 'input_'.$user_type.'terms_condition', 'placeholder' => 'Terms & Conditions', 'style' => 'height:80px;']) !!}
              <span class="text-danger">{{ $errors->first($user_type.'terms_condition') }}</span>
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
  
*/ ?>

@stop

@push('scripts')
<script src="{{ url('admin_assets/plugins/datetimepicker/build/jquery.datetimepicker.full.js') }}"></script>

<script>
  $('.date_time').datetimepicker(
    {
       format: 'Y-m-d H:i'
    }
  );
</script>
@endpush