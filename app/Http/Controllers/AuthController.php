<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;

/**
 * Authentification controller
 */
class AuthController extends Controller
{
    /**
     * Authentification
     * Authentificate member with email and password, keep JWT Token in session on authentification success
     * 
     * @param Request $request
     */
    public function auth(Request $request)
    {
        try {
            $response = app('api')->post('auth', [
                RequestOptions::JSON => ['email' => $request->input('email'), 'password'=> $request->input('password')]
            ]);
        } catch(\Exception $e) {
            return redirect()->route('auth', ['error' => 'Bad Credentials']);
        }
  
    
        if ($response->getStatusCode() == 200) {
            $content = json_decode((string)$response->getBody());
            $_SESSION['jwt'] = $content->data->token;
            return redirect()->route('main');
        } else {
            return redirect()->route('auth', ['error' => 'Bad Credentials']);
        }
    }
}
