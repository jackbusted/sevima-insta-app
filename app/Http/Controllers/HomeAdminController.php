<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\User;
use App\Models\TemporaryImageStorageModel;
use App\Models\ScheduleModel;
use App\Models\PaymentModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class HomeAdminController extends Controller
{
    public function index()
    {
        try {
            // check active schedule
            $activeSchedules = [];
            $schedules = ScheduleModel::whereIn('status', [0, 1, 2])->orderBy('open_date', 'DESC')->get();

            if (count($schedules) > 0) {
                foreach ($schedules as $model) {
                    $status = "";
                    switch ($model->status) {
                        case 0:
                            $status = "Ready to open";
                            break;
                        case 1:
                            $status = "Not started yet";
                            break;
                        case 2:
                            $status = "Already started";
                            break;
                        default:
                            $status = "Unknown";
                            break;
                    }

                    // check confirmed & unconfirmed
                    $unconfirmed = 0;
                    $confirmed = 0;

                    $unconfirmedPayments = PaymentModel::where('status', 0)->where('schedule_id', $model->id)->get();
                    if (count($unconfirmedPayments) > 0) {
                        for ($i=0; $i < count($unconfirmedPayments); $i++) { 
                            $unconfirmed++;
                        }
                    }

                    $confirmedPayments = PaymentModel::whereIn('status', [1, 4])->where('schedule_id', $model->id)->get();
                    if (count($confirmedPayments) > 0) {
                        for ($i=0; $i < count($confirmedPayments); $i++) { 
                            $confirmed++;
                        }
                    }

                    array_push($activeSchedules, array(
                        'id' => $model->id,
                        'name' => $model->name,
                        'class_test' => $model->class_test,
                        'execution' => $model->execution,
                        'open_date' => $model->open_date,
                        'exe_clock' => $model->exe_clock,
                        'status' => $status,
                        'unconfirmed' => $unconfirmed,
                        'confirmed' => $confirmed,
                    ));
                }
            }

            return view('admin-manage.index', [
                'title' => 'Dashboard',
                'active' => 'admin-manage',
                'activeSchedules' => $activeSchedules,
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function settingForm()
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $avatarImage = "";

            if ($user->avatar) {
                $avatarImage = $user->avatar;
            }

            // ...
            return view('admin-manage.setting-profile.index', [
                'title' => 'Setting Profile',
                'active' => 'admin-manage/setting-profile',
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'avatarImage' => $avatarImage,
            ]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function avatarImagePreview(Request $request)
    {
        try {
            $rules = [
                'avatar-image' => 'image|file|max:1024',
            ];
            $validatedData = $request->validate($rules);

            if ($request->hasFile('avatar-image')) {
                $fileName = uniqid('avatar_') . '.' . $request->file('avatar-image')->getClientOriginalExtension();
                $request->file('avatar-image')->move(public_path("storage/avatar/temporary-img/"), $fileName);

                $temporary = new TemporaryImageStorageModel();
                $temporary->image = "storage/avatar/temporary-img/{$fileName}";
                $temporary->save($validatedData);
            }

            // ...
            return json_encode(['code' => 200, 'message' => "success", 'img_preview' => $temporary]);
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        }
    }

    public function updateSettingProfile(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'name' => 'required|min:6|max:255',
                'username' => 'required|min:6|max:255',
                'email' => [
                    'required',
                    'email',
                    'min:6',
                    'max:255',
                    'regex:/^[a-zA-Z0-9._%+-]+@tefl\.com$/'
                ],
                'avatar-image' => 'image|file|max:1024',
            ];

            if ($request->input('password') != "") {
                $rules['password'] = 'min:6|max:255';
            }

            $messages = [
                'email.regex' => 'The email must be a valid @tefl.com address.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();

            if ($request->input('password') != "") {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user = User::where('id', Auth::user()->id)->first();

            $checkName = User::where('name', $validatedData['name'])->first();
            if ($checkName && $checkName->id != $user->id) {
                DB::rollBack();
                return response()->json(['message' => 'This Name is already exists'], 400);
            }

            $checkUsername = User::where('username', $validatedData['username'])->first();
            if ($checkUsername && $checkUsername->id != $user->id) {
                DB::rollBack();
                return response()->json(['message' => 'This Username is already used by ' . $checkUsername->name], 400);
            }

            $checkEmail = User::where('email', $validatedData['email'])->first();
            if ($checkEmail && $checkEmail->id != $user->id) {
                DB::rollBack();
                return response()->json(['message' => 'This Email is already used by ' . $checkEmail->name], 400);
            }

            if ($request->hasFile('avatar-image')) {
                $userName = $validatedData['name'];

                $cleanedPath = $this->sanitizePath($userName);
                $fileName = uniqid('avatar_') . '.' . $request->file('avatar-image')->getClientOriginalExtension();
                $request->file('avatar-image')->move(public_path("storage/avatar/{$cleanedPath}/"), $fileName);

                $user->avatar = "storage/avatar/{$cleanedPath}/{$fileName}";
            }

            $user->name = $validatedData['name'];
            $user->username = $validatedData['username'];
            $user->email = $validatedData['email'];
            if ($request->input('password') != "") {
                $user->password = $validatedData['password'];
            }
            $user->updated_at = Carbon::now('Asia/Jakarta');
            $user->update();

            // ...
            DB::commit();
            return response()->json(['message' => "User's profile updated"], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function deleteAvatar(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = User::where('id', Auth::user()->id)->first();
            $user->avatar = null;
            $user->update();

            // ...
            DB::commit();
            return response()->json(['message' => "User's avatar deleted"], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
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
