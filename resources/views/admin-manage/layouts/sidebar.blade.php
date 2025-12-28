<style>
    #sidebar ul.list-unstyled li a {
        text-decoration: none; /* Menghilangkan garis bawah pada tautan */
    }

    #sidebar ul.list-unstyled li.active a {
        font-weight: bold; /* Jika Anda ingin memberi gaya khusus pada tautan aktif */
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
        <li class="{{ Request::is('admin-manage') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" aria-current="page" href="/admin-manage">
                <i class="bi bi-house-fill"></i>
                Dashboard
            </a>
        </li>
        <li class="{{ Request::is('admin-manage/create-question*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/admin-manage/create-question">
                <i class="bi bi-file-earmark-plus"></i>
                Create Questions
            </a>
        </li>
        <li class="{{ Request::is('admin-manage/payment*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/admin-manage/payment">
                <i class="bi bi-card-checklist"></i>
                Approval and Schedule
            </a>
        </li>
        <li class="{{ Request::is('admin-manage/create-schedule*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/admin-manage/create-schedule">
                <i class="bi bi-calendar2-week-fill"></i>
                Manage Exam Schedule
            </a>
        </li>
        <li class="{{ Request::is('admin-manage/manage-score*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/admin-manage/manage-score">
                <i class="bi bi-ui-checks"></i>
                Manage Score
            </a>
        </li>
        <li class="{{ Request::is('admin-manage/setting-profile*') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-2" href="/admin-manage/setting-profile">
                <i class="bi bi-wrench"></i>
                Setting Profile
            </a>
        </li>
        {{-- <li class="{{ Request::is('admin-manage/review-quiz') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-3 mr-3" href="/admin-manage/review-quiz">
                <i class="bi bi-collection-fill"></i>
                Review Generated Listening
            </a>
        </li>
        <li class="{{ Request::is('admin-manage/review-quiz-structure') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-3 mr-3" href="/admin-manage/review-quiz-structure">
                <i class="bi bi-collection-fill"></i>
                Review Generated Structure
            </a>
        </li>
        <li class="{{ Request::is('admin-manage/review-quiz-reading') ? 'active' : '' }}">
            <a class="nav-link d-flex gap-3 mr-3" href="/admin-manage/review-quiz-reading">
                <i class="bi bi-collection-fill"></i>
                Review Generated Reading
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