<div class="container mar-zero" style="padding:0px;">
	<div class="col-lg-10 col-md-10 col-sm-13 col-xs-12 height--full dash-panel">
		<div class="height--full separated--sides pull-left full-width">
			<div style="padding:0px;" class="col-lg-3 col-md-3 col-sm-3 col-xs-12 flexbox__item one-fifth page-sidebar hidden--portable hide-sm-760">
				<ul class="site-nav">
					<li class="soft--ends">
						<div class="center-block three-quarters push-half--bottom">
							<div class="img--circle img--bordered img--shadow fixed-ratio fixed-ratio--1-1">
								@if(@Auth::user()->profile_picture->src == '')
								<img src="{{ url('images/user.jpeg')}}" class="img--full fixed-ratio__content">
								@else
								<img src="{{ @Auth::user()->profile_picture->src }}" class="img--full fixed-ratio__content profile_picture">
								@endif
							</div>
						</div>
						<div class="text--center">
							<div style="font-size: 16px;font-weight: 200;">
								{{ @Auth::user()->first_name}} {{ @Auth::user()->last_name}}
							</div>
							<div class="soft-half--top"></div>
						</div>
					</li>
					<li>
						<a href="{{ url('trip') }}" aria-selected="{{ (Route::current()->uri() == 'trip') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.header.mytrips')}}</a>
					</li>
					<li>
						<a href="{{ url('profile') }}" aria-selected="{{ (Route::current()->uri() == 'profile') ? 'true' : 'false' }}" class="side-nav-a">{{trans('messages.header.profil')}}</a>
					</li>
					<li>
						<a href="{{ route('referral') }}" aria-selected="{{ (Route::current()->uri() == 'referral') ? 'true' : 'false' }}" class="side-nav-a">
							{{trans('messages.referrals.referral')}}
						</a>
					</li>
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
					<li><a href="{{ url('sign_out')}}">{{trans('messages.header.logout')}}</a></li>
				</ul>
			</div>
