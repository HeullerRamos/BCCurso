<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\AlunoRequest;

class AlunoController extends Controller
{
    public function index()
    {
        $alunos = Aluno::all();
        return view('alunos.index', compact('alunos'));
    }

    public function create()
    {
        return view('alunos.create');
    }

    public function store(AlunoRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'nome' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'matricula' => 'required|integer',
            ]);

            $usuarioExists = User::where('email', $request->email)->exists();

            if ($usuarioExists) {
                if ($request->contexto == 'modal') {
                    $alunos = Professor::all();
                    return response()->json(['error' => 'Aluno já cadastrado', 'alunos' => $alunos]);
                } else {
                    return redirect('/alunos/create');
                }
            }

            $user = User::create([
                'password' => Hash::make(strtolower($request->email)),
                'name' => $request->nome,
                'email' => strtolower($request->email),
            ]);

            $user->assignRole('aluno');

            Aluno::create([
                'nome' => $request->nome,
                'matricula' => $request->matricula,
                'user_id' => $user->id,
            ]);
            DB::commit();

            try {
                $email = new CredentialMail($request);
                $email->sendMail();
            } catch (\Exception $error) {
                return redirect('/aluno')->with('error', "Ocorreu um erro no envio automático de email! Envie o email manualmente para: $request->email {$error->getMessage()}");
            }

            if ($request->contexto != 'modal') {
                return redirect('/aluno')->with('success', 'Aluno cadastrado com sucesso');
            } else {
                $alunos = Aluno::all();
                return response()->json(['alunos' => $alunos]);
            }
        } catch (\Exception $error) {
            DB::rollBack();
            return redirect('/aluno')->with('error', 'Erro ao cadastrar aluno!');
        }
    }

    public function edit($id)
    {
        $aluno = Aluno::findOrFail($id);
        return view('alunos.edit', compact('aluno'));
    }

    public function update(Request $request, $id)
    {
        $aluno = Aluno::findOrFail($id);
        $aluno->update([
            'nome' => $request->nome,
            'matricula' => $request->matricula,
        ]);

        return redirect()->route('aluno.index');
    }

    public function destroy($id)
    {
        try {
            $aluno = Aluno::where('id', $id)->first();
            Aluno::destroy($id);
            User::destroy($aluno->user_id);
            $tipo = "success";
            $mensagem = "Aluno removido com sucesso!";
        } catch (QueryException) {
            $tipo = "error";
            $mensagem = "Aluno utilizado no sistema!";
        }

        return redirect('/aluno')->with($tipo, $mensagem);
    }
}
