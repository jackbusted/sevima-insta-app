@extends('admin-manage.layouts.main')

@section('container')
<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ isset($id) ? 'Edit Question Group' : 'Create Question Group' }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-question.question-group.list') }}">
            <button
                id="back-btn"
                name="back-btn"
                class="btn btn-outline-danger"
                type="button"
            ><i class="bi bi-arrow-left"></i>Back</button>
        </a>
    </div>
</div>

<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <div class="col-lg-10">
        <form method="post" id="form-manage-question-group">
            @csrf
            <div class="mb-3">
                <label for="group-name" class="form-label">Question's Group Name</label>
                <input type="text" required class="form-control" name="group-name" id="group-name" value="{{ isset($groupName) ? $groupName : '' }}">
            </div>

            <br>
            @if (isset($id))
                <button id="updateData" type="submit" class="btn btn-primary">Update</button>
            @else
                <button id="submitData" type="submit" class="btn btn-primary">Save</button>
            @endif
            <a href="{{ URL::route('admin.manage-question.question-group.list') }}">
                <button
                    id="back-btn"
                    name="back-btn"
                    class="btn btn-danger ml-2"
                    type="button"
                >Back</button>
            </a>
        </form>
    </div>
</div>

<script>
    var id = 0;
    var isEdit = false;
    @if (isset($id))
        id = {{ $id }};
        isEdit = true;
    @endif

    if (isEdit) {
        document.getElementById('updateData').addEventListener('click', function(event) {
            event.preventDefault();

            var groupName = document.getElementById('group-name').value;
            if (groupName == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty question group name',
                    text: "Please fill question group's name",
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-manage-question-group'));
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('group_id', id);

            Swal.fire({
                title: "Are you sure?",
                text: "Update this question group data",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Update",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    showLoading()
                    $.ajax({
                        url: "{{ URL::route('admin.manage-question.question-group.update') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
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
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                var errorMessage = '';

                                $.each(errors, function(key, value) {
                                    errorMessage += value + ' ';
                                });

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: errorMessage,
                                    showConfirmButton: true,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Something went wrong',
                                    text: xhr.responseJSON.message,
                                    showConfirmButton: true,
                                });
                            }
                        }
                    });
                }
            });
        });
    } else {
        document.getElementById('submitData').addEventListener('click', function(event) {
            event.preventDefault();

            var groupName = document.getElementById('group-name').value;
            if (groupName == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty question group name',
                    text: "Please fill question group's name",
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-manage-question-group'));
            formData.append('_token', '{{ csrf_token() }}');

            Swal.fire({
                title: "Are you sure?",
                text: "Create new question group",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sure",
            })
            .then((result) => {
                if (result.isConfirmed) {
                    showLoading()
                    $.ajax({
                        url: "{{ URL::route('admin.manage-question.question-group.save') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
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
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                var errorMessage = '';

                                $.each(errors, function(key, value) {
                                    errorMessage += value + ' ';
                                });

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: errorMessage,
                                    showConfirmButton: true,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Something went wrong',
                                    text: xhr.responseJSON.message,
                                    showConfirmButton: true,
                                });
                            }
                        }
                    });
                }
            });
        });
    }
</script>
@endsection