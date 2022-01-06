@extends('templatesign')

@section('main')

<style>
    #gender_options {
        color: gray !important;
    }
    #gender_options option{
        color: black;
    }
</style>


<div id="app-wrapper" class="signup-riders" ng-controller="facebook_account_kit">
   @include('user.otp_popup')
  <header class="funnel" style="background:url('images/blue-global.png') center center repeat;" >
    <div class="bit bit--logo text--center">
      <a href="{{ url('/') }}">
       <img class="white_logo" src="{{ $logo_url }}" style="width: 109px; height: 50px;background-size: contain;">
     </a> 
   </div>
 </header>  
 <section class="content-signupdrive ">

  <div class="signup-wrapper">
    <div class="stage">
     
      <div class="form-wrapper text_frm">
        {{ Form::open(array('url' => 'forgotpassword','id'=>"form")) }}
        {{csrf_field()}}
        {!! Form::hidden('otp', '', ['id' => 'otp' ]) !!}
        {!! Form::hidden('request_type', '', ['id' => 'request_type' ]) !!}

        <input type="hidden" name="user_type" value="{{ Route::current()->uri() == 'forgot_password_rider' ? 'Rider' : (Route::current()->uri() == 'forgot_password_driver' ? 'Driver' : 'Company')}}">
        <h4 data-reactid="14">{{trans('messages.user.forgot_paswrd')}}</h4>

        <div data-reactid="15">
          <div style="-moz-box-sizing:border-box;font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:12px;line-height:24px;text-align:none;color:#939393;box-sizing:border-box;margin-bottom:0;margin-top:0;" data-reactid="16"></div>
            <div style="width:100%;" data-reactid="17">
              <div style="font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:14px;line-height:24px;text-align:none;color:#3e3e3e;box-sizing:border-box;margin-bottom:24px;" data-reactid="19">
                <div class="_style_CZTQ8" data-reactid="20">
                  {!! Form::text('email', '', ['id' => 'forgot_pass_number','class' => 'text-input input-group-addon  ae-form-field','placeholder' => 'Enter your Email or Mobile','autocorrect' => 'off','autocapitalize' => 'off' ]) !!}  
                </div>
                <span class="text-danger email_error">{{$errors->first('email')}}</span>
              </div>
          </div>
        </div>

        <div class="text--center" id="captcha-form-container" style="display: none;">
          <div id="captcha" class="push--bottom display--inline-block text--center"></div>
        </div>

        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="code" id="code" />

        @php
        $submit_method = site_settings('otp_verification') ? 'send_otp':'check_otp';
        @endphp

        <button id="submit-btn" ng-click="forgotPassword('{{$submit_method}}');" type="button" class="btn btn--full btn--primary btn--large btn--arrow signup-btn">
          <span class="float--left push-small--right">{{trans('messages.user.submit')}}</span>
          <i class="fa fa-long-arrow-right icon icon_right-arrow-thin"></i>
        </button>
        {{ Form::close() }}
       
       </div>
     </div>
   </section>
 </div>

 
</main>
<style>
 .logo-link
 {
  display: none;
}
.funnel
{
  height: 0px !important;
}

</style>
<script>
function showpass() {
  var x = document.getElementById("password");


  if (x.type === "password") {
    x.type = "text";
    $("#vissible_invissible").addClass('fa-eye-slash');
    $("#vissible_invissible").removeClass('fa-eye');

  } else {
    x.type = "password";
    $("#vissible_invissible").addClass('fa-eye');
    $("#vissible_invissible").removeClass('fa-eye-slash');

  }
}


</script>

@stop
