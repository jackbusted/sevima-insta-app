<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeUserController;
use App\Http\Controllers\HomeUserHistoryController;

use App\Http\Controllers\ManageQuestionController;
use App\Http\Controllers\HomeAdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\QuizController;

Route::get('/', function () {
    if(Auth::check()) {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return redirect('/admin-manage');
        } elseif ($user->role_id == 3) {
            return redirect('/homeuser');
        } else {
            // role_id lain

            return redirect('/unauthorized-account'); // nanti buatkan halaman sendiri
        }
    } else {
        return redirect('/login');
    }
});

Route::get('/dbconn', function () {
    return view('dbconnect', [
        "title" => "Testing Database Connection"
    ]);
});

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'store'])->name('register.save');

Route::middleware(['auth', 'admin'])->group(function () {
    // first page after login (admin)
    Route::get('/admin-manage', [HomeAdminController::class, 'index'])->name('home-admin.dashboard');

    // setting profile
    Route::get('/admin-manage/setting-profile', [HomeAdminController::class, 'settingForm'])->name('admin.setting-profile.form');
    Route::post('/admin-manage/setting-profile/avatar-preview', [HomeAdminController::class, 'avatarImagePreview'])->name('admin.setting-profile.avatar-preview');
    Route::post('/admin-manage/setting-profile/update', [HomeAdminController::class, 'updateSettingProfile'])->name('admin.setting-profile.update');
    Route::post('/admin-manage/setting-profile/delete-avatar', [HomeAdminController::class, 'deleteAvatar'])->name('admin.setting-profile.delete-avatar');

    // admin page for manage payment and registration
    Route::get('/admin-manage/payment', [PaymentController::class, 'adminViewTableRegistration'])->name('admin.manage-registration.view');
    Route::get('/admin-manage/payment/{id}', [PaymentController::class, 'adminViewManageRegistration'])->name('admin.manage-registration.list');
    Route::get('/admin-manage/payment/approval/{id}', [PaymentController::class, 'adminViewDetailRegistration'])->name('admin.manage-registration.approval');
    Route::post('/admin-manage/payment/approval/approve', [PaymentController::class, 'adminManageRegistrationApprove'])->name('admin.manage-registration.approve');
    Route::post('/admin-manage/payment/approval/reject', [PaymentController::class, 'adminManageRegistrationReject'])->name('admin.manage-registration.reject');
    Route::post('/admin-manage/payment/approval/cancel', [PaymentController::class, 'adminManageRegistrationCancel'])->name('admin.manage-registration.cancel');

    // admin page for manage schedule
    Route::get('/admin-manage/create-schedule', [QuizController::class, 'viewSchedule'])->name('admin.manage-schedule.view');
    Route::get('/admin-manage/create-schedule/create', [QuizController::class, 'getOption'])->name('admin.manage-schedule.create');
    Route::get('/admin-manage/create-schedule/edit/{id}', [QuizController::class, 'getEditSchedule'])->name('admin.manage-schedule.edit');
    Route::post('/admin-manage/create-schedule/save', [QuizController::class, 'saveSchedule'])->name('admin.manage-schedule.save');
    Route::post('/admin-manage/create-schedule/update', [QuizController::class, 'updateSchedule'])->name('admin.manage-schedule.update');
    Route::get('/admin-manage/create-schedule/delete/{id}', [QuizController::class, 'deleteSchedule'])->name('admin.manage-schedule.delete');
    Route::get('/admin-manage/create-schedule/open/{id}', [QuizController::class, 'openTestRegistration'])->name('admin.manage-schedule.open');
    Route::get('/admin-manage/create-schedule/execute/{id}', [QuizController::class, 'executeTestSchedule'])->name('admin.manage-schedule.execute');
    Route::post('/admin-manage/payment/update-link', [PaymentController::class, 'adminUpdateInvitationLink'])->name('admin.manage-schedule.update-link');
    Route::post('/admin-manage/payment/close-exam', [PaymentController::class, 'adminCloseExamSchedule'])->name('admin.manage-schedule.close-exam');

    // admin page for generate question
    Route::post('/admin-manage/generate-quiz/generate', [ManageQuestionController::class, 'generate'])->name('admin.manage-question.generate');
    Route::get('/admin-manage/review-quiz', [ManageQuestionController::class, 'viewGeneratedQuestion'])->name('admin.manage-question.review-generated');
    Route::get('/admin-manage/review-quiz-structure', [ManageQuestionController::class, 'viewGeneratedStructureQuestion'])->name('admin.manage-question.review-generated-structure');
    Route::get('/admin-manage/review-quiz-reading', [ManageQuestionController::class, 'viewGeneratedReadingQuestion'])->name('admin.manage-question.review-generated-reading');

    // admin page for create question
    Route::get('/admin-manage/create-question', [ManageQuestionController::class, 'showQuestionsList'])->name('admin.manage-question.view-created-question');
    Route::get('/admin-manage/create-question/create', [ManageQuestionController::class, 'create'])->name('admin.manage-question.create-form');
    Route::post('/admin-manage/create-question/save', [ManageQuestionController::class, 'saveQuestionDataV2'])->name('admin.manage-question.submit-form');
    Route::get('/admin-manage/create-question/detail/{id}', [ManageQuestionController::class, 'detailQuestion'])->name('admin.manage-question.detail-form');
    Route::get('/admin-manage/create-question/edit/{id}', [ManageQuestionController::class, 'getEdit'])->name('admin.manage-question.edit-form');
    Route::post('/admin-manage/create-question/save-edit', [ManageQuestionController::class, 'updateQuestionDataV2'])->name('admin.manage-question.submit-edit-form');
    Route::get('/admin-manage/create-question/delete/{id}', [ManageQuestionController::class, 'deleteQuestionData'])->name('admin.manage-question.delete');
    Route::post('/admin-manage/create-question/temporary-image', [ManageQuestionController::class, 'temporaryImagePreviewV2'])->name('admin.manage-question.temporary-image');

    // admin page for manage story audio / master audio
    Route::get('/admin-manage/create-question/story-audio', [ManageQuestionController::class, 'getStoryAudioList'])->name('admin.manage-question.story-audio.list');
    Route::get('/admin-manage/create-question/story-audio/create', [ManageQuestionController::class, 'formStoryAudioData'])->name('admin.manage-question.story-audio.create');
    Route::get('/admin-manage/create-question/story-audio/edit/{id}', [ManageQuestionController::class, 'detailStoryAudioData'])->name('admin.manage-question.story-audio.detail');
    Route::post('/admin-manage/create-question/story-audio/save', [ManageQuestionController::class, 'saveStoryAudioData'])->name('admin.manage-question.story-audio.save');
    Route::post('/admin-manage/create-question/story-audio/update', [ManageQuestionController::class, 'updateStoryAudioData'])->name('admin.manage-question.story-audio.update');
    Route::post('/admin-manage/create-question/story-audio/delete', [ManageQuestionController::class, 'deleteStoryAudioData'])->name('admin.manage-question.story-audio.delete');
    Route::get('/admin-manage/create-question/get-story-audio', [ManageQuestionController::class, 'getStoryAudio'])->name('admin.manage-question.get-story-audio');

    // admin page for manage question group
    Route::get('/admin-manage/create-question/question-group', [ManageQuestionController::class, 'getQuestionGroupList'])->name('admin.manage-question.question-group.list');
    Route::get('/admin-manage/create-question/question-group/create', [ManageQuestionController::class, 'formQuestionGroup'])->name('admin.manage-question.question-group.create');
    Route::post('/admin-manage/create-question/question-group/save', [ManageQuestionController::class, 'saveQuestionGroupData'])->name('admin.manage-question.question-group.save');
    Route::get('/admin-manage/create-question/question-group/edit/{id}', [ManageQuestionController::class, 'detailQuestionGroupData'])->name('admin.manage-question.question-group.detail');
    Route::post('/admin-manage/create-question/question-group/update', [ManageQuestionController::class, 'updateQuestionGroupData'])->name('admin.manage-question.question-group.update');
    Route::post('/admin-manage/create-question/question-group/delete', [ManageQuestionController::class, 'deleteQuestionGroupData'])->name('admin.manage-question.question-group.delete');
    Route::post('/admin-manage/create-question/question-group/activate', [ManageQuestionController::class, 'toggleActiveQuestionGroup'])->name('admin.manage-question.question-group.activate');
    Route::post('/admin-manage/create-question/question-group/activate-all', [ManageQuestionController::class, 'activateAllQuestionGroup'])->name('admin.manage-question.question-group.activate-all');

    // admin page for manage score
    Route::get('/admin-manage/manage-score', [PaymentController::class, 'viewParticipantsTable'])->name('admin.manage-score.view');
    Route::get('/admin-manage/manage-score/detail/{id}', [PaymentController::class, 'detailParticipantScore'])->name('admin.manage-score.detail');
    Route::post('/admin-manage/manage-score/approve', [PaymentController::class, 'approveScoreParticipant'])->name('admin.manage-score.approve');
    Route::post('/admin-manage/manage-score/update', [PaymentController::class, 'updateScoreParticipant'])->name('admin.manage-score.update');
    Route::post('/admin-manage/manage-score/approve-all', [PaymentController::class, 'approveAllParticipants'])->name('admin.manage-score.approve-all');
});

