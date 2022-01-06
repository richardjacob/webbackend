<!-- Add the sidebar's background. This div must be placed Immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>
</div>

<!-- jQuery 2.1.4 -->
<script src="{{ url('admin_assets/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<script src="{{ url('admin_assets/plugins/jQueryUI/jquery-ui.min.js') }}"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="{{ url('js/angular.js') }}"></script>
<script src="{{ url('js/angular-sanitize.js') }}"></script>

<script>
	var app = angular.module('App', ['ngSanitize']);
	var APP_URL = {!! json_encode(url('/')) !!}; 
	var COMPANY_ADMIN_URL = {!! json_encode(url('/'.LOGIN_USER_TYPE)) !!}; 
	var LOGIN_USER_TYPE = '{!! LOGIN_USER_TYPE !!}';
	var popup_code  = {!! session('error_code') ? session('error_code') : 0  !!};
	var STRIPE_PUBLISH_KEY = "{{ payment_gateway('publish','Stripe') }}";
</script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
	$.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Bootstrap 3.3.5 -->
<script src="{{ url('admin_assets/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ url('admin_assets/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ url('admin_assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
@if (!isset($exception))
@if (Route::current()->uri() == 'admin/dashboard' || Route::current()->uri() == 'company/dashboard')
<!-- Morris.js charts -->
<script src="{{ url('admin_assets/plugins/morris/raphael-min.js') }}"></script>
<script src="{{ url('admin_assets/plugins/morris/morris.min.js') }}"></script>
<!-- datepicker -->

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ url('admin_assets/dist/js/dashboard.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/add_user' || Route::current()->uri() == 'admin/edit_user/{id}')
<script src="{{ url('admin_assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/add_coupon_code' || Route::current()->uri() == 'admin/edit_coupon_code/{id}')
<script src="{{ url('admin_assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/driver' || Route::current()->uri() == 'admin/vehicle' || Route::current()->uri()
== 'company/vehicle' || Route::current()->uri() == 'admin/rider' || Route::current()->uri() == 'admin/admin_user' ||
Route::current()->uri() == 'admin/vehicle_type'|| Route::current()->uri() == 'admin/rating' || Route::current()->uri()
== 'company/rating' || Route::current()->uri() == 'admin/request' || Route::current()->uri() == 'company/request' ||
Route::current()->uri() == 'admin/cancel_trips' || Route::current()->uri() == 'company/cancel_trips' ||
Route::current()->uri() == 'admin/trips' || Route::current()->uri() == 'company/trips' || Route::current()->uri() ==
'admin/payments' || Route::current()->uri() == 'company/payments'|| Route::current()->uri() == 'admin/pages' ||
Route::current()->uri() == 'admin/metas' || Route::current()->uri() == 'admin/promo_code' || Route::current()->uri() ==
'admin/statements/{type}' || Route::current()->uri() == 'company/statements/{type}' || Route::current()->uri() ==
'admin/view_driver_statement/{driver_id}' || Route::current()->uri() == 'company/view_driver_statement/{driver_id}' ||
Route::current()->uri() == 'admin/currency' || Route::current()->uri() == 'admin/locations' || Route::current()->uri()
== 'admin/roles' || Route::current()->uri() == 'admin/manage_fare' || Route::current()->uri() == 'admin/language' ||
Route::current()->uri() == 'admin/help_category' || Route::current()->uri() == 'admin/help_subcategory' ||
Route::current()->uri() == 'admin/help' || Route::current()->uri() == 'admin/country' || Route::current()->uri() ==
'admin/payout/overall'|| Route::current()->uri() == 'admin/payout/driver_balance' || Route::current()->uri() ==
'company/payout/overall' || Route::current()->uri() == 'admin/payout/company/overall' || Route::current()->uri() ==
'admin/weekly_payout/{driver_id}' || Route::current()->uri() == 'company/weekly_payout/{driver_id}' ||
Route::current()->uri() == 'admin/weekly_payout/company/{company_id}' || Route::current()->uri() ==
'admin/per_week_report/{driver_id}/{start_date}/{end_date}' || Route::current()->uri() ==
'company/per_week_report/{driver_id}/{start_date}/{end_date}' || Route::current()->uri() ==
'admin/per_week_report/company/{company_id}/{start_date}/{end_date}' || Route::current()->uri() ==
'admin/per_day_report/{driver_id}/{date}' || Route::current()->uri() == 'company/per_day_report/{driver_id}/{date}' ||
Route::current()->uri() == 'admin/per_day_report/company/{company_id}/{date}' || Route::current()->uri() ==
'admin/later_booking' || Route::current()->uri() == 'company/later_booking' || Route::current()->uri() ==
'admin/company' || Route::current()->uri() == 'company/driver' || Route::current()->uri() == 'admin/cancel-reason' ||
Route::current()->uri() == 'admin/vehicle_make' || Route::current()->uri() == 'admin/vehicle_model' ||
Route::current()->uri() == 'admin/documents' || Route::current()->uri() == 'admin/support' ||
Route::current()->uri() == 'admin/manage_hub' ||
Route::current()->uri() == 'admin/best_driver' ||
Route::current()->uri() == 'admin/manage_employee'||
Route::current()->uri() == 'admin/bonuse'||
Route::current()->uri() == 'hub/employee_list'||
Route::current()->uri() == 'hub/acquisition_list'||
Route::current()->uri() == 'admin/owe_trip_list/{company_or_driver}/{id}' ||
Route::current()->uri() == 'company/owe_trip_list/{company_or_driver}/{id}' ||
Route::current()->uri() == 'admin/company_driver_list/{id}' ||
Route::current()->uri() == 'hub/hub_acquisition_driver/{id}/{all?}' ||
Route::current()->uri() == 'hub/hub_acquisition_driver' ||
Route::current()->uri() == 'admin/rider_offer/cash_back' ||
Route::current()->uri() == 'admin/rider_offer/referral_bonus' ||
Route::current()->uri() == 'admin/driver_offer/signing_bonus' ||
Route::current()->uri() == 'admin/driver_offer/trip_bonus'||
Route::current()->uri() == 'admin/driver_offer/online_bonus' ||
Route::current()->uri() == 'admin/driver_offer/referral_bonus' ||

