<title>Add Vehicle Details</title>
@extends('template_driver_dashboard') @section('main')
<style>
  fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 10!important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
  }
  legend.scheduler-border {
    width:inherit; /* Or auto */
    padding:0 10px; /* To give a bit of padding on the left and right */
    border-bottom:none;
}
</style>

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding: 0px;" ng-controller="vehicle_details" ng-init="errors = {{json_encode($errors->getMessages())}};">
  <div class="page-lead separated--bottom text--center text--uppercase">
    <h1 class="flush-h1 flush">Add Vehicle Detail</h1>
  </div>

  {!! Form::open(['url' => 'update_vehicle', 'class' => '','id'=>'vehicle_form','files' => true]) !!}
  <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 25px 0px 15px;">
    <div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.vehicle_make')}}</label>
      {!! Form::select('vehicle_make_id',$make, '', ['class' => 'form-control', 'id' => 'vehicle_make', 'placeholder' => trans('messages.driver_dashboard.select')]) !!}
      <span class="text-danger">{{ $errors->first('vehicle_make_id') }}</span>
    </div>
    <div class="col-lg-12 form-group vehicle_model">
      <label>{{trans('messages.driver_dashboard.vehicle_model')}}</label>
      {!! Form::select('vehicle_model_id',$model, '', ['class' => 'form-control', 'id' => 'vehicle_model', 'placeholder' => trans('messages.driver_dashboard.select')]) !!}
      <span class="text-danger">{{ $errors->first('vehicle_model_id') }}</span>
    </div>


    <div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.vehicle_number')}}</label>
      <div>
        <div class="col-sm-3" style="padding-left:0">
            <select name="city" class="form-control">
              <option value="">মেট্রো নির্বাচন করুন</option>
              @foreach($city as $val)
              <option value="{{$val->city}}">{{$val->city}}</option>
              @endforeach
          </select>
        </div>
        <div class="col-sm-2" style="padding-left:0">
            <select name="reg_letter" class="form-control">
              <option value="">বর্ণ</option>
              @foreach($letter as $val)
              <option value="{{$val->reg_letter}}">{{$val->reg_letter}}</option>
              @endforeach
          </select>
        </div>
        <div class="col-sm-2" style="padding-left:0">
            <select name="vehicle_class" class="form-control">
              <option value="">গাড়ির ক্লাস</option>
              @foreach($class as $val)
              <option value="{{$val->vehicle_class}}">{{$val->vehicle_class}}</option>
              @endforeach
          </select>
        </div>
        <div class="col-sm-1 text-center" style="padding:5px 0 0 0;width:5px;">-</div>


        <div class="col-sm-4">
          {!! Form::text('vehicle_number','', ['class' => 'form-control', 'id' => 'vehicle_number', 'maxlength' => '4', 'onkeyup' => 'validate(event)', 'placeholder' => trans('messages.driver_dashboard.vehicle_number')]) !!}
        </div>
      </div>      
      <span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
    </div>


    <div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.vehicle_color')}}</label>
      {!! Form::text('color','', ['class' => 'form-control', 'id' => 'color', 'placeholder' => trans('messages.driver_dashboard.vehicle_color')]) !!}
      <span class="text-danger">{{ $errors->first('color') }}</span>
    </div>
    <div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.vehicle_year')}}</label>
      {!! Form::text('year','', ['class' => 'form-control', 'id' => 'year', 'placeholder' => trans('messages.driver_dashboard.vehicle_year'),'autocomplete'=>'off']) !!}
      <span class="text-danger">{{ $errors->first('year') }}</span>
    </div>
    <!-- div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.vehicle_type')}}</label>
      <div class="cls_vehicle">
        @foreach($vehicle_type as $type)
        <li class="col-lg-6 col-md-12 col-12">
          <input type="checkbox" name="vehicle_type[]" id="vehicle_type" class="form-check-input vehicle_type" value="{{ $type->id }}"/> {{ $type->car_name }}
        </li>
        @endforeach
        <span class="text-danger">{{ $errors->first('vehicle_type') }}</span>
      </div>
    </div>

    <div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.handicap')}} {{trans('messages.ride.accessibility')}} {{trans('messages.driver_dashboard.available')}}</label>
      <div>
      {{ Form::radio('handicap', '1', false, ['class'=>'form-check-input']) }} {{ trans('messages.driver_dashboard.yes') }}
      {{ Form::radio('handicap', '0', false, ['class'=>'form-check-input']) }} {{ trans('messages.driver_dashboard.no') }}
      </div>
      <div class="text-danger">{{ $errors->first('handicap') }}</div>
    </div>

    <div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.child_seat')}} {{trans('messages.ride.accessibility')}} {{trans('messages.driver_dashboard.available')}}</label>
      <div>
      {{ Form::radio('child_seat', '1', false, ['class'=>'form-check-input']) }} {{ trans('messages.driver_dashboard.yes') }}
      {{ Form::radio('child_seat', '0', false, ['class'=>'form-check-input']) }} {{ trans('messages.driver_dashboard.no') }}
      </div>
      <div class="text-danger">{{ $errors->first('child_seat') }}</div>
    </div> -->
    <input type="hidden" name="vehicle_type[]" value="3">
    <input type="hidden" name="handicap" value="1">
    <input type="hidden" name="child_seat" value="1">

    @if($result->gender=='2')
    <div class="col-lg-12 form-group">
      <label>{{trans('messages.driver_dashboard.request_from')}}</label>
      <div>
      {{ Form::radio('request_from', '1', false, ['class'=>'form-check-input']) }} {{ trans('messages.profile.female') }}
      {{ Form::radio('request_from', '0', false, ['class'=>'form-check-input']) }} {{ trans('messages.driver_dashboard.both') }}
      </div>
      <div class="text-danger">{{ $errors->first('request_from') }}</div>
    </div>
    @else
      {{ Form::hidden('request_from', '0') }}
    @endif

    <div class="form-group">
      @foreach($documents as $document)
        @if($document->document_name == 'Registration Paper')
          <fieldset class="scheduler-border">
            <legend class="scheduler-border">{{$document->document_name}}</legend>

            <div class="col-lg-12 form-group">
              <div class="text-right">Front Side</div>
              <input type="file" name="{{$document->doc_name}}" class="form-control" />
              <span class="text-danger">
                {{ $errors->first($document->doc_name) }}
              </span>
            </div>

            <div class="col-lg-12 form-group">
                <div class="text-right">Back Side</div>
                <input type="file" name="{{$document->doc_name}}_back" class="form-control">
                <span class="text-danger">
                  {{ $errors->first($document->doc_name.'_back')}} 
                </span>
            </div>

            @if($document->expire_on_date == 'Yes')
            <div class="col-lg-12 form-group">
              <input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" autocomplete="off" />
              <span class="text-danger">
                {{ $errors->first('expired_date_'.$document->id) }}
              </span>
            </div>
            @endif

          </fieldset>
        @else
          <fieldset class="scheduler-border">
            <legend class="scheduler-border">{{$document->document_name}}</legend>
            <div class="col-lg-12 form-group">
              <input type="file" name="{{$document->doc_name}}" class="form-control" />
              <span class="text-danger">
                {{ $errors->first($document->doc_name) }}
              </span>
            </div>
            @if($document->expire_on_date == 'Yes')
            <div class="col-lg-12 form-group">
              <input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" autocomplete="off" />
              <span class="text-danger">
                {{ $errors->first('expired_date_'.$document->id) }}
              </span>
            </div>
            @endif
          </fieldset>
        @endif
       @endforeach
    </div>

    <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom: 0px !important;">
      <button style="padding: 0px 30px !important; font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue" id="update_btn">{{trans('messages.driver_dashboard.vehicle_add')}}</button>
    </div>
    {{ Form::close() }}
  </div>
