<title>Invoice</title>
@extends('template_without_header_footer')
@section('main')
<style>
    h1{
        padding-left    :   50px;
        font-size       :   30px;
        text-align: center;
        font-weight     :   bold;
    }
    table{
        border          :   1px solid black;
        border-collapse :   collapse;
        width           :   50%;
        font-size       :   16px;
    }
    tr, td{
        border          :   1px solid black;
        padding-left    :   15px;
    }
    th{
        padding-left    :   15px;
    }
    tr{
        border-collapse :   collapse;
    }
    div{
        padding-top     :   20px;
    }
    div{
        padding-top     :   25px;
    }
    img {
        border: 1px solid #c2c2c2;
        border-radius: 470% !important;
        object-fit: cover;
        height: 150px;
        width: 150px;
    }
    p{
        line-height: 15px;
    }
    .no-border,.no-border tr,.no-border td {
        border: none;
    }
    .width-60 {
        width: 40%;
    }
    .width-40 {
        width: 60%;
    }
</style>

<div style="padding-top: 10px; width:100%;" >
    <div ><h1>{{trans('messages.dashboard.trip_invoice')}}</h1>
    </div>
    <div >
        <table width="100%">
            <thead >
                <tr>
                    <th>{{trans('messages.dashboard.invoice_no')}}</th>
                    <th >{{trans('messages.dashboard.trip_date')}}</th>
                    <th>{{trans('messages.dashboard.invoice')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr  >
                    <td data-title="Invoice Number"> {{ $trip->id }} </td>
                    <td data-title="Trip date"> {{ date('F d, Y',strtotime($trip->created_at))}} </td>
                    <td data-title="Invoice"> {{ $trip->currency->original_symbol }}  {{ $trip->total_fare }} </td>
                </tr>
            </tbody>
        </table>
        <table class="no-border" width="100%">
            <tr>
                <td class="width-60">
                    <div class="col-sm-6">
                        <img src="{{ $trip->driver_thumb_image }}" >
                    </div>
                </td>
                <td class="width-40">
                    <div class="col-sm-6">
                        <p>{{trans('messages.dashboard.invoice_issued')}} {{$site_name}}{{trans('messages.dashboard.behalf')}}</p>
                        <p>{{ $trip->driver_name }}</p>
                        <p class="flush">{{ $trip->pickup_time }}</p>
                        <h6 class="color--neutral flush">{{!! html_entity_decode ($trip->pickup_location,ENT_QUOTES, 'UTF-8' ) !!}}</h6><br>
                        <p class="flush">{{ $trip->drop_time }}</p>
                        <h6 class="color--neutral flush">{{!! html_entity_decode ($trip->drop_location,ENT_QUOTES, 'UTF-8' )!!}}</h6><br>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div>
        {{-- <table>
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
    
</div>
</div>
</div>
</div>
</div>
</main>
<style type="text/css">
</style>
@stop