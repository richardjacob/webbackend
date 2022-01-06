@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<link rel="stylesheet" href="{{ url('admin_assets/plugins/datetimepicker/jquery.datetimepicker.css') }}">

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>Manage Emails <small>Send Email</small></h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Manage Emails</li>
      <li><a href="{{ url(LOGIN_USER_TYPE.'/send_email') }}"> Send Email</a></li>
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
            <h3 class="box-title">Send Email Form</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          {!! Form::open(['url' => 'admin/send_email', 'class' => 'form-horizontal']) !!}
          <div class="box-body">
            <span class="text-danger">(*)Fields are Mandatory</span>
            <div class="form-group">
              <label class="col-sm-3 control-label">Message Priority<em class="text-danger">*</em></label>
              <div class="col-sm-2">
                <input type="radio" id="priority_send_now" name="message_priority" value="now" checked class="priority">
                <label for="now" style="font-weight:normal">Send Now</label>
              </div>
              <div class="col-sm-2">
                <input type="radio" id="priority_schedule_label" name="message_priority" value="schedule" class="priority">
                <label for="schedule" style="font-weight:normal">Schedule</label>
              </div>
            </div>

            <div class="form-group" id="schedule_time">
              <label class="col-sm-3 control-label radio_label">Schedule Time<em class="text-danger">*</em></label>
              <div class="col-sm-2">
                <input type="text" name="schedule_time" id="filter-date" autocomplete="off" readonly="" style="padding:5px" /> 
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label">Email To<em class="text-danger">*</em></label>
              <div class="col-sm-1">
                All
                <input type="radio" name="to" value="to_all" class="send_to">
              </div>
              <div class="col-sm-2">
                Specific Users
                <input type="radio" name="to" value="to_specific" class="send_to" checked>
              </div>
            </div>
            <div class="form-group" id="email_textbox">
              <label for="input_email" class="col-sm-3 control-label">Email Address<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::text('email', '', ['class' => 'form-control', 'id' => 'input_email', 'placeholder' => 'Enter Email Addresses of Users']) !!}
                <span class="text-danger">{{ $errors->first('email') }}</span>
                <input type="hidden" id="email_address_list" value="{{ $email_address_list }}">
                <span class="small"> Note : Email will be sent for registered users only. </span>
              </div>
            </div>
            <div class="form-group">
              <label for="input_subject" class="col-sm-3 control-label">Subject<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                {!! Form::text('subject', '', ['class' => 'form-control', 'id' => 'input_subject', 'placeholder' => 'Subject']) !!}
                <span class="text-danger">{{ $errors->first('subject') }}</span>
              </div>
            </div>
            <div class="form-group">
              <label for="input_message" class="col-sm-3 control-label">Message (Salutation will be automatically added)<em class="text-danger">*</em></label>
              <div class="col-sm-6">
                <textarea id="txtEditor" name="txtEditor"></textarea>
                <textarea id="message" name="message" hidden="true">{{ old('message') }}</textarea>
                <span class="text-danger">{{ $errors->first('message') }}</span>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <a href="{{url('admin/send_email')}}" class="btn btn-default">Cancel</a>
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
@push('scripts')
<script type="text/javascript">
  $(document).ready(function(){
    $('#schedule_time').hide();
  });

  $('.priority').click(function(){
    if($(this).val() == 'schedule')
      $('#schedule_time').show();
    else
      $('#schedule_time').hide();
  });


  $("#txtEditor").Editor(); 
  $('.Editor-editor').html($('#message').val());

  $('.send_to').click(function(){
    if($(this).val() == 'to_specific')
      $('#email_textbox').show();
    else
      $('#email_textbox').hide();
  });

  var email_address = $('#email_address_list').val();
  email_address = $.parseJSON(email_address)
  function split( val ) {
    return val.split( /,\s*/ );
  }

  function extractLast( term ) {
    return split( term ).pop();
  }

  // don't navigate away from the field on tab when selecting an item
  $("#input_email").bind( "keydown", function( event ) {
    if(event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active) {
      event.preventDefault();
    }
  }).autocomplete({
      minLength: 0,
      source: function( request, response ) {
        // delegate back to autocomplete, but extract the last term
        response( $.ui.autocomplete.filter(
          email_address, extractLast( request.term ) ) );
      },
      focus: function() {
        // prevent value inserted on focus
        return false;
      },
      select: function( event, ui ) {
        var terms = split( this.value );
        // remove the current input
        terms.pop();
        // add the selected item
        terms.push( ui.item.value );
        // add placeholder to get the comma-and-space at the end
        terms.push( "" );
        this.value = terms.join( ", " );
        return false;
      }
    });
</script>

<script src="{{ url('admin_assets/plugins/datetimepicker/build/jquery.datetimepicker.full.js') }}"></script>

<script>
    jQuery(document).ready(function () {
        'use strict';
        jQuery('#filter-date').datetimepicker();
    });
</script>
@endpush