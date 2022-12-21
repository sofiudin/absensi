<div class="app-header header-shadow">
    <div class="app-header__logo">
        <div class="logo-src">
        </div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="app-header__content">
        <div class="app-header-left">
            <ul class="header-menu nav">
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-link-icon fa fa-clock"></i>
                        {{date('d-m-Y H:i:s')}}
                    </a>
                </li>
            </ul>
        </div>
        <div class="app-header-right">
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    <img width="42" class="rounded-circle" src="{{url('architect/css/images/pic.jpg')}}" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="widget-content-left  ml-3 header-user-info">
                            <div class="widget-heading">
                                Admin
                            </div>
                            <div class="widget-subheading">
                                Administrator
                            </div>
                        </div>
                        <div class="widget-content-right header-user-info ml-3">
                            <a href="{{ route('register') }}" class="btn-shadow p-1 btn btn-success btn-sm">
                                <i class="fa fa-user-plus text-white pr-1 pl-1"></i> Register
                            </a>
                            <a href="{{ route('logout') }}" class="btn-shadow p-1 btn btn-primary btn-sm">
                                <i class="fa fa-arrow-circle-left text-white pr-1 pl-1"></i> LogOut
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
