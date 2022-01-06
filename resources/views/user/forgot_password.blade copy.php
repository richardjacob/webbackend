@extends('templatesign')
@section('main')

<style>
  #gender_options {
    color: gray !important;
  }

  #gender_options option {
    color: black;
  }

  .inside-ion {
    color: #c6c6c6;
    position: absolute;
    right: 14px;
    top: 18px;
  }
</style>
{{-- <div id="app-wrapper" class="sigin-riders"> --}}

<header class="funnel" style="background:url('images/global.png') center center repeat;" data-reactid="5">
  <a href="{{ url('signin')}}" data-reactid="8">
    <div class=" text--center" data-reactid="6" style="padding-top: 35px;">
      <a href="{{ url('/') }}" style="background-image: url('{{ $logo_url }}'); background-size: contain;  background-position: 50% 50% !important;
       display: block;
       height: 50px !important;
       margin: auto;
       left: 0;
       background-repeat: no-repeat;
       width: 109px !important;
       object-fit: contain;" href=""></a>
    </div>
  </a>
</header>

<div class="flash-container">
  @if(Session::has('message'))
  <div class="alert text-center participant-alert " style="    background: #1fbad6 !important;color: #fff !important;margin-bottom: 0;" role="alert">
    <a href="#" class="alert-close text-white" data-dismiss="alert"></a>
    {!! Session::get('message') !!}
  </div>
  @endif
</div>
<div class=" text-center signupdrive" ng-controller="facebook_account_kit">
  @include('user.otp_popup')
  <div class="join-page">

    <div class="stage-wrapper narrow portable-one-whole forward" id="app-body" data-reactid="10">
      <div class="soft-tiny" data-reactid="11">
        <div data-reactid="12">

          {!! Form::open(['action' => 'UserController@forgotpassword','id'=>'form', 'class' => 'push--top-small forward']) !!}
          {{csrf_field()}}
          {!! Form::hidden('request_type', '', ['id' => 'request_type' ]) !!}
          {!! Form::hidden('otp', '', ['id' => 'otp' ]) !!}
          <input type="hidden" name="user_type" value="{{ Route::current()->uri() == 'forgot_password_rider' ? 'Rider' : (Route::current()->uri() == 'forgot_password_driver' ? 'Driver' : 'Company')}}">
          <h4 data-reactid="14">{{trans('messages.user.forgot_paswrd')}}</h4>

          <div data-reactid="15">
            <div style="-moz-box-sizing:border-box;font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:12px;line-height:24px;text-align:none;color:#939393;box-sizing:border-box;margin-bottom:0;margin-top:0;" data-reactid="16"></div>
            <div style="width:100%;" data-reactid="17">
              <div style="font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:14px;line-height:24px;text-align:none;color:#3e3e3e;box-sizing:border-box;margin-bottom:24px;" data-reactid="19">
                <div class="_style_CZTQ8" data-reactid="20">
                  {!! Form::text('email', '', ['class' => 'text-input input-group-addon ae-form-field','placeholder' => 'Enter your Email or Mobile','autocorrect' => 'off','autocapitalize' => 'off' ]) !!}
                </div>
                <span class="text-danger">{{$errors->first('email')}}</span>
              </div>
            </div>
          </div>

          {{-- <button class="btn btn--arrow btn--full blue-signin-btn" data-reactid="22" type="submit"><span class="push-small--right" data-reactid="23">{{trans('messages.user.next')}}</span><i class="fa fa-long-arrow-right icon icon_right-arrow-thin"></i></button> --}}

          <div class="layout__item one-whole push-small--bottom">
            <input type="hidden" name="step" value="basics">

            @php
            $submit_method = site_settings('otp_verification') ? 'send_otp':'check_otp';
            @endphp

            <button name="step" value="basics" class="btn--arrow btn--full error-retry-btn _style_3CjDXv" id="submit-btn" ng-click="showPopup('{{$submit_method}}');" type="button" style="box-sizing:border-box;text-decoration:none;color:#FFFFFF;display:inline-block;vertical-align:middle;text-align:center;margin:0;cursor:pointer;overflow:visible;background-color:#11939A;font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:600;font-size:14px;padding:11px 20px;border-radius:0px;border:2px solid #11939A;text-transform:uppercase;outline:none;line-height:18px;position:relative;transition:all 400ms ease;-moz-box-sizing:border-box;-webkit-transition:all 400ms ease;">{{trans('messages.user.submit')}}
            </button>
            {{-- <button onclick="myFunction()">Try it</button> --}}
          </div>

          {!! Form::close() !!}


        </div>
      </div>
    </div>
  </div>
  {{-- </div> --}}
  </main>
  <style>
    .logo-link {
      display: none;
    }

    .funnel {
      height: 0px !important;
    }
  </style>
  @stop