Route::current()->uri() == 'company/driver_offer/signing_bonus' ||
Route::current()->uri() == 'company/driver_offer/trip_bonus'||
Route::current()->uri() == 'company/driver_offer/online_bonus' ||
Route::current()->uri() == 'company/driver_offer/referral_bonus' ||

Route::current()->uri() == 'admin/activity_log' ||
Route::current()->uri() == 'admin/audit_log' ||
Route::current()->uri() == 'admin/sys_log' ||

Route::current()->uri() == 'admin/drivers_remarks/{remarks_status?}' ||
Route::current()->uri() == 'company/drivers_remarks/{remarks_status?}' ||
Route::current()->uri() == 'hub/drivers_remarks/{remarks_status?}' ||

Route::current()->uri() == 'admin/view_drivers_remarks/{id}' ||
Route::current()->uri() == 'company/view_drivers_remarks/{id}' ||
Route::current()->uri() == 'hub/view_drivers_remarks/{id}' ||

Route::current()->uri() == 'admin/manage_peak_hour' ||
Route::current()->uri() == 'admin/monitor_camera' ||
Route::current()->uri() == 'admin/hub_acquisition_list' ||
Route::current()->uri() == 'admin/sos_messages' ||
Route::current()->uri() == 'company/payout_preference' ||
Route::current()->uri() == 'company/dues' ||
Route::current()->uri() == 'admin/payout/company_balance' ||
Route::current()->uri() == 'company/transaction_history/paid_to_alesha' ||
Route::current()->uri() == 'company/transaction_history/balance_withdraw' ||
Route::current()->uri() == 'company/transaction_history/payout' ||
Route::current()->uri() == 'admin/company/transaction_history/paid_to_alesha/{company_id}' ||
Route::current()->uri() == 'admin/company/transaction_history/balance_withdraw/{company_id}' ||
Route::current()->uri() == 'admin/company/transaction_history/payout/{company_id}' ||

Route::current()->uri() == 'admin/complain_category' ||
Route::current()->uri() == 'admin/complain_sub_category' ||
Route::current()->uri() == 'admin/complain_list' ||
Route::current()->uri() == 'admin/contact_list'




)


