<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Jubaer\Zoom\Facades\Zoom;

use App\Models\User;
use App\Models\TemporaryImageStorageModel;
use App\Models\PostModel;
use App\Models\PostLikeModel;
use App\Models\CommentModel;

use App\Models\UserZoomBoundModel;
use App\Models\StoryAudioMasterModel;
use App\Models\QuestionModel;
use App\Models\CategoryModel;
use App\Models\AnswerLineModel;
use App\Models\PaymentModel;
use App\Models\ScheduleModel;
use App\Models\ListeningDataModel;
use App\Models\ListeningAnswerLineModel;
use App\Models\StructureDataModel;
use App\Models\StructureAnswerLineModel;
use App\Models\ReadingDataModel;
use App\Models\ReadingAnswerLineModel;
use App\Models\MasterListeningWithAudioModel;
use App\Models\ListeningWithAudioQuestionModel;
use App\Models\ListeningWithAudioAnswerLineModel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class HomeUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();

            $limit  = $request->get('limit', 10);
            $cursor = $request->get('cursor');

            $query = PostModel::with(['user', 'comments.user', 'likes'])->withCount(['likes', 'comments'])->orderByDesc('id');

            if ($cursor) {
                $query->where('id', '<', $cursor);
            }

            $posts = $query->limit($limit)->get();

            // ...
            return view('homeuser.index', [
                'title' => 'Home',
                'active' => 'homeuser',
                'data' => $posts,
                'next_cursor' => $posts->count() > 0 ? $posts->last()->id : null,
                'has_more' => $posts->count() === $limit,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function loadFeed(Request $request)
    {
        try {
            $limit  = $request->get('limit', 10);
            $cursor = $request->get('cursor');

            $query = PostModel::with(['user', 'comments.user', 'likes'])->withCount(['likes', 'comments'])->orderByDesc('id');

            if ($cursor) {
                $query->where('id', '<', $cursor);
            }

            $posts = $query->limit($limit)->get();

            return response()->json([
                'data' => $posts,
                'next_cursor' => $posts->count() > 0 ? $posts->last()->id : null,
                'has_more' => $posts->count() === $limit,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function imagePreview(Request $request)
    {
        try {
            $rules = [
                'post-image' => 'image|file|max:1024',
            ];
            $validatedData = $request->validate($rules);

            if ($request->hasFile('post-image')) {
                $fileName = uniqid('posts_') . '.' . $request->file('post-image')->getClientOriginalExtension();
                $request->file('post-image')->move(public_path("storage/posts/temporary-img/"), $fileName);

                $temporary = new TemporaryImageStorageModel();
                $temporary->image = "storage/posts/temporary-img/{$fileName}";
                $temporary->save($validatedData);
            }

            // ...
            return json_encode(['code' => 200, 'message' => "success", 'img_preview' => $temporary]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function postFeed(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = User::where('id', Auth::user()->id)->first();

            $rules = [
                'caption' => 'required|min:1',
                'post-image' => 'required|image|file|max:1024',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $validatedData = $validator->validated();

            $newFeed = new PostModel();

            if ($request->hasFile('post-image')) {
                $userName = $user->name;

                $cleanedPath = $this->sanitizePath($userName);
                $fileName = uniqid('feed_') . '.' . $request->file('post-image')->getClientOriginalExtension();
                $request->file('post-image')->move(public_path("storage/feed/{$cleanedPath}/"), $fileName);

                $newFeed->image = "storage/feed/{$cleanedPath}/{$fileName}";
            }

            $newFeed->user_id = $user->id;
            $newFeed->caption = $validatedData['caption'];
            $newFeed->save();

            // ...
            DB::commit();
            return redirect()->back()->with('success', 'Post berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function toggleLike($id)
    {
        try {
            $user = User::where('id', Auth::user()->id)->first();

            $like = PostLikeModel::where('post_id', $id)->where('user_id', $user->id)->first();

            $liked = false;
            if ($like && !$like->trashed()) {
                $like->delete();
                $liked = false;
            } else {
                PostLikeModel::updateOrCreate(
                    ['post_id' => $id, 'user_id' => $user->id],
                    ['deleted_at' => null]
                );
                $liked = true;
            }

            $count = PostLike::where('post_id', $id)->count();

            return redirect()->back()->with('success', 'Post berhasil');
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addComment(Request $request, $id)
    {
        try {
            $request->validate([
                'comment' => 'required|string|max:500'
            ]);
        
            $comment = CommentModel::create([
                'post_id' => $id,
                'user_id' => auth()->id(),
                'content' => $request->comment
            ]);
        
            return redirect()->back()->with('success', 'Post berhasil');
        } catch (\Exception $e) {
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