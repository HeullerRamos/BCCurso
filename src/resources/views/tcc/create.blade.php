@extends('layouts.main')
@section('title', 'Cadastrar TCC')
@section('content')

<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-graduation-cap fa-2x"></i>
            <h3 class="smaller-font">Cadastro do TCC</h3>
        </div>
    </div>
</div>

<div class="container mt-4">

    <div class="card-body">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <form method="post" action="{{ route('tcc.store') }}" enctype="multipart/form-data">
                        @csrf

                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Informações Gerais</h6>
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" id="titulo"
                                class="form-control @error('titulo') is-invalid @enderror" placeholder="Título do TCC"
                                required>
                            @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="resumo" class="form-label">Resumo <span class="text-danger">*</span></label>
                            <textarea name="resumo" id="resumo"
                                class="form-control @error('resumo') is-invalid @enderror" rows="4"
                                placeholder="Resumo do TCC" required></textarea>
                            @error('resumo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3"><i class="fas fa-users"></i> Participantes e Banca</h6>
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label for="aluno_id" class="form-label">Aluno <span class="text-danger">*</span></label>
                                <select name="aluno_id" id="aluno_id"
                                    class="form-select @error('aluno_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Selecione um aluno</option>
                                    @foreach ($alunos as $aluno => $key)
                                    <option value="{{ $aluno }}"> {{ $key }}</option>
                                    @endforeach
                                </select>
                                @error('aluno_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror                                <div class="mt-2">
                                    <a href="#" class="btn btn-info modal-trigger" data-bs-toggle="modal"
                                        data-bs-target="#createAluno">Cadastrar aluno</a>
                                </div>

                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="professor_id" class="form-label">Orientador <span class="text-danger">*</span></label>
                                <select name="professor_id" id="professor_id"
                                    class="form-select @error('professor_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Selecione um orientador</option>
                                    @foreach ($professores as $professor)
                                    <option value="{{ $professor->id }}"> {{ $professor->nome }} </option>
                                    @endforeach
                                </select>
                                @error('professor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror                                <div class="mt-2">
                                    <a href="#" class="btn btn-info modal-trigger" data-bs-toggle="modal"
                                        data-bs-target="#createProfessor">Cadastrar professor</a>
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="banca_id" class="form-label">Banca <span class="text-danger">*</span></label>
                                <select name="banca_id" id="banca_id"
                                    class="form-select @error('banca_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Selecione uma banca</option>
                                    @foreach ($bancas as $banca)
                                    <option value="{{ $banca->id }}">
                                        {{ date('d/m/Y', strtotime($banca->data)) }} - {{ $banca->local }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('banca_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror                                <div class="mt-2">
                                    <a href="#" class="btn btn-info modal-trigger" data-bs-toggle="modal"
                                        data-bs-target="#createBanca">Cadastrar banca</a>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3"><i class="fas fa-clipboard-check"></i> Detalhes Finais</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ano" class="form-label">Ano <span class="text-danger">*</span></label>
                                <input type="number" name="ano" id="ano"
                                    class="form-control @error('ano') is-invalid @enderror" min="2013"
                                    value="{{ $anoAtual }}" required>
                                @error('ano')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="0" selected>Aguardando defesa</option>
                                    <option value="1">Concluído</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3" id="arquivo_id" style="display: none;">
                            <label for="arquivo" class="form-label">Arquivo (Versão Final)</label>
                            <input type="file" name="arquivo" id="arquivo" class="form-control">
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="convite" id="convite" checked>
                            <label class="form-check-label" for="convite">
                                Gerar e publicar um convite de defesa do TCC
                            </label>
                        </div>                        <div class="d-flex mt-4">
                            <button type="submit"
                                class="btn custom-button custom-button-castastrar-tcc btn-default me-2">Cadastrar
                                TCC</button>
                            <a href="{{ route('tcc.index') }}"
                                class="btn custom-button custom-button-castastrar-tcc btn-default">Cancelar</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@include('modal.createAluno')
@include('modal.createBanca')

@include('modal.createProfessor')

@include('modal.createProfessorBanca')

@include('modal.createProfessorExterno')

<script>
document.getElementById("banca_id").addEventListener("change", function() {
    var selectedOption = this.options[this.selectedIndex];
    var selectedDate = selectedOption.getAttribute("data-data");
    document.getElementById("data").value = selectedDate;
});

var statusSelect = document.getElementById("status");
var conviteCheckbox = document.getElementById("convite");
var arquivo = document.getElementById("arquivo_id");
arquivo.style.display = "none";

statusSelect.addEventListener("change", function() {
    if (statusSelect.value === "1") {
        conviteCheckbox.checked = false;
        conviteCheckbox.disabled = true;
        arquivo.style.display = "block";
    } else {
        conviteCheckbox.disabled = false;
        conviteCheckbox.checked = true;
        arquivo.style.display = "none";
    }
});
</script>

@endsection