<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Throwable;

class GithubAuthenticateController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->scopes(['repo'])->redirect();
    }


    public function callback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = User::updateOrCreate([
                'github_id' => $githubUser->id
            ], [
                'name' => $githubUser->name,
                'github_nickname' => $githubUser->nickname,
                'github_avatar' => $githubUser->avatar,
                'github_token' => $githubUser->token,
                'github_id' => $githubUser->id,
                'email' => $githubUser->email,
                'password' => Hash::make(Str::random(10))
            ]);

            Auth::login($user);

            return Redirect::to('dashboard')->with('status', 'Gracefully logged in with Github!');
        } catch (Throwable $th) {
            return Redirect::to('/')->withErrors([$th->getMessage()]);
        }
    }
}