Route::middleware(['auth', 'user'])->group(function () {
    // dashboard / halaman utama user
    Route::get('/homeuser', [HomeUserController::class, 'index'])->name('tampilkan-halaman-utama');

    // bind email to zoom
    Route::get('/homeuser/bind-zoom', [HomeUserController::class, 'indexBindAccount'])->name('homeuser.bind-zoom.view');
    Route::post('/homeuser/bind-zoom/save', [HomeUserController::class, 'createZoomAccount'])->name('homeuser.bind-zoom.save');
    Route::post('/homeuser/bind-zoom/update', [HomeUserController::class, 'updateZoomAccount'])->name('homeuser.bind-zoom.update');

    // setting user
    Route::get('/homeuser/setting-user', [RegisterController::class, 'settingUserForm'])->name('homeuser.setting-user');
    Route::post('/homeuser/setting-user/avatar-preview', [RegisterController::class, 'avatarImagePreview'])->name('homeuser.setting-user.avatar-preview');
    Route::post('/homeuser/setting-user/update', [RegisterController::class, 'updateUserProfile'])->name('homeuser.setting-user.update');
    Route::post('/homeuser/setting-user/delete-avatar', [RegisterController::class, 'deleteAvatar'])->name('homeuser.setting-user.delete-avatar');

    // user test registration
    Route::get('/homeuser/test-registration', [PaymentController::class, 'userTestRegistration'])->name('homeuser.test-registration.view');
    Route::get('/homeuser/test-registration/final/{id}', [PaymentController::class, 'userViewDetailTestSchedule'])->name('homeuser.test-registration.final');
    Route::post('/homeuser/test-registration/save', [PaymentController::class, 'userTestSaveRegistration'])->name('homeuser.test-registration.save');
    Route::post('/homeuser/test-registration/update', [PaymentController::class, 'userUpdateAttachment'])->name('homeuser.test-registration.update');
    Route::post('/homeuser/test-registration/cancel', [PaymentController::class, 'userCancelTestRegistration'])->name('homeuser.test-registration.cancel');
    Route::post('/homeuser/test-registration/temporary-image', [PaymentController::class, 'temporaryImagePreview'])->name('homeuser.test-registration.temporary-image');

    // start test
    Route::get('/homeuser/start-test/check-schedule', [HomeUserController::class, 'checkExamSchedule'])->name('homeuser.check-exam-schedule');
    Route::get('/homeuser/start-test', [HomeUserController::class, 'checkExamSchedule'])->name('homeuser.execute-test.view');
    Route::post('/homeuser/start-test/update-audio-status', [HomeUserController::class, 'updateListeningAudio'])->name('homeuser.update-audio-status');
    Route::post('/homeuser/start-test/update-master-audio-status', [HomeUserController::class, 'updateMasterListeningAudio'])->name('homeuser.update-master-audio-status');
    Route::post('/homeuser/start-test/update-answer-line', [HomeUserController::class, 'updateUserAnswerLines'])->name('homeuser.update-answer-line');
    Route::get('/homeuser/start-test/next-exam', [HomeUserController::class, 'changeExamType'])->name('homeuser.get-next-exam');
    Route::get('/homeuser/complete-exam', [HomeUserController::class, 'completeExam'])->name('homeuser.complete-exam');

    // user test history
    Route::get('/homeuser/history', [HistoryController::class, 'viewHistory'])->name('homeuser.test-history.view');
});