@extends('layouts.main')

@section('title', 'Editar Banca')

@section('content')
<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-pencil-alt fa-2x"></i>
            <h3 class="smaller-font">Editar Banca</h3>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            {{-- O formulário agora aponta para a rota de UPDATE e usa o método PUT --}}
            <form method="post" action="{{ route('banca.update', $banca->id) }}">
                @csrf
                @method('PUT')

                {{-- LINHA 1: DATA E LOCAL LADO A LADO --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="data" class="form-label fw-bold">Data da banca*:</label>
                        {{-- O valor do campo é preenchido com os dados existentes --}}
                        <input type="date" name="data" id="data" class="form-control" value="{{ $banca->data }}" required>
                    </div>
                    <div class="col-md-8">
                        <label for="local" class="form-label fw-bold">Local*:</label>
                        <input type="text" name="local" id="local" class="form-control" value="{{ $banca->local }}" placeholder="Ex: Sala 201 ou link da chamada" required>
                    </div>
                </div>

                <hr>

                {{-- LINHA 2: MEMBROS INTERNOS --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3">Membros Internos</h5>
                        
                        {{-- SELEÇÃO DO PRESIDENTE --}}
                        <div class="mb-3">
                            <label for="presidente" class="form-label fw-bold">Presidente*:</label>
                            <select name="presidente" id="presidente" class="form-select" required>
                                <option value="" disabled>Selecione um presidente para a banca</option>
                                @foreach ($professores_internos as $professor)
                                    {{-- Adiciona o atributo 'selected' se o professor for o presidente atual --}}
                                    <option value="{{ $professor->id }}" data-professor-id="{{ $professor->id }}"
                                        {{ $banca->presidente_id == $professor->id ? 'selected' : '' }}>
                                        {{ $professor->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SELEÇÃO DOS AVALIADORES INTERNOS --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold">Avaliadores Internos:</label>
                                <a href="#" class="btn btn-outline-primary btn-sm py-0" data-bs-toggle="modal" data-bs-target="#createProfessor"><i class="fas fa-plus fa-xs"></i> Novo</a>
                            </div>
                            <div class="p-3 border rounded" style="max-height: 200px; overflow-y: auto;">
                                @foreach ($professores_internos as $professor_interno)
                                <div class="form-check">
                                    {{-- Adiciona o atributo 'checked' se o professor já estiver na banca --}}
                                    <input type="checkbox" class="form-check-input" name="professores_internos[]" id="professor_{{$professor_interno->id}}" value="{{$professor_interno->id}}"
                                        {{ $banca->professores->contains($professor_interno->id) ? 'checked' : '' }}>
                                    <label for="professor_{{$professor_interno->id}}" class="form-check-label">{{$professor_interno->nome}}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LINHA 3: MEMBROS EXTERNOS --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <h5 class="mb-3">Membros Externos</h5>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold">Avaliadores Externos:</label>
                                <a href="#" class="btn btn-outline-primary btn-sm py-0" data-bs-toggle="modal" data-bs-target="#createProfessorExterno"><i class="fas fa-plus fa-xs"></i> Novo</a>
                            </div>
                            <div class="p-3 border rounded" style="max-height: 200px; overflow-y: auto;">
                                @foreach ($professores_externos as $professor_externo)
                                <div class="form-check">
                                    {{-- Adiciona o atributo 'checked' se o professor já estiver na banca --}}
                                    <input type="checkbox" class="form-check-input" name="professores_externos[]" id="professor_externo_{{$professor_externo->id}}" value="{{$professor_externo->id}}"
                                        {{ $banca->professoresExternos->contains($professor_externo->id) ? 'checked' : '' }}>
                                    <label for="professor_externo_{{$professor_externo->id}}" class="form-check-label">{{$professor_externo->nome}} - {{$professor_externo->filiacao}}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- BOTÕES DE AÇÃO --}}
                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('banca.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Os seus @include para os modais continuam aqui --}}
@include('modal.createProfessor')
@include('modal.createProfessorExterno')

@stop

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Função para atualizar o estado do checkbox do presidente
        function updatePresidenteCheckbox() {
            var selectedPresidenteId = $('#presidente').find(':selected').data('professor-id');
            
            // Primeiro, habilita todos os checkboxes
            $('input[name="professores_internos[]"]').prop('disabled', false);

            // Se um presidente estiver selecionado, marca e desabilita o checkbox correspondente
            if (selectedPresidenteId) {
                var presidenteCheckbox = $('#professor_' + selectedPresidenteId);
                // Não alteramos o 'checked' aqui, apenas desabilitamos
                presidenteCheckbox.prop('disabled', true);
            }
        }

        // Executa a função quando a página carrega para verificar o estado inicial
        updatePresidenteCheckbox();

        // Executa a função sempre que o presidente for alterado
        $('#presidente').change(function() {
            // Ao mudar o presidente, desmarcamos todos e depois aplicamos a lógica
            $('input[name="professores_internos[]"]').prop('checked', false);
            updatePresidenteCheckbox();
            // Marca o novo presidente selecionado
            var selectedPresidenteId = $(this).find(':selected').data('professor-id');
            if(selectedPresidenteId) {
                 $('#professor_' + selectedPresidenteId).prop('checked', true);
            }
        });

        // O script AJAX para cadastro de novos professores continua igual
        // --- AJAX PARA PROFESSOR INTERNO ---
        $('#form-create-professor-interno-ajax').on('submit', function(e) { /* ... */ });
        // --- AJAX PARA PROFESSOR EXTERNO ---
        $('#form-create-professor-externo-ajax').on('submit', function(e) { /* ... */ });
    });
</script>
@endpush
