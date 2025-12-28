@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Participant's Score</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-4">
    <div class="mb-3">
        <label for="participants" class="form-label">Participant</label>
        <select class="form-select" name="participants" id="participantName">
            <option value="">All</option>
            @foreach ($participants as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="examClasses" class="form-label">Exam Class</label>
        <select class="form-select" name="examClasses" id="className">
            <option value="">All</option>
            @foreach ($examClasses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="examExecutions" class="form-label">Exam Execution</label>
        <select class="form-select" name="examExecutions" id="executionExam">
            <option value="">All</option>
            @foreach ($examExecutions as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label for="statuses" class="form-label">Status</label>
        <select class="form-select" name="statuses" id="scoreStatus">
            <option value="">All</option>
            @foreach ($statuses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-outline-warning" id="approve-all">Approve All</button>
    </div>
</div>
<br>

<div class="table-responsive col w-full">
    <table id="table-score" class="table table-striped table-sm" style="width: 100%">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">NPM</th>
                <th scope="col">Schedule Name</th>
                <th scope="col">Exam Class</th>
                <th scope="col">Date</th>
                <th scope="col">Execution</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <tr>
                <td style="vertical-align: middle;">{{ $data['name'] }}</td>
                <td style="vertical-align: middle;">{{ $data['npm'] }}</td>
                <td style="vertical-align: middle;">{{ $data['schedule_name'] }}</td>
                <td style="vertical-align: middle;">{{ $data['class_test'] }}</td>
                <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($data['date'])) }}</td>
                <td style="vertical-align: middle;">{{ $data['execution'] }}</td>
                <td style="vertical-align: middle;">{{ $data['status'] }}</td>
                <td>
                    {{-- tombol detail --}}
                    <div class="d-inline">
                        <a href="{{ URL::route('admin.manage-score.detail', ['id' => $data['id']]) }}">
                            <input type="hidden" value="{{ $data['id'] }}">
                            <button 
                                title="Detail"
                                type="submit" 
                                style="color: rgb(25, 0, 255)"
                                class="btn btn-outline-info" 
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br>
<hr>
<br>

<div class="accordion accordion-flush" id="accordionExamCategory">
    <div class="accordion-item">
        <h4 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-listening" aria-expanded="false" aria-controls="flush-collapse-listening">
                Listening Category
            </button>
        </h4>
        <div id="flush-collapse-listening" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-listening" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Schedule Name</th>
                                <th scope="col">Exam Date</th>
                                <th scope="col">Clock</th>
                                <th scope="col"># Participants</th>
                                <th scope="col"># Questions</th>
                                <th scope="col"># Duplicate</th>
                                <th scope="col">Similarity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultListening as $scheduleId => $categoryData)
                                <tr>
                                    <td style="vertical-align: middle;">{{ $categoryData['Listening']['schedule_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($categoryData['Listening']['schedule_date'])) }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Listening']['schedule_clock'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Listening']['participants'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Listening']['total_questions'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Listening']['duplicate_questions'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Listening']['similarity'] }} %</td>
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
                Structure Category
            </button>
        </h4>
        <div id="flush-collapse-structure" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-structure" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Schedule Name</th>
                                <th scope="col">Exam Date</th>
                                <th scope="col">Clock</th>
                                <th scope="col"># Participants</th>
                                <th scope="col"># Questions</th>
                                <th scope="col"># Duplicate</th>
                                <th scope="col">Similarity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultStructure as $scheduleId => $categoryData)
                                <tr>
                                    <td style="vertical-align: middle;">{{ $categoryData['Structure']['schedule_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($categoryData['Structure']['schedule_date'])) }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Structure']['schedule_clock'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Structure']['participants'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Structure']['total_questions'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Structure']['duplicate_questions'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Structure']['similarity'] }} %</td>
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
                Reading Category
            </button>
        </h4>
        <div id="flush-collapse-reading" class="accordion-collapse collapse" data-bs-parent="#accordionExamCategory">
            <div class="accordion-body">
                <div class="table-responsive col w-full">
                    <table id="table-reading" class="table table-striped table-sm" style="width: 100%">
                        <thead>
                            <tr>
                                <th scope="col">Schedule Name</th>
                                <th scope="col">Exam Date</th>
                                <th scope="col">Clock</th>
                                <th scope="col"># Participants</th>
                                <th scope="col"># Questions</th>
                                <th scope="col"># Duplicate</th>
                                <th scope="col">Similarity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultReading as $scheduleId => $categoryData)
                                <tr>
                                    <td style="vertical-align: middle;">{{ $categoryData['Reading']['schedule_name'] }}</td>
                                    <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($categoryData['Reading']['schedule_date'])) }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Reading']['schedule_clock'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Reading']['participants'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Reading']['total_questions'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Reading']['duplicate_questions'] }}</td>
                                    <td style="vertical-align: middle;">{{ $categoryData['Reading']['similarity'] }} %</td>
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
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#table-score')) {
        $('#table-score').DataTable().destroy();
    }
    var table = $('#table-score').DataTable();

    $('#participantName').change(function() {
        var participant = $(this).val();
        table.column(0).search(participant).draw();
    });

    $('#className').change(function() {
        var examClass = $(this).val();
        table.column(3).search(examClass).draw();
    });

    $('#executionExam').change(function() {
        var execExam = $(this).val();
        table.column(5).search(execExam).draw();
    });

    $('#scoreStatus').change(function() {
        var scoreStatus = $(this).val();
        table.column(6).search(scoreStatus).draw();
    });

    // ======================================================

    if ($.fn.DataTable.isDataTable('#table-listening')) {
        $('#table-listening').DataTable().destroy();
    }
    var listeningTable = $('#table-listening').DataTable();

    if ($.fn.DataTable.isDataTable('#table-structure')) {
        $('#table-structure').DataTable().destroy();
    }
    var structureTable = $('#table-structure').DataTable();

    if ($.fn.DataTable.isDataTable('#table-reading')) {
        $('#table-reading').DataTable().destroy();
    }
    var readingTable = $('#table-reading').DataTable();
});

document.getElementById('approve-all').addEventListener('click', function(event) {
    event.preventDefault();

    Swal.fire({
        title: "Are you sure?",
        text: "Approve all participant's score?",
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
                url: "{{ URL::route('admin.manage-score.approve-all') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
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
    })
});
</script>
@endsection