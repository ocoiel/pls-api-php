<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Database;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Auth;
use Illuminate\Http\Request;
use Firebase\Auth\Token\Exception\InvalidToken;
use App\Models\User;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function __construct(Auth $auth, Database $database)
    {
        $this->auth = $auth;
        $this->users = $database->getReference('users');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'cpf'  => 'required',
            'cep'  => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $auth = $this->auth;
        // dd($uid);
        $createToken = $auth->createCustomToken('tokentest');
        dd($createToken->payload[0]);

        $user = $this->users->push([
            'nome' => $request->nome,
            'cpf'  => $request->cpf,
            'cep'  => $request->cep,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'firebaseToken' => $createToken
        ]);

        // dd($user);

        $message = [
            'status'  => '201',
            'message' => 'Usuário criado com sucesso!',
            'id'      => $user->getKey(),
            'data'    => $user->getValue(),
            'firebaseToken' => $createToken
        ];

        return $message;
    }

    public function login(Request $request)
    {
        // $auth = app('firebase.auth');
        $auth = $this->auth;
        $idTokenString = $request->input('firebaseToken');
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

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
        catch(InvalidToken $e)
        {
            return response()->json([
                'message' => 'Não autorizado - Token informado é inválido: ' . $e->getMessage()
            ], 402);
        }

        $uid = $verifiedIdToken->getClaim('sub');

        // $user = User::where('firebaseUID', $uid)->first();
        $user = $this->users->getUser($uid);

        $tokenResult = $user->createToken('tokentest');

        $token = $tokenResult->token;

        $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        $signInResult = $auth->signInWithEmailAndPassword($request->email, $request->password);


        return response()->json([
            'id' => $user->id,
            'access_token' => $tokenResult->expires_at(),
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
            $tokenResult->token->expires_at
            )->toDateTimeString(),
            'olha essa merda aqui' => $signInResult
        ]);
    }
}
