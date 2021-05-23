<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UniversityController extends Controller
{

	public function create(request $request)
	{
		$validator = Validator::make($request->all(), [

			'caption' => 'required',
			'location' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 'fail', 'error' => $validator->errors()], 400);
		}
		if (!$request->hasFile('post_pic')) {
			return response()->json(['status' => 'fail', 'error' => "Picture can not be empty"], 400);
		}
		$user = User::where('api_token', $request->header('Authorization'))->first(); // api token is checked in  middleware so we know the user exists 
		$time = time();
		$ext = $request->file('post_pic')->extension();
		$ext = strtolower($ext);
		if ($ext != 'jpg' && $ext != 'png') {
			return response()->json(['status' => 'fail', 'error' => "Unacceptable file extension"], 400);
		}
		$name = $user->id . '_' . $time . '.' . $ext;
		$request->file('post_pic')->storeAs('/public/posts', $name);

		$post = Post::create([
			'caption' => $request->caption,
			'location' => $request->location,
			'user_id' => $user->id,
			'pic_address' => $name,
		]);
		//created_at & updated_at is going to be added automatically
		return response()->json(['status' => 'success', 'data' => $post->id], 200); //if you just want post's id
	}
	public function update($id, request $request)
	{
		$validator = Validator::make($request->all(), [

			'caption' => 'optional',
			'location' => 'optional',
		]);
		$user = User::where('api_token', $request->header('Authorization'))->first(); // api token is checked in  middleware so we know the user exists 
		$post = Post::where(['id' => $id, 'user_id' => $user->id])->first();
		if (!$post) {
			return response()->json(['status' => 'fail', 'error' => "user and post id does not match"], 400);
		}
		$counter = 0;
		if (!empty($request->location)) {
			$post->location = $request->location;
			$counter++;
		}
		if (!empty($request->caption)) {
			$post->caption = $request->caption;
			$counter++;
		}
		if ($counter) {
			$post->save();
			return response()->json(['status' => 'success', 'message' => "post deleted updated"], 200);
		} else {
			return response()->json(['status' => 'fail', 'message' => "all fields are empty, nothing to  update"], 400);
		}
	}
	public function show($id)
	{
		$post = Post::find($id);
		if (!$post) {
			return response()->json(['status' => 'fail', 'error' => "Invalid id"], 400);
		}
		return response()->json(['status' => 'success', 'data' => $post], 200);
	}
	public function destroy($id, request $request)
	{
		$user = User::where('api_token', $request->header('Authorization'))->first(); // api token is checked in  middleware so we know the user exists 
		$post = Post::where(['id' => $id, 'user_id' => $user->id])->first();
		if (!$post) {
			return response()->json(['status' => 'fail', 'error' => "user and post id does not match"], 400);
		}
		unlink('/public/posts/' . $post->pic_address);
		Post::destroy($id);
		return response()->json(['status' => 'success', 'message' => "post deleted successfully"], 200);
	}
}
