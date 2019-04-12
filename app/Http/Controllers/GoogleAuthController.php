<?php

namespace App\Http\Controllers;

use App\User;
use Google_Service_Drive;
use Google_Service_Oauth2;
use Google_Service_Sheets;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class GoogleAuthController extends Controller
{
    /**
     * @return mixed
     */
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes([
                Google_Service_Oauth2::USERINFO_EMAIL,
                Google_Service_Oauth2::USERINFO_PROFILE,
                Google_Service_Sheets::SPREADSHEETS,
                Google_Service_Drive::DRIVE,
            ])
            ->with([
                'access_type' => config('google.access_type'),
            ])->redirect();
    }


    /**
     * @GET
     * either sign in or sign up user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback()
    {
        $google_user = Socialite::driver('google')->user();
        if ($google_user) {
            $user = $this->userExists($google_user);
            auth()->loginUsingId($user->id);
            return redirect()->route('dashboard');
        }
        flash()->error('Something went wrong');
        return back();
    }


    /**
     * @param $google_user
     * @return User
     */
    private function userExists(SocialiteUser $google_user)
    {
        try {
            $user = User::whereEmail($google_user->getEmail())->whereGoogleId($google_user->getId())->firstOrFail();
            return tap($user)->update([
                'access_token'  => $google_user->token,
                'refresh_token' => $google_user->refreshToken,
            ]);
        }
        catch (ModelNotFoundException $e) {
            // no user record found means new user so we will create user and return the user object
            return User::create([
                'name'          => $google_user->getName(),
                'email'         => $google_user->getEmail(),
                'password'      => bcrypt($google_user->getId()),
                'google_id'     => $google_user->getId(),
                'avatar'        => $google_user->getAvatar(),
                'access_token'  => $google_user->token,
                'refresh_token' => $google_user->refreshToken,
            ]);
        }
    }
}
