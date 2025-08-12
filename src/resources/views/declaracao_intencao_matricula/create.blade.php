@extends('layouts.main')

@section('title', 'Declarar Intenção de Matrícula')

@section('content')
<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-graduation-cap fa-2x"></i>
            <h3 class="smaller-font">Declarar Intenção de Matrícula</h3>
        </div>
    </div>
</div>

<div class="container mt-5">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header text-white div-form">
            Nova Declaração de Intenção de Matrícula
        </div>
        <div class="card-body">
            <form action="{{ route('declaracao_intencao_matricula.store') }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="periodo" class="form-label">Número do Período <span class="text-danger">*</span></label>
                            <select class="form-control @error('periodo') is-invalid @enderror" id="periodo" name="periodo" required>
                                <option value="">Selecione o período</option>
                                @for($i = 1; $i <= 2; $i++)
                                    <option value="{{ $i }}" {{ old('periodo') == $i ? 'selected' : '' }}>{{ $i }}º Período</option>
                                @endfor
                            </select>
                            @error('periodo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ano" class="form-label">Ano <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('ano') is-invalid @enderror" id="ano" name="ano" value="{{ old('ano', date('Y')) }}" min="2020" max="{{ date('Y') + 5 }}" required>
                            @error('ano')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="intencao_matricula_id" class="form-label">Selecione a Intenção de Matrícula <span class="text-danger">*</span></label>
                    <select class="form-control @error('intencao_matricula_id') is-invalid @enderror" id="intencao_matricula_id" name="intencao_matricula_id" required>
                        <option value="">Selecione uma opção</option>
                        @foreach($intencoesMatricula as $intencao)
                            <option value="{{ $intencao->id }}" {{ old('intencao_matricula_id') == $intencao->id ? 'selected' : '' }}>
                                {{ $intencao->numero_periodo }}º Período - {{ $intencao->ano }} ({{ $intencao->disciplinas->count() }} disciplinas)
                            </option>
                        @endforeach
                    </select>
                    @error('intencao_matricula_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="alert alert-info mt-3">
                        <p><strong>Nota:</strong> Ao selecionar uma intenção de matrícula, você estará declarando interesse em cursar as disciplinas associadas a ela.</p>
                    </div>
                </div>

                <div id="previewDisciplinas" class="mt-4 mb-4 d-none">
                    <h5>Disciplinas da Intenção de Matrícula selecionada:</h5>
                    <div id="disciplinasContainer" class="table-responsive mt-2">
                        <!-- As disciplinas serão carregadas aqui via JavaScript -->
                    </div>
                </div>

                <hr>
                
                <div class="mt-4">
                    <a href="{{ route('declaracao_intencao_matricula.index') }}" class="btn custom-button ms-2">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn custom-button">
                        <i class="fas fa-save"></i> Declarar Intenção
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const intencaoSelect = document.getElementById('intencao_matricula_id');
    const previewDisciplinas = document.getElementById('previewDisciplinas');
    const disciplinasContainer = document.getElementById('disciplinasContainer');
    
    // Função para carregar disciplinas quando uma intenção é selecionada
    intencaoSelect.addEventListener('change', function() {
        const intencaoId = this.value;
        
        if (intencaoId) {
            fetch(`/api/intencao-matricula/${intencaoId}/disciplinas`)
                .then(response => response.json())
                .then(data => {
                    // Exibe o container de preview
                    previewDisciplinas.classList.remove('d-none');
                    
                    // Cria as tabelas para exibir as disciplinas
                    let disciplinasHTML = `
                        <table class="table table-bordered">
                            <!-- Primeira linha: Períodos 1-5 -->
                            <thead class="table-light">
                                <tr>
                                    ${[1,2,3,4,5].map(p => `<th class="text-center" style="width: 20%">${p}º Período</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="vertical-align: top;">
                                    ${[1,2,3,4,5].map(periodo => {
                                        const disciplinasDoPeriodo = data.filter(d => d.periodo === periodo);
                                        
                                        return `<td style="width: 20%; padding: 15px;">
                                            ${disciplinasDoPeriodo.length > 0 
                                                ? `<ul class="list-unstyled mb-0">
                                                    ${disciplinasDoPeriodo.map(d => `<li class="mb-2 small">${d.nome}</li>`).join('')}
                                                  </ul>`
                                                : `<div class="text-muted text-center small"><i>Nenhuma disciplina</i></div>`
                                            }
                                        </td>`;
                                    }).join('')}
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Segunda linha: Períodos 6-10 -->
                        <table class="table table-bordered" style="margin-top: 0;">
                            <thead class="table-light">
                                <tr>
                                    ${[6,7,8,9,10].map(p => `<th class="text-center" style="width: 20%">${p}º Período</th>`).join('')}
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="vertical-align: top;">
                                    ${[6,7,8,9,10].map(periodo => {
                                        const disciplinasDoPeriodo = data.filter(d => d.periodo === periodo);
                                        
                                        return `<td style="width: 20%; padding: 15px;">
                                            ${disciplinasDoPeriodo.length > 0 
                                                ? `<ul class="list-unstyled mb-0">
                                                    ${disciplinasDoPeriodo.map(d => `<li class="mb-2 small">${d.nome}</li>`).join('')}
                                                  </ul>`
                                                : `<div class="text-muted text-center small"><i>Nenhuma disciplina</i></div>`
                                            }
                                        </td>`;
                                    }).join('')}
                                </tr>
                            </tbody>
                        </table>
                    `;
                    
                    disciplinasContainer.innerHTML = disciplinasHTML;
                })
                .catch(error => {
                    console.error('Erro ao carregar disciplinas:', error);
                    previewDisciplinas.classList.add('d-none');
                });
        } else {
            // Esconde o container de preview quando nenhuma intenção está selecionada
            previewDisciplinas.classList.add('d-none');
        }
    });
    
    // Verifica se já existe uma intenção selecionada (caso de erro de validação)
    if (intencaoSelect.value) {
        intencaoSelect.dispatchEvent(new Event('change'));
    }
});
</script>

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
