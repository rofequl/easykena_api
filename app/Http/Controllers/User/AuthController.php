<?php

namespace App\Http\Controllers\User;

use App\Model\General;
use App\Model\User;
use App\Http\Controllers\Controller;
use App\Model\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Helper\CommonHelper;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            $user = new User();
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = app('hash')->make($request->input('password'));
            $user->save();

            return response()->json([
                'entity' => 'admins',
                'action' => 'create',
                'result' => 'success'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'entity' => 'admins',
                'action' => 'create',
                'result' => 'failed'
            ], 409);
        }
    }

    public function login(Request $request)
    {

        if ($request->mobile) {
            $this->validate($request, [
                'mobile' => 'required',
                'otp' => 'required',
            ]);
            $number = $this->phone_number($request->mobile);
            if (!$number) {
                return response()->json(['errors' => 'Invalid phone number'], 422);
            }
            $search = UserVerification::where('account_details', $number)->where('code', $request->otp)
                ->where('is_verified', 0)->first();
            if (!$search) {
                return response()->json(['message' => 'OTP did not match try again..'], 422);
            }
            $search->delete();

            $user = User::where('mobile', $number)->first();
            if (!$user) {
                $user = new User();
                $user->name = $request->mobile;
                $user->mobile = $number;
                $user->save();
            }
            $token = JWTAuth::fromUser($user);

            return $this->respondWithToken($token);

        } else {
            $this->validate($request, [
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            $credentials = $request->only(['email', 'password']);

            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['message' => 'Email and password are not match'], 401);
            }

            return $this->respondWithToken($token);
        }
    }

    public function profile()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);
        $user = Auth::user();
        $user->name = $request->name;
        $user->gender = $request->gender;
        $user->birthday = $request->birthday;
        $user->save();
    }

    public function mailUpdate(Request $request)
    {
        if ($request->email) {
            $this->validate($request, [
                'email' => 'required',
                'otp' => 'required',
            ]);
            $search = UserVerification::where('account_details', $request->email)->where('code', $request->otp)
                ->where('is_verified', 0)->first();
            if (!$search) {
                return response()->json(['message' => 'Invalid verification code..'], 422);
            }
            $search->delete();

            $user = Auth::user();
            $user->email = $request->email;
            $user->save();
        }
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return 'done';
    }

    public function sendOTP(Request $request)
    {
        $this->validate($request, [
            'mobile' => 'required',
        ]);
        $number = $this->phone_number($request->mobile);
        if (!$number) {
            return response()->json(['errors' => 'Invalid phone number'], 422);
        }
        $verification_code = CommonHelper::generateOTP(6);
        $this->sendMessage($number, $verification_code);

        $this->saveVerify($number, $verification_code);
    }

    public function sendMailOTP(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
        ]);

        $user = Auth::user();
        $check = User::where('email', $request->email)->where('id', '!=', $user->id)->first();
        if ($check) {
            return response()->json(['message' => 'This email already used try another'], 422);
        }
        $verification_code = CommonHelper::generateOTP(6);

        $subject = "Verify your email address.";
        $email = $request->email;
        $name = $user->name;
        $general = General::all()->first();
        Mail::send('email.verify', ['name' => $name, 'verification_code' => $verification_code, 'general' => $general],
            function ($mail) use ($email, $subject, $general) {
                $mail->from("finecourier@gmail.com", $general->app_name);
                $mail->to($email)->subject($subject);
            });
        $this->saveVerify($request->email, $verification_code);
    }

    public function sendMessage($number, $otp)
    {
        $message = 'Please enter the following code ' . $otp . ' to verify your account.';
        $post_url = 'https://api.mobireach.com.bd/SendTextMessage';
        $post_values = array(
            'Username' => env('MOBIREACH_USER'),
            'Password' => env('MOBIREACH_PASS'),
            'From' => env('MOBIREACH_FROM'),
            'To' => $number,
            'Message' => $message
        );

        $post_string = "";
        foreach ($post_values as $key => $value) {
            $post_string .= "$key=" . urlencode($value) . "&";
        }
        $post_string = rtrim($post_string, "& ");

        $request = curl_init($post_url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
        $post_response = curl_exec($request);
        curl_close($request);
    }

    public function sendMail($number, $otp)
    {

    }

    public function saveVerify($data, $code)
    {
        $search = UserVerification::where('account_details', $data)->first();
        if ($search) {
            $search->code = $code;
            $search->is_verified = 0;
            $search->save();
        } else {
            $insert = new UserVerification();
            $insert->account_details = $data;
            $insert->code = $code;
            $insert->save();
        }
    }
}