<script src="{{ url('admin_assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('admin_assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/add_room' || Route::current()->uri() == 'admin/edit_room/{id}' ||
Route::current()->uri() == 'admin/edit_rider/{id}' || Route::current()->uri() == 'admin/add_rider' ||
Route::current()->uri() == 'admin/edit_page/{id}' || Route::current()->uri() == 'admin/add_page/{id}' ||
Route::current()->uri() == 'admin/later_booking' || Route::current()->uri() == 'company/later_booking' ||
Route::current()->uri() == 'admin/add_company' || Route::current()->uri() == 'admin/edit_company/{id}' ||
Route::current()->uri() == 'admin/company' || Route::current()->uri() == 'company/edit_company/{id}')
<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key={{$map_key}}&sensor=false&libraries=places"></script>
<script src="{{ url('admin_assets/plugins/jQuery/jquery.validate.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/add_vehicle' || Route::current()->uri() == 'admin/edit_vehicle/{id}' ||
Route::current()->uri() == 'company/add_vehicle' || Route::current()->uri() == 'company/edit_vehicle/{id}' ||
Route::current()->uri() == 'admin/add-vehicle-make' || Route::current()->uri() == 'admin/edit-vehicle-make/{id}')
<script src="{{ url('admin_assets/plugins/jQuery/jquery.validate.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/trips' || Route::current()->uri() == 'admin/payments')
<script src="{{ url('admin_assets/dist/js/reports.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/add_page' || Route::current()->uri() == 'admin/edit_page/{id}' ||
Route::current()->uri() == 'admin/send_email' || Route::current()->uri() == 'admin/add_help' || Route::current()->uri()
== 'admin/edit_help/{id}')
<script src="{{ url('admin_assets/plugins/editor/editor.js') }}"></script>
<script type="text/javascript">
	$("[name='submit']").click(function(){
				$('#content').text($('#txtEditor').Editor("getText"));
				$('#message').text($('#txtEditor').Editor("getText"));
				$('#answer').text($('#txtEditor').Editor("getText"));
			});
</script>
@endif

@if (Route::current()->uri() == 'admin/map' || Route::current()->uri() == 'company/map' || Route::current()->uri() ==
'admin/detail_request/{id}' || Route::current()->uri() == 'company/detail_request/{id}')
<script async defer type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key={{ $map_key }}&sensor=false&callback=initMap&libraries=geometry">
</script>
@endif

@if (Route::current()->uri() == 'admin/heat-map' || Route::current()->uri() == 'company/heat-map')
<script async defer type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key={{ $map_key }}&libraries=visualization"></script>
<script src="{{ url('admin_assets/dist/js/heat_map.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/map' || Route::current()->uri() == 'company/map')
<script src="{{ url('admin_assets/dist/js/map.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/manual_booking/{id?}' || Route::current()->uri() ==
'company/manual_booking/{id?}')
<script type="text/javascript"
	src="https://maps.googleapis.com/maps/api/js?key={{$map_key}}&sensor=false&libraries=places"></script>
<script src="{{ url('admin_assets/dist/js/manual_booking.js') }}"></script>
<script src="{{ url('admin_assets/dist/js/moment.min.js') }}"></script>
<script src="{{ url('admin_assets/dist/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{ url('js/selectize.js') }}"></script>
<script src="{{ url('admin_assets/plugins/jQuery/jquery.validate.js') }}"></script>
<script src="{{ url('admin_assets/dist/js/jquery.multiselect.js') }}"></script>
@endif

@if (Route::current()->uri() == 'admin/detail_request/{id}' || Route::current()->uri() == 'company/detail_request/{id}')
<script src="{{ url('admin_assets/dist/js/request.js?v='.$version) }}"></script>
@endif

@if (Route::current()->uri() == 'admin/add_location' || Route::current()->uri() == 'admin/edit_location/{id}')
<script src="https://maps.googleapis.com/maps/api/js?key={{$map_key}}&libraries=drawing,places,geometry"></script>
@endif

@if (Route::current()->uri() == 'admin/add_manage_fare' || Route::current()->uri() == 'admin/edit_manage_fare/{id}' ||
Route::current()->uri() == 'admin/add_company' || Route::current()->uri() == 'admin/edit_company/{id}' ||
Route::current()->uri() == 'company/edit_company/{id}')
<script src="{{ url('admin_assets/dist/js/moment.min.js') }}"></script>
@endif

@if(Route::current()->uri() == 'admin/referral_settings')
<script src="{{ url('admin_assets/bootstrap/js/bootstrap-toggle.min.js') }}"></script>
@endif

@endif

<!-- AdminLTE App -->
<script src="{{ url('admin_assets/dist/js/demo.js') }}"></script>
<script src="{{ url('admin_assets/dist/js/app.js') }}"></script>
<script src="{{ url('admin_assets/dist/js/common.js?v='.@$version) }}"></script>
@if (Route::current()->uri() == 'company/payout_preferences')
{!! Html::script('js/common.js?v='.$version) !!}
@endif

