<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest; 
use App\Http\Requests\Auth\UpdateRequest; 
use Auth;
use App\Models\User;
use App\Events\userVerified as verifyEvent;
use Laravel\Passport\TokenRepository;
use App\Notifications\verifyUser;
use App\Notifications\userVerified;
use OTP;
use Hash;

class AuthController extends Controller
{
    public function __construct() {

        $this->middleware('auth:api')->only(['user','update','logout','verify','reverify']);
        // $this->middleware(['verified','role:admin'])->only(['user']);

    }

    public function login(LoginRequest $request) {
        $user = User::where('email', $request->login)->orwhere('phone', $request->login)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('MyApp');
                $success['status'] = "Success";
                $success['type'] = "Bearer";
                $success['access_token'] =  $token->accessToken;
                $success['access_token_expiration'] =  \Carbon\Carbon::create($token->token->expires_at);
                $success['user_data'] =  $user;
                return response()->json($success, 200);
            } else {
                $error = array(
                    'message'   => __('Unauthorized'),
                    'status'    => 401,
                );
                return response()->json($error, 401);
            }
        }
        else{ 
            $error = array(
                'message'   => __('Unauthorized'),
                'status'    => 401,
            );
            return response()->json($error, 401);
        } 
    }

    public function register(Request $request) {
        $validation = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|confirmed'
        ]);
   
        $input = $request->all();
        $user = User::create($input);
        $otp = OTP::generate($user, true);
		$user->notify(new verifyUser($otp->code));
        $success = array();
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['data'] =  $user;
        return response()->json($success, 201);
    }

    public function user() {
        return response()->json(\Auth::user(), 200);
    }

    public function set_avatar(Request $request) {
        $validation = $request->validate([
            'avatar' => 'required'
        ]);
        $user = Auth::user();
        $media = $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
        return $user;
    }

    public function update(UpdateRequest $request) {
        $user = \Auth::user();
        if($request->input('form_type') == "password") {
            return self::updatePassword($user, $request->only(['password', 'new_password']));
        } elseif ($request->input('form_type') == "data") {
            return self::updateData($user, $request->only(['name', 'phone', 'email']));
        }
    }

    public function verify(Request $request) {
		$validation = $request->validate([
			"otp" => "required|array|min:6|max:6"
		]);
		$user = Auth::user();
		$user_id = $user->id;
		if(OTP::handle($user_id, $request->get('otp')))  {
			Auth::user()->notify(new userVerified(Auth::user()));
			verifyEvent::dispatch($user);
			$response = array(
				"message"=> __("Successfully verified"),
				"code" => 200
			);
			return response()->json($response);
		} else {
			$response = array(
				"message"=> __("Failed to verify"),
				"code" => 401
			);
			return response()->json($response,401);
		}
    }

    public function reverify() {
		$user = Auth::user();
		$otp = OTP::generate($user, false);
		if( $otp )
		{
			$user->notify(new verifyUser($otp->code));
			$response = array(
				"message" => __("Verification code has been sent to your email address"),
				"code" => 201
			);
		} else {
			$response = array(
				"message" => __("Your account is already verified"),
				"code" => 201
			);
		}
		return response()->json($response);
    }


    public static function updatePassword(User $user, $data) {
        if (\Hash::check($data['password'], $user->password)) {
            $user->password = $data['new_password'];
            $user->save();
            return response()->json([
                'message' => __('successfully updated')
            ], 200);
        } else {
            return response()->json(['message' => __('wrong password')], 400);
        }
        
    }

    public static function updateData(User $user, $data) {
        $user->update($data);
        $user->save();
        return response()->json($user, 200);
    }

    public function logout(Request $request) {
        $tokenRepository = app(TokenRepository::class);
        if ($tokenRepository->revokeAccessToken($request->user()->token()->id)) {
            return response()->json([
                'message' => __('User logged out successfully.')
            ], 200);
        } 
        else{ 
            return response()->json([
                'message' => __('Something went wrong')
            ], 401);
        } 
    }
}
