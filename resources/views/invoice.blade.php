<!DOCTYPE html>
<html>

<head>
	<title>Trip Invoice</title>
	<style type="text/css">
		body,
		div,
		td,
		span {
			font-family: Arial, Verdana;
			color: #4C4242;
		}

		.container {
			width: 400px;
		}

		.bgcolor-deep {
			background-color: #26BBB9;
		}

		.bgcolor-light {
			background-color: #CFF5F1;
		}

		.text-left {
			text-align: left;
		}

		.text-center {
			text-align: center;
		}

		.text-right {
			text-align: right;
		}

		.text-bold {
			font-weight: bold;
		}

		.color-white {
			color: #ffffff;
		}

		.heading-padding {
			padding: 5px;
		}

		.heading-bottom-padding {
			padding: 10px 0px;
			margin-bottom: 10px;
		}

		img {
			width: 100%;
			cursor: default;
		}

		.float-left {
			float: left;
		}

		.float-right {
			float: right;
		}

		.bottom-line-rider {
			border-bottom: 2px solid #A7A7A7;
			font-size: 15px;
			margin-top: 10px;
			margin-bottom: 20px;
			padding-bottom: 2px;
			text-align: left;
			width: 50%;
		}

		.bottom-line-paymentBy {
			border-bottom: 2px solid #A7A7A7;
			font-size: 15px;
			margin-top: 10px;
			margin-bottom: 20px;
			padding-bottom: 2px;
			text-align: right;
			width: 50%;
			font-weight: normal;
		}

		.address {}

		.address .heading {
			margin-top: 20px;
			margin-bottom: 10px;
		}

		.address .text {
			color: #144F3A;
			line-height: 150%;
		}

		.top-margin {
			margin-top: 20px;
		}

		.bottom-margin {
			margin-bottom: 20px;
		}

		.top-border {
			border-top: 2px solid #A7A7A7
		}

		td {
			padding: 7px;
		}

		.button {
			border-radius: 10px;
			background: #26BBB9;
			color: #fff;
			padding: 11px;
			border-color: #26BBB9;
			width: 150px;
			font-size: 15px;
			text-align: center;
			font-weight: bold;
		}
	</style>
</head>

<body>
	<center>
		<div class="container">
			<a href="https://alesharide.com" style="text-decoration:none;">
				<img src="https://alesharide.com/images/logos/logo.png" style="width:200px;">
			</a>
			<br>

			<div class="bgcolor-deep text-center text-bold color-white heading-padding">Trip Invoice</div>
			<div class="bgcolor-light">
				<img src="{{url('images/car.png')}}">
			</div>

			<div class="heading-bottom-padding">
				<div class="float-left">
					Trip ID:#<span>{{$trip->id}}</span>
				</div>
				<div class="float-right">
					Date: <span>{{date("d F Y", strtotime($trip->end_trip))}}</span>
				</div>
			</div>

			<div class="bottom-line text-bold">
				<div class="float-left text-bold bottom-line-rider">
					{{$rider->first_name}} {{$rider->last_name}}
				</div>
				<div class="float-right bottom-line-paymentBy">
					Payment by: <span>{{$trip->payment_mode}}</span>
				</div>
			</div>

			<div class="address text-left">
				<div class="heading text-bold">Pickup Location:</div>
				<div class="text">{{$trip->pickup_location}}</div>
			</div>

			<div class="address text-left">
				<div class="heading text-bold">Drop Location:</div>
				<div class="text">{{$trip->drop_location}}</div>
			</div>

			<div class="address text-left top-margin">
				<div class="text">Your Trip With:</div>
				<span>{{$driver->first_name}} {{$driver->last_name}}</span>
			</div>

			<table class="top-margin bgcolor-light bottom-margin" width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>Base Fare</td>
					<td class="text-right">&#2547; {{$trip->base_fare}}</td>
				</tr>


				{{-- <tr>
					<td>Rider Access Fee</td>
					<td class="text-right">&#2547; {{$trip->access_fee}}</td>
				</tr> --}}


				<tr>
					<td>Distance ({{$trip->total_km}} km)</td>
					<td class="text-right">&#2547; {{$trip->distance_fare}}</td>
				</tr>

				{{-- <tr>
					<td>Time ({{$trip->total_time}} min)</td>
					<td class="text-right">&#2547; {{$trip->time_fare}}</td>
				</tr> --}}


				<tr>
					<td>Waiting Charge</td>
					<td class="text-right">&#2547; {{$trip->waiting_charge}}</td>
				</tr>

				<tr>
					<td class="top-border">Subtotal</td>
					<td class="text-right top-border">&#2547; {{$trip->subtotal_fare + $trip->access_fee +
						$trip->waiting_charge}}</td>
				</tr>
				<tr>
					<td>Promo</td>
					<td class="text-right">&#2547; -{{$trip->promo_amount}}</td>
				</tr>

				<tr>
					<td>Discount</td>
					<td class="text-right">&#2547; -{{$trip->discount}}</td>
				</tr>

				<?php
				if ($trip->wallet_amount != 0) { ?>
				<tr>
					<td>Balance Amount</td>
					<td class="text-right">&#2547; -{{$trip->wallet_amount}}</td>
				</tr>
				<?php	
				}
				?>

				<tr>
					<td>
						<div class="button">Net Fare</div>
					</td>
					<td class="text-right">&#2547; {{$trip->total_fare}}</td>
				</tr>
			</table>
		</div>

		@if($email == '0')
		<div class="bottom-margin" id="print_holder">
			<span onclick="print_invoice()" class="button" style="cursor:pointer">
				Print</span>
		</div>
		<script type="text/javascript">
			function print_invoice(){
				document.getElementById('print_holder').style.display = "none";
				window.print();
				document.getElementById('print_holder').style.display = "block";
			}
		</script>
		@else

		<div class="bottom-margin">
			<a href="https://{{get_domain()}}/invoice/{{base64_encode($rider->id)}}/{{base64_encode($trip->id)}}"
				class="button" style="text-decoration:none;color:#fff">Print</a>
		</div>
		@endif



	</center>
</body>

</html>