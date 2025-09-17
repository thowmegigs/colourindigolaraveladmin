<?php

namespace App\Helpers;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

class FirebaseHelper
{
    public static function getAccessToken()
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = new ServiceAccountCredentials(
            $scopes,
            storage_path('app/firebase/firebase-service-account.json') // Service account path
        );

        $authHandler = HttpHandlerFactory::build();
        $token = $credentials->fetchAuthToken($authHandler);

        return $token['access_token'] ?? null;
    }
}
