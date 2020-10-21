<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Database;
use Kreait\Firebase\ServiceAccount;


class UserController extends Controller
{
    public function __construct(Database $database)
    {
        $this->users = $database->getReference('users');
        // dd($this->users);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $users = $this->users->getValue();

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'cpf'  => 'required',
            'cep'  => 'required'
        ]);

        $user = $this->users->push([
            'nome' => $request->nome,
            'cpf'  => $request->cpf,
            'cep'  => $request->cep
        ]);

        $message = [
            'status'  => '201',
            'message' => 'Usuário criado com sucesso!',
            'id'      => $user->getKey(),
            'data'    => $user->getValue()
        ];

        return $message;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->users->orderByKey()->equalTo($id)->getValue();

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $updateData = [
            'title' => $request->title,
            'body'  => $request->body,
        ];

        $updates    = [
            $id => $updateData,
        ];

        $updatePost = $this->tabel_posts->update($updates);

        $message    = [
            'status'  => '203',
            'message' => 'Atualizado com sucesso!',
            'id'      => $id,
            'data'    => $updateData,
        ];

        return $message;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->tabel_posts
            ->getChild($id)
            ->remove();

        $message = [
            'status' => '204',
            'message' => 'Id: ' . $id . ' removido com sucesso!',
        ];

        return $message;
    }
}
