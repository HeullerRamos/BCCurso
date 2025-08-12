@extends('layouts.main')

@section('title', 'Detalhes da Intenção de Matrícula')

@section('content')
<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-graduation-cap fa-2x"></i>
            <h3 class="smaller-font">Detalhes da Intenção de Matrícula</h3>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header text-white div-form">
                    Intenção de Matrícula: {{ $intencao_matricula->numero_periodo }}º Período - {{ $intencao_matricula->ano }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">ID:</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $intencao_matricula->id }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Número do Período:</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $intencao_matricula->numero_periodo }}º
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Ano:</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $intencao_matricula->ano }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Disciplinas:</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            @if($intencao_matricula->disciplinas->count() > 0)
                                <ul class="list-unstyled">
                                    @foreach($intencao_matricula->disciplinas as $disciplina)
                                        <li>{{ $disciplina->nome }} ({{ $disciplina->periodo }}º período)</li>
                                    @endforeach
                                </ul>
                            @else
                                Nenhuma disciplina associada
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Criado em:</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $intencao_matricula->created_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Atualizado em:</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ $intencao_matricula->updated_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex gap-2">
                        <a href="{{ route('intencao_matricula.edit', $intencao_matricula->id) }}" class="btn custom-button">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('intencao_matricula.destroy', $intencao_matricula->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta intenção de matrícula?')">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </form>
                        <a href="{{ route('intencao_matricula.index') }}" class="btn custom-button custom-button-castastrar-tcc">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop