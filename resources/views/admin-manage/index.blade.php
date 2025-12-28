@extends('admin-manage.layouts.main')

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

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Welcome, {{ auth()->user()->name }}</h1>
</div>

<div class="hanging-icons">
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        @foreach ($activeSchedules as $data)
            <div class="col d-flex align-items-start">
                <div class="d-flex flex-column">
                    <div class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar3 icon-image" viewBox="0 0 16 16">
                            <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z"/>
                            <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                        </svg>
                    </div>
                    <div class="icon-square text-body-emphasis bg-body-secondary d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                        <a href="{{ URL::route('admin.manage-registration.list', ['id' => $data['id']]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle icon-image" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="fs-2 text-body-emphasis">Schedule</h3>
                    <p style="margin-bottom: 1mm;"><strong>There is active schedule</strong></p>
                    <p style="margin-bottom: 1mm;">Schedule's Name : {{ $data['name'] }}</p>
                    <p style="margin-bottom: 1mm;">Class : {{ $data['class_test'] }}</p>
                    <p style="margin-bottom: 1mm;">Execution : {{ $data['execution'] }}</p>
                    <p style="margin-bottom: 1mm;" id="exam-date-{{ $loop->index }}"></p>
                    <p style="margin-bottom: 1mm;" id="exam-time-{{ $loop->index }}"></p>
                    <p style="margin-bottom: 1mm;" id="exam-status-{{ $loop->index }}">
                        Status : <span id="status-badge-{{ $loop->index }}" class="badge"></span>
                    </p>
                    <p style="margin-bottom: 1mm;">Unconfirmed : {{ $data['unconfirmed'] }}</p>
                    <p style="margin-bottom: 1mm;">Confirmed : {{ $data['confirmed'] }}</p>
                </div>
                <script>
                    (function() {
                        let rawDate = {!! json_encode($data['open_date']) !!};
                        let rawTime = {!! json_encode($data['exe_clock']) !!};
                        let formattedDate = formatDate(rawDate);
                        let formattedTime = formatTime(rawTime);
                        let status = {!! json_encode($data['status']) !!}

                        let dateElement = document.getElementById('exam-date-{{ $loop->index }}');
                        let timeElement = document.getElementById('exam-time-{{ $loop->index }}');
                        let statusBadge = document.getElementById('status-badge-{{ $loop->index }}');

                        dateElement.textContent = 'Date : ' + formattedDate;
                        timeElement.textContent = 'Time : ' + formattedTime + " wib";

                        switch (status) {
                            case "Ready to open":
                                statusBadge.textContent = "Ready to open";
                                statusBadge.classList.add("badge-secondary");
                                break;
                            case "Not started yet":
                                statusBadge.textContent = "Not started yet";
                                statusBadge.classList.add("badge-info");
                                break;
                            case "Already started":
                                statusBadge.textContent = "Already started";
                                statusBadge.classList.add("badge-success");
                                break;
                            default:
                                break;
                        }

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
                    })();
                </script>
            </div>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
@endsection