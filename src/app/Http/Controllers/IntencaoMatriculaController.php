<?php

namespace App\Http\Controllers;

use App\Http\Requests\IntencaoMatriculaRequest;
use App\Models\IntencaoMatricula;
use App\Models\Disciplina;
use Illuminate\Http\Request;

class IntencaoMatriculaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        
        if ($buscar) {
            $intencoes = IntencaoMatricula::where('numero_periodo', $buscar)
                                        ->orWhere('ano', $buscar)
                                        ->orderBy('ano', 'desc')
                                        ->orderBy('numero_periodo')
                                        ->get();
        } else {
            $intencoes = IntencaoMatricula::orderBy('ano', 'desc')
                                         ->orderBy('numero_periodo')
                                         ->get();
        }

        return view('intencao_matricula.index', compact('intencoes', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $disciplinas = Disciplina::orderBy('periodo')->orderBy('nome')->get();
        return view('intencao_matricula.create', compact('disciplinas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IntencaoMatriculaRequest $request)
    {
        $intencao = IntencaoMatricula::create($request->validated());
        
        if ($request->has('disciplinas')) {
            $intencao->disciplinas()->sync($request->disciplinas);
        }

        return redirect()->route('intencao_matricula.index')
                         ->with('success', 'Intenção de matrícula cadastrada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(IntencaoMatricula $intencao_matricula)
    {
        $intencao_matricula->load('disciplinas');
        return view('intencao_matricula.show', compact('intencao_matricula'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IntencaoMatricula $intencao_matricula)
    {
        $disciplinas = Disciplina::orderBy('periodo')->orderBy('nome')->get();
        $intencao_matricula->load('disciplinas');
        return view('intencao_matricula.edit', compact('intencao_matricula', 'disciplinas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IntencaoMatriculaRequest $request, IntencaoMatricula $intencao_matricula)
    {
        $intencao_matricula->update($request->validated());
        
        if ($request->has('disciplinas')) {
            $intencao_matricula->disciplinas()->sync($request->disciplinas);
        } else {
            $intencao_matricula->disciplinas()->detach();
        }

        return redirect()->route('intencao_matricula.index')
                         ->with('success', 'Intenção de matrícula atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IntencaoMatricula $intencao_matricula)
    {
        $intencao_matricula->disciplinas()->detach();
        $intencao_matricula->delete();

        return redirect()->route('intencao_matricula.index')
                         ->with('success', 'Intenção de matrícula excluída com sucesso');
    }
}