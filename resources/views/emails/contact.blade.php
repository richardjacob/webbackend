@extends('emails.template')
@section('emails.main')
<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">

  
  <div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
  <p>Hi {{ $name }},</p>

  You have submitted a contact form at Alesharide.<br />
  Your provided Information was as follows:<br /><br />

  Your Name : {{ $name }}<br />
  Email : {{ $email }}<br />
  Mobile : {{ $mobile }}<br />
  Contact For : {{ $contact_for }}<br />
  Message : {{ $msg }}<br /><br />

  Please keep pasence. We'll contact you soon. Thanks for contacting us.<br><br>
  
  

  </div>


</div>

@stop