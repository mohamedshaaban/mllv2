<!-- =================================================== -->
<!-- ========== Top menu items (ordered left) ========== -->
<!-- =================================================== -->
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php
$lang = session('locale');
\App::setLocale($lang);
?>
<a href="{{ route('switch_lang' , [($lang == 'en' || !$lang || $lang =='') ? 'ar' : 'en'])}}" style="margin: 0 0 0 47px;z-index:10000;" class="main"
>

    @if( $lang == 'en' || !$lang || $lang =='')

        عربي
    @else

        English
    @endif



</a>
<ul class="nav navbar-nav d-md-down-none">

    @if (backpack_auth()->check())
        <!-- Topbar. Contains the left part -->
        @include(backpack_view('inc.topbar_left_content'))

    @endif

</ul>
<!-- ========== End of top menu left items ========== -->


<!-- ========================================================= -->
<!-- ========= Top menu right items (ordered right) ========== -->
<!-- ========================================================= -->
<ul class="nav navbar-nav ml-auto @if(session('locale') == 'ar') mr-0 @endif">
    @if (backpack_auth()->guest())
        <li class="nav-item"><a class="nav-link" href="{{ route('backpack.auth.login') }}">{{ trans('backpack::base.login') }}</a>
        </li>
        @if (config('backpack.base.registration_open'))
            <li class="nav-item"><a class="nav-link" href="{{ route('backpack.auth.register') }}">{{ trans('backpack::base.register') }}</a></li>
        @endif
    @else
        <!-- Topbar. Contains the right part -->
        @include(backpack_view('inc.topbar_right_content'))
        @include(backpack_view('inc.menu_user_dropdown'))
    @endif
</ul>
<!-- ========== End of top menu right items ========== -->
