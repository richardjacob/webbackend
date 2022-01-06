<title>Documents</title>
@extends('template_driver_dashboard') 
@section('main')

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

<!--
<fieldset class="scheduler-border">
    <legend class="scheduler-border">Heading</legend>
    <div>
        content
    </div>
</fieldset>
-->


<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" ng-controller="payment" style="padding:0px;">
  <div class="page-lead separated--bottom  text--center text--uppercase">
    <h1 class="flush-h1 flush"> @lang('messages.driver_dashboard.driver_documents') </h1>
  </div>

    {!! Form::open(['url' => 'driver_document', 'class' => '','id'=>'vehicle_form','files' => true]) !!}
      <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 25px 0px 15px;">

        @foreach($driver_documents as $document)   
          @if(
            $document->document_name == 'Driving License' || 
            $document->document_name == 'NID Or Passport' ||
            $document->document_name == 'NID'
          )
            <!-- Both Design Here -->
            <fieldset class="scheduler-border">
              <legend class="scheduler-border">{{$document->document_name}}</legend>

              <div class="col-lg-12 form-group" >
                <div class="col-lg-6" style="padding-left:0">
                  @if($document->document_name == 'Driving License')
                    <div>
                      <div class="text-right">Smart Card/ Slip</div>
                      <div>
                        {!! Form::select($document->doc_name.'_document_type', $card_slip_array, @$document->document_type, ['class' => 'form-control', 'id' => 'document_type', 'placeholder' => 'Select']) !!}
                        <span class="text-danger">{{ $errors->first('document_type') }}</span>
                      </div>
                    </div>                        
                  @elseif($document->document_name == 'NID Or Passport' || $document->document_name == 'NID')
                    <div>
                      <div class="text-right">NID/ Passport</div>
                      <div>
                        {!! Form::select($document->doc_name.'_document_type', $nid_passport_array, @$document->document_type, ['class' => 'form-control', 'id' => 'document_type', 'placeholder' => 'Select']) !!}
                        <span class="text-danger">{{ $errors->first('document_type') }}</span>
                      </div>
                    </div>
                  @endif

                  <div>
                    <div class="text-right">Front Side</div>
                    <input type="file" name="{{$document->doc_name}}" class="form-control">
                    <span class="text-danger">
                      {{ $errors->first($document->doc_name) }} 
                    </span>
                  </div>

                  <div style="padding-top:10px;">
                    <div class="text-right">Back Side</div>
                    <input type="file" name="{{$document->doc_name}}_back" class="form-control">
                    <span class="text-danger">
                      {{ $errors->first($document->doc_name.'_back') }} 
                    </span>
                  </div>

                  @if($document->expiry_required == '1')
                  <div class="text-right" style="padding-top:10px;">Expiry Date</div>
                  <div class="col-lg-12 form-group" style="padding:0px;">
                    <input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" value="{{$document->expired_date}}">
                    <span class="text-danger"> 
                      {{ $errors->first('expired_date_'.$document->id) }}
                    </span>         
                  </div>
                  @endif
                </div>

                <div class="col-lg-6 text-right">
                  @php $image = ($document->document !='') ? $document->document : url('images/driver_doc.png'); @endphp
                  <div class="license-img">
                    <a href="{{$image}}" target="_blank">
                      <img style="width:200px;height:200px" src="{{$image}}">
                    </a>
                  </div>   
                </div>
              </div>
            </fieldset>
          @else
          <fieldset class="scheduler-border">
              <legend class="scheduler-border"><label>{{$document->document_name}}</label></legend>
            <div class="col-lg-12 form-group">
              <input type="file" name="{{$document->doc_name}}" class="form-control">
              <span class="text-danger">
                {{ $errors->first($document->doc_name) }} 
              </span>
              @php $image = ($document->document !='') ? $document->document : url('images/driver_doc.png'); @endphp
              <div class="license-img">
              <a href="{{$image}}" target="_blank">
                <img style="width:200px;height:200px" src="{{$image}}">
              </a>
              </div>          
            </div>        
            @if($document->expiry_required == '1')
            <div class="col-lg-12 form-group">
              <input type="date" min="{{ date('Y-m-d') }}" name="expired_date_{{$document->id}}" class="form-control" value="{{$document->expired_date}}">
              <span class="text-danger"> 
                {{ $errors->first('expired_date_'.$document->id) }}
              </span>         
            </div>
            @endif
          </fieldset>
          @endif

        @endforeach
      <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom:0px !important;">
        <button style="padding: 0px 30px !important;font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue" id="update_btn">{{trans('messages.user.update')}}</button>
    </div>
  </div>
  {{ Form::close() }}


</div>
</div>
</div>
</div>
</div>
</main>
@stop
<style type="text/css">
    .btn-input:hover, .btn:hover, .file-input:hover, .tooltip:hover, .btn, .btn-input, .file-input, .tooltip {
    background: transparent !important;
    border: none !important;
}
.btn--link .icon_left-arrow {
    -webkit-transition: left .4s ease;
    transition: left .4s ease;
    position: relative;
    left: -2;
    padding-left: 10px;
}
.btn--link:focus .icon_left-arrow, .btn--link:hover .icon_left-arrow {
    left: -6px;
}
@media (max-width: 400px){
    #btn-pad.btn.btn--primary.btn-blue{
      font-size: 11px !important;
      padding:0px 20px !important;
    }
}
</style>
