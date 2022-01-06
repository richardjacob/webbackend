<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Car Acquistion Report</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <style>
        body {margin: 20px}
        th{
          font-weight:normal; 
          text-align:center;
        }
        h3,h4{
          font-weight:bold;
          font-family: Verdana, Arial, Helvetica, sans-serif;
        }
        th,td,strong{
          font-family: Verdana, Arial, Helvetica, sans-serif;
          font-size:11px;
        }

        .middle{
          vertical-align: middle !important;
        }
        #filter{
            margin:10px;
        }
        #filter td:first-child{
            padding-right:20px;
        }
        #filter td{
            padding-right:20px;
        }

        @media print {
          #back{
            display:none;
          }
        }



      </style>

</head>
<body onload="window.print();">
   <div class="box-body">
      <div class="text-right" id="back" >
        <button class="btn btn-primary" onclick="window.history.back();">Go Back</button>
      </div>
 
    <center>
      <div class="text-center">
        <h3>Alesha Ride Limited</h3>
      </div>
      <div class="text-center">
        <h4>Car Acquisition Report</h4>                      
      </div>
    </center>

    <table id='filter'>
      @if(@$start_date !='')
      <tr>
        <td>Date Range</td>
        <td>:</td>
        <td>
          {{date("d F Y", strtotime($start_date))}}
          @if(@$end_date !='') To {{date("d F Y", strtotime($end_date))}} @endif
        </td>
     </tr>
     @endif

     @if(@$code !='')
      <tr>
        <td>Referral Code</td>
        <td>:</td>
        <td>{{@$code}}</td>
      </tr>
     @endif

     @if(@$employee_name !='')
      <tr>
        <td>Employee</td>
        <td>:</td>
        <td>{{@$employee_name}}</td>
      </tr>
     @endif

     @if(@$hub_name !='')
      <tr>
        <td>Hub</td>
        <td>:</td>
        <td>{{$hub_name}}</td>
      </tr>
     @endif
    </table>

    <table class="table table-bordered">
      <tr>
        <th rowspan="2" class="middle">Sl#</th>
        <th rowspan="2" class="middle">Driver ID</th>
        <th rowspan="2" class="middle">Driver Name</th>
        <th rowspan="2" class="middle">Driver's Number</th>
        <th rowspan="2" class="middle">Car Number Plate No</th>
        <th rowspan="2" class="middle">Camera</th>
        <th colspan="3" class="middle">Personal Documents</th>
        <th colspan="4" class="middle">Car Documents</th>
        <th rowspan="2" class="middle" style="border-left : 1px solid #eee">Acquistioned By</th>
        <th rowspan="2" class="middle">Status</th>
        <th rowspan="2" class="middle">Remarks</th>
      </tr>
      <tr>
        <th class="middle">Photo</th>
        <th class="middle">Driver's License</th>
        <th class="middle">NID</th>
        <th class="middle">Registration</th>
        <th class="middle">Tax Token</th>
        <th class="middle">Enlistment</th>
        <th class="middle">Fitness Certificate</th>
      </tr>

      @foreach($list as $d)
        <tr>
          <td class="middle">{{$d->serial}}</td>
          <td class="middle">{{$d->id}}</td>
          <td class="middle">{{$d->driver_name}}</td>
          <td class="middle">{{$d->mobile_number}}</td>
          <td class="middle">{!! driver_vehicle($d->id) !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'camera', 'icon') !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'photo', 'icon') !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'driving_license', 'icon') !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'nid', 'icon') !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'registration_paper', 'icon') !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'tax_token', 'icon') !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'enlistment_certificate', 'icon') !!}</td>
          <td class="text-center middle">{!! is_exist($d->id, 'fitness_certificate', 'icon') !!}</td>
          <td class="middle">{{$d->employee_name}} ({{$d->refaral_id}})</td>
          <td>{{driver_status($d->id)}}</td>  
          <td>{{driver_last_remarks($d->id)}}</td>
        </tr>
      @endforeach
    </table>

   </div>

</body>



            