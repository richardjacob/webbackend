@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Only checked Documents</h1>
      <ol class="breadcrumb">
        <li>
          <a href="dashboard"><i class="fa fa-dashboard"></i> Home</a>
        </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/driver') }}"> Drivers </a>
        </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/drivers_documents') }}">Drivers Documents</a>
        </li>
        <li>
          <a href="{{ url(LOGIN_USER_TYPE.'/drivers_documents/only_checked_documents') }}"> Only checked Documents</a>
        </li>
      </ol>
   </section>
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header">
                  {!! Form::open(['url' => LOGIN_USER_TYPE.'/drivers_documents/only_checked_documents', 'class' => 'form-horizontal', 'method' => 'GET', 'id' => 'frm']) !!}

                  <div class="col-sm-12">
                    <input type="checkbox" id="checkAll" name="checkAll" @if(@$checkAll == 'All') checked="" @endif value="All">
                    Check/ Uncheck All  
                  </div>

                  <div class="col-lg-12">
                    <div class="col-md-2" style="padding:10px 0px;">
                      <label>Car Documents</label>
                    </div>
                    @foreach($car_documents as $doc)
                      <div class="col-md-2" style="padding:5px 0px;">
                        <input type="checkbox" name="document_id[]" class="chk" value="{{$doc->id}}"
                        @if(in_array($doc->id, @$document_id)) checked="" @endif > {{$doc->document_name}}
                      </div>
                    @endforeach
                  </div>

                  <div class="col-lg-12">
                    <div class="col-md-2" style="padding:10px 0px;">
                      <label>Personal Documents</label>
                    </div>
                    @foreach($driver_documents as $doc)
                      <div class="col-md-2" style="padding:10px 0px;">
                        <input type="checkbox" name="document_id[]" class="chk" value="{{$doc->id}}"
                        @if(in_array($doc->id, @$document_id)) checked="" @endif > {{$doc->document_name}}
                      </div>
                    @endforeach
                    <div class="col-sm-2">
                      <select name="per_page" class="form-control">
                        <option value="">Per Page</option>
                        @foreach(array('10','20','50','100') as $p)
                          <option value="{{$p}}" @if($p == @$per_page) selected @endif>{{$p}}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-sm-1 text-right">
                      <button type="submit" class="btn btn-primary form_submit">
                        <i class="fa fa-search"></i> Search
                      </button>
                    </div>
                    <div class="col-sm-1">                      
                       <button type="submit" name="print" value="Print" class="btn btn-success form_submit">
                         <i class="fa fa-print"></i> Print
                       </button>
                    </div>
                  </div>
                  {!! Form::close() !!}
               </div>
               
               <!-- /.box-header -->
               <style type="text/css">
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
               </style>

            @if($req !='')
              <div class="text-center">
                <h3>Alesha Ride Limited</h3>
              </div>
              <div class="text-center" style="padding-bottom: 10px;">
                <h4>Drivers Documents Report<h4>                      
              </div>

              

                @if($car_doc = '')@endif
                @if($car_doc_count = 1)@endif

                @foreach($car_documents as $doc)
                  @if(in_array($doc->id, @$document_id))                    
                    <?php 
                      $car_doc.='<div class="col-md-2">'.$car_doc_count.'. '.$doc->document_name.'</div>';
                      $car_doc_count++;
                    ?>
                  @endif
                @endforeach

                @if($driver_doc = '')@endif
                @if($driver_doc_count = 1)@endif
                @foreach($driver_documents as $doc2)
                  @if(in_array($doc2->id, @$document_id))
                    <?php 
                      $driver_doc.='<div class="col-md-2">'.$driver_doc_count.'. '.$doc2->document_name.'</div>';
                        $driver_doc_count++;
                    ?>
                  @endif
                @endforeach

              <div class="row">
                
                @if($car_doc !='')
                <div class="col-md-12">
                  <div class="col-md-2">
                    <label>Car Documents</label>
                  </div>
                   {!! $car_doc !!}
                </div>
                @endif

                @if($driver_doc !='')
                <div class="col-md-12">                
                  <div class="col-md-2">
                    <label>Personal Documents</label>
                  </div>
                  {!! $driver_doc !!}
                </div>
                @endif

              </div>
              
              @if(isset($list) AND is_object($list) and count($list) > 0)
               <div class="box-body">                  
                  <div class="row">                    
                    <div class="text-left col-md-6">
                      Page {{$list->currentPage()}} of {{$list->lastPage()}}
                    </div>
                    <div class="text-right col-md-6">
                      Showings records from {{$list->firstItem()}} to {{$list->lastItem()}} of total {{$list->total()}}
                    </div>
                  </div>

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
                      <th rowspan="2" class="middle" style="border-left : 1px solid #eee">Status</th>
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

                    @foreach($list as $i => $d)
                      <tr>
                        <td class="middle">{{$list->firstItem() + $i}}</td>
                        <td class="middle">{{$d->id}}</td>
                        <td class="middle">
                          <?php 
                            $user = Auth::guard('admin')->user();
                          ?>
                          @if(@$user->can('view_driver_profile'))
                            <a href="{{url(LOGIN_USER_TYPE.'/driver/profile/'.$d->id)}}" target="_blank">{{$d->driver_name}}</a>
                          @else 
                            {{$d->driver_name}}
                          @endif
                        </td>
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
                  <div class="text-center">
                    {{$list->appends(request()->input())->links()}}
                  </div>
               </div>
              @else
                <div class="aler alert-danger text-center">No record found</div>
              @endif
            @endif
            </div>
         </div>
      </div>
   </section>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$( ".date" ).datepicker({
     autoclose: true,
     todayHighlight: true,
     format: 'dd-mm-yyyy' 
});




$('#hub_id').on('change', function() {
  var hub_id = this.value;
  $.ajax({
    url: "{{url(LOGIN_USER_TYPE.'/hub_employee_ajax')}}",
    type:"POST",
    data:{
      id:hub_id,
      _token: "{{ csrf_token() }}"
    },
    success:function(response){
      if(response) {
        $('#employee_id').html(response);
      }
    },
  });
});


$("#checkAll").change(function () {
  $("input:checkbox").prop('checked', $(this).prop("checked"));
});

$(".chk").change(function () { 

  var uncheque = "";

  $('.chk').each(function () {
    if(!this.checked) uncheque ='1';
  });

  if(uncheque != "") $("#checkAll").prop('checked', false);
  else $("#checkAll").prop('checked', true);

}); 

</script>
@endpush
