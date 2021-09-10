<?php

namespace App\Providers;

use App\Hashing;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class CustomUserProvider extends EloquentUserProvider {

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        define('COOKIE_KEY', getenv('COOKIE_KEY'));
        define('APP_PROFILE', 5); // App User Profile in store dashboard

        $hashing = new Hashing();
        $plain = $credentials['password'];
        $hash = $user->getAuthPassword();

        return $user->id_profile === APP_PROFILE && $user->active === 1 && $hashing->checkHash($plain, $hash);
    }

}
