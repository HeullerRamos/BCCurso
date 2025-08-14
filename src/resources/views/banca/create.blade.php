@extends('layouts.main')

@section('title', 'Criar Banca')

@section('content')
<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-chalkboard fa-2x"></i>
            <h3 class="smaller-font">Criar Nova Banca</h3>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card-body">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <form method="post" action="{{ route('banca.store') }}">
                        @csrf

                        <h6 class="mb-3"><i class="fas fa-info-circle"></i> Informações da Banca</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="data" class="form-label">Data da banca <span class="text-danger">*</span></label>
                                <input type="date" name="data" id="data" class="form-control @error('data') is-invalid @enderror" required>
                                @error('data')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label for="local" class="form-label">Local <span class="text-danger">*</span></label>
                                <input type="text" name="local" id="local" class="form-control @error('local') is-invalid @enderror" placeholder="Ex: Sala 201 ou link da chamada" required>
                                @error('local')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3"><i class="fas fa-users"></i> Membros Internos</h6>
                        <div class="mb-3">
                            <label for="presidente" class="form-label">Presidente <span class="text-danger">*</span></label>
                            <select name="presidente" id="presidente" class="form-select @error('presidente') is-invalid @enderror" required>
                                <option value="" disabled selected>Selecione um presidente para a banca</option>
                                @foreach ($professores_internos as $professor)
                                <option value="{{ $professor->id }}" data-professor-id="{{ $professor->id }}"> {{ $professor->nome }} </option>
                                @endforeach
                            </select>
                            @error('presidente')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="professores" class="form-label">Professores internos</label>
                            <div class="mt-2">
                                <a href="#" class="btn btn-info modal-trigger" data-bs-toggle="modal" data-bs-target="#createProfessor">
                                    Cadastrar professor
                                </a>
                            </div>
                            @foreach ($professores_internos as $professor_interno)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="professores_internos[]" id="professor_{{$professor_interno->id}}" value="{{$professor_interno->id}}">
                                <label for="professor_{{$professor_interno->id}}" class="form-check-label text-wrap">{{$professor_interno->nome}} </label>
                            </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3"><i class="fas fa-user-tie"></i> Membros Externos</h6>
                        <div class="mb-3">
                            <label for="professores" class="form-label">Professores externos</label>
                            <div class="mt-2">
                                <a href="#" class="btn btn-info modal-trigger" data-bs-toggle="modal" data-bs-target="#createProfessorExterno">
                                    Cadastrar professor externo
                                </a>
                            </div>
                            @foreach ($professores_externos as $professor_externo)
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="professores_externos[]" id="professor_externo_{{$professor_externo->id}}" value="{{$professor_externo->id}}">
                                <label for="professor_externo_{{$professor_externo->id}}" class="form-check-label text-wrap">{{$professor_externo->nome}} - {{$professor_externo->filiacao}}</label>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex mt-4">
                            <button type="submit" class="btn custom-button custom-button-castastrar-tcc btn-default me-2">Cadastrar Banca</button>
                            <a href="{{ route('banca.index') }}" class="btn custom-button custom-button-castastrar-tcc btn-default">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('modal.createProfessor')
@include('modal.createProfessorExterno')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#presidente').change(function() {
            var selectedPresidenteId = $(this).find(':selected').data('professor-id');

            $('input[name="professores_internos[]"]').prop('checked', false);
            $('input[name="professores_internos[]"]').prop('disabled', false);

            // Marque o checkbox correspondente ao presidente selecionado
            $('#professor_' + selectedPresidenteId).prop('checked', true);
            $('#professor_' + selectedPresidenteId).prop('disabled', true);
        });
    });
</script>
@endsection
