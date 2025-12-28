@extends('admin-manage.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Approval and Schedule</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>
<br>

<div class="col-lg-4">
    <div class="mb-3">
        <label for="examClass" class="form-label">Exam Class</label>
        <select class="form-select" name="examClass" id="examClass">
            <option value="">All</option>
            @foreach ($examClasses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="executions" class="form-label">Execution</label>
        <select class="form-select" name="executions" id="executions">
            <option value="">All</option>
            @foreach ($executions as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="examStatus" class="form-label">Exam Status</label>
        <select class="form-select" name="examStatus" id="examStatus">
            <option value="">All</option>
            @foreach ($examStatuses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
</div>
<br>

<div class="table-responsive col w-full">
    <table id="table-payment" class="table table-striped table-sm" style="width:100%">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Class</th>
                <th scope="col">Execution</th>
                <th scope="col">Exam Date</th>
                <th scope="col">Time Execution</th>
                <th scope="col">Unconfirmed</th>
                <th scope="col">Total</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <tr>
                <td style="vertical-align: middle;">{{ $data['schedule_name'] }}</td>
                <td style="vertical-align: middle;">{{ $data['class_test'] }}</td>
                <td style="vertical-align: middle;">{{ $data['execution'] }}</td>
                <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($data['open_date'])) }}</td>
                <td style="vertical-align: middle;">{{ $data['exe_clock'] }}</td>
                <td style="vertical-align: middle;">{{ $data['unconfirmed_capacity'] }}</td>
                <td style="vertical-align: middle;">{{ $data['total_capacity'] }}</td>
                <td style="vertical-align: middle;">{{ $data['status'] }}</td>
                <td>
                    {{-- tombol detail --}}
                    <a href="{{ URL::route('admin.manage-registration.list', ['id' => $data['id']]) }}">
                        <button
                            title="View Participants"
                            value="{{ $data['id'] }}"
                            class="delete-btn btn btn-outline-info mb-1"
                            style="color: rgb(25, 0, 255)"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.js"></script>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#table-payment')) {
        $('#table-payment').DataTable().destroy();
    }
    var table = $('#table-payment').DataTable();

    $('#examClass').change(function() {
        var examClass = $(this).val();
        table.column(1).search(examClass).draw();
    });
    $('#executions').change(function() {
        var executions = $(this).val();
        table.column(2).search(executions).draw();
    });
    $('#examStatus').change(function() {
        var examStatus = $(this).val();
        table.column(7).search(examStatus).draw();
    });
})
</script>
@endsection