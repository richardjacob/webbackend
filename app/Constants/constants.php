<?php

$payment_methods = array(
	["key" => "cash", "value" => 'Cash', 'icon' => "https://".env('DOMAIN')."/images/icon/cash.png"],
	/*["key" => "paypal", "value" => 'PayPal', 'icon' => asset("images/icon/paypal.png")],
	["key" => "braintree", "value" => 'Card Payment', 'icon' => asset("images/icon/card.png")],
	["key" => "stripe", "value" => 'Card Payment', 'icon' => asset("images/icon/card.png")],*/
	["key" => "banktransfer", "value" => 'Nagad', 'icon' => "https://".env('DOMAIN')."/images/icon/banktransfer.png"],
	["key" => "nagad", "value" => 'Nagad', 'icon' => "https://".env('DOMAIN')."/images/icon/nagad.png"],
	["key" => "bkash", "value" => 'Bkash', 'icon' => "https://".env('DOMAIN')."/images/icon/bkash.png"],
	["key" => "rocket", "value" => 'Rocket', 'icon' => "https://".env('DOMAIN')."/images/icon/rocket.png"],
	["key" => "upay", "value" => 'Upay', 'icon' => "https://".env('DOMAIN')."/images/icon/upay.png"],
);

if(!defined('PAYMENT_METHODS')) {
	define('PAYMENT_METHODS', $payment_methods);	
}

$payout_methods = array(
	// ["key" => "bank_transfer", "value" => 'Bank Transfer'],
	// ["key" => "paypal", "value" => 'PayPal'],
	// ["key" => "stripe", "value" => 'Stripe'],
	//["key" => "cash", "value" => 'Cash'],
	["key" => "banktransfer", "value" => 'Bank Transfer'],
	["key" => "nagad", "value" => 'Nagad'],
	["key" => "bkash", "value" => 'Bkash'],
	["key" => "rocket", "value" => 'Rocket'],
	["key" => "upay", "value" => 'Upay'],
);

if(!defined('PAYOUT_METHODS')) {
	define('PAYOUT_METHODS', $payout_methods);	
}

if(!defined('CACHE_HOURS')) {
	define('CACHE_HOURS', 24);	
}