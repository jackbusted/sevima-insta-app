@extends('homeuser.layouts.main')

@section('container')
<style>
    .feature-icon {
        width: 4rem;
        height: 4rem;
        border-radius: .75rem;
    }

    .icon-square {
        width: 3rem;
        height: 3rem;
        border-radius: .75rem;
    }

    .icon-image {
        width: 2rem;
        height: 2rem;
        border-radius: .75rem;
    }

    .text-shadow-1 {
        text-shadow: 0 .125rem .25rem rgba(0, 0, 0, .25);
    }

    .text-shadow-2 {
        text-shadow: 0 .25rem .5rem rgba(0, 0, 0, .25);
    }

    .text-shadow-3 {
        text-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .25);
    }

    .card-cover {
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
    }

    .feature-icon-small {
        width: 3rem;
        height: 3rem;
    }
</style>

<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome, {{ auth()->user()->name }}</h1>
</div>

<div id="hanging-icons">
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div id="schedule-notice" style="display: none;">
            <div class="col d-flex align-items-start">
                <div class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-check-fill icon-image" viewBox="0 0 16 16">
                        <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2m-5.146-5.146-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708.708"/>
                    </svg>
                </div>
                <div>
                    <h3 class="fs-2 text-body-emphasis">Schedule</h3>
                    <p>You have registered for the exam schedule</p>
                    <p id="exam-date"></p>
                    <p id="exam-time"></p>
                    <p id="register-status">
                        Status : <span id="status-badge" class="badge"></span>
                    </p>
                </div>
            </div>
        </div>
        <div id="incoming-notice" style="display: none;">
            <div class="col d-flex align-items-start">
                <div class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-alarm-fill icon-image" viewBox="0 0 16 16">
                        <path d="M6 .5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H9v1.07a7.001 7.001 0 0 1 3.274 12.474l.601.602a.5.5 0 0 1-.707.708l-.746-.746A6.97 6.97 0 0 1 8 16a6.97 6.97 0 0 1-3.422-.892l-.746.746a.5.5 0 0 1-.707-.708l.602-.602A7.001 7.001 0 0 1 7 2.07V1h-.5A.5.5 0 0 1 6 .5m2.5 5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9zM.86 5.387A2.5 2.5 0 1 1 4.387 1.86 8.04 8.04 0 0 0 .86 5.387M11.613 1.86a2.5 2.5 0 1 1 3.527 3.527 8.04 8.04 0 0 0-3.527-3.527"/>
                    </svg>
                </div>
                <div>
                    <h3 class="fs-2 text-body-emphasis" id="incoming-title"></h3>
                    <p id="incoming-message"></p>
                    <p id="incoming-date"></p>
                    <p id="incoming-time"></p>
                    @if ($joinUrl != null)
                        <a href="{{ $joinUrl }}" target="_blank" class="btn btn-primary">
                            Join Zoom
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div id="profile-notice" style="display: none;">
            <div class="col d-flex align-items-start">
                <div class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-square-fill icon-image" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="fs-2 text-body-emphasis">Fill NPM</h3>
                    <p>Please complete your profile</p>
                    <a href="{{ URL::route('homeuser.setting-user') }}" class="btn btn-primary">
                        Set up
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    @if (isset($showScheduleNotice))
        var show = false;
        show = {!! json_encode($showScheduleNotice) !!}
        if (show) {
            const scheduleNotice = document.getElementById('schedule-notice');
            scheduleNotice.style.display = 'block';

            let rawDate = {!! json_encode($date) !!}
            let rawTime = {!! json_encode($time) !!}
            let formattedDate = formatDate(rawDate);
            let formatteTime = formatTime(rawTime);
            let status = {!! json_encode($paymentStatus) !!}
            let statusBadge = $("#status-badge");

            $("#exam-date").text('Date : '+formattedDate);
            $("#exam-time").text('Time : '+formatteTime+" wib")
            if (status === "Confirmed") {
                statusBadge.text("Confirmed");
                statusBadge.addClass("badge-success");
            } else if (status === "Rejected") {
                statusBadge.text("Rejected");
                statusBadge.addClass("badge-danger");
            } else {
                statusBadge.text(status);
                statusBadge.addClass("badge-secondary");
            }
        }
    @endif

    @if (isset($showIncomingNotice))
        var show = false;
        var isAlreadyStarted = false;
        show = {!! json_encode($showIncomingNotice) !!}
        isAlreadyStarted = {!! json_encode($isAlreadyStarted) !!}
        if (show) {
            const incomingNotice = document.getElementById('incoming-notice');
            incomingNotice.style.display = 'block';

            let rawDate = {!! json_encode($incomingDate) !!}
            let rawTime = {!! json_encode($incomingTime) !!}
            let formattedDate = formatDate(rawDate);
            let formatteTime = formatTime(rawTime);

            if (isAlreadyStarted) {
                $("#incoming-title").text('Already Started')
                $('#incoming-message').text('Your exam is already started! Please join the room immediately!')
                $("#incoming-date").text('Date : '+formattedDate)
                $("#incoming-time").text('Time : '+formatteTime+" wib")
            } else {
                $("#incoming-title").text('Incoming Exam')
                $('#incoming-message').text('You have incoming exam schedule.')
                $("#incoming-date").text('Date : '+formattedDate)
                $("#incoming-time").text('Time : '+formatteTime+" wib")
            }
        }
    @endif

    @if (isset($showNpmNotice))
        var show = false;
        show = {!! json_encode($showNpmNotice) !!}
        if (show) {
            const profileNotice = document.getElementById('profile-notice');
            profileNotice.style.display = 'block';
        }
    @endif

    function formatDate(dateString) {
        let date = new Date(dateString);
        let options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };

        return date.toLocaleDateString('en-US', options);
    }

    function formatTime(timeString) {
        let timeParts = timeString.split(':');
        let formattedTime = timeParts[0] + ':' + timeParts[1];

        return formattedTime;
    }
</script>
@endsection