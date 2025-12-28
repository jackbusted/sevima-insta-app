@extends('admin-manage.layouts.main')

@section('container')
<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ isset($id) ? 'Edit Story Audio' : 'Create Story Audio' }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-question.story-audio.list') }}">
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
        <form method="post" id="form-manage-story-audio" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="audio-name" class="form-label">Audio's Name</label>
                <input type="text" required class="form-control" name="audio-name" id="audio-name" value="{{ isset($audioName) ? $audioName : '' }}">
            </div>
            <div class="mb-3">
                <label for="story-audio" class="form-label">Story Audio</label>
                <input type="file" class="form-control" name="story-audio" id="story-audio" accept=".mp3,.wav" onchange="previewAudio()">
            </div>
            <div class="mb-3" id="story-audio-preview" style="display: none;">
                <audio controls class="story-audio-preview col-sm-5" id="story-audio-preview-tag"></audio>
            </div>

            <br>
            @if (isset($id))
                <button id="updateData" type="submit" class="btn btn-primary">Update</button>
            @else
                <button id="submitData" type="submit" class="btn btn-primary">Save</button>
            @endif
            <a href="{{ URL::route('admin.manage-question.story-audio.list') }}">
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
        const previousAudio = "{{ asset($audioFile) }}";
        const audioPreviewDiv = document.querySelector('#story-audio-preview');
        const audioPreviewTag = document.querySelector('#story-audio-preview-tag');
        audioPreviewTag.src = previousAudio;
        audioPreviewDiv.style.display = 'block';

        id = {{ $id }};
        isEdit = true;
    @endif

    function previewAudio() {
        const storyAudio = document.querySelector('#story-audio');
        const audioPreviewDiv = document.querySelector('#story-audio-preview');
        const audioPreviewTag = document.querySelector('#story-audio-preview-tag');

        if (storyAudio.files && storyAudio.files[0]) {
            const oFReader = new FileReader();
            oFReader.readAsDataURL(storyAudio.files[0]);
            oFReader.onload = function(oFREvent) {
                audioPreviewTag.src = oFREvent.target.result;
                audioPreviewDiv.style.display = 'block';
            }
        } else {
            audioPreviewTag.src = '';
            audioPreviewDiv.style.display = 'none';
        }
    }

    if (isEdit) {
        document.getElementById('updateData').addEventListener('click', function(event) {
            event.preventDefault();

            var audioName = document.getElementById('audio-name').value;
            var audioFile = document.getElementById('story-audio').value;
            const audioPreviewTag = document.querySelector('#story-audio-preview-tag');

            if (audioName == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty audio name',
                    text: "Please fill audio's name",
                    showConfirmButton: true,
                });
                return;
            }

            if (!audioPreviewTag.src) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty audio file',
                    text: "Audio's file is required",
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-manage-story-audio'));
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('audio_id', id);

            Swal.fire({
                title: "Are you sure?",
                text: "Update this audio's story",
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
                        url: "{{ URL::route('admin.manage-question.story-audio.update') }}",
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
                                    title: "Something went wrong",
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

            var audioName = document.getElementById('audio-name').value;
            var audioFile = document.getElementById('story-audio').value;
            const audioPreviewTag = document.querySelector('#story-audio-preview-tag');

            if (audioName == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty audio name',
                    text: "Please fill audio's name",
                    showConfirmButton: true,
                });
                return;
            }

            if (!audioPreviewTag.src) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty audio file',
                    text: "Audio's file is required",
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-manage-story-audio'));
            formData.append('_token', '{{ csrf_token() }}');

            Swal.fire({
                title: "Are you sure?",
                text: "Create new audio's story",
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
                        url: "{{ URL::route('admin.manage-question.story-audio.save') }}",
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
                                    title: "Something went wrong",
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