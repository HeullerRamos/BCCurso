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

<div class="container mt-5">
    <div class="card mb-4">
        <div class="card-header text-white div-form">
            Declaração de Intenção: {{ $declaracao->periodo }}º Período - {{ $declaracao->ano }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <h6 class="mb-0">Aluno:</h6>
                </div>
                <div class="col-sm-9 text-secondary">
                    {{ $declaracao->aluno->nome }} (Matrícula: {{ $declaracao->aluno->matricula }})
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-3">
                    <h6 class="mb-0">Período:</h6>
                </div>
                <div class="col-sm-9 text-secondary">
                    {{ $declaracao->periodo }}º
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-3">
                    <h6 class="mb-0">Ano:</h6>
                </div>
                <div class="col-sm-9 text-secondary">
                    {{ $declaracao->ano }}
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-3">
                    <h6 class="mb-0">Data de Declaração:</h6>
                </div>
                <div class="col-sm-9 text-secondary">
                    {{ $declaracao->created_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header text-white div-form">
            Disciplinas Declaradas
        </div>
        <div class="card-body">
            @foreach($declaracao->intencoesMatricula as $intencao)
                <h5>Intenção de Matrícula: {{ $intencao->numero_periodo }}º Período - {{ $intencao->ano }}</h5>
                
                @if($intencao->disciplinas->count() > 0)
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <!-- Primeira linha: Períodos 1-5 -->
                            <thead class="table-light">
                                <tr>
                                    @for($periodo = 1; $periodo <= 5; $periodo++)
                                        <th class="text-center" style="width: 20%">{{ $periodo }}º Período</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="vertical-align: top;">
                                    @for($periodo = 1; $periodo <= 5; $periodo++)
                                        <td style="width: 20%; padding: 15px;">
                                            @php
                                                $disciplinasDoPeriodo = $intencao->disciplinas->where('periodo', $periodo);
                                            @endphp
                                            
                                            @if($disciplinasDoPeriodo->count() > 0)
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($disciplinasDoPeriodo as $disciplina)
                                                        <li class="mb-2 small">{{ $disciplina->nome }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-muted text-center small">
                                                    <i>Nenhuma disciplina</i>
                                                </div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Segunda linha: Períodos 6-10 -->
                        <table class="table table-bordered" style="margin-top: 0;">
                            <thead class="table-light">
                                <tr>
                                    @for($periodo = 6; $periodo <= 10; $periodo++)
                                        <th class="text-center" style="width: 20%">{{ $periodo }}º Período</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="vertical-align: top;">
                                    @for($periodo = 6; $periodo <= 10; $periodo++)
                                        <td style="width: 20%; padding: 15px;">
                                            @php
                                                $disciplinasDoPeriodo = $intencao->disciplinas->where('periodo', $periodo);
                                            @endphp
                                            
                                            @if($disciplinasDoPeriodo->count() > 0)
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($disciplinasDoPeriodo as $disciplina)
                                                        <li class="mb-2 small">{{ $disciplina->nome }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="text-muted text-center small">
                                                    <i>Nenhuma disciplina</i>
                                                </div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mt-3">
                        Nenhuma disciplina associada a esta intenção de matrícula.
                    </div>
                @endif
                
                <hr>
            @endforeach
            
            <div class="mt-4">
                <a href="{{ route('declaracao_intencao_matricula.index') }}" class="btn custom-button">
                    <i class="fas fa-arrow-left"></i> Voltar para Lista
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: hidden; /* Removendo scroll horizontal */
    max-height: none; /* Removendo a altura máxima para evitar cortes */
}

.table {
    margin-bottom: 0; /* Removendo o espaço entre as tabelas */
    table-layout: fixed; /* Garante tamanho uniforme das colunas */
    width: 100%;
}

.table th, .table td {
    width: 20%; /* Cada coluna ocupa exatamente 20% da largura */
}

/* Responsividade para telas menores */
@media (max-width: 768px) {
    .table th, .table td {
        font-size: 0.85rem;
    }
    
    .table-responsive {
        overflow-x: auto; /* Permitir scroll horizontal apenas em telas muito pequenas */
    }
}
</style>
@stop
