@extends('homeuser.layouts.main')

@section('container')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.css" rel="stylesheet">

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Exam Registration</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-4">
    <div class="mb-3">
        <label for="examClasses" class="form-label">Exam Class</label>
        <select class="form-select" name="examClasses" id="examClass">
            <option value="">All</option>
            @foreach ($examClasses as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="examExecutions" class="form-label">Exam Execution</label>
        <select class="form-select" name="examExecutions" id="examExecution">
            <option value="">All</option>
            @foreach ($examExecutions as $data)
                <option value="{{ $data }}">{{ $data }}</option>
            @endforeach
        </select>
    </div>
</div>

<br>
<div class="col w-full">
    <div class="mb-3">
        <label for="schedule" class="form-label"><b>Step 1.</b> Choose schedule by view detail first</label>
    </div>
    <div class="table-responsive">
        <table id="table-schedule" class="table table-striped table-sm" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">Detail</th>
                    <th scope="col">Class</th>
                    <th scope="col">Start Exam</th>
                    <th scope="col">Execution</th>
                    <th scope="col">Start Time</th>
                    <th scope="col">Capacity</th>
                    <th scope="col">Availability</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <td style="vertical-align: middle;">{{ $data['name'] }}</td>
                    <td style="vertical-align: middle;">{{ $data['class_test'] }}</td>
                    <td style="vertical-align: middle;">{{ $data['execution'] }}</td>
                    <td style="vertical-align: middle;">{{ date('Y-m-d', strtotime($data['open_date'])) }}</td>
                    <td style="vertical-align: middle;">{{ $data['exe_clock'] }}</td>
                    <td style="vertical-align: middle;">{{ $data['capacity'] }}</td>
                    <td style="vertical-align: middle;">{{ ($data['capacity'] >= 30) ? "Full" : "Available" }}</td>
                    <td>
                        {{-- tombol detail --}}
                        <div class="d-inline">
                            <a href="{{ URL::route('homeuser.test-registration.final', ['id' => $data['id']]) }}">
                                <input type="hidden" value="{{ $data['id'] }}">
                                <button
                                    title="Detail"
                                    type="submit"
                                    class="btn btn-outline-warning mb-1"
                                    style="color: rgb(199, 25rgb(170, 255, 0)"
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
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/r-2.5.0/sc-2.3.0/sl-1.7.0/datatables.min.js"></script>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#table-schedule')) {
        $('#table-schedule').DataTable().destroy();
    }
    var table = $('#table-schedule').DataTable();

    $('#examClass').change(function() {
        var category = $(this).val();
        table.column(1).search(category).draw();
    });

    $('#examExecution').change(function() {
        var group = $(this).val();
        table.column(2).search(group).draw();
    });
})
</script>
@endsection