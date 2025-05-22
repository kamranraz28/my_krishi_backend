<nav class="pcoded-navbar">
    <div class="navbar-wrapper">
        <div class="navbar-content scroll-div">
            <div class="">
                <div class="main-menu-header">
                    @if (Auth::user()->image !== null)
                        <img class="img-fluid rounded-circle" src="{{ asset('storage/img/' . Auth::user()->image) }}"
                            alt="User-Profile-Image" style="width: 40px; height: 40px;">
                    @else
                        <img class="img-fluid rounded-circle" src="{{ asset('assets/images/user/avatar-2.jpg') }}"
                            alt="User-Profile-Image" style="width: 40px; height: 40px;">
                    @endif

                    <div class="user-details">
                        <span>{{ Auth::user()->name }}</span>
                        <div id="more-details">
                            Admin
                            <i class="fa fa-chevron-down m-l-5"></i>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="nav-user-link">
                    <ul class="list-unstyled">
                        <li class="list-group-item"><a href=""><i
                                    class="feather icon-user m-r-5"></i>View Profile</a></li>
                        <li class="list-group-item"><a href="{{ route('userLogout') }}"><i
                                    class="feather icon-log-out m-r-5"></i>Logout</a></li>
                    </ul>
                </div>
            </div>

            <ul class="nav pcoded-inner-navbar">
                <li class="nav-item pcoded-menu-caption">
                    <label>Navigation</label>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                        <span class="pcoded-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('projects.index') }}" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-box"></i></span>
                        <span class="pcoded-mtext">Projects</span>
                    </a>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-align-justify"></i></span>
                        <span class="pcoded-mtext">People</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('agents') }}">Agent</a></li>
                        <li><a href="{{ route('investors') }}">Investor</a></li>
                    </ul>
                </li>

                <li class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-align-justify"></i></span>
                        <span class="pcoded-mtext">Bookings</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('officePendingPayment') }}">Office Payment Review</a></li>
                        <li><a href="{{ route('bankPendingPayment') }}">Bank Payment Review</a></li>

                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('terms.index') }}" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-box"></i></span>
                        <span class="pcoded-mtext">Terms & Conditions</span>
                    </a>
                </li>



            </ul>
        </div>
    </div>
</nav>