@stack('scripts')

<script type="text/javascript">
	$(document).ready(function() {
    if(popup_code == 1) {
      $('#payout_popup').modal('show');
    }

    $("#input_mobile").keydown(function() {
      $('.box-footer .btn').removeAttr('disabled');
    });


  });
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
</script>

<script type="text/javascript">
	function googleTranslateElementInit()
	{
		var placeholders = document.querySelectorAll('[placeholder]');

		if (placeholders.length) {
			// convert to array
			placeholders = Array.prototype.slice.call(placeholders);

			// copy placeholder text to a hidden div
			var div = $('<div id="placeholders" style="visibility:hidden;"></div>');

			placeholders.forEach(function(input) {
				var text = input.placeholder;
				div.append('<div>' + text + '</div>');    
			});

			$('body').append(div);

			new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');

		}
		if(placeholders[0] == undefined) {
			var originalPH = '';
		}
		else {
			var originalPH = placeholders[0].placeholder; 
		}
	    // save the first placeholder in a closure

	    // check for changes and update as needed
	    setInterval(function() {
	    	if (isTranslated() || $('.goog-te-combo').val() == 'en' || $('.goog-te-combo').val() == '') {
	    		updatePlaceholders();
	    		originalPH = placeholders[0].placeholder;
	    	}
	    }, 500);
	    
	    // hoisted
	    function isTranslated()
	    {
	    	var currentPH = $($('#placeholders > div')[0]).text();
	    	return !(originalPH == currentPH);
	    }
	    
	    function updatePlaceholders()
	    {
	    	$('#placeholders > div').each(function(i, div){
	    		placeholders[i].placeholder = $(div).text();
	    	});
	    	$('#placeholders').hide();
	    }
	}

	function preventBack()
	{
		previous_url = document.referrer.substr(document.referrer.lastIndexOf('/') + 1)
		if (previous_url == "signin" || previous_url == "" || previous_url == "signin_company") {
			window.history.forward();
		}
	}
	setTimeout("preventBack()", 0);

</script>
<!-- get in tuch pop_up_email -->

@if(CheckGetInTuchpopup())
<script src="{{ asset('admin_assets/plugins/jQuery/jquery.validate.js') }}"></script>
{!! Html::script('js/pop_up_email.js?v='.$version) !!}
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer>
</script>
@endif


<!-- get in tuch pop_up_email -->
@if(CheckGetInTuchpopup())
@include('popup_email')
@endif

