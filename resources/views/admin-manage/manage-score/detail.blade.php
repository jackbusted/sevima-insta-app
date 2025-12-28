@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Participant's Score</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-score.view') }}">
            <button
                id="back-btn"
                name="back-btn"
                class="btn btn-outline-danger"
                type="button"
            ><i class="bi bi-arrow-left"></i>Back</button>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <h4>Participant's Detail</h4>
        <hr>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" value="{{ $name }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="npm" class="form-label">NPM</label>
            <input type="text" class="form-control" name="npm" id="npm" value="{{ $npm }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="schedule-name" class="form-label">Schedule's Name</label>
            <input type="text" class="form-control" name="schedule-name" id="schedule-name" value="{{ $scheduleName }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="exam-class" class="form-label">Class</label>
            <input type="text" class="form-control" name="exam-class" id="exam-class" value="{{ $examClass }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="execution" class="form-label">Execution</label>
            <input type="text" class="form-control" name="execution" id="execution" value="{{ $execution }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="exam-date" class="form-label">Date</label>
            <input type="text" class="form-control" name="exam-date" id="exam-date" value="" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="exam-clock" class="form-label">Time</label>
            <input type="text" class="form-control" name="exam-clock" id="exam-clock" value="" disabled readonly>
        </div>
    </div>

    <div class="col-lg-6">
        <h4>Score Section</h4>
        <hr>
        <div class="mb-3">
            <label for="score-status" class="form-label">Score's Status</label>
            <br>
            @if ($approvalStatus)
                <span class="badge text-bg-success">Approved</span>
            @else
                <span class="badge text-bg-warning">Waiting Approval</span>
            @endif
        </div>
        <div class="mb-3">
            <label for="final-score" class="form-label">Final Score</label>
            <input type="number" class="form-control" name="final-score" id="final-score" value="{{ $realScore }}" disabled readonly>
        </div>
        <div class="mb-3">
            <label for="admin-score" class="form-label">Score From Admin</label>
            <input type="number" class="form-control" name="admin-score" id="admin-score" value="{{ $adminScore }}" min="0">
        </div>
        <div class="mb-3">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="radio" role="switch" id="toggle-real-score" name="show-score-options" {{ $showRealScore ? 'checked' : '' }}>
                <label class="form-check-label" for="toggle-real-score">Show Real Score</label>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="radio" role="switch" id="toggle-admin-score" name="show-score-options" {{ $showAdminScore ? 'checked' : '' }}>
                <label class="form-check-label" for="toggle-admin-score">Show Score From Admin</label>
            </div>
        </div>
        <br>
        <div class="mb-3">
            <button type="submit" class="btn btn-success mr-2" id="approve-score" {{ $approvalStatus ? 'disabled' : '' }}>Approve Score</button>
            <button type="submit" class="btn btn-secondary" id="update-score">Update</button>
        </div>
    </div>
</div>

<br>
<div class="accordion accordion-flush" id="accordionExamCategory">
    <div class="accordion-item">
        <h4 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-listening-A" aria-expanded="false" aria-controls="flush-collapse-listening-A">
                Listening Part A
            </button>
        </h4>
        <div id="flush-collapse-listening-A" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-listening-a" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Audio</th>
                                <th scope="col">Is Listened</th>
                                <th scope="col">Answer Lines</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataListeningPartA as $data)
                                <tr>
                                    <td>{{ $data['number'] }}</td>
                                    <td>{{ $data['name'] }}</td>
                                    <td>
                                        @if ($data['is_listened'] == "Yes")
                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                        @else
                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <table class="table table-striped">
                                            @foreach ($data['answer_lines'] as $line)
                                                <tr>
                                                    <td>
                                                        <strong>Option:</strong> {{ $line['name'] }}
                                                    </td>
                                                    <td>
                                                        <strong>Right answer:</strong>
                                                        @if ($line['right_answer'])
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>Chosen:</strong>
                                                        @if ($line['chosen'])
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h4 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-listening-B" aria-expanded="false" aria-controls="flush-collapse-listening-B">
                Listening Part B
            </button>
        </h4>
        <div id="flush-collapse-listening-B" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-listening-b" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Story Audio</th>
                                <th scope="col">Is Listened</th>
                                <th scope="col">Question Lines</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataListeningPartB as $data)
                                <tr>
                                    <td>{{ $data['audio_name'] }}</td>
                                    <td>
                                        @if ($data['is_listened'] == "Yes")
                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                        @else
                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <table class="table table-striped">
                                            @foreach ($data['question_lines'] as $questions)
                                                <tr>
                                                    <td colspan="3">
                                                        <strong>Number :</strong> {{ $questions['number'] }}<br>
                                                        <strong>Audio Name :</strong> {{ $questions['name'] }}<br>
                                                        <strong>Is Listened :</strong>
                                                        @if ($questions['is_listened'] == "Yes")
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                        <br><strong>Answer Lines :</strong> 
                                                    </td>
                                                </tr>
                                                @foreach ($questions['answer_lines'] as $lines)
                                                    <tr>
                                                        <td style="padding-left: 20px;">
                                                            <strong>Options :</strong> {{ $lines['name'] }}
                                                        </td>
                                                        <td>
                                                            <strong>Right answer :</strong>
                                                            @if ($lines['right_answer'])
                                                                <span class="badge rounded-pill text-bg-success">Yes</span>
                                                            @else
                                                                <span class="badge rounded-pill text-bg-danger">No</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>Chosen :</strong>
                                                            @if ($lines['chosen'])
                                                                <span class="badge rounded-pill text-bg-success">Yes</span>
                                                            @else
                                                                <span class="badge rounded-pill text-bg-danger">No</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr><td colspan="3"><hr></td></tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h4 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-listening-C" aria-expanded="false" aria-controls="flush-collapse-listening-C">
                Listening Part C
            </button>
        </h4>
        <div id="flush-collapse-listening-C" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-listening-c" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Story Audio</th>
                                <th scope="col">Is Listened</th>
                                <th scope="col">Question Lines</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataListeningPartC as $data)
                                <tr>
                                    <td>{{ $data['audio_name'] }}</td>
                                    <td>
                                        @if ($data['is_listened'] == "Yes")
                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                        @else
                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        <table class="table table-striped">
                                            @foreach ($data['question_lines'] as $questions)
                                                <tr>
                                                    <td colspan="3">
                                                        <strong>Number :</strong> {{ $questions['number'] }}<br>
                                                        <strong>Audio Name :</strong> {{ $questions['name'] }}<br>
                                                        <strong>Is Listened :</strong>
                                                        @if ($questions['is_listened'] == "Yes")
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                        <br><strong>Answer Lines :</strong> 
                                                    </td>
                                                </tr>
                                                @foreach ($questions['answer_lines'] as $lines)
                                                    <tr>
                                                        <td style="padding-left: 20px;">
                                                            <strong>Options :</strong> {{ $lines['name'] }}
                                                        </td>
                                                        <td>
                                                            <strong>Right answer :</strong>
                                                            @if ($lines['right_answer'])
                                                                <span class="badge rounded-pill text-bg-success">Yes</span>
                                                            @else
                                                                <span class="badge rounded-pill text-bg-danger">No</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>Chosen :</strong>
                                                            @if ($lines['chosen'])
                                                                <span class="badge rounded-pill text-bg-success">Yes</span>
                                                            @else
                                                                <span class="badge rounded-pill text-bg-danger">No</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr><td colspan="3"><hr></td></tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h4 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-structure" aria-expanded="false" aria-controls="flush-collapse-structure">
                Structure Questions
            </button>
        </h4>
        <div id="flush-collapse-structure" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-structure" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Question Name</th>
                                <th scope="col" style="width: 25%">Question Words</th>
                                <th scope="col" style="width: 50%">Answer Lines</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataStructureQuestions as $data)
                                <tr>
                                    <td>{{ $data['number'] }}</td>
                                    <td>{{ $data['name'] }}</td>
                                    <td>{{ isset($data['question_words']) ? $data['question_words'] : '' }}</td>
                                    <td>
                                        <table class="table table-striped">
                                            @foreach ($data['answer_lines'] as $line)
                                                <tr>
                                                    <td>
                                                        <strong>Option:</strong> {{ $line['name'] }}
                                                    </td>
                                                    <td>
                                                        <strong>Right answer:</strong>
                                                        @if ($line['right_answer'])
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>Chosen:</strong>
                                                        @if ($line['chosen'])
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="accordion-item">
        <h4 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-reading" aria-expanded="false" aria-controls="flush-collapse-reading">
                Reading Questions
            </button>
        </h4>
        <div id="flush-collapse-reading" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-reading" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Number</th>
                                <th scope="col">Question Name</th>
                                <th scope="col" style="width: 25%">Question Words</th>
                                <th scope="col" style="width: 50%">Answer Lines</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataReadingQuestions as $data)
                                <tr>
                                    <td>{{ $data['number'] }}</td>
                                    <td>{{ $data['name'] }}</td>
                                    <td>{{ isset($data['question_words']) ? $data['question_words'] : '' }}</td>
                                    <td>
                                        <table class="table table-striped">
                                            @foreach ($data['answer_lines'] as $line)
                                                <tr>
                                                    <td>
                                                        <strong>Option:</strong> {{ $line['name'] }}
                                                    </td>
                                                    <td>
                                                        <strong>Right answer:</strong>
                                                        @if ($line['right_answer'])
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>Chosen:</strong>
                                                        @if ($line['chosen'])
                                                            <span class="badge rounded-pill text-bg-success">Yes</span>
                                                        @else
                                                            <span class="badge rounded-pill text-bg-danger">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.js"></script>