</div>
</div>
</div>
</div>
</main>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="{{ url('admin_assets/plugins/datepicker/bootstrap-datepicker3.css') }}">
<script>
  $("#year").datepicker({
    format: "yyyy",
    viewMode: "years", 
    minViewMode: "years",
    autoclose : true,
    startDate: '1950',
    endDate: '<?php echo date('Y'); ?>'
  });

  function validate(evt) {
    var theEvent = evt || window.event;

    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
    // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /[0-9]|\./;
    if( !regex.test(key) ) {
      theEvent.returnValue = false;
      if(theEvent.preventDefault) theEvent.preventDefault();
    }
    getBanglaNum(evt);
  }



  function getBanglaNum(event) {
    var vehicle_number = $('#vehicle_number').val();

    var myArr = vehicle_number.split("");
    var unicode_text = "";
    var unicode_char = "";
    var char='',i;

    for (i = 0; i < myArr.length; i++) {
      char = myArr[i];
      switch(char) {
        case '0': unicode_char='০'; break;
        case '1': unicode_char='১'; break;
        case '2': unicode_char='২'; break;
        case '3': unicode_char='৩'; break;
        case '4': unicode_char='৪'; break;
        case '5': unicode_char='৫'; break;
        case '6': unicode_char='৬'; break;
        case '7': unicode_char='৭'; break;
        case '8': unicode_char='৮'; break;
        case '9': unicode_char='৯'; break;

        case '০': unicode_char='০'; break;
        case '১': unicode_char='১'; break;
        case '২': unicode_char='২'; break;
        case '৩': unicode_char='৩'; break;
        case '৪': unicode_char='৪'; break;
        case '৫': unicode_char='৫'; break;
        case '৬': unicode_char='৬'; break;
        case '৭': unicode_char='৭'; break;
        case '৮': unicode_char='৮'; break;
        case '৯': unicode_char='৯'; break;
        default: unicode_char = ''; break;
      } 
      unicode_text+=unicode_char;
    }    

    $('#vehicle_number').val(unicode_text);
  }
</script>
@endsection
