@extends('layouts.main')

@section('title', 'Postagens')

@section('content')
<style>
    /* Estilos para a página de postagens com paleta azul marinho */
    .postagens-header {
        background-color: var(--primary-blue);
        color: var(--text-light);
        padding: 2rem 0;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
        border-bottom: 3px solid var(--secondary-blue);
    }
    
    .postagens-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 30%;
        height: 100%;
        background: linear-gradient(135deg, transparent 0%, rgba(102, 153, 204, 0.1) 100%);
        z-index: 1;
    }
    
    .postagens-title-container {
        position: relative;
        z-index: 2;
    }
    
    .postagens-title {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .postagens-title i {
        color: var(--light-blue);
        filter: drop-shadow(0 0 5px rgba(102, 153, 204, 0.5));
    }
    
    .postagens-title h2 {
        font-weight: 600;
        margin: 0;
        font-size: 1.8rem;
        letter-spacing: 0.5px;
    }
    
    .search-container {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius-lg);
        padding: 0.8rem;
        margin-top: 1.5rem;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: var(--transition-normal);
    }
    
    .search-container:focus-within {
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: var(--shadow-md);
        border-color: var(--light-blue);
    }
    
    .search-input-group {
        display: flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.15);
        border-radius: var(--border-radius-md);
        padding: 0.5rem 1rem;
    }
    
    .search-input-group i {
        color: var(--text-light);
        margin-right: 0.8rem;
    }
    
    .search-input {
        background-color: transparent;
        border: none;
        color: var(--text-light);
        width: 100%;
        padding: 0.5rem 0;
        outline: none;
    }
    
    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }
    
    .postagens-card {
        border: none;
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        background-color: white;
    }
    
    .card-header {
        background-color: var(--primary-blue);
        color: var(--text-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.2rem 1.5rem;
        font-weight: 500;
        font-size: 1.1rem;
        border-bottom: none;
    }
    
    .btn-cadastrar {
        background-color: var(--accent-green);
        color: white;
        border: none;
        border-radius: var(--border-radius-md);
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: var(--transition-normal);
        box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
    }
    
    .btn-cadastrar:hover {
        background-color: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    }
    
    .postagens-table {
        margin-bottom: 0;
    }
    
    .postagens-table th {
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: var(--primary-blue);
        border-bottom: 2px solid #e9ecef;
        background-color: #f8f9fa;
    }
    
    .postagens-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f2f2f2;
    }
    
    .postagens-table tr {
        transition: var(--transition-fast);
    }
    
    .postagens-table tr:hover {
        background-color: rgba(102, 153, 204, 0.1);
    }
    
    /* Indicador de postagem sem imagem */
    .no-image-badge {
        font-size: 0.7rem;
        background-color: #f2f2f2;
        color: #6c757d;
        padding: 0.2rem 0.6rem;
        border-radius: 1rem;
        margin-left: 0.5rem;
        vertical-align: middle;
    }
    
    /* Estilizando título das postagens */
    .post-title {
        color: var(--primary-blue);
        font-weight: 500;
        transition: var(--transition-fast);
        display: inline-block;
        max-width: 350px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .post-title:hover {
        color: var(--secondary-blue);
    }
    
    /* Estilizando data de criação */
    .post-date {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    /* Estilizando botões de ação */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-view {
        background-color: var(--accent-green);
        color: white;
    }
    
    .btn-edit {
        background-color: var(--secondary-blue);
        color: white;
    }
    
    .btn-delete {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-view, .btn-edit, .btn-delete {
        border: none;
        width: 32px;
        height: 32px;
        border-radius: var(--border-radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-normal);
    }
    
    .btn-view:hover, .btn-edit:hover, .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }
    
    /* Estilizando checkbox de fixação */
    .pin-checkbox-container {
        display: flex;
        justify-content: center;
    }
    
    .pin-post-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--secondary-blue);
    }
    
    /* Estilos para postagens fixadas */
    .pinned-row {
        background-color: rgba(102, 153, 204, 0.1);
    }
    
    .pinned-indicator {
        color: var(--secondary-blue);
        margin-left: 0.5rem;
        font-size: 0.9rem;
    }
    
    /* Responsividade */
    @media (max-width: 768px) {
        .postagens-table th:nth-child(2), 
        .postagens-table td:nth-child(2) {
            display: none;
        }
        
        .post-title {
            max-width: 200px;
        }
    }
    
    @media (max-width: 576px) {
        .search-container {
            margin-top: 1rem;
        }
        
        .postagens-title h2 {
            font-size: 1.5rem;
        }
        
        .postagens-table th, 
        .postagens-table td {
            padding: 0.8rem;
        }
        
        .post-title {
            max-width: 150px;
        }
    }
