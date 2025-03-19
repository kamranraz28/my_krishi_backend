<header class="navbar pcoded-header navbar-expand-lg navbar-light header-dark">
    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        <a href="#!" class="b-brand">

        <img src="{{ asset('assets/images/logo.png') }}" alt="Software Logo" class="logo">

        </a>
    </div>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li>
                @can('notification_access')
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <i class="icon feather icon-bell"></i>
                        <span class="badge badge-pill badge-danger">{{ $notifications->count() }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notification">
                        <div class="noti-head">
                            <h6 class="d-inline-block m-b-0">Notifications</h6>
                            <div class="float-right">
                                <a href="{{ route('clearAll')}}">clear all</a>
                            </div>
                        </div>
                        <ul class="noti-body">
                            @foreach ($notifications as $notification)
                                <li class="notification" >
                                    <div class="media">
                                        @php
                                            // Get the user who created the notification
                                            $creator = $notification->created_by ? $notification->created_by()->first() : null;
                                        @endphp

                                        <img class="img-fluid rounded-circle"
                                            src="{{ $creator && $creator->image ? asset('storage/img/' . $creator->image) : asset('assets/images/user/avatar-2.jpg') }}"
                                            alt="User-Profile-Image" style="width: 35px; height: 35px;">

                                        <div class="media-body">
                                            <p><strong>{{ $notification->message }}</strong>
                                                <span style="float: right;">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endcan
            </li>
            <li>
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="feather icon-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-notification">
                        <div class="pro-head">
                            @if (Auth::user()->image !== null)
                                <img class="img-fluid rounded-circle"
                                    src="{{ asset('storage/img/' . Auth::user()->image) }}" alt="User-Profile-Image"
                                    style="width: 40px; height: 40px;">
                            @else
                                <img class="img-fluid rounded-circle" src="{{ asset('assets/images/user/avatar-2.jpg') }}"
                                    alt="User-Profile-Image" style="width: 40px; height: 40px;">
                            @endif
                            <span>{{ Auth::user()->name }}</span>
                            <a href="{{ route('userLogout') }}" class="dud-logout" title="Logout">
                                <i class="feather icon-log-out"></i>
                            </a>
                        </div>
                        <ul class="pro-body">
                            <li><a href="" class="dropdown-item"><i
                                        class="feather icon-user"></i> Profile</a></li>
                            <li><a href="{{ route('userLogout') }}" class="dropdown-item"><i
                                        class="feather icon-lock"></i> Lock Screen</a></li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
