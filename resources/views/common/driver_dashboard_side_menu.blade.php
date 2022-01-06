<div class="container mar-zero" style="padding:0px;">
    <div class="col-lg-10 col-md-10 col-sm-13 col-xs-12  height--full dash-panel">
        <div class="height--full pull-left separated--sides full-width">
            <div style="padding:0px;" class="col-lg-3 col-md-3 col-sm-3 col-xs-12 flexbox__item one-fifth page-sidebar hidden--portable hide-sm-760">
                <ul class="site-nav">
                    <li class="soft--ends">
                        <div class="center-block three-quarters push-half--bottom">
                            <div class="img--circle img--bordered img--shadow fixed-ratio fixed-ratio--1-1">
                                @if(@Auth::user()->profile_picture->src == '')
                                <img src="{{ url('images/user.jpeg')}}" class="img--full fixed-ratio__content">
                                @else
                                <img src="{{ @Auth::user()->profile_picture->src }}"  class="img--full fixed-ratio__content profile_picture">
                                @endif
                            </div>
                        </div>
                        <div class="text--center">
                            <div style="    font-size: 16px;
                            font-weight: 200;">{{ @Auth::user()->first_name}} {{ @Auth::user()->last_name}}</div>
                            <div class="soft-half--top">
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="{{ url('driver_profile') }}" aria-selected="{{ (Route::current()->uri() == 'driver_profile') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.header.profil')}}</a>
                    </li>
                    <li>
                        <a href="{{ url('documents/'.@Auth::user()->id) }}" aria-selected="{{ (Route::currentRouteName() == 'documents') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.driver_dashboard.driver_documents')}}</a>
                    </li>
                     <li>
                        <a href="{{ url('vehicle/'.@Auth::id()) }}" aria-selected="{{ in_array(Route::currentRouteName(),array('vehicle','add_vehicle','edit_vehicle')) ? 'true' : 'false' }}"  class="side-nav-a" >{{trans('messages.driver_dashboard.vehicle')}}</a>
                    </li>
                    <li>
                        <a href="{{ url('driver_payment') }}" aria-selected="{{ (Route::current()->uri() == 'driver_payment') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.header.payment')}}</a>
                    </li>

                    {{-- <li>
                        <a href="{{ url('payout_preferences') }}" aria-selected="{{ (Route::current()->uri() == 'payout_preferences') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.account.payout')}}</a>
                    </li> --}}

                    <li>
                        <a href="javascript:void(0);" class="side-nav-a social-dropdown-btn 
                         @if(
                            Route::current()->uri() == 'paid_to_alesha' || Route::current()->uri() == 'received_from_alesha/{type}'
                        )
                        active
                        @endif
                        ">Transaction History<i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-container site_nav" 
                        @if(
                            Route::current()->uri() == 'paid_to_alesha' || Route::current()->uri() == 'received_from_alesha/{type}'
                        )
                        style="display: block;"
                        @endif >

                            <li style="display: flex;">
                                <a href="{{ url('paid_to_alesha') }}" aria-selected="{{ (Route::current()->uri() == 'paid_to_alesha') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.api.paid_to_alesha')}}</a>
                            </li>
                            <li style="display: flex;">
                                <a href="{{ url('received_from_alesha/balance') }}" aria-selected="{{ (Route::current()->uri() == 'received_from_alesha/{type}') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.api.received_from_alesha')}}</a>
                            </li>

                            <!-- Paid to Alesha
                            Collected from Alesha
                                ->withdraw Balance
                                ->payout -->
                        </ul>
                    </li>

                    <li>
                        <a href="{{ url('driver_invoice') }}" aria-selected="{{ (Route::current()->uri() == 'driver_invoice') ? 'true' : 'false' }}"  class="side-nav-a" >{{trans('messages.header.invoice')}}</a>
                    </li>
                    <li>
                        <a href="{{ url('driver_trip') }}" aria-selected="{{ (Route::current()->uri() == 'driver_trip') ? 'true' : 'false' }}"  class="side-nav-a">{{trans('messages.header.mytrips')}}</a>
                    </li>
                    <!--Commented By Nishat Start-->
                    
                    <!--<li>-->
                    <!--    <a href="{{ route('driver_payout_preference') }}" aria-selected="{{ (Route::current()->uri() == 'payout_preferences') ? 'true' : 'false' }}" class="sidenav-item">{{trans('messages.account.payout')}}</a>-->
                    <!--</li>-->
                    <!--Commented By Nishat End-->
                    
                    @if(Auth::user()->company_id == '1')
                    <li>
                        <a href="{{ route('driver_referral') }}" aria-selected="{{ (Route::current()->uri() == 'driver_referral') ? 'true' : 'false' }}" class="side-nav-a">
                            {{trans('messages.referrals.referral')}}
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="javascript:void(0);" class="side-nav-a social-dropdown-btn">Support<i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-container site_nav">
                            @foreach($support_links as $support_link)
                            @if($support_link->id==1)
                                @php $support_link->link = 'https://web.whatsapp.com/send?phone=+'.$support_link->link @endphp
                            @elseif($support_link->id==2)
                                @php $support_link->link = 'https://join.skype.com/invite/'.$support_link->link @endphp
                            @endif
                            <li style="display: flex;align-items: center;">
                                <img src="{{ $support_link->image_src }}" style="width: 20px;height: 20px;margin-right: 10px;">
                                <a target="_blank" class="side-nav-a" href="{{ $support_link->link }}">{{ $support_link->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    <li>
                        <a href="{{ url('sign_out')}}">{{trans('messages.header.logout')}}</a>
                    </li>
                </ul>
            </div>
