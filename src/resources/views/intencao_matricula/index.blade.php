@extends('layouts.main')

@section('title', 'Intenções de Matrícula')

@section('content')
<div class="page-header">
    <div class="container">
        <div class="title-container">
            <div class="page-title">
                <i class="fas fa-graduation-cap fa-2x"></i>
                <h2>Gerenciar Intenções de Matrícula</h2>
            </div>
            
            <div class="row campo-busca">
                <div class="col-md-12">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar em todos os campos" aria-label="Buscar">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="content-card">
        <div class="card-header">
            <span>Intenções de Matrícula</span>
            <a href="{{ route('intencao_matricula.create') }}" class="btn-cadastrar">
                <i class="fas fa-plus"></i> Cadastrar
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="intencaoTable" class="table data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Período</th>
                            <th>Ano</th>
                            <th>Disciplinas</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($intencoes as $intencao)
                            <tr>
                                <td>{{ $intencao->id }}</td>
                                <td>{{ $intencao->numero_periodo }}º</td>
                                <td>{{ $intencao->ano }}</td>
                                <td>{{ $intencao->disciplinas->count() }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('intencao_matricula.show', $intencao->id) }}" class="btn-view" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('intencao_matricula.edit', $intencao->id) }}" class="btn-edit" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('intencao_matricula.destroy', $intencao->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta intenção de matrícula?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhuma intenção de matrícula encontrada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var searchText = $(this).val().toLowerCase();
            $("#intencaoTable tbody tr").filter(function() {
                var rowData = $(this).find("td:not(:last-child)").text().toLowerCase();
                $(this).toggle(rowData.indexOf(searchText) > -1);
            });
        });
    });
</script>
@stop