<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Drivers Documents Report</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <style>
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
        .heading{
            margin:10px;
        }
        .heading td:first-child{
            padding-right:20px;
            width:180px;
        }
        .heading td{
            padding-right:20px;
        }

        @media print {
          #back{
            display:none;
          }
        }

        @page 
        {
            size: auto;   /* auto is the initial value */
            margin: 0mm;  /* this affects the margin in the printer settings */
        }
   
        td{              
          border-bottom: 1px solid #acacac;
          border-right: 1px solid #acacac;
          border-collapse: collapse;
          padding:5px;
        }
        td:first-child{
          border-left: 1px solid #acacac;
        } 
        thead th:first-child{
          border-left: 1px solid #acacac;
        } 
        thead th {
          border-top: 1px solid #acacac;
          border-bottom: 1px solid #acacac;
          border-right: 1px solid #acacac;
          border-collapse: collapse;
          padding:5px;
        } 
            
        table{ page-break-inside:auto }
        tr    { page-break-inside:avoid; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }
        .col-noborder {border-top:none;border-left:none !important;"}
  </style>


</head>
 <body onload="window.print();">
  <div class="box-body">
      <div class="text-right" id="back" >
        <button class="btn btn-primary" onclick="window.history.back();">Go Back</button>
      </div>

    
    @if(isset($list) AND is_object($list) and count($list) > 0)
    <div class="row"> 
        <table class="table table-bordered" cellspacing="0">
          <thead>
            <tr>
              <td colspan="15" style="text-align:center;border:none !important;">
              <h3>Alesha Ride Limited</h3>
                <div  style="padding-bottom: 10px;">
                  <h4>Drivers Documents Report</h4>                      
                </div>
              </td>
            </tr>

            <tr>
              <th rowspan="2" class="middle">Sl#</th>
              <th rowspan="2" class="middle">Driver ID</th>
              <th rowspan="2" class="middle">Driver Name</th>
              <th rowspan="2" class="middle">Driver's Number</th>
              <th rowspan="2" class="middle">Car Number Plate No</th>
              <th rowspan="2" class="middle">Camera</th>
              <th colspan="3" class="middle" >Personal Documents</th>
              <th colspan="4" class="middle">Car Documents</th>
              <th rowspan="2" class="middle">Status</th>
              <th rowspan="2" class="middle" style="border-left : 1px solid #eee">Remarks</th>
            </tr>
            <tr>
              <th class="middle col-noborder">Photo</th>
              <th class="middle col-noborder">Driver's License</th>
              <th class="middle col-noborder">NID</th>

              <th class="middle col-noborder">Registration</th>
              <th class="middle col-noborder">Tax Token</th>
              <th class="middle col-noborder">Enlistment</th>
              <th class="middle col-noborder">Fitness Certificate</th>
            </tr>
        </thead>

        <tfoot>
          <tr>
            <td colspan="15" style="text-align:right; border:none;">{{date('d F Y, h:i:s A')}}</td>         
        </tfoot>

        <tbody class="print_body">
          @foreach($list as $d)
            <tr class="paging">
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
              <td>{{driver_status($d->id)}}</td>
              <td>{{driver_last_remarks($d->id)}}</td>
            </tr>
          @endforeach
      </tbody>
      </table>
    </div>
    @endif
  </div>
</body>