</style>

<div class="postagens-header">
    <div class="container">
        <div class="postagens-title-container">
            <div class="postagens-title">
                <i class="fas fa-newspaper fa-2x"></i>
                <h2>Gerenciar Notícias</h2>
            </div>
            
            <div class="search-container">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Buscar notícias..." aria-label="Buscar">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="postagens-card">
        <div class="card-header">
            <span>Notícias</span>
            <a href="{{ route('postagem.create') }}" class="btn btn-cadastrar">
                <i class="fas fa-plus"></i> Cadastrar
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="postagemTable" class="table postagens-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Criação</th>
                            <th>Ação</th>
                            <th>Fixar</th>
                        </tr>
                    </thead>
                            <tbody>
                        @foreach ($postagens as $postagem)
                        <tr class="{{ $postagem->isPinned() ? 'pinned-row' : '' }}">
                            <td>
                                <span class="post-title" data-toggle="tooltip" data-placement="top" title="{{ $postagem->titulo }}">
                                    {{ $postagem->titulo }}
                                </span>
                                @if(!$postagem->capa || !$postagem->menu_inicial)
                                    <span class="no-image-badge">sem imagem</span>
                                @endif
                                @if($postagem->isPinned())
                                    <i class="fas fa-thumbtack pinned-indicator" title="Notícia fixada"></i>
                                @endif
                            </td>
                            <td>
                                <span class="post-date">{{ date_format($postagem->created_at, 'd/m/Y H:i:s') }}</span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('postagem.destroy', $postagem->id) }}">
                                    @csrf
                                    <div class="action-buttons">
                                        <a class="btn-view" href="{{ route('postagem.show', ['id' => $postagem->id]) }}" title="Visualizar">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <input name="_method" type="hidden" value="DELETE">
                                        <a href="{{ route('postagem.edit', $postagem->id) }}" class="btn-edit" title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button type="submit" class="btn-delete" title="Excluir" onclick="return confirm('Deseja realmente excluir esse registro?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <div class="pin-checkbox-container">
                                    <input class="form-check-input pin-post-checkbox" type="checkbox"
                                           id="pinPost{{ $postagem->id }}"
                                           data-post-id="{{ $postagem->id }}"
                                           {{ $postagem->isPinned() ? 'checked' : '' }}>
                                </div>
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var searchText = $(this).val().toLowerCase();
            $("#postagemTable tbody tr").filter(function() {
                // Excluindo a última coluna que é a de ação do filtro
                var rowData = $(this).find("td:not(:last-child)").text().toLowerCase();
                $(this).toggle(rowData.indexOf(searchText) > -1);
            });
        });
    });

    const checkboxes = document.querySelectorAll('.pin-post-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const postId = this.dataset.postId;
            const isChecked = this.checked;
            const url = `{{ url('/postagens') }}/${postId}/toggle-pin`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({
                    _token: '{{ csrf_token() }}',
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    console.log(data.message);
                } else {
                    console.error('Erro ao alternar o status de fixação:', data.message);
                    this.checked = false;
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                this.checked = !isChecked;
            });
        });
    });
</script>
@stop
