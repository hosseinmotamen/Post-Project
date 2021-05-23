<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AutheticatedUser
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next)
	{

		if ($request->header('Authorization') === null) {
			return response()->json(['status' => 'fail', 'error' => 'Api token can not be empty'], 401);
		}
		$header = $request->header('Authorization');
		$user = User::where(['api_token' => $header])->first();
		if (!$user) {
			return response()->json(['status' => 'fail', 'error' => 'Invalid api token'], 401);
		}
		return $next($request);
	}
}
