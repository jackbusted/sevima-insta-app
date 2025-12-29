<style>
    #sidebar ul.list-unstyled li a {
        text-decoration: none;
    }

    #sidebar ul.list-unstyled li.active a {
        font-weight: bold;
    }
</style>

<nav id="sidebar">
    <div class="custom-menu">
        <button type="button" id="sidebarCollapse" class="btn btn-primary"></button>
    </div>
    <div class="img bg-wrap text-center py-4" style="background-image: url({{ asset('images/bg_1.jpg') }});">
        @if (auth()->check())
        <div class="user-logo">
            <div class="img" style="background-image: url({{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('images/logo.png') }});"></div>
            <h3>{{ auth()->user()->name }}</h3>
        </div>
        @endif
    </div>
    <ul class="list-unstyled components mb-5">
        <li class="{{ Request::is('homeuser') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" aria-current="page" href="/homeuser">
                <i class="bi bi-house-fill"></i>
                Home
            </a>
        </li>
        {{-- <li class="{{ Request::is('homeuser/test-registration*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/homeuser/test-registration">
                <i class="bi bi-file-earmark"></i>
                Exam Registration
            </a>
        </li>
        <li class="{{ Request::is('homeuser/history*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/homeuser/history">
                <i class="bi bi-cart"></i>
                Exam's History
            </a>
        </li> --}}
        <li class="{{ Request::is('homeuser/setting-user*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/homeuser/setting-user">
                <i class="bi bi-wrench"></i>
                Setting Profile
            </a>
        </li>
        {{-- <li>
            <a class="nav-link d-flex gap-2" target="_blank" href="/homeuser/start-test">
                <i class="bi bi-people"></i>
                START EXAM
            </a>
        </li> --}}
        <li>
            <form action="/logout" method="post">
                @csrf
                <a class="nav-link d-flex gap-2" href="">
                    <button type="submit" class="nav-link d-flex gap-2">
                        <i class="bi bi-box-arrow-right"></i>
                        Sign Out
                    </button>
                </a>
            </form>
        </li>
    </ul>
</nav>