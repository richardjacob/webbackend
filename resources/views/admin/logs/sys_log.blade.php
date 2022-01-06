@extends('admin.template')

@section('main')
 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>Logs <small>Sys Log</small></h1>
      <ol class="breadcrumb">
         <li>
            <a href="dashboard">
               <i class="fa fa-dashboard"></i> Home
            </a>
         </li>
         <li class="active">Logs <small>Sys Log</small></li>
         <li class="active">
            <a href="{{url(LOGIN_USER_TYPE.'/sys_log')}}">Sys Log</a>
         </li>
      </ol>
   </section>
   <!-- Main content -->
   <div class="row">
      <div class="col-md-6">
         <section class="content" style="background:#fff">
            <div class="box-body">
               <h3 style="text-align: center">Admin Server</h3>
               <table class="table">
                  <thead class="thead-light">
                     <tr>
                        <th>SL.#</th>
                        <th>File</th>
                        <th>Size (MB)</th>
                        <th colspan="2" class="text-center">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($dir as $key => $file)
                        @if (!$file->isDot())
                        <tr>
                           <td class="col-sm-1">{{($key+1)}}</td>
                           <td class="col-sm-7">{{$file}}</td>
                           <td class="col-sm-2">{{number_format(($file->getSize()/(1024*1024)),2)}}</td>
                           <td class="col-sm-1">
                              <a href="{{url('logs/'.$file)}}" target='_blank' class="btn btn-xs btn-primary">
                              <i class="fa fa-eye"></i>
                              View
                              </a>
                           </td>
                           
                           <td class="col-sm-1">
                              <a data-href="delete_log_file/{{$file}}" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#confirm-delete">
                                 <i class="glyphicon glyphicon-trash"></i>
                                 Delete
                              </a>
                           </td>
                        </tr>
                        @endif
                     @endforeach
                  </tbody>
               </table>
            </div>
      </section>
      </div>

      <div class="col-md-6">
         <section class="content" style="background:#fff">
            <div class="box-body">
               <h3 style="text-align: center">API Server</h3>
               <table class="table">
                  <thead class="thead-light">
                     <tr>
                        <th>SL.#</th>
                        <th>File</th>
                        <th>Size (MB)</th>
                        <th colspan="2" class="text-center">Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($api_log as $key => $file)
                        @if (@$file['name'] !='')
                        <tr>
                           <td class="col-sm-1">{{($key+1)}}</td>
                           <td class="col-sm-7">{{$file['name']}}</td>
                           <td class="col-sm-2">{{$file['size']}}</td>
                           <td class="col-sm-1">
                              <a href="//{{env('DOMAIN').'/logs/'.$file['name']}}" target='_blank' class="btn btn-xs btn-primary">
                              <i class="fa fa-eye"></i>
                              View
                              </a>
                           </td>
                           <td class="col-sm-1">
                              <a data-href="delete_api_log_file/{{$file['name']}}" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#confirm-delete">
                                 <i class="glyphicon glyphicon-trash"></i>
                                 Delete
                              </a>
                           </td>
                        </tr>
                        @endif
                     @endforeach
                  </tbody>
               </table>
            </div>
      </section>
      </div>
    </div>

</div>
@endsection

