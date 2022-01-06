@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Contact <small>Tracking</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Contact</li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/contact_list') }}">Contact List</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/tracking_movement_contact/'.$result->id) }}">Tracking</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12 col-md-offset-0">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
                <div class="col-sm-11">
                    <h3 class="box-title">Contact Tracking Details</h3>
                </div>
                <div class="col-sm-1 text-right">
                    @if(auth('admin')->user()->can('movement_contact'))
                    <a href="{{ url(LOGIN_USER_TYPE.'/movement_contact/'.$result->id) }}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus"></i></a>
                    @endif
                </div>
            </div>
            <!-- /.box-header -->
              <div class="box-body" id="print">
             
                    <table class="table table-bordered">                
                      <tr>
                        <td class="col-sm-2">Message :</td>
                        <td class="col-sm-10" colspan="3">{{ $result->msg }}
                      </tr>

                      <tr>
                        <td class="col-sm-2">Name :</td>
                        <td class="col-sm-4">{{ $result->name }}</td>

                        <td class="col-sm-2">Email :</td>
                        <td class="col-sm-4">{{ $result->email }}</td>
                      </tr>

                      <tr>
                        <td>Mobile :</td>
                        <td>{{ $result->mobile }}</td>

                        <td>Contact for :</td>
                        <td>{{ $result->contact_for }}</td>
                      </tr>

                      <tr>
                        <td class="col-sm-2">Status :</td>
                        <td class="col-sm-4">{{ $result->status }}</td>

                        <td class="col-sm-2">Contact Time :</td>
                        <td class="col-sm-4">{{ date(" h:i a, d M Y", strtotime($result->created_at)) }}</td>
                      </tr>
                    </table>
                </table>
            
                <table class="table table-bordered" style="margin-top:50px !important;">
                    <tr>
                        <th>SL.#</th>
                        <th>Resolve/ Processing by</th>
                        <th>Processing in Details</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Entry Time</th>
                    </tr>
                    @foreach($movement as $key => $data)
                        <tr>
                            <td>{{ ($key+1) }}</td>
                            <td>{{  $data->process_by }}</td>
                            <td>{{  $data->process }}</td>
                            <td>{{  $data->remarks }}</td>
                            <td>
                                @if($data->status == '1') Completed
                                @elseif($data->status == '2') Processing
                                @endif
                            </td>
                            <td>{{ date(" h:i a, d M Y", strtotime($data->created_at)) }}</td>
                        </tr>
                    @endforeach
                </table>                

              </div>
              <!-- /.box-body -->
              <div class="text-center" style="padding: 10px;">
                <button name="print" value="Print" class="btn btn-success" onclick="_print()">
                    <i class="fa fa-print"></i> Print
                </button>
              </div>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<script src="{{ url('js/print.js') }}"></script>
@stop
