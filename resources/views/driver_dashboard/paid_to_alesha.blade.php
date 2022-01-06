<title>Paid to Alesha</title>
@extends('template_driver_dashboard')
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content driver_payment" style="padding:0px;" ng-controller="payment" ng-cloak>
    <div class="page-lead separated--bottom  text--center text--uppercase">
        <h1 class="flush-h1 flush"> @lang('messages.api.paid_to_alesha') </h1>
    </div>

    <div style="padding:10px;">
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
                @foreach($list as $sl => $r)
                <tr>
                    <td>{{($sl+1)}}</td>
                    <td>{{$r['transaction_id']}}</td>
                    <td>{{$r['amount']}}</td>
                    <td>{{date('d-m-Y', strtotime($r['created_at']))}}</td>
                </tr>
                @endforeach
            </tbody>

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

