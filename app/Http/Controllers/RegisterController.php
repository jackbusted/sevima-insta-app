<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\User;
use App\Models\TemporaryImageStorageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class RegisterController extends Controller
{
    public function index()
    {
        return view('register.index', [
            'title' => 'Register',
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'name' => 'required|min:6|max:255',
                'username' => ['required', 'min:4', 'max:255', 'unique:users'],
                'email' => [
                    'required',
                    'email',
                    'min:6',
                    'max:255',
                    'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
                ],
                'password' => 'required|min:6|max:255'
            ];

            $messages = [
                'email.regex' => 'The email must be a valid Gmail address.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();

            $validatedData['password'] = Hash::make($validatedData['password']);
            $validatedData['role_id'] = 3;
            User::create($validatedData);

            // ...
            DB::commit();
            return response()->json(['message' => 'Registration Successful. Please Login'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function settingUserForm()
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $avatarImage = "";

            if ($user->avatar) {
                $avatarImage = $user->avatar;
            }
            // ...
            return view('homeuser.setting-user.index', [
                'id' => $user->id,
                'title' => 'Setting Profile',
                'active' => 'homeuser/setting-user',
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'avatarImage' => $avatarImage,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateUserProfile(Request $request)
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
                    'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
                ],
                'avatar-image' => 'image|file|max:1024',
            ];

            if ($request->input('password') != "") {
                $rules['password'] = 'min:6|max:255';
            }

            $messages = [
                'email.regex' => 'The email must be a valid Gmail address.',
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

            $user_id = $request->input('user_id');

            $checkName = User::where('name', $validatedData['name'])->first();
            if ($checkName && $checkName->id != $user_id) {
                DB::rollBack();
                return response()->json(['message' => 'This Name is already exists'], 400);
            }

            $checkUsername = User::where('username', $validatedData['username'])->first();
            if ($checkUsername && $checkUsername->id != $user_id) {
                DB::rollBack();
                return response()->json(['message' => 'This Username is already used by ' . $checkUsername->name], 400);
            }

            $checkEmail = User::where('email', $validatedData['email'])->first();
            if ($checkEmail && $checkEmail->id != $user_id) {
                DB::rollBack();
                return response()->json(['message' => 'This Email is already used by ' . $checkEmail->name], 400);
            }

            $user = User::where('id', $user_id)->first();

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
