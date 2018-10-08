<!-- BEGIN HEADER -->
<div class="page-header">
    <!-- BEGIN HEADER TOP -->
    <div class="page-header-top">
        <div class="container">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
				<a href="{{ url('/') }}">
                <img src="{{ asset("images/pd-logo.png")}}" alt="logo" class="logo-default" style="max-width: 100px;margin-top: 15px !important; max-height: 250px">
				</a>
            </div>
            <!-- END LOGO -->
        </div>
    </div>
    <!-- END HEADER TOP -->
    <!-- BEGIN HEADER MENU -->
    <div class="page-header-menu">
        <div class="container">
            <!-- BEGIN MEGA MENU -->
            <div class="hor-menu  ">
                <ul class="nav navbar-nav">
                    <li class="active">
                        <a href="{{ url('members')}}"> 
                            List Members
                        </a>
                    </li>
                    <li class="menu-dropdown classic-menu-dropdown">
                        <a href="javascript:;">Bachat Mandal<span class="arrow"></span></a>
                        <ul class="dropdown-menu pull-left">
                            <li>
                                <a class="nav-link" href="{{url('member-accounts')}}">Bachat Members</a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{url('multiple-account')}}">Bachat Accounts</a>
                            </li>
                            <li>
                                <a class="nav-link" href="{{url('loans')}}">Bachat Loan Accounts</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- END MEGA MENU -->
        </div>
    </div>
    <!-- END HEADER MENU -->
</div>
<div id="AjaxLoaderDiv" style="display: none;z-index:99999 !important;">
    <div style="width:100%; height:100%; left:0px; top:0px; position:fixed; opacity:0; filter:alpha(opacity=40); background:#000000;z-index:999999999;">
    </div>
    <div style="float:left;width:100%; left:0px; top:50%; text-align:center; position:fixed; padding:0px; z-index:999999999;">
        <img src="{{ asset('/') }}/images/ajax-loader.gif">
        </center>
    </div>
</div>
