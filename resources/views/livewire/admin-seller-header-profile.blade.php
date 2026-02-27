<div>
        @if (Auth::guard('moh')->check())
            <div class="user-info-dropdown">
                <div class="dropdown">
                    <a
                        class="dropdown-toggle"
                        href="#"
                        role="button"
                        data-toggle="dropdown"
                    >
                        <span class="user-icon">
                            <img src="{{ $admin->picture }}" alt="" />
                        </span>
                        <span class="user-name">{{ $admin->name }}</span>
                    </a>
                    <div
                        class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list"
                    >
                        <a class="dropdown-item" href="{{ route('moh.profile') }}"
                            ><i class="dw dw-user1"></i> Profile</a
                        >

                        <a class="dropdown-item" href="{{ route('moh.logout_handler') }}" onclick="event.preventDefault(); document.getElementById('adminLogoutForm').submit();"
                            ><i class="dw dw-logout"></i> Log Out</a
                        >
                        <form id="adminLogoutForm" action="{{ route('moh.logout_handler') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        @elseif( Auth::guard('midwife')->check())
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a
                    class="dropdown-toggle"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                >
                    <span class="user-icon">
                        <img src="{{ $seller->picture }}" alt="" />
                    </span>
                    <span class="user-name">{{ $seller->name }}</span>
                </a>
                <div
                    class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list"
                >
                    <a class="dropdown-item" href="{{ route('midwife.profile') }}"
                        ><i class="dw dw-user1"></i> Profile</a
                    >
                    <a class="dropdown-item" href="{{ route('midwife.logout') }}" onclick="event.preventDefault();document.getElementById('sellerLogoutForm').submit();"
                        ><i class="dw dw-logout"></i> Log Out</a
                    >
                    <form action="{{ route('midwife.logout') }}" id="sellerLogoutForm" method="POST">@csrf</form>
                </div>
            </div>
        </div>
        @elseif( Auth::guard('parent')->check())
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a
                    class="dropdown-toggle"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                >
                    <span class="user-icon">
                        <img src="{{ $client->picture }}" alt="" />
                    </span>
                    <span class="user-name">{{ $client->name }}</span>
                </a>
                <div
                    class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list"
                >
                    <a class="dropdown-item" href="{{ route('parent.profile') }}"
                        ><i class="dw dw-user1"></i> Profile</a
                    >
                   
                    <a class="dropdown-item" href="{{ route('parent.logout') }}" onclick="event.preventDefault();document.getElementById('clientLogoutForm').submit();"
                        ><i class="dw dw-logout"></i> Log Out</a
                    >
                    <form action="{{ route('parent.logout') }}" id="clientLogoutForm" method="POST">@csrf</form>
                </div>
            </div>
        </div>
        @endif
</div>
