@extends('layouts.main')

@section('title', 'Banca')

@section('content')
<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-chalkboard fa-2x"></i>
            <h3 class="smaller-font">Gerenciar Banca</h3>
        </div>
    </div>
</div>
<div class="container">


    <br>
    <div class="row campo-busca">
        <div class="col-md-12">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar em todos os campos" aria-label="Buscar">
        </div>
    </div>
    <br>

    <div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-white div-form">
                    Banca
                    {{--<a href="{{ route('banca.create') }}" class="btn btn-success btn-sm float-end">Cadastrar</a> --}}
                    <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modalCriarBanca">
                        Cadastrar
                    </button>
                    </div>
                <div class="card-body">

                    <table id="bancaTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Local</th>
                                <th>Membros</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bancas as $banca)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($banca->data)) }}</td>
                                <td class="text-wrap">{{ $banca->local }}</td>
                                <td class="text-wrap">
                                    <span style="font-weight: bold;"> Presidente da banca:</span><br>
                                    <span>{{ $banca->presidente->servidor->nome }}</span><br>

                                    @if (count($banca->professoresExternos) > 0)
                                    <span style="font-weight: bold;"> Professores Externos:</span><br>
                                    @foreach ($banca->professoresExternos as $professor_externo)
                                    <span>{{ $professor_externo->nome }} - {{ $professor_externo->filiacao }}
                                    </span><br>
                                    @endforeach
                                    <br>
                                    @endif

                                    @if (count($banca->professores) > 0)
                                    <span style="font-weight: bold;"> Professores Internos:</span><br>
                                    @foreach ($banca->professores as $professor)
                                    <span>{{ $professor->servidor->nome }}</span>
                                    <br>


                                    @endforeach
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('banca.destroy', $banca->id) }}">
                                        @csrf
                                        <a class="btn btn-success btn-sm" href="{{ route('banca.show', ['id' => $banca->id]) }}"><i class="fa-solid fa-eye"></i></a>
                                        <input name="_method" type="hidden" value="DELETE">
                                        <a href="{{ route('banca.edit', $banca->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                        <button type="submit" class="btn btn-danger btn-sm" title='Delete' onclick="return confirm('Deseja realmente excluir essa banca?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>


</div>


<div class="modal fade" id="modalCriarBanca" tabindex="-1" aria-labelledby="modalCriarBancaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <form method="post" action="{{ route('banca.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCriarBancaLabel">
                        <i class="fas fa-chalkboard me-2"></i>Criar Nova Banca
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <label for="data" class="form-label">Data da banca*:</label>
                            <input type="date" name="data" id="data" class="form-control" required>
                        </div>
                        <div class="col-md-7">
                            <label for="local" class="form-label">Local*:</label>
                            <input type="text" name="local" id="local" class="form-control" placeholder="Ex: Sala 201 ou link da chamada" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        {{-- Area para profs internos --}}
                        <div class="col-md-6">
                            <h6 class="mb-3">Membros Internos</h6>
                            <div class="mb-3">
                                <label for="presidente" class="form-label">Presidente*:</label>
                                <select name="presidente" id="presidente" class="form-select" required>
                                    <option value="" disabled selected>Selecione um presidente</option>
                                    @foreach ($professores_internos as $professor)
                                        <option value="{{ $professor->id }}" data-professor-id="{{ $professor->id }}"> {{ $professor->nome }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Avaliadores Internos:</label>
                                <div class="p-2 border rounded" style="max-height: 150px; overflow-y: auto;">
                                    @foreach ($professores_internos as $professor_interno)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="professores_internos[]" id="professor_{{$professor_interno->id}}" value="{{$professor_interno->id}}">
                                        <label for="professor_{{$professor_interno->id}}" class="form-check-label">{{$professor_interno->nome}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Area para externos --}}
                        <div class="col-md-6">
                             <h6 class="mb-3">Membros Externos</h6>
                             <div class="mb-3">
                                <label class="form-label">Avaliadores Externos:</label>
                                <div class="p-2 border rounded" style="max-height: 220px; overflow-y: auto;">
                                @foreach ($professores_externos as $professor_externo)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="professores_externos[]" id="professor_externo_{{$professor_externo->id}}" value="{{$professor_externo->id}}">
                                        <label for="professor_externo_{{$professor_externo->id}}" class="form-check-label">{{$professor_externo->nome}} - {{$professor_externo->filiacao}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    {{-- Botão para fechar o modal --}}
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    {{-- Botão para submeter o formulário --}}
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Salvar Cadastro</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>

    document.addEventListener("DOMContentLoaded", function() {
        const presidenteSelect = document.getElementById('presidente');
        
        if (presidenteSelect) {
            presidenteSelect.addEventListener('change', function() {
                // Pega o ID do professor a partir do atributo data-professor-id
                const selectedPresidenteId = this.options[this.selectedIndex].getAttribute('data-professor-id');
                
                // Seleciona todos os checkboxes de professores internos
                const checkboxes = document.querySelectorAll('input[name="professores_internos[]"]');
                
                checkboxes.forEach(checkbox => {
                    // Habilita e desmarca todos primeiro
                    checkbox.disabled = false;
                    checkbox.checked = false;
                });
                
                if (selectedPresidenteId) {
                    // Encontra o checkbox correspondente ao presidente
                    const presidenteCheckbox = document.getElementById('professor_' + selectedPresidenteId);
                    if (presidenteCheckbox) {
                        // Marca e desabilita para evitar que seja desmarcado
                        presidenteCheckbox.checked = true;
                        presidenteCheckbox.disabled = true;
                    }
                }
            });
        }
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var searchText = $(this).val().toLowerCase();
            $("#bancaTable tbody tr").filter(function() {
                // Excluindo a última coluna que é a de ação do filtro
                var rowData = $(this).find("td:not(:last-child)").text().toLowerCase();
                $(this).toggle(rowData.indexOf(searchText) > -1);
            });
        });
    });
</script>
@stop