<script>
    var userID = {{ $userID }};
    var scheduleID = {{ $scheduleID }};
    let rawExamDate = {!! json_encode($examDate) !!};
    let rawExamClock = {!! json_encode($examClock) !!};
    let examDate = formatDate(rawExamDate);
    let examClock = formatTime(rawExamClock);

    const showExamDate = document.getElementById('exam-date');
    showExamDate.value = examDate;

    const showExamClock = document.getElementById('exam-clock');
    showExamClock.value = examClock+' wib';

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

    document.getElementById('approve-score').addEventListener('click', function(event) {
        event.preventDefault();

        var realScore = document.getElementById('final-score').value;
        var adminScore = document.getElementById('admin-score').value;
        var showRealScore = document.getElementById('toggle-real-score').checked;
        var showAdminScore = document.getElementById('toggle-admin-score').checked;

        if (adminScore && adminScore < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid score input',
                text: "Score must be greater than zero",
                showConfirmButton: true,
            });
            return false;
        }

        if (!adminScore) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid score input',
                text: "Please input a valid score",
                showConfirmButton: true,
            });
            return false;
        }

        if (showRealScore == false && showAdminScore == false) {
            Swal.fire({
                icon: 'error',
                title: 'Nothing to show',
                text: "Please choose score display option",
                showConfirmButton: true,
            });
            return false;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "Approve this participant's score?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sure",
        })
        .then(result => {
            if (result.isConfirmed) {
                showLoading()
                $.ajax({
                    url: "{{ URL::route('admin.manage-score.approve') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: userID,
                        schedule_id: scheduleID,
                        real_score: realScore,
                        admin_score: adminScore,
                        show_real_score: showRealScore,
                        show_admin_score: showAdminScore,
                    },
                    success: function(resp) {
                        hideLoading()
                        Swal.fire({
                            icon: 'success',
                            title: 'Nice!',
                            text: resp.message,
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        hideLoading()
                        Swal.fire({
                            icon: 'error',
                            title: "Something went wrong",
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                });
            }
        });
    });

    document.getElementById('update-score').addEventListener('click', function(event) {
        event.preventDefault();

        var realScore = document.getElementById('final-score').value;
        var adminScore = document.getElementById('admin-score').value;
        var showRealScore = document.getElementById('toggle-real-score').checked;
        var showAdminScore = document.getElementById('toggle-admin-score').checked;

        if (adminScore && adminScore < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid score input',
                text: "Score must be greater than zero",
                showConfirmButton: true,
            });
            return false;
        }

        if (!adminScore) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid score input',
                text: "Please input a valid score",
                showConfirmButton: true,
            });
            return false;
        }

        if (showRealScore == false && showAdminScore == false) {
            Swal.fire({
                icon: 'error',
                title: 'Nothing to show',
                text: "Please choose score display option",
                showConfirmButton: true,
            });
            return false;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "Update this participant's score?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sure",
        })
        .then(result => {
            if (result.isConfirmed) {
                showLoading()
                $.ajax({
                    url: "{{ URL::route('admin.manage-score.update') }}",
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: userID,
                        schedule_id: scheduleID,
                        real_score: realScore,
                        admin_score: adminScore,
                        show_real_score: showRealScore,
                        show_admin_score: showAdminScore,
                    },
                    success: function(resp) {
                        hideLoading()
                        Swal.fire({
                            icon: 'success',
                            title: 'Nice!',
                            text: resp.message,
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        hideLoading()
                        Swal.fire({
                            icon: 'error',
                            title: "Something went wrong",
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#table-listening-a')) {
            $('#table-listening-a').DataTable().destroy();
        }
        var listeningA_Table = $('#table-listening-a').DataTable();

        if ($.fn.DataTable.isDataTable('#table-listening-b')) {
            $('#table-listening-b').DataTable().destroy();
        }
        var listeningB_Table = $('#table-listening-b').DataTable();

        if ($.fn.DataTable.isDataTable('#table-listening-c')) {
            $('#table-listening-c').DataTable().destroy();
        }
        var listeningC_Table = $('#table-listening-c').DataTable();

        if ($.fn.DataTable.isDataTable('#table-structure')) {
            $('#table-structure').DataTable().destroy();
        }
        var structureTable = $('#table-structure').DataTable();

        if ($.fn.DataTable.isDataTable('#table-reading')) {
            $('#table-reading').DataTable().destroy();
        }
        var readingTable = $('#table-reading').DataTable();
    });
</script>
@endsection