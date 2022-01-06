@extends('template_without_header_footer')
@section('main')
<body onload="window.print()" onfocus="window.close()">
<style type="text/css">
	tr,td{border:none !important}
</style>

	<div class="table-no-border" style="overflow: visible;">
        <table class="col-sm-12 table-striped table-condensed">
            <tbody>                    
                <tr>
                    <th>Vehicle name</th>
                    <td>{{ $result->car_type->car_name }} </td>
                </tr>
                <tr>
                    <th class="col-sm-4">Driver name</th>
                    <td class="col-sm-8">{{ $result->driver_name}}</td>
                </tr>
                <tr>
                    <th class="col-sm-4">Rider name</th>
                    <td class="col-sm-8">{{ $result->users->first_name }}</td>
                </tr>
                @if(LOGIN_USER_TYPE != 'company')
				<tr>
					<th class="col-sm-4">Company name</th>
					<td class="col-sm-8">{{ $result->driver->company->name }}</div>
				</tr>
				@endif
				<tr>
					<th class="col-sm-4">Trip date</th>
					<td class="col-sm-8">{{ $result->begin_date }}</td>
				</tr>
				<tr>
					<th class="col-sm-4">Arrive Time</th>
					<td class="col-sm-8">{{ $result->getFormattedTime('arrive_time') }}</td>
				</tr>
				<tr>
					<th class="col-sm-4">Start Trip</th>
					<td class="col-sm-8">{{ $result->getFormattedTime('begin_trip') }}</td>
				</tr>
				<tr>
					<th class="col-sm-4">End Trip</th>
					<td class="col-sm-8">{{ $result->getFormattedTime('end_trip') }}</td>
				</tr>
				<tr>
					<th class="col-sm-4">Pickup Location</th>
					<td class="col-sm-8">{{ $result->pickup_location }}</td>
				</div>
				<tr>
					<th class="col-sm-4">Drop Location</th>
					<td class="col-sm-8">
						{{ $result->drop_location }}
					</td>
				</tr>
				<tr>
					<th class="col-sm-4">Currency</th>
					<td class="col-sm-8">{{ $result->currency_code }}</td>
				</tr>

				@foreach($invoice_data as $invoice)
					<tr>
						<th class="col-sm-4">{{ $invoice['key'] }}</th>
						<td class="col-sm-8">{{ $invoice['value'] }}</td>
					</tr>
					@endforeach
					
					<tr>
						<th class="col-sm-4">Status</th>
						<td class="col-sm-8">{{ $result->status }}</td>
					</tr>
					
					@if($result->status == "Cancelled")
					<tr>
						<th class="col-sm-4">Cancelled Reason</th>
						<td class="col-sm-8">{{ @$result->cancel->cancel_reason->reason }}</td>
					</tr>
					<tr>
						<th class="col-sm-4">Cancelled Message</th>
						<td class="col-sm-8">{{ @$result->cancel->cancel_comments }}</td>
					</tr>
					<tr>
						<th class="col-sm-4">Cancelled By</th>
						<td class="col-sm-8">{{ @$result->cancel->cancelled_by }}</td>
					</tr>
					<tr>
						<th class="col-sm-4">Cancelled Date</th>
						<td class="col-sm-8">{{ @$result->cancel->created_at }}</td>
					</tr>
					@endif

					@if(LOGIN_USER_TYPE != 'company')
						<tr>
							<th>Transaction ID</th>
							<td>{{ @$result->paykey }}</td>
						</tr>
						@if($result->driver->company->id == 1 && $result->driver->default_payout_credentials == '')
						<tr>
							<td colspan="2">Yet, Driver doesn't enter his Payout details.</td>
						</tr>
						@elseif($result->status == "Completed" && $result->payout_status == "Paid")
						<tr>
							<th>Payout Status</th>
							<td class="col-sm-8">Payout successfully sent..</td>
						</tr>
						@endif
					@endif

					@if($result->driver->company_id != 1)
						@if($result->driver->company->default_payout_credentials == '')
							<tr>
								<td colspan="2">Yet, Company doesn't enter his Payout details.</td>
							</tr>
						@else
							
						@endif						
					@endif
                
            </tbody>
        </table>
    </div>
@endsection