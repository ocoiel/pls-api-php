<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\Auth\Token\Exception\InvalidToken;
use App\Models\User;

class AuthController extends Controller
{
    public function auth(Request $resquest) 
    {
        $auth = app('firebase.auth');
        $idTokenString = $resquest->input('firebaseToken');

        try 
        {
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
        } 
        catch(\InvalidArgumentException $e) 
        {
            return response()->json([
                'message' => 'Não autorizado - Token não informado: ' . $e->getMessage()
            ], 401); 
        }
        catch(InavlidToken $e) 
        {
            return response()->json([
                'message' => 'Não autorizado - Token informado é inválido: ' . $e->getMessage()
            ], 402);
        }

        $uid = $verifiedIdToken->getClaim('sub');

        $user = User::where('firebaseUID', $uid)->first();

        $tokenResult = $user->createToken('tokentest');

        $token = $tokenResult->token;

        $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        return response()->json([
            'id' => $user->id,
            'access_token' => $tokenResult->expires_at(),
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
            $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
}
