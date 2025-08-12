@extends('layouts.main')

@section('title', 'Minhas Intenções de Matrícula')

@section('content')
<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-graduation-cap fa-2x"></i>
            <h3 class="smaller-font">Minhas Intenções de Matrícula</h3>
        </div>
    </div>
</div>

<div class="container mt-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header text-white div-form d-flex justify-content-between align-items-center">
            <span>Intenções de Matrícula Declaradas</span>
            <a href="{{ route('declaracao_intencao_matricula.create') }}" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Nova Declaração
            </a>
        </div>
        <div class="card-body">
            @if($declaracoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Ano</th>
                                <th>Data de Declaração</th>
                                <th>Disciplinas</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($declaracoes as $declaracao)
                                <tr>
                                    <td>{{ $declaracao->periodo }}º Período</td>
                                    <td>{{ $declaracao->ano }}</td>
                                    <td>{{ $declaracao->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $totalDisciplinas = 0;
                                            foreach($declaracao->intencoesMatricula as $intencao) {
                                                $totalDisciplinas += $intencao->disciplinas->count();
                                            }
                                        @endphp
                                        {{ $totalDisciplinas }} disciplinas
                                    </td>
                                    <td>
                                        <a href="{{ route('declaracao_intencao_matricula.show', $declaracao->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Ver Detalhes
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <p>Você ainda não declarou intenção de matrícula para nenhum período.</p>
                    <a href="{{ route('declaracao_intencao_matricula.create') }}" class="btn custom-button mt-2">
                        <i class="fas fa-plus"></i> Declarar Intenção de Matrícula
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@stop
