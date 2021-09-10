<?php

namespace App\Providers;

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
        $plain = $credentials['password'];

        $key = "eOjvN4YcYv50AAIF0NNRBjWTydxhJgEJoxzI1czmoHJaPtRtNd9vxAwO";
        $hash = md5($key.$plain);

        //TODO change 5 with appropriate profile ID
        return $hash === $user->getAuthPassword() && $user->id_profile === 5 && $user->active === 1;
    }

}
