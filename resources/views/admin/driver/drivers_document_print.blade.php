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


        @page {
            size: letter;
            margin: .5in;
        }

        @media print {
            table.paging thead td, table.paging tfoot td {
                height: .5in;
            }
        }

        header, footer {
            width: 100%; height: .5in;
        }

        header {
            position: absolute;
            top: 0;
        }

        @media print {
            header, footer {
                position: fixed;
            }
            
            footer {
                bottom: 0;
            }
        }
      </style>


</head>



<!-- <header>(repeated header)</header>

<table class=paging><thead><tr><td>&nbsp;</td></tr></thead><tbody><tr><td>

(content goes here)

</td></tr></tbody><tfoot><tr><td>&nbsp;</td></tr></tfoot></table>

<footer>(repeated footer)</footer> -->




<body onload="window.print();">
  <div class="box-body">
      <div class="text-right" id="back" >
        <button class="btn btn-primary" onclick="window.history.back();">Go Back</button>
      </div>
    <header>
    <div class="text-center">
      <div class="text-center">
        <h3>Alesha Ride Limited</h3>
      </div>
      <div class="text-center" style="padding-bottom: 10px;">
        <h4>Drivers Documents Report</h4>                      
      </div>
    </div>
      @if($car_doc = '')@endif
      @if($car_doc_count = 1)@endif

      @foreach($car_documents as $doc)
        @if(in_array($doc->id, @$document_id))                    
          <?php 
            $car_doc.='<td>'.$car_doc_count.'. '.$doc->document_name.'</td>';
            $car_doc_count++;
          ?>
        @endif
      @endforeach

      @if($driver_doc = '')@endif
      @if($driver_doc_count = 1)@endif
      @foreach($driver_documents as $doc2)
        @if(in_array($doc2->id, @$document_id))
          <?php 
            $driver_doc.='<td>'.$driver_doc_count.'. '.$doc2->document_name.'</td>';
              $driver_doc_count++;
          ?>
        @endif
      @endforeach

    <div class="col-md-12">
      @if($car_doc !='')
      <table class="heading">
        <tr>
          <td>
            <label>Car Documents</label>
          </td>
           {!! $car_doc !!}
        </tr>
      </table>
      @endif

      @if($driver_doc !='')
      <table class="heading">
        <tr>
          <td>
            <label>Personal Documents</label>
          </td>
           {!! $driver_doc !!}
        </tr>
      </table>
      @endif
      @if($car_doc !='' OR $driver_doc !='')
        <div class="col-md-12" style="padding-left:30px;">Following documents are {{$given}} :</div>
      @endif
    </div>
      </header>
    
   
    
    @if(isset($list) AND is_object($list) and count($list) > 0)
    <div class="row">  
        <table class="table table-bordered ">
          <tr>
            <th rowspan="2" class="middle">Sl#</th>
            <th rowspan="2" class="middle">Driver ID</th>
            <th rowspan="2" class="middle">Driver Name</th>
            <th rowspan="2" class="middle">Driver's Number</th>
            <th rowspan="2" class="middle">Car Number Plate No</th>
            <th rowspan="2" class="middle">Camera</th>
            <th colspan="3" class="middle">Personal Documents</th>
            <th colspan="4" class="middle">Car Documents</th>
            <th rowspan="2" class="middle">Status</th>
            <th rowspan="2" class="middle" style="border-left : 1px solid #eee">Remarks</th>
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
              <td>{{driver_status($d->id)}}</td>
              <td>{{driver_last_remarks($d->id)}}</td>
            </tr>
          @endforeach
        </table>
    </div>
    @endif
  </div>
</body>
