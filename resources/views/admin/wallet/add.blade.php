@extends('admin.template')

@section('main')
<style> 
    #suggesstion_list{float:left;list-style:none;margin-top:-3px;padding:0;width:250px;position:absolute;z-index:999999}
    #suggesstion_list li{padding:10px;background:#f0f0f0;border-bottom:#bbb9b9 1px solid;border-left:#bbb9b9 1px solid;border-right:#bbb9b9 1px solid}
    #suggesstion_list li:hover{background:#ece3d2;cursor:pointer}
    #search-box{padding:10px;border:#a8d4b1 1px solid;border-radius:4px}
</style>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Manage Balance & Promo <small>Add Balance Amount - {{ $user_type }}</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/wallet/'.$user_type) }}">Balance</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/wallet/add/'.$user_type) }}">Add</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12 col-sm-offset-0">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Add Balance Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => route('add_wallet',['user_type' => $user_type]), 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="user_id" class="col-sm-3 control-label">Username<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('user_id1', '', ['class' => 'form-control search-box', 'id' => 'rider_info', 'data-id' => 'search-box', 'placeholder' => 'Enter Rider Name or Mobile Number', 'autocomplete' => 'off', 'ng-model' => 'user_id1', 'onKeyup' => 'suggestion(this.value)' ]) !!}
                    <input type="hidden" id="search_user" name="user_id">
                    <span class="text-danger">{{ $errors->first('user_id') }}</span>
                    <div id="suggesstion-box"></div>
                  </div>
               </div>
                
                <div class="form-group">
                  <label for="input_amount" class="col-sm-3 control-label">Amount<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('amount', '', ['class' => 'form-control', 'id' => 'input_amount', 'placeholder' => 'Amount']) !!}
                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_currency_code" class="col-sm-3 control-label">Currency code<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::select('currency_code', $currency, '', ['class' => 'form-control', 'id' => 'input_currency_code', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('currency_code') }}</span>
                  </div>
                </div>
              
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
               <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
                 <button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
              </div>
              <!-- /.box-footer -->
            {!! Form::close() !!}
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
@endsection

<script type="text/javascript">
  function suggestion(keyword){
      if(keyword.length >=3){
        var url = "{{url('admin/ballance_suggestion')}}?keywords="+keyword;

        $.ajax({
          type:'GET',
          url:url,
          beforeSend:function(){
            $("#rider_info").css("background","#eee");
          },
          success:function(data){
            $("#suggesstion-box").show();
            $("#suggesstion-box").html(data);
            $("#rider_info").css("background","#FFF");
          }        
        });
      }
  }

  function select_from_suggestion(label, val){
    $("#rider_info").val(label);
    $("#suggesstion-box").hide();
    $("#search_user").val(val);

    
  }

</script>
