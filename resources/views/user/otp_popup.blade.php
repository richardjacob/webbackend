<div class="modal otp-popup text-left poppayout fade" id="otp_popup" aria-hidden="false" style="" tabindex="-1">
	<div id="modal-add-otp-set-address" class="modal-content">
		<div class="panel-header">
			<button type="button" class="close" data-dismiss="modal"></button>
			<h3>
				{{ trans('messages.signup.otp') }}
			</h3>
		</div>
		<div class="flash-container otp-flash-message" id="otp_resended_flash" style="display: none;"></div>
		<div class="panel-body">
			<div class="otp-field">
				<label>
					{{ trans('messages.signup.otp') }}
				</label>
				<div class="otp-input">
				{!! Form::text('otp', '', ['id' => 'otp_input','autocomplete'=>"otp",'class'=>'form-control']) !!}
				<span class="text-danger otp_error"></span>
			</div>
			</div>
		</div>
		<div class="panel-footer otp_footer">
			<div class="row">
				<div class="col-sm-6">
					<input type="button" value="{{ trans('messages.user.submit') }}" class="btn blue-signin-btn" ng-click="showPopup('check_otp');">
				</div>
				<div class="col-sm-6 text-right">
					<a class="text-primary" ng-click="showPopup('resend_otp');">{{ trans('messages.signup.resend_otp') }}</a>
				</div>
			</div>
		</div>
	</div>
</div>
