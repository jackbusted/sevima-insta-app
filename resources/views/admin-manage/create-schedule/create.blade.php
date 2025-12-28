@extends('admin-manage.layouts.main')

@section('container')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pickadate/lib/themes/default.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pickadate/lib/themes/default.date.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pickadate/lib/picker.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pickadate/lib/picker.date.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ isset($id) ? 'Edit Exam Schedule' : 'Create Exam Schedule' }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
        </div>
    </div>
</div>

<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-schedule.view') }}">
            <button id="back-btn" name="back-btn" class="btn btn-outline-danger" type="button"><i class="bi bi-arrow-left"></i>Back</button>
        </a>
    </div>
</div>

<div class="col-lg-8">
    <div class="mb-3">
        <label for="schedule-name" class="form-label">Schedule Name</label>
        <input type="text" class="form-control" name="schedule-name" id="schedule-name" value="{{ isset($schedule_name) ? $schedule_name : '' }}">
    </div>
    <div class="mb-3">
        <label for="classes" class="form-label">Choose Class</label>
        <select class="form-select" name="class_name" id="classSelect">
            @foreach ($classes as $class)
                <option value="{{ $class }}">{{ $class }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="execution" class="form-label">Execution</label>
        <select class="form-select" name="execution_name" id="executionSelect">
            @foreach ($executions as $execution)
                <option value="{{ $execution }}">{{ $execution }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="date" class="form-label">Exam Date</label>
        <input class="form-select" type="text" name="date" id="datepicker" placeholder="Select date"/>
    </div>
    <div class="mb-3">
        <label for="time" class="form-label">Exam Time</label>
        <input class="form-select" type="text" id="timepicker" placeholder="Select time">
    </div>

    <br>
    @if (isset($id))
        <button id="updateSchedule" type="submit" class="btn btn-primary">Update</button>
    @else
        <button id="createSchedule" type="submit" class="btn btn-primary">Create</button>
    @endif
</div>

<script>
    $(document).ready(function(){
        $('#datepicker').pickadate({
            min: 0,
            format: 'yyyy-mm-dd',
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        var timePicker = flatpickr("#timepicker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });

        @if (isset($id))
            var rawTime = {!! json_encode($time) !!};
            timePicker.setDate(rawTime, true);
        @endif
    });

    var id = 0;
    var isEdit = false;
    @if (isset($id))
        id = {{ $id }};
        isEdit = true;

        var selectedClass = {!! json_encode($selected_class) !!};
        var classesSelect = document.getElementById('classSelect');
        classesSelect.value = selectedClass;

        var selectedExec = {!! json_encode($selected_execution) !!};
        var executionSelect = document.getElementById('executionSelect');
        executionSelect.value = selectedExec;

        var selectedDate = {!! json_encode($date) !!};
        var datePicker = document.getElementById('datepicker');
        datePicker.value = selectedDate;
    @endif

    if (isEdit) {
        document.getElementById('updateSchedule').addEventListener('click', function(event){
            event.preventDefault();

            var scheduleName = document.getElementById('schedule-name').value;
            var selected_class = document.getElementById('classSelect').value;
            var selected_execution = document.getElementById('executionSelect').value;

            // ambil nilai tanggal yang dipilih menggunakan pickadate
            var datepicker = $('#datepicker').pickadate();
            var date = datepicker.pickadate('picker').get('select', 'yyyy-mm-dd');
            var selectedTime = document.getElementById('timepicker').value;

            if (!scheduleName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Schedule Name',
                    text: "Please fill schedule's name",
                    showConfirmButton: true,
                });
                return;
            }

            if (!selected_class) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Date',
                    text: 'Please select exam class.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!selected_execution) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Date',
                    text: 'Please select exam execution.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!date) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Date',
                    text: 'Please select exam date.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!selectedTime) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Exam Time',
                    text: 'Please select exam time.',
                    showConfirmButton: true,
                });
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "Update Exam Schedule",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sure",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ URL::route('admin.manage-schedule.update') }}",
                        type: 'post',
                        data: {
                            _token: '{{ csrf_token() }}',
                            schedule_id: id,
                            schedule_name: scheduleName,
                            class_name: selected_class,
                            execution_name: selected_execution,
                            date: date,
                            time: selectedTime,
                        },
                        success: function (resp) {
                            Swal.fire(
                                'Nice!',
                                resp.message,
                                'success'
                            ).then(function () {
                                location.reload();
                            });  
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            Swal.fire({
                                icon: 'error',
                                title: xhr.statusText,
                                text: xhr.responseJSON.message,
                                showConfirmButton: true,
                            });
                        }
                    });
                }
            });
        });
    } else {
        document.getElementById('createSchedule').addEventListener('click', function(event){
            event.preventDefault();

            var scheduleName = document.getElementById('schedule-name').value;
            var selected_class = document.getElementById('classSelect').value;
            var selected_execution = document.getElementById('executionSelect').value;

            // ambil nilai tanggal yang dipilih menggunakan pickadate
            var datepicker = $('#datepicker').pickadate();
            var date = datepicker.pickadate('picker').get('select', 'yyyy-mm-dd');
            var selectedTime = document.getElementById('timepicker').value;

            if (!scheduleName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Schedule Name',
                    text: "Please fill schedule's name",
                    showConfirmButton: true,
                });
                return;
            }

            if (!selected_class) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Date',
                    text: 'Please select exam class.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!selected_execution) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Date',
                    text: 'Please select exam execution.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!date) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Date',
                    text: 'Please select exam date.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!selectedTime) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Exam Time',
                    text: 'Please select exam time.',
                    showConfirmButton: true,
                });
                return;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "Add New Exam Schedule",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sure",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    showLoading();
                    $.ajax({
                        url: "{{ URL::route('admin.manage-schedule.save') }}",
                        type: 'post',
                        data: {
                            _token: '{{ csrf_token() }}',
                            schedule_name: scheduleName,
                            class_name: selected_class,
                            execution_name: selected_execution,
                            date: date,
                            time: selectedTime,
                        },
                        success: function (resp) {
                            hideLoading()
                            Swal.fire(
                                'Nice!',
                                resp.message,
                                'success'
                            ).then(function () {
                                location.reload();
                            });  
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            hideLoading()
                            Swal.fire({
                                icon: 'error',
                                title: xhr.statusText,
                                text: xhr.responseJSON.message,
                                showConfirmButton: true,
                            });
                        }
                    });
                }
            });
        });
    }
</script>
@endsection