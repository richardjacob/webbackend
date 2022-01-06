<title>Invoice</title>
@extends('template_without_header_footer')
@section('main')
<body onload="window.print()" onfocus="window.close()">
    <div class="container">
        <div class="row">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding-top: 10px;">
        <div class="page-lead separated--bottom  text--center text--uppercase pull-left" style="margin-bottom: 0px !important;padding-bottom: 5px !important;">
            <h1 class="flush-h1 flush">
            @lang('messages.dashboard.trip_invoice')
            </h1>
            <small style="text-transform: none;text-align: left;float: left;padding: 20px 20px 0px;">
            @lang('messages.dashboard.dwnld_invoice') {{ $site_name}} @lang('messages.dashboard.feedback')
            </small>
        </div>
        <div id="no-more-tables" style="overflow: visible;">
            <table class="col-sm-12 table-bordered table-striped table-condensed cf">
                <thead class="cf">
                    <tr>
                        <th>{{trans('messages.dashboard.invoice_no')}}</th>
                        <th >{{trans('messages.dashboard.trip_date')}}</th>
                        <th>{{trans('messages.dashboard.invoice')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="trip-expand__origin collapsed" >
                        <td data-title="Invoice Number">{{ $trip->id }}</td>
                        <td data-title="Trip date">{{ date('F d, Y',strtotime($trip->created_at))}}</td>
                        <td data-title="Invoice">{{ $trip->currency->original_symbol }} {{ $trip->total_invoice }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" >
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <img src="{{ $trip->rider_profile_picture }}" class='img--circle img--bordered img--shadow driver-avatar' >
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <p>{{trans('messages.dashboard.invoice_issued')}}{{$site_name}}{{trans('messages.dashboard.behalf')}}</p>
                <p>{{ $trip->rider_name }}</p>
                <div class="text--left">
                    <div class="trip-address grid grid--full soft-double--bottom">
                        
                        <div class="grid__item one-tenth" style="margin:6px 0px;">
                            <div class="icon icon_route-dot color--positive"></div>
                        </div>
                        <div class="grid__item nine-tenths">
                            <p class="flush">{{ $trip->pickup_time }}</p>
                            <h6 class="color--neutral flush">{{ $trip->pickup_location }}</h6>
                        </div>
                    </div>
                    <div class="trip-address grid grid--full">
                        <div class="grid__item one-tenth" style="margin:6px 0px;">
                            <div class="icon icon_route-dot color--negative"></div>
                        </div>
                        <div class="grid__item nine-tenths">
                            <p class="flush">{{ $trip->drop_time }}</p>
                            <h6 class="color--neutral flush">{{ $trip->drop_location }}</h6>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
                <div id="no-more-tables" class="table-no-border" style="overflow: visible;">
                    {{-- <table class="col-sm-12 table-bordered table-striped table-condensed cf">
                        <thead class="cf">
                            <tr>
                                <th>{{trans('messages.dashboard.date')}}</th>
                                <th>{{trans('messages.dashboard.desc')}}</th>
                                <th>{{trans('messages.dashboard.net_amt')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice_data as $key => $invoice)
                            <tr class="trip-expand__origin collapsed text-{{ $invoice['colour'] }}">
                                <td> {{ ($key == 0) ? date('F d, Y') : ''}} </td>
                                <td class="text--left "> {{ $invoice['key'] }} </td>
                                <td class="text--right "> {{ $invoice['value'] }} </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table> --}}


                    <table class="trip_invoice_table bgcolor-light-invoice" width="100%" cellpadding="0" cellspacing="0">


                        {{-- @foreach($invoice_data as $key => $invoice)
                        <tr>
                            <td> {{ $invoice['key'] }} </td>
                            <td class="text--right"> {{ $invoice['value'] }} </td>
                        </tr>
                        @endforeach --}}
                        
                        <tr>
                            <td>Base Fare</td>
                            <td class="text-right">&#2547; {{$trip->base_fare}}</td>
                        </tr>
                        <tr>
                            <td>Rider Access Fee</td>
                            <td class="text-right">&#2547; {{$trip->access_fee}}</td>
                        </tr>
                        <tr>
                            {{-- <td>Distance ({{$trip->total_km}} km)</td> --}}
                            <td>Distance Fare</td>
                            <td class="text-right">&#2547; {{$trip->distance_fare}}</td>
                        </tr>
                        <tr>
                            {{-- <td>Time ({{$trip->total_time}} min)</td> --}}
                            <td>Time Fare</td>
                            <td class="text-right">&#2547; {{$trip->time_fare}}</td>
                        </tr>
            
            
                        <tr>
                            <td>Waiting Charge</td>
                            <td class="text-right">&#2547; {{$trip->waiting_charge}}</td>
                        </tr>
            
                        <tr>
                            <td class="top-border">Subtotal</td>
                            <td class="text-right top-border">&#2547; {{$trip->subtotal_fare + $trip->access_fee + $trip->waiting_charge}}</td>
                        </tr>
                        <tr>
                            <td>Promo</td>
                            <td class="text-right">&#2547; -{{$trip->promo_amount}}</td>
                        </tr>
            
                        <tr>
                            <td>Discount</td>
                            <td class="text-right">&#2547; -{{$trip->discount}}</td>
                        </tr>
            
                        <tr>
                            <td>Rider Balance Amount</td>
                            <td class="text-right">&#2547; -{{$trip->wallet_amount}}</td>
                        </tr>
                    
            
                        <tr class="top-border">
                            <td > <a style="font-size: 16px; color: green" >Collectable Amount</a></td>
                            <td class="text-right">&#2547; {{$trip->total_fare}}</td>
                        </tr>
                        <?php
                            if ($trip->driver_payout > $trip->owe_amount) {?>
                                <tr>
                                    <td>Alesha Ride Will Pay You</td>
                                    <td class="text-right">&#2547; {{$trip->driver_payout}}</td>
                                </tr>
                                <tr>
                                    <td>Payable to Alesha Ride</td>
                                    <td class="text-right">&#2547; - 0.00 </td>
                                </tr>
                        
                            <?php
                                if ($trip->payment_mode == "Nagad" || $trip->payment_mode == "Nagad & Wallet") {?>
                                    <tr>
                                        <td>
                                            <div class="button">Net Income</div>
                                        </td> 
                                        <td class="text-right">&#2547; {{$trip->driver_payout}}</td>
                                    </tr>
                                    <?php   }else { ?>
                                        <tr>
                                            <td>
                                                <div class="button">Net Income</div>
                                            </td> 
                                            <td class="text-right">&#2547; {{$trip->total_fare + $trip->driver_payout}}</td>
                                        </tr>
                                    <?php
                                } 
                            }else if ($trip->owe_amount > $trip->driver_payout) { ?>
                                <tr>
                                    <td>Alesha Ride Will Pay You</td>
                                    <td class="text-right">&#2547; 0.00 </td>
                                </tr>
                                <tr>
                                    <td>Payable to Alesha Ride</td>
                                    <td class="text-right">&#2547; -{{$trip->access_fee + $trip->driver_or_company_commission}} .00 </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="button">Net Income</div>
                                    </td> 
                                    <td class="text-right">&#2547; {{$trip->total_fare - ($trip->access_fee + $trip->driver_or_company_commission)}}</td>
                                </tr>
                            <?php   }else { ?>
                                <tr>
                                    <td>Alesha Ride Will Pay You</td>
                                    <td class="text-right">&#2547; 0.00 </td>
                                </tr>
                                <tr>
                                    <td>Payable to Alesha Ride</td>
                                    <td class="text-right">&#2547; 0.00 </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="button">Net Income</div>
                                    </td> 
                                    <td class="text-right">&#2547; {{$trip->total_fare}}</td>
                                </tr>
                            <?php   } ?>

                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection