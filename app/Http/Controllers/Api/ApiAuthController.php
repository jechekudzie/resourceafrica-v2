<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{
    use ApiResponses;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ],
            [
                'email.required' => 'Your username is required',
                'email.max' => 'Your username is too long and most probably incorrect',
                'password.required' => 'A password is required to login',
            ]
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $user = User::where('email','=', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $token = $user->createToken('Resource Africa Password Grant Client');
                Auth::login($user);

                if($user->organisations)
                {
                    $organisations = $user->organisations;
                    if($organisations)
                        {
                            $organisations->each(function ($organisation) use ($token, $user) {
                                $organisation->load(['organisationRoles','safariOperators']);
                            });
                        }
                }

                return $this->ok('Welcome ' . $user->name, [
                    'user' => $user,
                    'token' => $token,
                ]);
            } else {
                return $this->error(
                    'Sorry, the details you have provided have not been recognised, please review and try again or contact our support team to get further assistance.',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        } else {
            return $this->error(
                'Sorry, you cannot login at this time. Your user was not found.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function register(Request $request)
    {

    }

    public function getMyProfile(){
        $authUser = Auth::user();

        // Find User by user_id
        $user = User::with(['organisations', 'roles'])
            ->where('id', '=', $authUser->id)
            ->first();

        if ($user) {
            return $this->ok('Hey ' . $user->name, [
                'user' => $user,
            ]);
        } else {
            return $this->notFound('Sorry, your profile was not found.');
        }
    }

    public function updateMyProfile(Request $request) {
        $validator = Validator::make(
            $request->all(), [
                'name' => 'required|string',
                'email' => 'required|email',
            ]
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        // Get currently authenticated user
        $authUser = Auth::user();

        $user = User::where('id', '=', $authUser->id)
            ->first();

        if (!$user) {
            return $this->notFound('User not found.');
        }

        DB::transaction(function() use ($user, $request, $authUser) {
            // Update basic member information
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
            ]);
        });

        $user->refresh();

        return $this->ok('Profile updated successfully.', [
            'user' => $user->load(['organisations', 'roles']),
        ]);
    }

    public function updateMyPassword(Request $request) {
        $validator = Validator::make(
            $request->all(), [
            'current_password' => ['required'],
            'password' => ['required', 'confirmed'],
        ],
            [
                'current_password.required' => 'Your current password is required to process your request.',
                'password.required' => 'A password is required to process your request.',
                'password.confirmed' => 'You need to confirm your password in order to process your request.'
            ]
        );

        if ($validator->fails()) {
            return $this->validationError($validator->errors()->all());
        }

        $authUser = Auth::user();
        if (!$authUser) {
            return $this->error('You must be logged in', Response::HTTP_UNAUTHORIZED);
        }

        if (Hash::check($request->current_password, $authUser->password)) {
            $authUser->password = Hash::make($request->input('password'));
            $authUser->save();

            return $this->ok('Password updated successfully.');
        } else {
            return $this->error(
                'Current password is incorrect. Please review and try again.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function requestAccountDeletion(Request $request) {
        $authUser = Auth::user();
        if (!$authUser) {
            return $this->error('You must be logged in', Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('id', '=', $authUser->id)->first();

        if (!$user) {
            return $this->notFound('User not found.');
        }

        // First revoke all tokens
        $authUser->token()->revoke();
        $authUser->delete();

        return $this->deleted('Account deletion requested successful.');
    }
}
