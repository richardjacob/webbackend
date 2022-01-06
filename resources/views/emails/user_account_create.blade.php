 <!DOCTYPE html>
 <html>
 <head>
   <title>Verify Your Email</title>
 </head>
 <body>
<center>
  <a href="https://alesharide.com" style="text-decoration:none;">
    <img src="https://alesharide.com/images/logos/logo.png" style="width:200px;">
  </a>
</center>
<div>Dear 
@if($gender =='1') Mr @elseif($gender =='2') Ms @endif {{$first_name.' '.$last_name}}</div>

<div>
Thank you for registering with ALESHA RIDE. Please click the below link to verify and continue the registration process.
<br /><br />

Please click to <a href="https://alesharide.com/user/verify/{{base64_encode($id)}}">CONFIRM HERE!</a>
<br /><br />
Or copy below link & paste in your browser
https://alesharide.com/user/verify/{{base64_encode($id)}}
<br /><br />
Your Information :<br />
Name : {{$first_name.' '.$last_name}}<br />
Gender : @if($gender =='1') Male @elseif($gender =='2') Female @elseif($gender =='3') Other @endif <br />
Email : {{$email}}<br />
Phone Number : {{$country_code}}{{$mobile_number}}<br />
<br /><br />
Thank You<br />
ALESHA RIDE Team<br />
</div>
 </body>
 </html>

 