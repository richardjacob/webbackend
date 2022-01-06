
<style>
.active span{
	/*background-color:#fff !important; 
	color:#000 !important;
	padding:10px 50px 10px 10px  */
}
</style>

<aside class="main-sidebar">
	<section class="sidebar">
		<div class="user-panel">
			<div class="pull-left image">
				@php
				if(LOGIN_USER_TYPE=='company'){
				$user = Auth::guard('company')->user();
				$company_user = true;
				$hub_user = false;
				$first_segment = 'company';
				}
				else if(LOGIN_USER_TYPE=='hub'){
				$user = Auth::guard('hub')->user();
				$company_user = false;
				$hub_user = true;
				$first_segment = 'hub';
				}
				else{
				$user = Auth::guard('admin')->user();
				$company_user = false;
				$hub_user = false;
				$first_segment = 'admin';
				}
				@endphp
				@if(!$company_user || $user->profile ==null)
				<img src="{{ url('admin_assets/dist/img/avatar04.png') }}" class="img-circle" alt="User Image">
				@else
				<img src="{{ $user->profile }}" class="img-circle" alt="User Image">
				@endif
			</div>
			<div class="pull-left info">
				<p>{{ (!$company_user)?((!$hub_user)?$user->username:$user->employee_name):$user->name }}</p>
				<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>
		<ul class="sidebar-menu">
			<li class="header">MAIN NAVIGATION</li>
			<li class="{{ (Route::current()->uri() == $first_segment.'/dashboard') ? 'active' : ''  }}"><a
					href="{{ url($first_segment.'/dashboard') }}"><i
						class="fa fa-dashboard"></i><span>Dashboard</span></a></li>

			@if($company_user)
			<li class="{{ (Route::current()->uri() == $first_segment.'/owe') ? 'active' : ''  }}">
				<a href="{{ url($first_segment.'/payout_preference') }}"><i class="fa fa-money"></i><span>Payout
						Preference</span></a>
			</li>
			@endif


			@if(LOGIN_USER_TYPE=='admin' OR LOGIN_USER_TYPE=='company')
			@if(@$user->can('manage_admin'))
			<li class="treeview {{ (
			Route::current()->uri() == 'admin/admin_user' ||
			Route::current()->uri() == 'admin/add_admin_user' ||
			Route::current()->uri() == 'admin/edit_admin_users/{id}' ||
			Route::current()->uri() == 'admin/roles' ||
			Route::current()->uri() == 'admin/add_role' ||
			Route::current()->uri() == 'admin/edit_role/{id}'


			) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-user-plus"></i> <span>Manage Admin</span> <i
						class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li class="{{ (
					Route::current()->uri() == 'admin/admin_user' ||
					Route::current()->uri() == 'admin/add_admin_user' ||					
					Route::current()->uri() == 'admin/edit_admin_users/{id}'
					) ? 'active' : ''  }}"><a
							href="{{ url('admin/admin_user') }}"><i class="fa fa-circle-o"></i><span>Admin
								Users</span></a></li>
					<li class="{{ (
					Route::current()->uri() == 'admin/roles' ||
					Route::current()->uri() == 'admin/add_role' ||
					Route::current()->uri() == 'admin/edit_role/{id}'
					) ? 'active' : ''  }}"><a
							href="{{ url('admin/roles') }}"><i class="fa fa-circle-o"></i><span>Roles &
								Permissions</span></a></li>
				</ul>
			</li>
			@endif

			<!--@if($company_user && $user->id != 1)-->
			<!--<li class="{{ (Route::current()->uri() == $first_segment.'/payout_preferences') ? 'active' : ''  }}"><a href="{{ url($first_segment.'/payout_preferences') }}"><i class="fa fa-paypal"></i><span>Payout Preferences</span></a></li>-->
			<!--@endif-->


			@if(@$user->can('view_company'))
			<li class="{{ (
					Route::current()->uri() == 'admin/company' ||
					Route::current()->uri() == 'admin/add_company' ||
					Route::current()->uri() == 'admin/edit_company/{id}' ||
					Route::current()->uri() == 'admin/company/transaction_history/paid_to_alesha/{company_id}' ||
					Route::current()->uri() == 'admin/company/transaction_history/balance_withdraw/{company_id}' ||
					Route::current()->uri() == 'admin/company/transaction_history/payout/{company_id}'					
					) ? 'active' : ''  }}">
				<a href="{{ url('admin/company') }}"><i class="fa fa-building"></i><span>Manage Company</span></a>
			</li>
			@endif


			<!-- @if(@$user->can('view_company'))
				<li class="treeview {{ (
					Route::current()->uri() == 'admin/company' ||
					Route::current()->uri() == 'admin/company/transaction_history/paid_to_alesha' ||
					Route::current()->uri() == 'admin/company/transaction_history/balance_withdraw' ||
					Route::current()->uri() == 'admin/company/transaction_history/payout'					
					) ? 'active' : ''  }}">
					<a href="#">
					<i class="fa fa-building"></i> <span>Manage Company</span> <i class="fa fa-angle-left pull-right"></i>
					</a>
					<ul class="treeview-menu">
						@if(@$user->can('view_company'))
						<li class="{{ (Route::current()->uri() == 'admin/company') ? 'active' : ''  }}">
							<a href="{{ url('admin/company') }}"><i class="fa fa-circle-o"></i> <span>Manage Company</span></a>
						</li>
						@endif

						@if(@$user->can('paid_to_alesha'))
						<li class="{{ (Route::current()->uri() == 'admin/company/transaction_history/paid_to_alesha') ? 'active' : ''  }}">
							<a href="{{ url('admin/company/transaction_history/paid_to_alesha') }}"><i class="fa fa-circle-o"></i> <span>Paid to Alesha</span></a>
						</li>
						@endif

						@if(@$user->can('balance_withdraw'))
						<li class="{{ (Route::current()->uri() == 'admin/company/transaction_history/balance_withdraw') ? 'active' : ''  }}">
							<a href="{{ url('admin/company/transaction_history/balance_withdraw') }}"><i class="fa fa-circle-o"></i> <span>Balance Withdraw</span></a>
						</li>
						@endif

						@if(@$user->can('payout'))
						<li class="{{ (Route::current()->uri() == 'admin/company/transaction_history/payout') ? 'active' : ''  }}">
							<a href="{{ url('admin/company/transaction_history/payout') }}"><i class="fa fa-circle-o"></i> <span>Payout</span></a>
						</li>
						@endif
					</ul>
				</li>
				@endif -->

			<!-- @if(@$user->can('view_company'))
				<li class="{{ (Route::current()->uri() == 'admin/company') ? 'active' : ''  }}"><a href="{{ url('admin/company') }}"><i class="fa fa-building"></i><span>Manage Company</span></a></li>
				@endif -->

			<?php /*
				@if($company_user || @$user->can('view_driver'))
				<li class="{{ (Route::current()->uri() == $first_segment.'/driver') ? 'active' : ''  }}"><a href="{{ url($first_segment.'/driver') }}"><i class="fa fa-dribbble"></i><span>Manage Driver</span></a></li>
				@endif
	 			*/ ?>


			@if($company_user ||
			@$user->can('view_driver') ||
			@$user->can('view_monitor_camera') ||
			@$user->can('add_monitor_camera') ||
			@$user->can('edit_monitor_camera') ||
			@$user->can('car_acquisition') ||
			@$user->can('drivers_documents') ||

			@$user->can('driver_status_all_documents') ||
			@$user->can('driver_status_checked') ||
			@$user->can('driver_status_verified') ||
			@$user->can('driver_status_trained') ||
			@$user->can('driver_status_active') ||
			@$user->can('owner') ||
			@$user->can('partner') ||
			@$user->can('drivers_under_partner') ||
			@$user->can('uncheck_owner_driver') ||
			@$user->can('otp') ||

			@$user->can('driver_status_all_documents_print') ||
			@$user->can('driver_status_checked_print') ||
			@$user->can('driver_status_verified_print') ||
			@$user->can('driver_status_trained_print') ||
			@$user->can('driver_status_active_print')
			)
			<!-- removed || $hub_user  -->
			<li class="treeview {{ (
				Route::current()->uri() == 'admin/driver' || 
				Route::current()->uri() == 'admin/add_driver' || 
				Route::current()->uri() == 'company/driver' ||

				Route::current()->uri() == 'admin/edit_driver/{id}' || 

				Route::current()->uri() == 'admin/drivers_remarks' ||
				Route::current()->uri() == 'company/drivers_remarks' ||

				Route::current()->uri() == 'admin/monitor_camera' ||

				Route::current()->uri() == 'admin/drivers_remarks/{remarks_status?}' ||
				Route::current()->uri() == 'company/drivers_remarks/{remarks_status?}' ||

				Route::current()->uri() == 'admin/view_drivers_remarks/{id}' ||
				Route::current()->uri() == 'company/view_drivers_remarks/{id}' ||

				Route::current()->uri() == 'admin/edit_drivers_remarks/{id}' ||
				Route::current()->uri() == 'company/edit_drivers_remarks/{id}' ||

				Route::current()->uri() == 'admin/add_drivers_remarks/{id?}' ||
				Route::current()->uri() == 'company/add_drivers_remarks/{id?}' ||

				Route::current()->uri() == 'admin/add_monitor_camera' ||
				Route::current()->uri() == 'admin/edit_monitor_camera/{id}' ||

				Route::current()->uri() == 'admin/car_acquisition' ||
				Route::current()->uri() == 'admin/drivers_documents' ||
				Route::current()->uri() == 'admin/drivers_documents/only_checked_documents' ||

				Route::current()->uri() == 'admin/driver_status/all_documents' ||
				Route::current()->uri() == 'admin/driver_status/checked' ||
				Route::current()->uri() == 'admin/driver_status/verified' ||
				Route::current()->uri() == 'admin/driver_status/trained' ||
				Route::current()->uri() == 'admin/driver_status/active' ||
				Route::current()->uri() == 'admin/driver_status/owner' ||
				Route::current()->uri() == 'admin/driver_status/partner' ||
				Route::current()->uri() == 'admin/driver_status/drivers_under_partner' ||
				Route::current()->uri() == 'admin/driver_status/uncheck_owner_driver' ||

				Route::current()->uri() == 'admin/driver_status/all_documents' ||
				Route::current()->uri() == 'admin/driver_status_print/checked' ||
				Route::current()->uri() == 'admin/driver_status_print/verified' ||
				Route::current()->uri() == 'admin/driver_status_print/trained' ||
				Route::current()->uri() == 'admin/driver_status_print/active' ||
				Route::current()->uri() == 'admin/otp'
				

				) ? 'active' : ''  }}">

				<!-- removed || $hub_user  
					Route::current()->uri() == 'hub/driver' ||
					Route::current()->uri() == 'hub/edit_driver/{id}' ||
					Route::current()->uri() == 'hub/drivers_remarks' ||
					Route::current()->uri() == 'hub/drivers_remarks/{remarks_status?}' ||
					Route::current()->uri() == 'hub/view_drivers_remarks/{id}' ||
					Route::current()->uri() == 'hub/edit_drivers_remarks/{id}' ||
					Route::current()->uri() == 'hub/add_drivers_remarks/{id?}'
				-->


				<a href="#">
					<i class="fa fa-user-plus"></i> <span>Manage Driver</span> <i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if($company_user || $hub_user || @$user->can('view_driver'))
					<li class="{{ (
							Route::current()->uri() == $first_segment.'/driver' ||
							Route::current()->uri() == 'admin/add_driver' || 
							Route::current()->uri() == $first_segment.'/edit_driver/{id}'
							) ? 'active' : ''  }}">

						<a href="{{ url($first_segment.'/driver') }}">
							<i class="fa fa-circle-o"></i>
							<span>Driver</span>
						</a>
					</li>
					@endif

					@if(@$user->can('drivers_remarks') || @$user->can('view_drivers_remarks') ||
					@$user->can('edit_drivers_remarks'))

					<!-- removed $hub_user || -->
					<li class="{{ (
							Route::current()->uri() == $first_segment.'/drivers_remarks/{remarks_status?}' || 
							Route::current()->uri() == $first_segment.'/edit_drivers_remarks/{id}' || 
							Route::current()->uri() == $first_segment.'/view_drivers_remarks/{id}' || 
							Route::current()->uri() == $first_segment.'/add_drivers_remarks/{id?}') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/drivers_remarks/0') }}">
							<i class="fa fa-circle-o"></i>
							<span>Driver's Remarks</span>
						</a>
					</li>
					@endif

					@if($hub_user || @$user->can('add_monitor_camera') || @$user->can('view_monitor_camera') ||
					@$user->can('edit_monitor_camera'))
					<li class="{{ ( 
							Route::current()->uri() == $first_segment.'/monitor_camera' || 
							Route::current()->uri() == $first_segment.'/edit_monitor_camera/{id}' || 
							Route::current()->uri() == $first_segment.'/add_monitor_camera') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/monitor_camera') }}">
							<i class="fa fa-circle-o"></i>
							<span>Monitor And Camera</span>
						</a>
					</li>
					@endif

					@if(@$user->can('car_acquisition'))
					<li class="{{(Route::current()->uri() == 'admin/car_acquisition') ? 'active' : ''}}">
						<a href="{{ url('admin/car_acquisition') }}">
							<i class="fa fa-car"></i>
							<span>Car Acquistion</span>
						</a>
					</li>
					@endif

					@if($user->can('drivers_documents'))
					<li class="{{ ( 
							Route::current()->uri() == $first_segment.'/drivers_documents' || Route::current()->uri() == $first_segment.'/drivers_documents/only_checked_documents'
							) ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/drivers_documents') }}">
							<i class="fa fa-file"></i>
							<span>Drivers Documents</span>
						</a>
					</li>
					@endif

					<!-- @if($user->can('driver_status'))
						<li class="{{ ( 
							Route::current()->uri() == $first_segment.'/driver_status/{type}'
							) ? 'active' : ''  }}">
							<a href="{{ url($first_segment.'/driver_status/all_documents') }}">
								<i class="fa fa-battery-half"></i>
								<span>Driver Status</span>
							</a>
						</li>
						@endif -->


					@if(
					@$user->can('driver_status_all_documents') ||
					@$user->can('driver_status_checked') ||
					@$user->can('driver_status_verified') ||
					@$user->can('driver_status_trained') ||
					@$user->can('driver_status_active') ||
					@$user->can('driver_status_all_documents_print')||
					@$user->can('driver_status_checked_print') ||
					@$user->can('driver_status_verified_print') ||
					@$user->can('driver_status_trained_print')||
					@$user->can('driver_status_active_print')
					)

					@php
					if(@$user->can('driver_status_all_documents')) $driver_status_type = "all_documents";
					elseif(@$user->can('driver_status_checked')) $driver_status_type = "checked";
					elseif(@$user->can('driver_status_verified')) $driver_status_type = "verified";
					elseif(@$user->can('driver_status_trained')) $driver_status_type = "trained";
					elseif(@$user->can('driver_status_active')) $driver_status_type = "active";
					elseif(@$user->can('driver_status_all_documents_print')) $driver_status_type = "all_documents";
					elseif(@$user->can('driver_status_checked_print')) $driver_status_type = "checked";
					elseif(@$user->can('driver_status_verified_print')) $driver_status_type = "verified";
					elseif(@$user->can('driver_status_trained_print')) $driver_status_type = "trained";
					elseif(@$user->can('driver_status_active_print')) $driver_status_type = "active";
					@endphp

					<li class="{{ ( 
								Route::current()->uri() == $first_segment.'/driver_status/'.@$type
								) ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/driver_status/'.$driver_status_type) }}">
							<i class="fa fa-battery-half"></i>
							<span>Driver Status</span>
						</a>
					</li>
					@endif

					@if($user->can('otp'))
					<li class="{{ ( 
							Route::current()->uri() == $first_segment.'/otp'
							) ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/otp') }}">
							<i class="fa fa-file"></i>
							<span>OTP</span>
						</a>
					</li>
					@endif


				</ul>
			</li>
			@endif
			
			@if(($company_user || @$user->can('manage_vehicle')) || $user->can('manage_vehicle_type'))
			<li class="treeview {{ (
					Route::current()->uri() == 'admin/vehicle_type' || 
					Route::current()->uri() == 'admin/add_vehicle' || 
					Route::current()->uri() == 'admin/edit_vehicle/{id}' || 
					Route::current()->uri() == 'admin/add_vehicle_type' || 
					Route::current()->uri() == 'admin/view_vehicle/{id}' || 
					Route::current()->uri() == 'admin/edit_vehicle_type/{id}' || 
					Route::current()->uri() == 'admin/vehicle/change_vehicle/{vehicle_id}' || 
					Route::current()->uri() == $first_segment.'/vehicle') ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-taxi"></i>
					<span> Manage Vehicles</span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if($company_user || @$user->can('manage_vehicle'))
					<li class="{{ (
							Route::current()->uri() == $first_segment.'/add_vehicle' || 
							Route::current()->uri() == $first_segment.'/view_vehicle/{id}' || 
							Route::current()->uri() == $first_segment.'/edit_vehicle/{id}' || 
							Route::current()->uri() == $first_segment.'/vehicle' || 
							Route::current()->uri() == $first_segment.'/vehicle/change_vehicle/{vehicle_id}'
							) ? 'active' : ''  }}"><a
							href="{{ url($first_segment.'/vehicle') }}"><i
								class="fa fa-taxi"></i><span>Vehicles</span></a></li>
					@endif
					@if(@$user->can('manage_vehicle_type'))
					<li class="{{ (
							Route::current()->uri() == 'admin/add_vehicle_type' || 
					        Route::current()->uri() == 'admin/edit_vehicle_type/{id}' || 
							Route::current()->uri() == 'admin/vehicle_type') ? 'active' : ''  }}"><a
							href="{{ url('admin/vehicle_type') }}"><i class="fa fa-car"></i><span>Vehicles
								Types</span></a></li>
					@endif
				</ul>
			</li>
			@endif



			@if(@$user->can('view_rider'))
			<li class="treeview {{ (
					Route::current()->uri() == 'admin/rider' || 
					Route::current()->uri() == 'admin/add_rider' || 
					Route::current()->uri() == 'admin/edit_rider/{id}' || 
					Route::current()->uri() == 'admin/add_rider_in_group/{id}' || 
					Route::current()->uri() == 'admin/view_rider_group_list/{id}' || 
					Route::current()->uri() == 'admin/rider_group') ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-user-plus"></i> <span>Rider</span> <i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li class="{{ (
					Route::current()->uri() == 'admin/rider' || 
					Route::current()->uri() == 'admin/add_rider' || 
					Route::current()->uri() == 'admin/edit_rider/{id}'

						) ? 'active' : ''  }}"><a
							href="{{ url('admin/rider') }}"><i class="fa fa-circle-o"></i><span>Manage Rider</span></a>
					</li>
					<li class="{{ (
							Route::current()->uri() == 'admin/add_rider_in_group/{id}' || 
							Route::current()->uri() == 'admin/view_rider_group_list/{id}' || 
							Route::current()->uri() == 'admin/rider_group') ? 'active' : ''  }}"><a href="{{ url('admin/rider_group') }}"><i
								class="fa fa-circle-o"></i><span>Rider Group</span></a></li>
				</ul>
			</li>
			@endif




			@if(@$user->can('view_documents'))
			<li class="{{ (
			Route::current()->uri() == 'admin/documents' ||
			Route::current()->uri() == 'admin/add_document' ||
			Route::current()->uri() == 'admin/edit_document/{id}'
			) ? 'active' : ''  }}"><a
					href="{{ url('admin/documents') }}"><i class="fa fa-users"></i><span>Manage Documents</span></a>
			</li>
			@endif



			@if($user->can('complain') || $user->can('complain_category') || $user->can('complain_sub_category') || $user->can('complain_list'))
			<li class="treeview {{ (
				Route::current()->uri() == 'admin/complain_list' || 

				Route::current()->uri() == 'admin/movement_complain/{id}' ||
				Route::current()->uri() == 'admin/edit_movement_complain' ||	
				Route::current()->uri() == 'admin/tracking_movement_complain/{id}' ||								

				Route::current()->uri() == 'admin/complain_category' || 
				Route::current()->uri() == 'admin/add_complain_category' || 
				Route::current()->uri() == 'admin/edit_complain_category/{id}' || 
				Route::current()->uri() == 'admin/delete_complain_category/{id}' || 
				
				Route::current()->uri() == 'admin/complain_sub_category' || 
				Route::current()->uri() == 'admin/add_complain_sub_category' || 
				Route::current()->uri() == 'admin/edit_complain_sub_category/{id}' || 
				Route::current()->uri() == 'admin/delete_complain_sub_category/{id}' ||
				 
				Route::current()->uri() == 'admin/contact_list' || 
				Route::current()->uri() == 'admin/movement_contact/{id}' ||
				Route::current()->uri() == 'admin/edit_movement_contact' ||	
				Route::current()->uri() == 'admin/tracking_movement_contact/{id}'

				) ? 'active' : ''  }}">

				<a href="#">
					<i class="fa fa-comments"></i>
					<span> Complain</span><i class="fa fa-angle-left pull-right"></i>
				</a>

				<ul class="treeview-menu">
					@if($user->can('complain_category'))
					<li class="{{ (
						Route::current()->uri() == 'admin/complain_category' || 
						Route::current()->uri() == 'admin/add_complain_category' || 
						Route::current()->uri() == 'admin/edit_complain_category/{id}' || 
						Route::current()->uri() == 'admin/delete_complain_category/{id}'						
						) ? 'active' : ''  }}">
						<a href="{{ url('admin/complain_category') }}">
							<i class="fa fa-circle-o"></i><span> Category </span>
						</a>
					</li>
					@endif

					@if($user->can('complain_sub_category'))
					<li class="{{ (
						Route::current()->uri() == 'admin/complain_sub_category' || 
						Route::current()->uri() == 'admin/add_complain_sub_category' || 
						Route::current()->uri() == 'admin/edit_complain_sub_category/{id}' || 
						Route::current()->uri() == 'admin/delete_complain_sub_category/{id}'						
						) ? 'active' : ''  }}">
						<a href="{{ url('admin/complain_sub_category') }}">
							<i class="fa fa-circle-o"></i><span> Sub Category </span>
						</a>
					</li>
					@endif

					@if($user->can('complain_list'))
					<li class="{{ (
						Route::current()->uri() == 'admin/complain_list' || 
						Route::current()->uri() == 'admin/add_complain' || 
						Route::current()->uri() == 'admin/edit_complain/{id}' || 
						Route::current()->uri() == 'admin/movement_complain/{id}' || 
						Route::current()->uri() == 'admin/edit_movement_complain' || 
						Route::current()->uri() == 'admin/tracking_movement_complain/{id}'	
						
						) ? 'active' : ''  }}">
						<a href="{{ url('admin/complain_list') }}">
							<i class="fa fa-circle-o"></i><span> Complain List </span>
						</a>
					</li>
					@endif

					@if($user->can('contact_list'))
					<li class="{{ (
						Route::current()->uri() == 'admin/contact_list' || 
						Route::current()->uri() == 'admin/movement_contact/{id}' ||
						Route::current()->uri() == 'admin/edit_movement_contact' || 
						Route::current()->uri() == 'admin/tracking_movement_contact/{id}'	
												
						) ? 'active' : ''  }}">
						<a href="{{ url('admin/contact_list') }}">
							<i class="fa fa-circle-o"></i><span> Contact List </span>
						</a>
					</li>
					@endif
					
				</ul>

			</li>

			@endif






			@if(@$user->can('manage_send_message'))
			<!-- $company_user ||  -->
			<li class="{{ (Route::current()->uri() == $first_segment.'/send_message') ? 'active' : ''  }}"><a
					href="{{ url($first_segment.'/send_message') }}"><i class="fa fa-bullhorn"></i><span>Send
						Messages</span></a></li>
			@endif
			@if(@$user->can('manage_email_settings') || @$user->can('manage_send_email'))
			<li
				class="treeview {{ (Route::current()->uri() == 'admin/email_settings' || Route::current()->uri() == 'admin/send_email') ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-envelope-o"></i>
					<span>Manage Emails</span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if(@$user->can('manage_send_email'))
					<li class="{{ (Route::current()->uri() == 'admin/send_email') ? 'active' : ''  }}">
						<a href="{{ url('admin/send_email') }}"><i class="fa fa-circle-o"></i>
							<span>Send Email</span>
						</a>
					</li>
					@endif
					@if(@$user->can('manage_email_settings'))
					<li class="{{ (Route::current()->uri() == 'admin/email_settings') ? 'active' : ''  }}">
						<a href="{{ url('admin/email_settings') }}"><i class="fa fa-circle-o"></i>
							<span>Email Settings</span>
						</a>
					</li>
					@endif
				</ul>
			</li>
			@endif

			<!--@if((($company_user && @$user->status == 'Active') || @$user->can('manage_manual_booking')) || ($company_user || @$user->can('manage_manual_booking')))-->
			<!--<li class="treeview {{ (Route::current()->uri() == $first_segment.'/manual_booking/{id?}' || Route::current()->uri() == $first_segment.'/later_booking') ? 'active' : ''  }}">-->
			<!--	<a href="#">-->
			<!--		<i class="fa fa-taxi"></i>-->
			<!--		<span> Manage Manual Booking </span><i class="fa fa-angle-left pull-right"></i>-->
			<!--	</a>-->
			<!--	<ul class="treeview-menu">-->
			<!--		@if(($company_user && @$user->status == 'Active') || @$user->can('manage_manual_booking'))-->
			<!--		<li class="{{ (Route::current()->uri() == $first_segment.'/manual_booking/{id?}') ? 'active' : ''  }}"><a href="{{ url($first_segment.'/manual_booking') }}"><i class="fa fa-address-book" aria-hidden="true"></i><span>Manual Booking</span></a></li>-->
			<!--		@endif-->
			<!--		@if($company_user || @$user->can('manage_manual_booking'))-->
			<!--		<li class="{{ (Route::current()->uri() == $first_segment.'/later_booking') ? 'active' : ''  }}"><a href="{{ url($first_segment.'/later_booking') }}"><i class="fa fa-list-alt"></i><span>View Manual/Schedule Booking</span></a></li>-->
			<!--		@endif-->
			<!--	</ul>-->
			<!--</li>-->
			<!--@endif-->


			@if(@$user->can('view_vehicle_make'))
			<li
				class="{{ (
				Route::current()->uri() == 'admin/vehicle_make' || 
				Route::current()->uri() == 'admin/add-vehicle-make' || 
				Route::current()->uri() == 'admin/edit-vehicle-make/{id}'
				) ? 'active' : ''  }}">
				<a href="{{ url('admin/vehicle_make') }}"><i class="fa fa fa-car"></i><span>Vehicle Brand</span></a>
			</li>
			@endif
			@if(@$user->can('view_vehicle_model'))
			<li
				class="{{ (
				Route::current()->uri() == 'admin/vehicle_model' || 
				Route::current()->uri() == 'admin/add-vehicle_model' || 
				Route::current()->uri() == 'admin/edit-vehicle_model/{id}' || 
				Route::current()->uri() == 'admin/view_vehicle_model') ? 'active' : ''  }}">
				<a href="{{ url('admin/vehicle_model') }}"><i class="fa fa fa-car"></i><span>Vehicle Model</span></a>
			</li>
			@endif

			@if(@$user->can('view_additional_reason'))
			<li class="{{ (
				Route::current()->uri() == 'admin/additional-reasons'
			) ? 'active' : ''  }}"><a
					href="{{ url('admin/additional-reasons') }}"><i class="fa fa fa-dollar"></i><span> Additional
						Reasons</span></a></li>
			@endif

			@if(@$user->can('view_manage_reason'))
			<li class="{{ (
				Route::current()->uri() == 'admin/cancel-reason' ||				
				Route::current()->uri() == 'admin/add-cancel-reason' ||				
				Route::current()->uri() == 'admin/edit-cancel-reason/{id}'			

				) ? 'active' : ''  }}"><a
					href="{{ url('admin/cancel-reason') }}"><i class="fa fa-bar-chart"></i><span>Manage Cancel
						Reason</span></a></li>
			@endif

			@if(@$user->can('manage_locations'))
			<li class="{{ (
			Route::current()->uri() == 'admin/locations' || 
			Route::current()->uri() == 'admin/add_location' || 
			Route::current()->uri() == 'admin/edit_location/{id}'
			
			) ? 'active' : ''  }}">
				<a href="{{ url('admin/locations') }}">
					<i class="fa fa-map-o"></i><span>Manage Locations</span>
				</a>
			</li>
			@endif

			<?php /*
				@if(@$user->can('manage_peak_based_fare'))
				<li class="{{ (Route::current()->uri() == 'admin/manage_fare') ? 'active' : ''  }}"><a href="{{ url('admin/manage_fare') }}"><i class="fa fa fa-dollar"></i><span>Manage Fare</span></a></li>
				@endif
				*/ ?>

			@if(@$user->can('manage_peak_based_fare') || @$user->can('manage_peak_hour'))
			<li class="treeview {{ (
				Route::current()->uri() == 'admin/manage_fare' || 
				Route::current()->uri() == 'admin/add_manage_fare' || 
				Route::current()->uri() == 'admin/manage_peak_hour' ||
				Route::current()->uri() == 'admin/edit_peak_hour/{id}' ||
				Route::current()->uri() == 'admin/edit_manage_fare/{id}'

				) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-taxi"></i>
					<span> Manage Fare </span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if(@$user->can('manage_peak_based_fare'))
					<li class="{{ (
					Route::current()->uri() == 'admin/manage_fare' ||
					Route::current()->uri() == 'admin/add_manage_fare' ||
					Route::current()->uri() == 'admin/edit_manage_fare/{id}'
					) ? 'active' : ''  }}"><a
							href="{{ url('admin/manage_fare') }}"><i class="fa fa-paper-plane-o"></i><span>Manage
								Fare</span></a></li>
					@endif

					@if(@$user->can('manage_peak_hour'))
					<li
						class="{{ (Route::current()->uri() == 'admin/manage_peak_hour' || Route::current()->uri() == 'admin/edit_peak_hour/{id}') ? 'active' : ''}}">
						<a href="{{ url('admin/manage_peak_hour') }}"><i class="fa fa-clock-o"></i><span> Manage Peak
								Hour</span></a>
					</li>
					@endif


				</ul>
			</li>
			@endif



			@if($company_user || @$user->can('manage_requests') || @$user->can('manage_trips') ||
			@$user->can('manage_cancel_trips') || @$user->can('manage_payments') || @$user->can('manage_rating'))
			<li
				class="treeview {{ (
				Route::current()->uri() == $first_segment.'/request' || 
				Route::current()->uri() == $first_segment.'/trips' || 
				Route::current()->uri() == $first_segment.'/cancel_trips' || 
				Route::current()->uri() == $first_segment.'/payments' || 
				Route::current()->uri() == $first_segment.'/rating' || 
				Route::current()->uri() == $first_segment.'/detail_request/{id}' || 
				Route::current()->uri() == $first_segment.'/view_trips/{id}'

				) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-taxi"></i>
					<span> Manage Trips </span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if($company_user || @$user->can('manage_requests'))
					<li class="{{ (
					Route::current()->uri() == $first_segment.'/request' ||
					Route::current()->uri() == $first_segment.'/detail_request/{id}'

					) ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/request') }}">
							<i class="fa fa-paper-plane-o"></i>
							<span>Manage Ride Requests</span>
						</a>
					</li>
					@endif

					@if($company_user || @$user->can('manage_trips'))
					<li class="{{ (
						Route::current()->uri() == $first_segment.'/trips' || 
						Route::current()->uri() == $first_segment.'/view_trips/{id}'
						) ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/trips') }}">
							<i class="fa fa-taxi"></i>
							<span>Manage Trips</span>
						</a>
					</li>
					@endif

					@if($company_user || @$user->can('manage_cancel_trips'))
					<li class="{{ (
						Route::current()->uri() == $first_segment.'/cancel_trips'
					) ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/cancel_trips') }}">
							<i class="fa fa-chain-broken"></i>
							<span>Manage Canceled Trips</span>
						</a>
					</li>
					@endif

					@if($company_user || @$user->can('manage_payments'))
					<li class="{{ (Route::current()->uri() == $first_segment.'/payments') ? 'active' : ''  }}"><a
							href="{{ url($first_segment.'/payments') }}"><i class="fa fa-usd"></i><span>Manage
								Payments</span></a></li>
					@endif

					@if($company_user || @$user->can('manage_rating'))
					<li class="{{ (Route::current()->uri() == $first_segment.'/rating') ? 'active' : ''  }}"><a
							href="{{ url($first_segment.'/rating') }}"><i
								class="fa fa-star"></i><span>Ratings</span></a></li>
					@endif
				</ul>
			</li>
			@endif

			@if( @$user->can('manage_driver_payments') || @$user->can('manage_company_payments') ||
			@$user->can('driver_balance_payout'))
			<li class="treeview {{ (Route::current()->uri() == 'admin/payout/overall' || 
					Route::current()->uri() == 'admin/payout/company/overall' || 
					Route::current()->uri() == 'company/payout/overall' || 
					Route::current()->uri() == 'admin/payout/driver_balance'  || 
					Route::current()->uri() == 'admin/payout/company_balance'   || 
					Route::current()->uri() == 'admin/weekly_payout/{driver_id}'   || 
					Route::current()->uri() == 'admin/per_week_report/{driver_id}/{start_date}/{end_date}' ||
					Route::current()->uri() == 'admin/per_day_report/{driver_id}/{date}'
					
					) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-dollar" aria-hidden="true"></i> <span>Manage Payouts</span> <i
						class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if(@$user->can('manage_company_payment'))
					<li class="{{ (Route::current()->uri() == 'admin/payout/company/overall') ? 'active' : ''  }}"><a
							href="{{ url('admin/payout/company/overall') }}"><i class="fa fa-circle-o"></i><span>Company
								Payouts</span></a></li>
					@endif

					@if(@$user->can('manage_driver_payments'))
					<li class="{{ (
					Route::current()->uri() == $first_segment.'/payout/overall' ||
					Route::current()->uri() == 'admin/weekly_payout/{driver_id}'  ||
					Route::current()->uri() == 'admin/per_week_report/{driver_id}/{start_date}/{end_date}'   ||
					Route::current()->uri() == 'admin/per_day_report/{driver_id}/{date}'

					) ? 'active' : ''  }}"><a
							href="{{ url($first_segment.'/payout/overall') }}"><i
								class="fa fa-circle-o"></i><span>Driver Payouts</span></a></li>
					@endif

					@if(@$user->can('driver_balance_payout'))
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/payout/driver_balance') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/payout/driver_balance') }}"><i
								class="fa fa-circle-o"></i><span>Driver Balance Payouts</span></a>
					</li>
					@endif

					@if(@$user->can('company_balance_payout'))
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/payout/company_balance') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/payout/company_balance') }}"><i
								class="fa fa-circle-o"></i><span>Company Balance Payouts</span></a>
					</li>
					@endif
				</ul>
			</li>
			@endif

			@if($company_user || @$user->can('manage_owe_amount'))
			<li class="{{ (
				Route::current()->uri() == $first_segment.'/owe' ||
				Route::current()->uri() == $first_segment.'/company_owe/{id}'
				) ? 'active' : ''  }}">
				<a href="{{ url($first_segment.'/owe') }}">
					<i class="fa fa-money"></i>
					<span>Manage Owe Amount</span>
				</a>
			</li>
			@endif

			@if($company_user))
			<li class="{{ (Route::current()->uri() == $first_segment.'/dues') ? 'active' : ''  }}"
				style="margin-top:-20px;"><a href="{{ url($first_segment.'/dues') }}"><i
						class="fa fa-toggle-on"></i><span>Dues</span></a>
			</li>

			<li class="treeview {{ (
					Route::current()->uri() == $first_segment.'/transaction_history/paid_to_alesha' || 
					Route::current()->uri() == $first_segment.'/transaction_history/balance_withdraw' || 
					Route::current()->uri() == $first_segment.'/transaction_history/payout'
					) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-area-chart"></i> <span>Transaction History</span> <i
						class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/transaction_history/paid_to_alesha') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/transaction_history/paid_to_alesha') }}"><i
								class="fa fa-circle-o"></i><span>Paid to Alesha</span></a>
					</li>

					<li
						class="{{ (Route::current()->uri() == $first_segment.'/transaction_history/balance_withdraw') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/transaction_history/balance_withdraw') }}"><i
								class="fa fa-circle-o"></i><span>Balance Withdraw</span></a>
					</li>

					<li
						class="{{ (Route::current()->uri() == $first_segment.'/transaction_history/payout') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/transaction_history/payout') }}"><i
								class="fa fa-circle-o"></i><span>Payout</span></a>
					</li>
				</ul>
			</li>
			@endif

			@if($company_user || @$user->can('manage_statements'))
			<li
				class="treeview {{ (
				Route::current()->uri() == $first_segment.'/statements/{type}' ||
				Route::current()->uri() == $first_segment.'/view_driver_statement/{driver_id}'

				) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-area-chart"></i> <span>Manage Statements</span> <i
						class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/statements/overall') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/statements/overall') }}"><i
								class="fa fa-circle-o"></i><span>Overall Ride Statments</span></a>
					</li>
					<li class="{{ (
					Route::current()->uri() == $first_segment.'/statements/driver'  ||
					Route::current()->uri() == $first_segment.'/view_driver_statement/{driver_id}'
					) ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/statements/driver') }}"><i
								class="fa fa-circle-o"></i><span>Driver Statement</span></a>
					</li>
				</ul>
			</li>
			@endif
			@if(@$user->can('manage_wallet') || @$user->can('manage_promo_code'))
			<li
				class="treeview {{ (
					Route::current()->uri() == 'admin/wallet/{user_type}' || 
					Route::current()->uri() == 'admin/wallet/add/{user_type}' ||
					Route::current()->uri() == 'admin/wallet/edit/{user_type}/{id}' ||
					Route::current()->uri() == 'admin/promo_code' ||
					Route::current()->uri() == 'admin/add_promo_code' ||
					Route::current()->uri() == 'admin/edit_promo_code/{id}'
					) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-google-wallet"></i> <span>Manage Balance & Promo</span> <i
						class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if($company_user || @$user->can('manage_wallet'))
					<li class="treeview {{ (@$navigation == 'manage_wallet') ? 'active' : ''  }}">
						<a href="{{ route('wallet',['user_type' => 'Rider']) }}"><i class="fa fa-circle-o"></i>
							<span> Manage Balance Amount </span>
						</a>
					</li>
					@endif
					@if(@$user->can('manage_promo_code'))
					<li class="{{ (Route::current()->uri() == 'admin/promo_code') ? 'active' : ''  }}"><a
							href="{{ url('admin/promo_code') }}"><i class="fa fa-circle-o"></i><span>Manage Promo
								Code</span></a></li>
					@endif
				</ul>
			</li>
			@endif

			@if(@$user->can('manage_rider_referrals') || @$user->can('manage_driver_referrals'))
			<li
				class="treeview {{ (
					Route::current()->uri() == 'admin/referrals/rider' ||  
					Route::current()->uri() == 'admin/referrals/driver' ||
					Route::current()->uri() == 'admin/referrals/{id}'
					) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-users"></i>
					<span>Referrals</span><i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if(@$user->can('manage_rider_referrals'))
					<li class="{{ (
						Route::current()->uri() == 'admin/referrals/rider'
						) ? 'active' : ''  }}">
						<a href="{{ url('admin/referrals/rider') }}"><i class="fa fa-circle-o"></i>
							<span> Riders </span>
						</a>
					</li>
					@endif
					@if(@$user->can('manage_driver_referrals'))
					<li class="{{ (
						Route::current()->uri() == 'admin/referrals/driver'					
						) ? 'active' : ''  }}">
						<a href="{{ url('admin/referrals/driver') }}"><i class="fa fa-circle-o"></i>
							<span> Drivers </span>
						</a>
					</li>
					@endif
				</ul>
			</li>
			@endif

			@if(@$user->can('manage_map') || @$user->can('manage_heat_map'))
			<li
				class="treeview {{ (Route::current()->uri() == $first_segment.'/map' || Route::current()->uri() == $first_segment.'/heat-map') ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-map-marker" aria-hidden="true"></i> <span>Manage Map</span> <i
						class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li class="{{ (Route::current()->uri() == $first_segment.'/map') ? 'active' : ''  }}"><a
							href="{{ url($first_segment.'/map') }}"><i class="fa fa-circle-o"></i><span>Map
								View</span></a></li>
					<li class="{{ (Route::current()->uri() == $first_segment.'/heat-map') ? 'active' : ''  }}"><a
							href="{{ url($first_segment.'/heat-map') }}"><i
								class="fa fa-circle-o"></i><span>HeatMap</span></a></li>
				</ul>
			</li>
			@endif

			@if(@$user->can('manage_api_credentials'))
			<li class="{{ (Route::current()->uri() == 'admin/api_credentials') ? 'active' : ''  }}"><a
					href="{{ url('admin/api_credentials') }}"><i class="fa fa-gear"></i><span>Api Credentials</span></a>
			</li>
			@endif
			@if(@$user->can('manage_payment_gateway'))
			<li class="{{ (Route::current()->uri() == 'admin/payment_gateway') ? 'active' : ''  }}"><a
					href="{{ url('admin/payment_gateway') }}"><i class="fa fa-paypal"></i><span>Payment
						Gateway</span></a></li>
			@endif
			@if(@$user->can('manage_fees'))
			<li class="{{ (Route::current()->uri() == 'admin/fees') ? 'active' : ''  }}"><a
					href="{{ url('admin/fees') }}"><i class="fa fa-dollar"></i><span>Manage Fees</span></a></li>
			@endif
			@if(@$user->can('manage_referral_settings'))
			<li class="{{ (Route::current()->uri() == 'admin/referral_settings') ? 'active' : ''  }}"><a
					href="{{ url('admin/referral_settings') }}"><i class="fa fa-users"></i><span>Referral & Bonus
						Settings</span></a></li>
			@endif
			@if(@$user->can('manage_metas'))
			<li class="{{ (
				Route::current()->uri() == 'admin/metas' ||
				Route::current()->uri() == 'admin/edit_meta/{id}'
				) ? 'active' : ''  }}"><a
					href="{{ url('admin/metas') }}"><i class="fa fa-bar-chart"></i><span>Manage Metas</span></a></li>
			@endif
			@if(@$user->can('manage_country'))
			<li class="{{ (
				Route::current()->uri() == 'admin/country' ||
				Route::current()->uri() == 'admin/add_country' ||
				Route::current()->uri() == 'admin/edit_country/{id}'
				
				) ? 'active' : ''  }}"><a
					href="{{ url('admin/country') }}"><i class="fa fa-globe"></i><span>Manage Country</span></a></li>
			@endif
			@if(@$user->can('manage_currency'))
			<li class="{{ (
				Route::current()->uri() == 'admin/currency' ||
				Route::current()->uri() == 'admin/add_currency' ||
				Route::current()->uri() == 'admin/edit_currency/{id}'

				) ? 'active' : ''  }}"><a
					href="{{ url('admin/currency') }}"><i class="fa fa-eur"></i><span>Manage Currency</span></a></li>
			@endif
			@if(@$user->can('manage_language'))
			<li class="{{ (
				Route::current()->uri() == 'admin/language' || 
				Route::current()->uri() == 'admin/add_language' || 
				Route::current()->uri() == 'admin/edit_language/{id}'
				) ? 'active' : ''  }}"><a
					href="{{ url('admin/language') }}"><i class="fa fa-language"></i><span>Manage Language</span></a>
			</li>
			@endif
			@if(@$user->can('manage_static_pages'))
			<li class="{{ (
				Route::current()->uri() == 'admin/pages' ||
				Route::current()->uri() == 'admin/add_page'  ||
				Route::current()->uri() == 'admin/edit_page/{id}' 
				
				) ? 'active' : ''  }}"><a
					href="{{ url('admin/pages') }}"><i class="fa fa-newspaper-o"></i><span>Manage Static
						Pages</span></a></li>
			@endif

			@if(@$user->can('manage_help'))
			<li
				class="treeview {{ (
					Route::current()->uri() == 'admin/help' ||
					Route::current()->uri() == 'admin/add_help' || 
					Route::current()->uri() == 'admin/help_category' || 
					Route::current()->uri() == 'admin/add_help_category' || 					
					Route::current()->uri() == 'admin/help_subcategory' || 					
					Route::current()->uri() == 'admin/add_help_subcategory'
					) ? 'active' : ''  }}">
				<a href="#">
					<i class="fa fa-support"></i> <span>Manage Help</span> <i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					<li class="{{ (
						Route::current()->uri() == 'admin/help' ||
						Route::current()->uri() == 'admin/add_help'						
						) ? 'active' : ''  }}"><a
							href="{{ url('admin/help') }}"><i class="fa fa-circle-o"></i><span>Help</span></a></li>
					<li class="{{ (
						Route::current()->uri() == 'admin/help_category' ||
						Route::current()->uri() == 'admin/add_help_category'
						) ? 'active' : ''  }}"><a
							href="{{ url('admin/help_category') }}"><i
								class="fa fa-circle-o"></i><span>Category</span></a></li>
					<li class="{{ (
						Route::current()->uri() == 'admin/help_subcategory' ||
						Route::current()->uri() == 'admin/add_help_subcategory'
						) ? 'active' : ''  }}"><a
							href="{{ url('admin/help_subcategory') }}">
							<i class="fa fa-circle-o"></i><span>Sub Category</span></a></li>
				</ul>
			</li>
			@endif

			@if(@$user->can('manage_join_us'))
			<li class="{{ (Route::current()->uri() == 'admin/join_us') ? 'active' : ''  }}"><a
					href="{{ url('admin/join_us') }}"><i class="fa fa-share-alt"></i><span>Join Us Links</span></a></li>
			@endif
			@if(@$user->can('manage_support'))
			<li class="{{ (
				Route::current()->uri() == 'admin/support' || 
				Route::current()->uri() == 'admin/add_support' || 
				Route::current()->uri() == 'admin/edit_support/{id}'
				) ? 'active' : ''  }}"><a
					href="{{ url('admin/support') }}"><i class="fa fa-globe"></i><span>Manage Support</span></a></li>
			@endif

			@if(@$user->can('manage_site_settings'))
			<li class="{{ (Route::current()->uri() == 'admin/site_setting') ? 'active' : ''  }}">
				<a href="{{ url('admin/site_setting') }}">
					<i class="fa fa-cogs"></i>
					<span>Site Setting</span>
				</a>
			</li>
			@endif

			@if(@$user->can('best_driver'))
			<li class="{{ (Route::current()->uri() == 'admin/best_driver') ? 'active' : ''  }}"><a
					href="{{ url('admin/best_driver') }}"><i class="fa fa-users"></i><span> Best Driver</span></a></li>
			@endif




			<!-- <li class="{{ (Route::current()->uri() == 'admin/bonuse') ? 'active' : ''  }}">
					<a href="{{ url('admin/bonuse') }}">
						<i class="fa fa-money"></i>
							<span>Bonus</span>
			        </a>
		        </li> -->
			{{-- Driver And Rider Offer Start --}}
			@if($company_user || @$user->can('driver_offer'))
			<li class="treeview 
				{{ (
					Route::current()->uri() == $first_segment.'/driver_offer/signing_bonus' || 
					Route::current()->uri() == $first_segment.'/driver_offer/trip_bonus'|| 
					Route::current()->uri() == $first_segment.'/driver_offer/referral_bonus' || 
					Route::current()->uri() == $first_segment.'/driver_offer/online_bonus') ? 'active' : ''  }}">

				<a href="#">
					<i class="fa fa-support"></i> <span>Driver Offer</span> <i class="fa fa-angle-left pull-right"></i>
				</a>

				<ul class="treeview-menu">
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/driver_offer/signing_bonus') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/driver_offer/signing_bonus') }}">
							<i class="fa fa-circle-o"></i>
							<span>Signing Bonus</span>
						</a>
					</li>
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/driver_offer/trip_bonus') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/driver_offer/trip_bonus') }}">
							<i class="fa fa-circle-o"></i>
							<span>Trip Bonus</span>
						</a>
					</li>
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/driver_offer/referral_bonus') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/driver_offer/referral_bonus') }}">
							<i class="fa fa-circle-o"></i>
							<span>Referral Bonus</span>
						</a>
					</li>
					<li
						class="{{ (Route::current()->uri() == $first_segment.'/driver_offer/online_bonus') ? 'active' : ''  }}">
						<a href="{{ url($first_segment.'/driver_offer/online_bonus') }}">
							<i class="fa fa-circle-o"></i>
							<span>Online Bonus</span>
						</a>
					</li>
				</ul>
			</li>
			@endif

			@if(@$user->can('rider_offer'))
			<li class="treeview {{ (
					Route::current()->uri() == 'admin/rider_offer/referral_bonus' || 
				    Route::current()->uri() == 'admin/rider_offer/cash_back') ? 'active' : ''  }}">

				<a href="#">
					<i class="fa fa-support"></i> <span>Rider Offer</span> <i class="fa fa-angle-left pull-right"></i>
				</a>

				<ul class="treeview-menu">
					<li class="{{ (Route::current()->uri() == 'admin/rider_offer/referral_bonus') ? 'active' : ''  }}">
						<a href="{{ url('admin/rider_offer/referral_bonus') }}">
							<i class="fa fa-circle-o"></i>
							<span>Referral Bonus</span>
						</a>
					</li>
					<li class="{{ (Route::current()->uri() == 'admin/rider_offer/cash_back') ? 'active' : ''  }}">
						<a href="{{ url('admin/rider_offer/cash_back') }}">
							<i class="fa fa-circle-o"></i>
							<span>Cash Back</span>
						</a>
					</li>
				</ul>
			</li>
			@endif
			<!-- Driver And Rider Offer End  -->

			@if(@$user->can('activity_log'))
			<li class="treeview 
				@if(
					Route::current()->uri() == 'admin/activity_log' || 
					Route::current()->uri() == 'admin/audit_log' || 
					Route::current()->uri() == 'admin/sys_log' || 
					Route::current()->uri() == 'admin/all_log'
				)
				active
				@endif ">

				<a href="#">
					<i class="fa fa-history"></i> <span>Logs</span> <i class="fa fa-angle-left pull-right"></i>
				</a>
				<ul class="treeview-menu">
					@if(@$user->can('audit_log'))
					<li class="{{ (Route::current()->uri() == 'admin/activity_log') ? 'active' : ''  }}">
						<a href="{{ url('admin/activity_log') }}">
							<i class="fa fa-circle-o"></i>
							<span>Activity Log</span>
						</a>
					</li>
					@endif

					@if(@$user->can('audit_log'))
					<li class="{{ (Route::current()->uri() == 'admin/audit_log') ? 'active' : ''  }}">
						<a href="{{ url('admin/audit_log') }}">
							<i class="fa fa-circle-o"></i>
							<span>Audit Log</span>
						</a>
					</li>
					@endif

					@if(@$user->can('sys_log'))
					<li class="{{ (Route::current()->uri() == 'admin/sys_log') ? 'active' : ''  }}">
						<a href="{{ url('admin/sys_log') }}">
							<i class="fa fa-circle-o"></i>
							<span>Sys Log</span>
						</a>
					</li>
					@endif

					@if(@$user->can('all_log'))
					<li class="{{ (Route::current()->uri() == 'admin/admin_logs') ? 'active' : ''  }}">
						<a href="{{ url('admin/all_logs') }}" target="_blank">
							<i class="fa fa-circle-o"></i>
							<span>Admin Logs</span>
						</a>
					</li>

					<li class="{{ (Route::current()->uri() == 'admin/logs') ? 'active' : ''  }}">
						<a href="{{ url('admin/api_logs') }}" target="_blank">
							<i class="fa fa-circle-o"></i>
							<span>API Logs</span>
						</a>
					</li>
					@endif

					@if(@$user->can('clear_cache'))
					<li class="{{ (Route::current()->uri() == 'admin/clear_cache') ? 'active' : ''  }}">
						<a href="{{ url('admin/clear_cache') }}">
							<i class="fa fa-circle-o"></i>
							<span>Clear Cache</span>
						</a>
					</li>
					@endif
				</ul>
			</li>
			@endif


			@if(@$user->can('hub_management'))
			<li class="treeview {{ (
					Route::current()->uri() == 'admin/manage_hub' || 
					Route::current()->uri() == 'admin/add_hub' || 
					Route::current()->uri() == 'admin/edit_hub/{id}' || 
					Route::current()->uri() == 'admin/manage_employee' || 
					Route::current()->uri() == 'admin/add_employee' || 
					Route::current()->uri() == 'admin/edit_hub_employee/{id}' || 
					Route::current()->uri() == 'admin/report' || 
					Route::current()->uri() == 'admin/hub_acquisition_list') ? 'active' : ''  }}">

				<a href="#">
					<i class="fa fa-support"></i> <span>Hub Management</span> <i
						class="fa fa-angle-left pull-right"></i>
				</a>

				<ul class="treeview-menu">
					<li class="{{ (
							Route::current()->uri() == 'admin/manage_hub' || 
							Route::current()->uri() == 'admin/edit_hub/{id}' || 
							Route::current()->uri() == 'admin/add_hub') ? 'active' : ''  }}">
						<a href="{{ url('admin/manage_hub') }}">
							<i class="fa fa-circle-o"></i>
							<span>Manage Hub</span>
						</a>
					</li>

					<li class="{{ (
							Route::current()->uri() == 'admin/add_employee' ||
							Route::current()->uri() == 'admin/edit_hub_employee/{id}' ||
							Route::current()->uri() == 'admin/manage_employee') ? 'active' : ''  }}">
						<a href="{{ url('admin/manage_employee') }}">
							<i class="fa fa-circle-o"></i>
							<span>Manage Employee</span>
						</a>
					</li>
				</ul>
			</li>
			@endif






			@if(@$user->can('sos_messages'))
			<li class="{{(Route::current()->uri() == 'admin/sos_messages') ? 'active' : ''}}">
				<a href="{{ url('admin/sos_messages') }}">
					<i class="fa fa-bullhorn"></i>
					<span>SOS Messages</span>
				</a>
			</li>
			@endif



			@elseif(LOGIN_USER_TYPE=='hub')
			<!-- hub start -->
			@if($hub_user && $user->role_id == 4)
			<li class="{{ (Route::current()->uri() == 'hub/employee_list') ? 'active' : '' }}">
				<a href="{{ url('hub/employee_list') }}">
					<i class="fa fa-building"></i>
					<span>Employee List</span>
				</a>
			</li>

			<li class="{{ (Route::current()->uri() == 'hub/acquisition_list') ? 'active' : '' }}">
				<a href="{{ url('hub/acquisition_list') }}">
					<i class="fa fa-building"></i>
					<span>Acquisition List</span>
				</a>
			</li>
			@endif

			@if($hub_user && $user->role_id == 5)

			<li class="{{ (Route::current()->uri() == 'hub/acquisition_list') ? 'active' : '' }}">
				<a href="{{ url('hub/acquisition_list') }}">
					<i class="fa fa-building"></i>
					<span>Acquisition List</span>
				</a>
			</li>

			@endif
			<!-- hub end -->
			@endif


		</ul>
	</section>
</aside>