<script type="text/javascript">
	function printPageArea(areaID){
	    var prtContent = document.getElementById(areaID);
		var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=800,toolbar=0,scrollbars=0,status=0');
		WinPrint.document.write('<html><head>');
		WinPrint.document.write('<link rel="stylesheet" href="{{ url("admin_assets/bootstrap/css/bootstrap.min.css") }}">');
		WinPrint.document.write('<link rel="stylesheet" href="{{ url("admin_assets/dist/css/AdminLTE.css") }}">');
		WinPrint.document.write('<style>.col-sm-5,.col-sm-6{float:left}</style>');
		WinPrint.document.write('</head><body onload="print();close();">');
		WinPrint.document.write(prtContent.innerHTML);
		WinPrint.document.write('</body></html>');
		WinPrint.document.close();
		WinPrint.focus();
	}

	function make_payout_modal(account_number,amount,type,driver_id,payout_method,redirect_url,payout_text,start_date,end_date,day,trip_id){
		$('#payout_modal').modal('show'); 
		$('#payout_modal #start_date').val('');
		$('#payout_modal #end_date').val('');
		$('#payout_modal #amount').val('');		

		$('#payout_modal #label_account_nember').html(account_number);
		$('#payout_modal #amount').val(amount);
		$('#payout_modal #type').val(type);
		$('#payout_modal #driver_id').val(driver_id);
		$('#payout_modal #redirect_url').val(redirect_url);
		$('#payout_modal #start_date').val(start_date);
		$('#payout_modal #end_date').val(end_date);
		$('#payout_modal #day').val(day);
		$('#payout_modal #trip_id').val(trip_id);
		$('#payout_modal #payout_text').html(payout_text);	
		$('#payout_modal #payout_method').html(payout_method);		
	}

	
	function make_payout_modal_company(account_number,amount,type,company_id,payout_method,redirect_url,payout_text,start_date,end_date,day,trip_id){
		$('#payout_modal_company').modal('show'); 
		$('#payout_modal_company #start_date').val('');
		$('#payout_modal_company #end_date').val('');
		$('#payout_modal_company #amount').val('');		

		$('#payout_modal_company #label_account_nember').html(account_number);
		$('#payout_modal_company #amount').val(amount);
		$('#payout_modal_company #type').val(type);
		$('#payout_modal_company #company_id').val(company_id);
		$('#payout_modal_company #redirect_url').val(redirect_url);
		$('#payout_modal_company #start_date').val(start_date);
		$('#payout_modal_company #end_date').val(end_date);
		$('#payout_modal_company #day').val(day);
		$('#payout_modal_company #trip_id').val(trip_id);
		$('#payout_modal_company #payout_text').html(payout_text);	
		$('#payout_modal_company #payout_method').html(payout_method);

		
	}


	function make_bonus_payout_modal(account_number,balance_id,bonus_id,user_id,amount,payout_method,redirect_url,payout_text){
		$('#bonus_payout_modal').modal('show'); 
		$('#bonus_payout_modal #tr_no').val('');

		$('#bonus_payout_modal #label_account_nember').html(account_number);
		$('#bonus_payout_modal #amount').val(amount);
		$('#bonus_payout_modal #balance_id').val(balance_id);
		$('#bonus_payout_modal #bonus_id').val(bonus_id);
		$('#bonus_payout_modal #user_id').val(user_id);
		$('#bonus_payout_modal #redirect_url').val(redirect_url);
		$('#bonus_payout_modal #payout_text').html(payout_text);	
		$('#bonus_payout_modal #payout_method').html(payout_method);	
	}

	function owe_trip_list_modal(id,name,company_or_driver){
		$('#owe_trip_list_modal').modal('show'); 
		$('#owe_trip_list_modal #company_driver_name').html(name);

		$.ajax({url: "owe_trip_list/"+company_or_driver+"/"+id, success: function(result){
		    $('#owe_trip_list_modal tbody').html(result);
		}});
			
	}




</script>

<script src="{{ url('admin_assets/plugins/datetimepicker/build/jquery.datetimepicker.full.js') }}"></script>

<script>
	jQuery(document).ready(function () {
        'use strict';
        jQuery('#conversation_date').datetimepicker(); 
        jQuery('#followup_date').datetimepicker();        
    });
</script>

<script type="text/javascript">
	$('#input_start_time').on('change', function() {
		var start_index =$("#input_start_time")[0].selectedIndex;

		var index = 0;
		$('#input_end_time option').each(function(index) {
		    if(index <= start_index) {
		        $(this).attr('disabled', true);
		    }else $(this).attr('disabled', false);
		});
	});

	function validate(evt) {
	  var theEvent = evt || window.event;

	  // Handle paste
	  if (theEvent.type === 'paste') {
	      key = event.clipboardData.getData('text/plain');
	  } else {
	  // Handle key press
	      var key = theEvent.keyCode || theEvent.which;
	      key = String.fromCharCode(key);
	  }
	  var regex = /[0-9]|\./;
	  if( !regex.test(key) ) {
	    theEvent.returnValue = false;
	    if(theEvent.preventDefault) theEvent.preventDefault();
	  }
	}


	$("#driver_check_nid").click(function () { 
		var nid_number =  $('#nid_number').val();
		var user_id =$(this).data('id');
		if(nid_number.length == 10 || nid_number.length == 13 || nid_number.length == 17){
		
		  $.ajax({
		    url: "{{url('admin/ajax/verify_nid')}}",
		    type:"POST",
		    data:{
		      nid:nid_number,
		      user_id:user_id,  
		      _token: "{{ csrf_token() }}"
		    },
		    success:function(data){
		      if(data == 'Success') {
		        $('#driver_check_nid').removeClass('text-primary');
		        $('#driver_check_nid').removeClass('fa-refresh');
		        $('#driver_check_nid').addClass('fa-check');
		        $('#driver_check_nid').addClass('text-success');            
		        $('#nid_number').attr('disabled', 'disabled'); 
		      }else{
				  if(data == '') alert("NID Server Error. Please try later. ");
		          else alert(data);
		      }
		    },
		  });
		}else{
		  alert("NID Number must be 10, 13 or 17 digits.");
		}
	});




</script>



</body>

</html>