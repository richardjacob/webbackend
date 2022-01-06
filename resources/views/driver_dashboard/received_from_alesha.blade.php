<title>Received from Alesha</title>
@extends('template_driver_dashboard')
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content driver_payment" style="padding:0px;" ng-controller="payment" ng-cloak>
    <div class="page-lead separated--bottom  text--center text--uppercase">
        <h1 class="flush-h1 flush"> @lang('messages.api.received_from_alesha') </h1>
    </div>

    <div style="padding:10px;">
        <div class="text-right" style="padding-bottom:10px;">
             <a class="btn" href="{{ url('received_from_alesha/balance') }}" style="
             @if($type == 'balance') 
                background: #007BFF; color:#fff !important; 
            @else 
                background: #eee;color:#000 !important; 
            @endif
            ">@lang('messages.api.balance_withdraw')</a>

            <a class="btn" href="{{ url('received_from_alesha/payout') }}"
            style="
                @if($type == 'payout') 
                    background: #007BFF; color:#fff !important; 
                @else 
                    background: #eee;color:#000 !important; 
                @endif
            ">@lang('messages.api.payout')</a>
        </div>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th class="bg-default">SL#</th>
                    <th>Transaction Id</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @if($type == 'payout')
                    @foreach($list as $sl => $r)
                    <tr>
                        <td>{{($sl+1)}}</td>
                        <td>{{$r['transaction_id']}}</td>
                        <td>{{$r['amount']}}</td>
                        <td>{{date('d-m-Y', strtotime($r['transaction_date']))}}</td>
                    </tr>
                    @endforeach
                @else
                    @foreach($list as $sl => $r)
                    <tr>
                        <td>{{($sl+1)}}</td>
                        <td>{{$r['transaction_id']}}</td>
                        <td>{{$r['amount']}}</td>
                        <td>{{date('d-m-Y', strtotime($r['created_at']))}}</td>
                    </tr>
                    @endforeach
                @endif                
            </tbody>
            <!-- transaction_date -->
        </table>
        <div class="text-right">Page {{$list->currentPage()}} of {{$list->lastPage()}}</div>
        <div class="text-center"> {{ $list->links() }}</div>


    </div>
    


</div>
</div>
</div>
</div>
</div>
</main>
@endsection

