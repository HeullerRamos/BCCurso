@extends('layouts.main')
@section('title', 'TCC')
@section('content')

<div class="page-header">
    <div class="container">
        <div class="title-container">
            <div class="page-title">
                <i class="fas fa-graduation-cap fa-2x"></i>
                <h2>Gerenciar TCC</h2>
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
            <span>TCC</span>
            <a href="{{ route('tcc.create') }}" class="btn-cadastrar">
                <i class="fas fa-plus"></i> Cadastrar
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tccTable" class="table data-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Resumo</th>
                            <th>Arquivo</th>
                            <th>Aluno</th>
                            <th>Orientador</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tccs as $tcc)
                            <tr>
                                <td>
                                    <span class="data-title" data-toggle="tooltip" data-placement="top" title="{{ $tcc->titulo }}">
                                        {{$tcc->titulo}}
                                    </span>
                                </td>

                                <td data-toggle="tooltip" data-placement="top" title="{{ $tcc->resumo }}">
                                    {{ $tcc->resumo }}
                                </td>

                                <td>
                                    @if ($tcc->arquivo)
                                        <a href="{{ URL::asset('storage') }}/{{ $tcc->arquivo->path }}"
                                            download>{{ strlen($tcc->arquivo->nome) > 30 ? substr($tcc->arquivo->nome, 0, 30) . '...' : $tcc->arquivo->nome }}</a>
                                    @else
                                        Não há arquivo cadastrado!
                                    @endif
                                </td>
                                <td data-toggle="tooltip" data-placement="top" title="{{ $tcc->aluno->nome }}">
                                    {{ $tcc->aluno->nome }}
                                </td>
                                <td data-toggle="tooltip" data-placement="top">
                                    {{ $professores->contains($tcc->professor_id) ? $professores->where('id', $tcc->professor_id)->first()->nome : '' }}
                                </td>
                                <td>
                                    <span class="data-date">{{ $tcc->status == 0 ? 'Aguardando defesa' : 'Concluido' }}</span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('tcc.destroy', $tcc->id) }}">
                                        @csrf
                                        <div class="action-buttons">
                                            <a class="btn-view" href="{{ route('tcc.view', ['id' => $tcc->id]) }}" title="Visualizar">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if ($tcc->status == 0)
                                                <a href="" class="btn-edit modal-trigger" data-bs-toggle="modal" data-bs-target="#concluirTcc_{{ $tcc->id }}" title="Concluir">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            @endif
                                            <input name="_method" type="hidden" value="DELETE">
                                            <a href="{{ route('tcc.edit', $tcc->id) }}" class="btn-edit" title="Editar">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <button type="submit" class="btn-delete" title="Excluir" onclick="return confirm('Deseja realmente excluir esse registro?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @include('modal.concluirTcc', ['modalId' => 'concluirTcc_' . $tcc->id])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var searchText = $(this).val().toLowerCase();
            $("#tccTable tbody tr").filter(function() {
                // Excluindo a última coluna que é a de ação do filtro
                var rowData = $(this).find("td:not(:last-child)").text().toLowerCase();
                $(this).toggle(rowData.indexOf(searchText) > -1);
            });
        });
    });
</script>

@endsection
