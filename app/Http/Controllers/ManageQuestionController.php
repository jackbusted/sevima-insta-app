<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\User;
use App\Models\RoleModel;
use App\Models\QuestionModel;
use App\Models\QuestionGroupModel;
use App\Models\CategoryModel;
use App\Models\AnswerLineModel;
use App\Models\StoryAudioMasterModel;
use App\Models\ScheduleModel;
use App\Models\ListeningDataModel;
use App\Models\ListeningAnswerLineModel;
use App\Models\StructureDataModel;
use App\Models\StructureAnswerLineModel;
use App\Models\ReadingDataModel;
use App\Models\ReadingAnswerLineModel;
use App\Models\TemporaryImageStorageModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class ManageQuestionController extends Controller
{
    // =================================== manage question data ===================================
    public function showQuestionsList()
    {
        try {
            $questions = QuestionModel::get();

            $questionCategory = [];
            $questionGroups = [];
            foreach ($questions as $model) {
                $questionGroup = QuestionGroupModel::where('id', $model->group_id)->first();
                if ($questionGroup) {
                    $questionGroups[] = $questionGroup->name;
                }
            }

            $questionCategory = ['Listening Part A', 'Listening Part B', 'Listening Part C', 'Structure', 'Reading'];
            $questionGroups = array_unique($questionGroups);

            $data = [];
            foreach ($questions as $model) {
                $group = "";
                $category = CategoryModel::where('id', $model->category_id)->first();
                $questionGroup = QuestionGroupModel::where('id', $model->group_id)->first();
                if ($questionGroup) {
                    $group = $questionGroup->name;
                }

                $status = "Never Used";
                if ($category->id == 1) {
                    $listeningGenerated = ListeningDataModel::where('question_id', $model->id)->get();

                    if (count($listeningGenerated) > 0) {
                        $status = "Already Used";
                    }
                } else if ($category->id == 2) {
                    $structureGenerated = StructureDataModel::where('question_id', $model->id)->get();

                    if (count($structureGenerated) > 0) {
                        $status = "Already Used";
                    }
                } else if ($category->id == 3) {
                    $readingGenerated = ReadingDataModel::where('question_id', $model->id)->get();

                    if (count($readingGenerated) > 0) {
                        $status = "Already Used";
                    }
                }

                array_push($data, array(
                    'id' => $model->id,
                    'title' => $model->title,
                    'category' => $category->name_ctg,
                    'group' => $group,
                    'status' => $status,
                    'last_updated' => $model->updated_at,
                ));
            }

            return view('admin-manage.create-question.index', [
                'title' => 'Manage Questions',
                'active' => 'admin-manage/create-question',
                'categories' => $questionCategory,
                'questionGroup' => $questionGroups,
                'datas' => $data
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function create()
    {
        return view('admin-manage.create-question.form', [
            'title' => 'Create Question',
            'active' => 'admin-manage/create-new-question',
            'categories' => CategoryModel::orderBy('name_ctg', 'ASC')->get(),
            'storyAudios' => StoryAudioMasterModel::all(),
            'questionGroup' => QuestionGroupModel::all(),
        ]);
    }

    public function getEdit($id)
    {
        try {
            $question = QuestionModel::where('id', $id)->first();
            $answerLine = AnswerLineModel::where('question_id', $question->id)->orderBy('id', 'asc')->get();

            // ...
            return view('admin-manage.create-question.form', [
                'id' => $id,
                'title' => 'Edit Question Data',
                'active' => 'admin-manage/create-new-question',
                'questionTitle' => $question->title,
                'categories' => CategoryModel::orderBy('name_ctg', 'ASC')->get(),
                'storyAudios' => StoryAudioMasterModel::all(),
                'questionGroup' => QuestionGroupModel::all(),
                'categoryID' => $question->category_id,
                'questionWords' => $question->question_words,
                'audioFile' => $question->audio,
                'imageFile' => $question->image,
                'storyAudioID' => $question->story_audio_id,
                'groupID' => $question->group_id,
                'aChoice' => $answerLine[0]->name,
                'bChoice' => $answerLine[1]->name,
                'cChoice' => $answerLine[2]->name,
                'dChoice' => $answerLine[3]->name,
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function saveQuestionDataV2(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'title' => 'required|max:255',
                'category_id' => 'required',
                'group_id' => 'required',
                'image' => 'image|file|max:2048',
            ];

            $questionGroup = $request->input('group_id');
            $questionCategory = $request->input('category_id');
            $category = CategoryModel::where('id', $questionCategory)->first();
            if ($questionCategory == 1) {
                $rules['audio-part-a'] = 'required|mimes:mp3|max:10240';
            }

            if ($questionCategory == 4 || $questionCategory == 5) {
                $rules['question-audio'] = 'required|mimes:mp3|max:10240';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();
            $questionTitle = $request->input('title');
            $checkQuestion = QuestionModel::whereRaw('LOWER(title) = ?', strtolower($questionTitle))
            ->where('category_id', $questionCategory)
            ->where('group_id', $questionGroup)
            ->first();
            if ($checkQuestion != null) {
                DB::rollBack();
                return response()->json(['message' => 'This question is already exists'], 400);
            }
            $cleanedPath = $this->sanitizePath($questionTitle);

            $question = new QuestionModel();
            if ($questionCategory == 1) {
                if ($request->hasFile('audio-part-a')) {
                    $fileName = uniqid($category->name_ctg . '_') . '.' . $request->file('audio-part-a')->getClientOriginalExtension();
                    $request->file('audio-part-a')->move(public_path("storage/questions/{$cleanedPath}/audio/"), $fileName);

                    $question->title = $questionTitle;
                    $question->category_id = $questionCategory;
                    $question->email = Auth::user()->email;
                    $question->audio = "storage/questions/{$cleanedPath}/audio/{$fileName}";
                    $question->group_id = $questionGroup;
                    $question->created_by = Auth::user()->id;
                    $question->updated_at = Carbon::now('Asia/Jakarta');
                    $question->save($validatedData);
                } else {
                    DB::rollBack();
                    return response()->json(['message' => 'Please upload audio file for Listening Category'], 400);
                }
            }

            if ($questionCategory == 2 || $questionCategory == 3) {
                if ($request->hasFile('image')) {
                    $fileName = uniqid($category->name_ctg . '_') . '.' . $request->file('image')->getClientOriginalExtension();
                    $request->file('image')->move(public_path("storage/questions/{$cleanedPath}/image/"), $fileName);
                    $questionWords = $request->input('question-words');

                    $question->title = $questionTitle;
                    $question->category_id = $questionCategory;
                    $question->email = Auth::user()->email;
                    $question->image = "storage/questions/{$cleanedPath}/image/{$fileName}";
                    $question->question_words = $questionWords;
                    $question->group_id = $questionGroup;
                    $question->created_by = Auth::user()->id;
                    $question->updated_at = Carbon::now('Asia/Jakarta');
                    $question->save($validatedData);
                } else {
                    DB::rollBack();
                    return response()->json(['message' => 'Please upload image file for ' . $category->name_ctg . ' Category'], 400);
                }
            }

            if ($questionCategory == 4 || $questionCategory == 5) {
                if ($request->hasFile('question-audio')) {
                    $fileName = uniqid($category->name_ctg . '_') . '.' . $request->file('question-audio')->getClientOriginalExtension();
                    $request->file('question-audio')->move(public_path("storage/questions/{$cleanedPath}/audio/"), $fileName);
                    $storyAudioID = $request->input('story_audio_id');

                    $question->title = $questionTitle;
                    $question->category_id = $questionCategory;
                    $question->email = Auth::user()->email;
                    $question->audio = "storage/questions/{$cleanedPath}/audio/{$fileName}";
                    $question->story_audio_id = $storyAudioID;
                    $question->group_id = $questionGroup;
                    $question->created_by = Auth::user()->id;
                    $question->updated_at = Carbon::now('Asia/Jakarta');
                    $question->save($validatedData);
                } else {
                    DB::rollBack();
                    return response()->json(['message' => 'Please upload audio file for Listening Category'], 400);
                }
            }

            $trueChoice = $request->input('a-choice');
            $trueAnswer = new AnswerLineModel();
            $trueAnswer->question_id = $question->id;
            $trueAnswer->name = $trueChoice;
            $trueAnswer->right_answer = true;
            $trueAnswer->created_by = Auth::user()->id;
            $trueAnswer->updated_at = Carbon::now('Asia/Jakarta');
            $trueAnswer->save();

            $choices = ['b-choice', 'c-choice', 'd-choice'];
            foreach ($choices as $choice) {
                $answer_line = new AnswerLineModel();
                $answer_line->question_id = $question->id;
                $answer_line->name = $request->input($choice);
                $answer_line->created_by = Auth::user()->id;
                $answer_line->updated_at = Carbon::now('Asia/Jakarta');
                $answer_line->save();
            }

            // ...
            DB::commit();
            return response()->json(['message' => 'New Question Data Added'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateQuestionDataV2(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('question_id');
            $question = QuestionModel::where("id", $id)->first();

            $rules = [
                'title' => 'required|max:255',
                'category_id' => 'required',
                'group_id' => 'required',
                'image' => 'image|file|max:2048',
            ];

            $questionGroup = $request->input('group_id');
            $questionCategory = $request->input('category_id');
            $category = CategoryModel::where('id', $questionCategory)->first();
            if ($questionCategory == 1) {
                $rules['audio-part-a'] = 'mimes:mp3|max:10240';
            }

            if ($questionCategory == 4 || $questionCategory == 5) {
                $rules['question-audio'] = 'mimes:mp3|max:10240';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();
            $questionTitle = $request->input('title');
            $checkQuestion = QuestionModel::whereRaw('LOWER(title) = ?', strtolower($questionTitle))->first();
            if ($checkQuestion != null && $checkQuestion->id != $id) {
                DB::rollBack();
                return response()->json(['message' => 'This question is already exists'], 400);
            }
            $cleanedPath = $this->sanitizePath($questionTitle);

            if ($questionCategory == 1) {
                if ($request->hasFile('audio-part-a')) {
                    $fileName = uniqid($category->name_ctg . '_') . '.' . $request->file('audio-part-a')->getClientOriginalExtension();
                    $request->file('audio-part-a')->move(public_path("storage/questions/{$cleanedPath}/audio/"), $fileName);

                    $question->audio = "storage/questions/{$cleanedPath}/audio/{$fileName}";
                } else {
                    if ($question->audio == "" || $question->audio == null || $question->audio == "0") {
                        DB::rollBack();
                        return response()->json(['message' => 'Please insert audio for listening question'], 400);
                    }
                }

                $question->title = $questionTitle;
                $question->category_id = $questionCategory;
                $question->email = Auth::user()->email;
                $question->image = "";
                $question->question_words = "";
                $question->story_audio_id = 0;
                $question->group_id = $questionGroup;
                $question->updated_at = Carbon::now('Asia/Jakarta');
                $question->updated_by = Auth::user()->id;
                $question->update();
            }

            if ($questionCategory == 2 || $questionCategory == 3) {
                if ($request->hasFile('image')) {
                    $fileName = uniqid($category->name_ctg . '_') . '.' . $request->file('image')->getClientOriginalExtension();
                    $request->file('image')->move(public_path("storage/questions/{$cleanedPath}/image/"), $fileName);

                    $question->image = "storage/questions/{$cleanedPath}/image/{$fileName}";
                } else {
                    if ($question->image == "" || $question->image == null) {
                        DB::rollBack();
                        return response()->json(['message' => 'Please upload image file for ' . $category->name_ctg . ' Category'], 400);
                    }
                }
                $questionWords = $request->input('question-words');

                $question->title = $questionTitle;
                $question->category_id = $questionCategory;
                $question->email = Auth::user()->email;
                $question->audio = "";
                $question->question_words = $questionWords;
                $question->story_audio_id = 0;
                $question->group_id = $questionGroup;
                $question->updated_at = Carbon::now('Asia/Jakarta');
                $question->updated_by = Auth::user()->id;
                $question->update();
            }

            if ($questionCategory == 4 || $questionCategory == 5) {
                if ($request->hasFile('question-audio')) {
                    $fileName = uniqid($category->name_ctg . '_') . '.' . $request->file('question-audio')->getClientOriginalExtension();
                    $request->file('question-audio')->move(public_path("storage/questions/{$cleanedPath}/audio/"), $fileName);

                    $question->audio = "storage/questions/{$cleanedPath}/audio/{$fileName}";
                } else {
                    if ($question->audio == "" || $question->audio == null || $question->audio == "0") {
                        DB::rollBack();
                        return response()->json(['message' => 'Please upload image file for ' . $category->name_ctg . ' Category'], 400);
                    }
                }
                $storyAudioID = $request->input('story_audio_id');

                $question->title = $questionTitle;
                $question->category_id = $questionCategory;
                $question->email = Auth::user()->email;
                $question->image = "";
                $question->question_words = "";
                $question->story_audio_id = $storyAudioID;
                $question->group_id = $questionGroup;
                $question->updated_at = Carbon::now('Asia/Jakarta');
                $question->updated_by = Auth::user()->id;
                $question->update();
            }

            // ...
            $trueChoice = $request->input('a-choice');
            $trueAnswer = AnswerLineModel::where('question_id', $question->id)->where('right_answer', true)->first();
            $trueAnswer->name = $trueChoice;
            $trueAnswer->updated_by = Auth::user()->id;
            $trueAnswer->updated_at = Carbon::now('Asia/Jakarta');
            $trueAnswer->update();

            $answerLines = AnswerLineModel::where('question_id', $question->id)->where('right_answer', false)->get();
            $choices = ['b-choice', 'c-choice', 'd-choice'];
            foreach ($choices as $index => $choice) {
                if (isset($answerLines[$index])) {
                    $answerLines[$index]->name = $request->input($choice);
                    $answerLines[$index]->updated_by = Auth::user()->id;
                    $answerLines[$index]->updated_at = Carbon::now('Asia/Jakarta');
                    $answerLines[$index]->update();
                }
            }

            // ...
            DB::commit();
            return response()->json(['message' => 'Question Data Updated'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(), 500]);
        }
    }

    public function getStoryAudio(Request $request)
    {
        try {
            $storyAudioID = $request->input('story_audio_id');
            $storyAudioData = StoryAudioMasterModel::where('id', $storyAudioID)->first();

            // ...
            return json_encode(
                [
                    'message' => 'success',
                    'audioFile' => $storyAudioData->audio_file,
                ]
            );
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function deleteQuestionData($id)
    {
        try {
            DB::beginTransaction();

            $question = QuestionModel::where('id', $id)->first();
            $category = CategoryModel::where('id', $question->category_id)->first();
            if ($category->id == 1) {
                $listeningGenerated = ListeningDataModel::where('question_id', $question->id)->get();

                if (count($listeningGenerated) > 0) {
                    DB::rollBack();
                    return response()->json(['message' => 'This question is already used'], 400);
                }
            } 

            if ($category->id == 2) {
                $structureGenerated = StructureDataModel::where('question_id', $question->id)->get();

                if (count($structureGenerated) > 0) {
                    DB::rollBack();
                    return response()->json(['message' => 'This question is already used'], 400);
                }
            } 

            if ($category->id == 3) {
                $readingGenerated = ReadingDataModel::where('question_id', $question->id)->get();

                if (count($readingGenerated) > 0) {
                    DB::rollBack();
                    return response()->json(['message' => 'This question is already used'], 400);
                }
            }

            $question->deleted_at = Carbon::now('Asia/Jakarta');
            $question->deleted_by = Auth::user()->id;
            $question->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'Success delete question data', 'redirect' => route('admin.manage-question.view-created-question')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // =================================== manage question group ===================================
    public function getQuestionGroupList()
    {
        try {
            $questionGroups = QuestionGroupModel::orderBy('id', 'asc')->get();

            $activeAll = false;
            $activeCount = 0;
            $data = [];
            foreach($questionGroups as $line) {
                $questionsCount = QuestionModel::where('group_id', $line->id)->count();

                array_push($data, array(
                    'id' => $line->id,
                    'name' => $line->name,
                    'last_updated' => $line->updated_at,
                    'questions' => $questionsCount,
                ));

                if ($line->active) {
                    $activeCount++;
                }
            }

            if (count($questionGroups) == $activeCount) {
                $activeAll = true;
            }

            return view('admin-manage.question-group.index', [
                'title' => 'Manage Question Group',
                'active' => 'admin-manage/create-question',
                'datas' => $data,
                'questionGroups' => $questionGroups,
                'activeAll' => $activeAll,
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function toggleActiveQuestionGroup(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $status = $request->input('status');

            $questionGroup = QuestionGroupModel::where('id', $id)->first();
            if (!$questionGroup) {
                DB::rollBack();
                return response()->json(['message' => 'Question group not found'], 404);
            }

            $questionGroup->active = $status;
            $questionGroup->updated_by = Auth::user()->id;
            $questionGroup->updated_at = Carbon::now('Asia/Jakarta');
            $questionGroup->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'Success update question group status'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activateAllQuestionGroup(Request $request)
    {
        try {
            DB::beginTransaction();

            $status = $request->input('status');
            $questionGroups = QuestionGroupModel::get();
            if (count($questionGroups) > 0) {
                foreach ($questionGroups as $data) {
                    $data->active = $status;
                    $data->updated_by = Auth::user()->id;
                    $data->updated_at = Carbon::now('Asia/Jakarta');
                    $data->update();
                }
            }

            // ...
            DB::commit();
            return response()->json(['message' => 'Success update all question group status'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function formQuestionGroup()
    {
        try {
            return view('admin-manage.question-group.form', [
                'title' => 'Create Question Group',
                'active' => 'admin-manage/create-question',
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function detailQuestionGroupData($id)
    {
        try {
            $questionGroup = QuestionGroupModel::where('id', $id)->first();

            // ...
            return view('admin-manage.question-group.form', [
                'title' => 'Edit Question Group',
                'active' => 'admin-manage/create-question',
                'id' => $questionGroup->id,
                'groupName' => $questionGroup->name,
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function saveQuestionGroupData(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'group-name' => 'required|min:6|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();

            $checkData = QuestionGroupModel::whereRaw('LOWER(name) = ?', strtolower($validatedData['group-name']))->first();
            if ($checkData) {
                DB::rollBack();
                return response()->json(['message' => 'This question group name is already exists'], 400);
            }

            $groupName = new QuestionGroupModel;
            $groupName->name = $validatedData['group-name'];
            $groupName->created_by = Auth::user()->id;
            $groupName->updated_at = Carbon::now('Asia/Jakarta');
            $groupName->save($validatedData);

            // ...
            DB::commit();
            return response()->json(['message' => 'Successfully create new Question Group'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateQuestionGroupData(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'group-name' => 'required|min:6|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();

            $group_id = $request->input('group_id');

            // check data
            $checkData = QuestionGroupModel::whereRaw('LOWER(name) = ?', strtolower($validatedData['group-name']))->first();
            if ($checkData && $checkData->id != $group_id) {
                DB::rollBack();
                return response()->json(['message' => 'This question group name is already exists'], 400);
            }

            $groupName = QuestionGroupModel::where('id', $group_id)->first();
            $groupName->name = $validatedData['group-name'];
            $groupName->updated_by = Auth::user()->id;
            $groupName->updated_at = Carbon::now('Asia/Jakarta');
            $groupName->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'Successfully update Question Group data'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteQuestionGroupData(Request $request)
    {
        try {
            DB::beginTransaction();

            $id = $request->input('id');
            $groupData = QuestionGroupModel::where('id', $id)->first();

            // check used data
            $questions = QuestionModel::where('group_id', $groupData->id)->get();
            if (count($questions) > 0) {
                DB::rollBack();
                return response()->json(['message' => 'This question group is already used'], 400);
            }

            $groupData->deleted_at = Carbon::now('Asia/Jakarta');
            $groupData->deleted_by = Auth::user()->id;
            $groupData->update();

            // ...
            DB::commit();
            return response()->json(['message' => 'Successfully delete this Question Group Data'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // =================================== story audio / master audio ===================================
    public function getStoryAudioList()
    {
        try {
            $storyAudios = StoryAudioMasterModel::all();

            $data = [];
            foreach ($storyAudios as $line) {
                array_push($data, array(
                    'id' => $line->id,
                    'name' => $line->audio_name,
                    'last_updated' => $line->updated_at,
                ));
            }

            return view('admin-manage.story-audio.index', [
                'title' => 'Manage Story Audio',
                'active' => 'admin-manage/create-question',
                'datas' => $data,
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function formStoryAudioData()
    {
        try {
            return view('admin-manage.story-audio.form', [
                'title' => 'Create Story Audio',
                'active' => 'admin-manage/create-question',
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function detailStoryAudioData($id)
    {
        try {
            $storyAudioData = StoryAudioMasterModel::where('id', $id)->first();

            // ...
            return view('admin-manage.story-audio.form', [
                'title' => 'Edit Story Audio',
                'active' => 'admin-manage/create-question',
                'id' => $storyAudioData->id,
                'audioName' => $storyAudioData->audio_name,
                'audioFile' => $storyAudioData->audio_file,
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function saveStoryAudioData(Request $request)
    {
        try {
            $rules = [
                'audio-name' => 'required|max:255',
                'story-audio' => 'required|mimes:mp3|max:10240',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            $audioStoryName = $request->input('audio-name');
            $checkAudioName = StoryAudioMasterModel::whereRaw('LOWER(audio_name) = ?', strtolower($audioStoryName))->first();
            if ($checkAudioName != null) {
                return response()->json(['message' => "Audio's name is already exists"], 400);
            }
            $cleanedPath = $this->sanitizePath($audioStoryName);

            if ($request->hasFile('story-audio')) {
                $fileName = uniqid('story-audio_') . '.' . $request->file('story-audio')->getClientOriginalExtension();
                $request->file('story-audio')->move(public_path("storage/story-audio/{$cleanedPath}/audio/"), $fileName);

                $storyAudioData = new StoryAudioMasterModel();
                $storyAudioData->audio_name = $audioStoryName;
                $storyAudioData->audio_file = "storage/story-audio/{$cleanedPath}/audio/{$fileName}";
                $storyAudioData->created_by = Auth::user()->id;
                $storyAudioData->updated_at = Carbon::now('Asia/Jakarta');
                $storyAudioData->save($validatedData);
            } else {
                return response()->json(['message' => "Please upload audio file"], 400);
            }

            // ...
            return response()->json(['message' => 'New story audio has been created'], 200);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function updateStoryAudioData(Request $request)
    {
        try {
            $rules = [
                'audio-name' => 'required|max:255',
            ];

            if ($request->hasFile('story-audio')) {
                $rules['story-audio'] = 'required|mimes:mp3|max:10240';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            $id = $request->input('audio_id');
            $audioStoryName = $request->input('audio-name');
            $checkAudioName = StoryAudioMasterModel::whereRaw('LOWER(audio_name) = ?', strtolower($audioStoryName))->first();
            if ($checkAudioName && $checkAudioName->id != $id) {
                return response()->json(['message' => "Audio's name is already exists"], 400);
            }
            $cleanedPath = $this->sanitizePath($audioStoryName);

            $storyAudioData = StoryAudioMasterModel::where('id', $id)->first();
            if ($storyAudioData->audio_file == null) {
                if ($request->hasFile('story-audio')) {
                    $fileName = uniqid('story-audio_') . '.' . $request->file('story-audio')->getClientOriginalExtension();
                    $request->file('story-audio')->move(public_path("storage/story-audio/{$cleanedPath}/audio/"), $fileName);
    
                    $storyAudioData->audio_name = $audioStoryName;
                    $storyAudioData->audio_file = "storage/story-audio/{$cleanedPath}/audio/{$fileName}";
                    $storyAudioData->updated_by = Auth::user()->id;
                    $storyAudioData->updated_at = Carbon::now('Asia/Jakarta');
                    $storyAudioData->update($validatedData);
                } else {
                    return response()->json(['message' => "Please upload audio file"], 400);
                }
            } else {
                if ($request->hasFile('story-audio')) {
                    $fileName = uniqid('story-audio_') . '.' . $request->file('story-audio')->getClientOriginalExtension();
                    $request->file('story-audio')->move(public_path("storage/story-audio/{$cleanedPath}/audio/"), $fileName);
    
                    $storyAudioData->audio_name = $audioStoryName;
                    $storyAudioData->audio_file = "storage/story-audio/{$cleanedPath}/audio/{$fileName}";
                    $storyAudioData->updated_by = Auth::user()->id;
                    $storyAudioData->updated_at = Carbon::now('Asia/Jakarta');
                    $storyAudioData->update($validatedData);
                } else {
                    $storyAudioData->audio_name = $audioStoryName;
                    $storyAudioData->updated_by = Auth::user()->id;
                    $storyAudioData->updated_at = Carbon::now('Asia/Jakarta');
                    $storyAudioData->update($validatedData);
                }
            }

            // ...
            return response()->json(['message' => "Story audio has been updated"], 200);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function deleteStoryAudioData(Request $request)
    {
        try {
            $id = $request->input('id');
            $audioData = StoryAudioMasterModel::where('id', $id)->first();

            // check used data
            $questions = QuestionModel::where('story_audio_id', $audioData->id)->get();
            if (count($questions) > 0) {
                return response()->json(['message' => 'This story audio is already used'], 400);
            }

            $audioData->deleted_at = Carbon::now('Asia/Jakarta');
            $audioData->deleted_by = Auth::user()->id;
            $audioData->update();

            // ...
            return response()->json(['message' => 'Successfully delete this story audio'], 200);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    // =================================== kebutuhan skripsi ===================================
    public function viewGeneratedQuestion()
    {
        $dataListening = ListeningDataModel::get();

        $data = [];
        $classTest = [];
        $execution = [];
        $openDate = [];
        $userName = [];
        foreach ($dataListening as $model) {
            $schedule = ScheduleModel::where('id', $model->schedule_id)->first();
            $user = User::where('id', $model->user_id)->first();

            $classTest[] = $schedule->class_test;
            $execution[] = $schedule->execution;
            $openDate[] = $schedule->open_date;
            $userName[] = $user->name;

            array_push($data, array(
                'class_test' => $schedule->class_test,
                'execution' => $schedule->execution,
                'open_date' => $schedule->open_date,
                'exe_clock' => $schedule->exe_clock,
                'user_name' => $user->name,
                'question_id' => $model->question_id,
                'question_title' => $model->title,
            ));
        }

        $classTest = array_unique($classTest);
        $execution = array_unique($execution);
        $openDate = array_unique($openDate);
        $userName = array_unique($userName);

        return view('admin-manage.review-quiz.index', [
            'title' => 'Generated Questions (Listening)',
            'active' => 'admin-manage/review-quiz',
            'class_test' => $classTest,
            'class_execution' => $execution,
            'date_test' => $openDate,
            'user_name' => $userName,
            'datas' => $data,
        ]);
    }

    public function viewGeneratedStructureQuestion()
    {
        $dataStructure = StructureDataModel::get();

        $data = [];
        $classTest = [];
        $execution = [];
        $openDate = [];
        $userName = [];
        foreach ($dataStructure as $model) {
            $schedule = ScheduleModel::where('id', $model->schedule_id)->first();
            $user = User::where('id', $model->user_id)->first();

            $classTest[] = $schedule->class_test;
            $execution[] = $schedule->execution;
            $openDate[] = $schedule->open_date;
            $userName[] = $user->name;

            array_push($data, array(
                'class_test' => $schedule->class_test,
                'execution' => $schedule->execution,
                'open_date' => $schedule->open_date,
                'exe_clock' => $schedule->exe_clock,
                'user_name' => $user->name,
                'question_id' => $model->question_id,
                'question_title' => $model->title,
            ));
        }

        $classTest = array_unique($classTest);
        $execution = array_unique($execution);
        $openDate = array_unique($openDate);
        $userName = array_unique($userName);

        return view('admin-manage.review-quiz-structure.index', [
            'title' => 'Generated Questions (Structure)',
            'active' => 'admin-manage/review-quiz',
            'class_test' => $classTest,
            'class_execution' => $execution,
            'date_test' => $openDate,
            'user_name' => $userName,
            'datas' => $data,
        ]);
    }

    public function viewGeneratedReadingQuestion()
    {
        $dataReading = ReadingDataModel::get();

        $data = [];
        $classTest = [];
        $execution = [];
        $openDate = [];
        $userName = [];
        foreach ($dataReading as $model) {
            $schedule = ScheduleModel::where('id', $model->schedule_id)->first();
            $user = User::where('id', $model->user_id)->first();

            $classTest[] = $schedule->class_test;
            $execution[] = $schedule->execution;
            $openDate[] = $schedule->open_date;
            $userName[] = $user->name;

            array_push($data, array(
                'class_test' => $schedule->class_test,
                'execution' => $schedule->execution,
                'open_date' => $schedule->open_date,
                'exe_clock' => $schedule->exe_clock,
                'user_name' => $user->name,
                'question_id' => $model->question_id,
                'question_title' => $model->title,
            ));
        }

        $classTest = array_unique($classTest);
        $execution = array_unique($execution);
        $openDate = array_unique($openDate);
        $userName = array_unique($userName);

        return view('admin-manage.review-quiz-reading.index', [
            'title' => 'Generated Questions (Reading)',
            'active' => 'admin-manage/review-quiz',
            'class_test' => $classTest,
            'class_execution' => $execution,
            'date_test' => $openDate,
            'user_name' => $userName,
            'datas' => $data,
        ]);
    }

    // =================================== tools ===================================
    public function temporaryImagePreviewV2(Request $request)
    {
        try {
            $rules = [
                'image' => 'image|file|max:2048',
            ];
            $validatedData = $request->validate($rules);

            $questionCategory = $request->input('category_id');
            $category = CategoryModel::where('id', $questionCategory)->first();

            $fileName = uniqid($category->name_ctg . '_') . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path("storage/questions/temporary-img/"), $fileName);

            $temporary = new TemporaryImageStorageModel();
            $temporary->image = "storage/questions/temporary-img/{$fileName}";
            $temporary->save($validatedData);

            // ...
            return json_encode(['code' => 200, 'message' => "success", 'img_preview' => $temporary]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    private function sanitizePath($path)
    {
        // Replace any unwanted characters with underscores or remove them
        $cleanedPath = preg_replace('/[^\w\d\/._-]/', '', $path);

        // Optionally, convert spaces to underscores (or another character)
        $cleanedPath = str_replace(' ', '_', $cleanedPath);

        return $cleanedPath;
    }
}
