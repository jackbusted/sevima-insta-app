@extends('admin-manage.layouts.main')

@section('container')
<div class="justify-content-between flex-wrap flex-md-nowrap align-items-center pl-3 pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ isset($id) ? 'Edit Question Data' : 'Add New Question' }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0"></div>
</div>

<div class="col-lg-10">
    <div class="mb-3">
        <a href="{{ URL::route('admin.manage-question.view-created-question') }}">
            <button
                id="back-btn"
                name="back-btn"
                class="btn btn-outline-danger"
                type="button"
            ><i class="bi bi-arrow-left"></i>Back</button>
        </a>
    </div>
</div>

<form action="" method="post" id="form-question-data" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="title" class="form-label">Title of Question</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ isset($questionTitle) ? $questionTitle : '' }}">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">For Category</label>
                <select class="form-select" name="category_id" id="categorySelect">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name_ctg }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="group-name" class="form-label">Question Group</label>
                <select class="form-select" name="group_id" id="groupSelect">
                    @foreach ($questionGroup as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- listening part A --}}
            <div id="listening-part-a" style="display: block;">
                <div class="mb-3">
                    <label for="audio-part-a" class="form-label">Speaker's Audio</label>
                    <input type="file" class="form-control" id="audio-part-a" name="audio-part-a" accept=".mp3,.wav" onchange="previewAudioPartA()">
                </div>
                <div class="mb-3" id="audioPreviewPartA" style="display: none;">
                    <audio controls class="audio-preview" id="audioPreviewTagPartA"></audio>
                </div>
            </div>

            {{-- listening with story telling --}}
            <div id="listening-with-story" style="display: none;">
                <div class="mb-3">
                    <label for="story-audio" class="form-label">Select Story Audio</label>
                    <select class="form-select" name="story_audio_id" id="story-audio-select">
                        @foreach ($storyAudios as $audio)
                            <option value="{{ $audio->id }}">{{ $audio->audio_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3" id="storyAudioLabel" style="display: none;">
                    <label for="audio-with-story" class="form-label">Preview Story Audio</label>
                </div>
                <div class="mb-3" id="storyAudioPreviewDiv" style="display: none;">
                    <audio controls class="audio-preview" id="storyAudioPreviewTag"></audio>
                </div>
                <div class="mb-3">
                    <label for="question-audio" class="form-label">Question Audio</label>
                    <input type="file" class="form-control" id="question-audio" name="question-audio" accept=".mp3,.wav" onchange="previewAudioWithStory()">
                </div>
                <div class="mb-3" id="questionAudioPreview" style="display: none;">
                    <audio controls class="audio-preview" id="questionAudioPreviewTag"></audio>
                </div>
            </div>

            {{-- structure and reading --}}
            <div id="question-with-image" style="display: none;">
                <div class="mb-3">
                    <label for="image" class="form-label">Image For Question</label>
                    <input type="file" class="form-control" id="image" name="image" accept=".jpeg,.jpg,.png" onchange="previewImage()">
                </div>
                <div class="mb-3" id="imagePreview" style="display: none;">
                    <a href="" id="imageBoxPreview" data-baguettebox>
                        <img class="img-preview img-fluid mb-3 col-sm-5" id="imagePreviewTag">
                    </a>
                </div>
                <div class="mb-3">
                    <label for="question-words" class="form-label">Question Words</label>
                    <input type="text" class="form-control" id="question-words" name="question-words" placeholder="(Optional)" value="{{ isset($questionWords) ? $questionWords : '' }}">
                </div>
            </div>

            <br>
            @if (isset($id))
                <button id="updateData" type="submit" class="btn btn-primary">Update</button>
            @else
                <button id="submitData" type="submit" class="btn btn-primary">Save</button>
            @endif
            <a href="{{ URL::route('admin.manage-question.view-created-question') }}">
                <button
                    id="back-btn"
                    name="back-btn"
                    class="btn btn-danger ml-2"
                    type="button"
                >Back</button>
            </a>
        </div>

        <div class="col-lg-6">
            {{-- answer lines --}}
            <div class="mb-3">
                <label for="a-choice" class="form-label">A - Choice (Right Answer)</label>
                <input type="text" class="form-control" id="a-choice" name="a-choice" value="{{ isset($aChoice) ? $aChoice : '' }}">
            </div>
            <div class="mb-3">
                <label for="b-choice" class="form-label">B - Choice</label>
                <input type="text" class="form-control" id="b-choice" name="b-choice" value="{{ isset($bChoice) ? $bChoice : '' }}">
            </div>
            <div class="mb-3">
                <label for="c-choice" class="form-label">C - Choice</label>
                <input type="text" class="form-control" id="c-choice" name="c-choice" value="{{ isset($cChoice) ? $cChoice : '' }}">
            </div>
            <div class="mb-3">
                <label for="d-choice" class="form-label">D - Choice</label>
                <input type="text" class="form-control" id="d-choice" name="d-choice" value="{{ isset($dChoice) ? $dChoice : '' }}">
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.min.css">

<script>
    var id = 0;
    var isEdit = false;
    var listeningPartA = document.getElementById('listening-part-a');
    var listeningWithStory = document.getElementById('listening-with-story');
    var questionWithImage = document.getElementById('question-with-image');
    var questionCategory = 1;
    var questionGroup = 1;
    var currentStoryAudioID = 1;
    var labelStoryAudio = document.getElementById('storyAudioLabel');

    @if (isset($id))
        id = {{ $id }};
        isEdit = true;

        var categoryID = {!! json_encode($categoryID) !!};
        var categorySelect = document.getElementById('categorySelect');
        categorySelect.value = categoryID;
        questionCategory = categoryID;

        var groupID = {!! json_encode($groupID) !!};
        var groupSelect = document.getElementById('groupSelect');
        if (groupID != 0) {
            groupSelect.value = groupID;
            questionGroup = groupID;
        }

        if (categoryID == 1) {
            listeningPartA.style.display = 'block';
            listeningWithStory.style.display = 'none';
            questionWithImage.style.display = 'none';

            const audio = "{{ asset($audioFile) }}"
            const audioPreviewDiv = document.querySelector('#audioPreviewPartA');
            const audioPreviewTag = document.querySelector('#audioPreviewTagPartA');
            audioPreviewTag.src = audio;
            audioPreviewDiv.style.display = 'block';
        }

        if (categoryID == 2 || categoryID == 3) {
            listeningPartA.style.display = 'none';
            listeningWithStory.style.display = 'none';
            questionWithImage.style.display = 'block';

            const imgPreviewTag = document.querySelector('#imagePreviewTag');
            const imgPreviewDiv = document.querySelector('#imagePreview');
            const preview = document.querySelector('#imageBoxPreview');
            imgPreviewTag.src = "{{ asset($imageFile) }}";
            preview.href = "{{ asset($imageFile) }}";
            imgPreviewDiv.style.display = 'block';
            baguetteBox.run('#imagePreview');
        }

        if (categoryID == 4 || categoryID == 5) {
            listeningPartA.style.display = 'none';
            listeningWithStory.style.display = 'block';
            questionWithImage.style.display = 'none';

            var storyAudioID = {!! json_encode($storyAudioID) !!};
            var storyAudioSelect = document.getElementById('story-audio-select');
            currentStoryAudioID = storyAudioID;
            storyAudioSelect.value = storyAudioID;
            getStoryAudio(storyAudioID);

            const audio = "{{ asset($audioFile) }}"
            const audioPreviewDiv = document.querySelector('#questionAudioPreview');
            const audioPreviewTag = document.querySelector('#questionAudioPreviewTag');
            audioPreviewTag.src = audio;
            audioPreviewDiv.style.display = 'block';
        }
    @endif

    function previewAudioPartA() {
        const audio = document.querySelector('#audio-part-a');
        const audioPreviewDiv = document.querySelector('#audioPreviewPartA');
        const audioPreviewTag = document.querySelector('#audioPreviewTagPartA');

        if (audio.files && audio.files[0]) {
            const oFReader = new FileReader();
            oFReader.readAsDataURL(audio.files[0]);
            oFReader.onload = function(oFREvent) {
                audioPreviewTag.src = oFREvent.target.result;
                audioPreviewDiv.style.display = 'block';
            }
        } else {
            audioPreviewDiv.style.display = 'none';
            audioPreviewTag.src = '';
        }
    }

    function previewAudioWithStory() {
        const audio = document.querySelector('#question-audio');
        const audioPreviewDiv = document.querySelector('#questionAudioPreview');
        const audioPreviewTag = document.querySelector('#questionAudioPreviewTag');

        if (audio.files && audio.files[0]) {
            const oFReader = new FileReader();
            oFReader.readAsDataURL(audio.files[0]);
            oFReader.onload = function(oFREvent) {
                audioPreviewTag.src = oFREvent.target.result;
                audioPreviewDiv.style.display = 'block';
            }
        } else {
            audioPreviewDiv.style.display = 'none';
            audioPreviewTag.src = '';
        }
    }

    function previewImage() {
        const image = document.querySelector('#image');
        const imagePreviewDiv = document.querySelector('#imagePreview');
        const imagePreviewTag = document.querySelector('#imagePreviewTag');
        const preview = document.querySelector('#imageBoxPreview');

        if (image.files && image.files[0]) {
            const oFReader = new FileReader();
            oFReader.readAsDataURL(image.files[0]);
            oFReader.onload = function(oFREvent) {
                imagePreviewTag.src = oFREvent.target.result;
                imagePreviewDiv.style.display = 'block';

                var formData = new FormData(document.getElementById('form-question-data'));
                formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{ URL::route('admin.manage-question.temporary-image') }}",
                    type: 'post',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        var parsedResp = $.parseJSON(resp);
                        var tempImagePreview = '{{ asset('') }}' + parsedResp.img_preview.image;
                        preview.href = tempImagePreview
                        baguetteBox.run('#imagePreview');
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        Swal.fire({
                            icon: 'error',
                            title: xhr.statusText,
                            text: xhr.responseJSON.message,
                            showConfirmButton: true,
                        });
                    }
                });
            }
        } else {
            imagePreviewTag.src = '';
            imagePreviewDiv.style.display = 'none';
        }
    }

    function getStoryAudio(id) {
        $.ajax({
            url: "{{ URL::route('admin.manage-question.get-story-audio') }}",
            type: 'get',
            data: {
                _token: '{{ csrf_token() }}',
                story_audio_id: id,
            },
            success: function(resp) {
                parsedResp = JSON.parse(resp);
                let cleanedAudioPath = "";
                if (parsedResp.audioFile != null) {
                    cleanedAudioPath = parsedResp.audioFile.replace(/['"]+/g, '');
                }

                const storyAudio = `{{ asset('') }}${cleanedAudioPath}`;
                const storyAudioPreviewDiv = document.querySelector('#storyAudioPreviewDiv');
                const storyAudioPreviewTag = document.querySelector('#storyAudioPreviewTag');
                storyAudioPreviewTag.src = storyAudio;
                storyAudioPreviewDiv.style.display = 'block';
                labelStoryAudio.style.display = 'block';
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr.responseJSON.message);
            }
        });
    }

    document.getElementById('categorySelect').addEventListener('change', function(event) {
        event.preventDefault();
        var selectedCategoryId = event.target.value;

        if (selectedCategoryId == 1) {
            // listening part A
            listeningPartA.style.display = 'block';
            listeningWithStory.style.display = 'none';
            questionWithImage.style.display = 'none';
        }

        if (selectedCategoryId == 2 || selectedCategoryId == 3) {
            // structure or reading
            listeningPartA.style.display = 'none';
            listeningWithStory.style.display = 'none';
            questionWithImage.style.display = 'block';
        }

        if (selectedCategoryId == 4 || selectedCategoryId == 5) {
            // listening part b or part c
            listeningPartA.style.display = 'none';
            listeningWithStory.style.display = 'block';
            questionWithImage.style.display = 'none';

            getStoryAudio(currentStoryAudioID);
        }

        questionCategory = selectedCategoryId
    })

    document.getElementById('groupSelect').addEventListener('change', function(event) {
        event.preventDefault();
        questionGroup = event.target.value;
    })

    document.getElementById('story-audio-select').addEventListener('change', function(event) {
        event.preventDefault();
        var storyAudioID = event.target.value;
        currentStoryAudioID = storyAudioID;
        getStoryAudio(currentStoryAudioID);
    })

    if (isEdit) {
        document.getElementById('updateData').addEventListener('click', function(event) {
            event.preventDefault();

            var title = document.getElementById('title').value;
            var a_choice = document.getElementById('a-choice').value;
            var b_choice = document.getElementById('b-choice').value;
            var c_choice = document.getElementById('c-choice').value;
            var d_choice = document.getElementById('d-choice').value;

            const audioListening = document.querySelector('#audioPreviewTagPartA');
            const questionAudio = document.querySelector('#questionAudioPreviewTag');
            const imagePreviewTag = document.querySelector('#imagePreviewTag');

            if (!title) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Column',
                    text: 'Please fill Title of this question.',
                    showConfirmButton: true,
                });
                return;
            }

            if (questionCategory == 1) {
                if (!audioListening.src) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty audio file',
                        text: 'Please choose audio file for Listening Section.',
                        showConfirmButton: true,
                    });
                    return;
                }
            }

            if (questionCategory == 2 || questionCategory == 3) {
                if (!imagePreviewTag.src) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty image for question',
                        text: 'Please upload image for the question.',
                        showConfirmButton: true,
                    });
                    return;
                }
            }

            if (questionCategory == 4 || questionCategory == 5) {
                if (!questionAudio.src) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty audio file',
                        text: 'Please choose audio file for Listening Section.',
                        showConfirmButton: true,
                    });
                    return;
                }
            }

            if (!a_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill A choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!b_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill B choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!c_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill C choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!d_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill D choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-question-data'));
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('question_id', id);

            Swal.fire({
                title: "Are you sure?",
                text: "Update this question data",
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
                        url: "{{ URL::route('admin.manage-question.submit-edit-form') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            hideLoading();
                            Swal.fire({
                                icon: 'success',
                                title: 'Nice!',
                                text: resp.message,
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            hideLoading();
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
        })
    } else {
        document.getElementById('submitData').addEventListener('click', function(event) {
            event.preventDefault();

            var title = document.getElementById('title').value;
            var a_choice = document.getElementById('a-choice').value;
            var b_choice = document.getElementById('b-choice').value;
            var c_choice = document.getElementById('c-choice').value;
            var d_choice = document.getElementById('d-choice').value;

            const audioListening = document.querySelector('#audioPreviewTagPartA');
            const questionAudio = document.querySelector('#questionAudioPreviewTag');
            const imagePreviewTag = document.querySelector('#imagePreviewTag');

            if (!title) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Column',
                    text: 'Please fill Title of this question.',
                    showConfirmButton: true,
                });
                return;
            }

            if (questionCategory == 1) {
                if (!audioListening.src) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty audio file',
                        text: 'Please choose audio file for Listening Section.',
                        showConfirmButton: true,
                    });
                    return;
                }
            }

            if (questionCategory == 2 || questionCategory == 3) {
                if (!imagePreviewTag.src) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty image for question',
                        text: 'Please upload image for the question.',
                        showConfirmButton: true,
                    });
                    return;
                }
            }

            if (questionCategory == 4 || questionCategory == 5) {
                if (!questionAudio.src) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty audio file',
                        text: 'Please choose audio file for Listening Section.',
                        showConfirmButton: true,
                    });
                    return;
                }
            }

            if (!a_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill A choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!b_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill B choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!c_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill C choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            if (!d_choice) {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty answer line',
                    text: 'Please fill D choice line.',
                    showConfirmButton: true,
                });
                return;
            }

            var formData = new FormData(document.getElementById('form-question-data'));
            formData.append('_token', '{{ csrf_token() }}');

            Swal.fire({
                title: "Are you sure?",
                text: "Create new question data",
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
                        url: "{{ URL::route('admin.manage-question.submit-form') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(resp) {
                            hideLoading();
                            Swal.fire({
                                icon: 'success',
                                title: 'Nice!',
                                text: resp.message,
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            hideLoading();
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
        })
    }
</script>
@endsection