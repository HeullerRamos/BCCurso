@extends('layouts.main')
@section('title', 'Visualização de Postagem')
@section('content')

<style>
    :root {
        --navy-primary: #1c2c4c;
        --navy-secondary: #283f6c;
        --blue-medium: #4682b4;
        --blue-light: #6699cc;
        --blue-lighter: #dbe9f6;
        --text-dark: #2c3e50;
        --text-light: #ffffff;
        --accent-color: #28a745;
        --border-radius: 12px;
        --shadow-standard: 0 5px 15px rgba(0, 0, 0, 0.08);
    }
    
    .post-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .post-header {
        margin-bottom: 2rem;
        position: relative;
        padding-bottom: 1rem;
    }
    
    .post-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--navy-primary);
        line-height: 1.2;
        margin-bottom: 1rem;
    }
    
    .post-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .post-date, .post-update {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .post-category {
        display: inline-block;
        background-color: var(--navy-secondary);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .post-cover {
        width: 100%;
        border-radius: var(--border-radius);
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-standard);
    }
    
    .post-cover img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .post-cover-inner {
        width: 100%;
        margin-bottom: 1.5rem;
        border-radius: var(--border-radius) var(--border-radius) 0 0;
        overflow: hidden;
    }
    
    .post-cover-inner img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .post-cover-divider {
        height: 2px;
        background: linear-gradient(to right, var(--blue-light), var(--navy-primary), var(--blue-light));
        margin: 0 10% 2rem 10%;
        opacity: 0.6;
        border-radius: 1px;
    }
    
    .post-content {
        background-color: white;
        border-radius: var(--border-radius);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-standard);
    }
    
    .post-text {
        color: var(--text-dark);
        line-height: 1.7;
        font-size: 1.05rem;
    }
    
    .post-text h1 {
        color: var(--navy-primary);
        font-size: 2.2rem;
        font-weight: 700;
        margin: 1.5rem 0 1rem;
    }
    
    .post-text h2 {
        color: var(--navy-secondary);
        font-size: 1.8rem;
        font-weight: 600;
        margin: 1.3rem 0 0.8rem;
    }
    
    .post-text h3 {
        color: var(--blue-medium);
        font-size: 1.5rem;
        font-weight: 600;
        margin: 1.2rem 0 0.7rem;
    }
    
    .post-text p {
        margin-bottom: 1rem;
    }
    
    .post-text ul, .post-text ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .post-text li {
        margin-bottom: 0.5rem;
    }
    
    .post-text a {
        color: var(--blue-medium);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .post-text a:hover {
        color: var(--navy-primary);
        text-decoration: underline;
    }
    
    /* Barra lateral */
    .post-sidebar {
        margin-top: 3rem;
    }
    
    .sidebar-card {
        background-color: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-standard);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .sidebar-header {
        background-color: var(--navy-primary);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }
    
    .sidebar-header i {
        color: var(--blue-light);
    }
    
    .sidebar-body {
        padding: 1.5rem;
    }
    
    /* Galeria de imagens */
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    
    .gallery-item {
        position: relative;
        padding-bottom: 100%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .gallery-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    
    .gallery-item img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Lista de arquivos */
    .file-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .file-item {
        padding: 0.75rem 1rem;
        background-color: var(--blue-lighter);
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: background-color 0.2s ease;
    }
    
    .file-item:hover {
        background-color: rgba(102, 153, 204, 0.2);
    }
    
    .file-icon {
        color: var(--navy-secondary);
        font-size: 1.25rem;
    }
    
    .file-link {
        color: var(--navy-primary);
        text-decoration: none;
        font-weight: 500;
        flex-grow: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .file-link:hover {
        text-decoration: underline;
        color: var(--blue-medium);
    }
    
    /* Botão de voltar */
    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--navy-secondary);
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        margin-top: 1rem;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    
    .back-button:hover {
        background-color: var(--navy-primary);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }
    
    .empty-state {
        padding: 1.5rem;
        text-align: center;
        color: #6c757d;
        font-style: italic;
    }
    
    /* Responsividade */
    @media (max-width: 992px) {
        .post-sidebar {
            margin-top: 2rem;
        }
        
        .post-title {
            font-size: 2.2rem;
        }
    }
    
    @media (max-width: 768px) {
        .post-title {
            font-size: 1.8rem;
        }
        
        .post-content {
            padding: 1.5rem;
        }
        
        .post-text {
            font-size: 1rem;
        }
        
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 576px) {
        .post-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .post-title {
            font-size: 1.6rem;
        }
        
        .post-content {
            padding: 1.2rem;
        }
    }
</style>

<div class="post-container">
    <a href="{{ url()->previous() }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
    
    <div class="row mt-4">
        <div class="col-lg-8">
            <article>
                <!-- Post header -->
                <div class="post-header">
                    <h1 class="post-title">{{ $postagem->titulo }}</h1>
                    <div class="post-meta">
                        <div class="post-date">
                            <i class="far fa-calendar-alt"></i>
                            Publicado em {{ \Carbon\Carbon::parse($postagem->created_at)->isoFormat('DD [de] MMMM [de] YYYY, HH[h]mm') }}
                        </div>
                        <div class="post-update">
                            <i class="far fa-clock"></i>
                            Atualizado em {{ \Carbon\Carbon::parse($postagem->updated_at)->isoFormat('DD [de] MMMM [de] YYYY, HH[h]mm') }}
                        </div>
                        <span class="post-category">{{ $tipo_postagem->nome }}</span>
                    </div>
                </div>

                <!-- Post content with cover image -->
                <div class="post-content">
                    @if ($postagem->menu_inicial)
                    @php $capa = $postagem->capa; @endphp
                    @if (Storage::disk('public')->exists($capa->imagem))
                    <div class="post-cover-inner">
                        <img src="{{ URL::asset('storage') }}/{{ $capa->imagem }}" alt="{{ $postagem->titulo }}">
                    </div>
                    <div class="post-cover-divider"></div>
                    @endif
                    @endif
                    
                    <div class="post-text">{!! $postagem->texto !!}</div>
                </div>
            </article>
        </div>
        
        <div class="col-lg-4">
            <div class="post-sidebar">
                <!-- Images gallery -->
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <i class="far fa-images"></i> Galeria de Imagens
                    </div>
                    <div class="sidebar-body">
                        @if (count($postagem->imagens) > 0)
                        <div class="gallery-grid">
                            @foreach ($postagem->imagens as $imagem)
                            @if (Storage::disk('public')->exists($imagem->imagem))
                            <a class="gallery-item" href="{{ URL::asset('storage') }}/{{ $imagem->imagem }}" target="_blank">
                                <img src="{{ URL::asset('storage') }}/{{ $imagem->imagem }}" alt="{{ $postagem->titulo }}">
                            </a>
                            @endif
                            @endforeach
                        </div>
                        @else
                        <div class="empty-state">Nenhuma imagem disponível</div>
                        @endif
                    </div>
                </div>
                
                <!-- File attachments -->
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <i class="far fa-file-alt"></i> Arquivos
                    </div>
                    <div class="sidebar-body">
                        @if (count($postagem->arquivos) > 0)
                        <div class="file-list">
                            @foreach ($postagem->arquivos as $arquivo)
                            <div class="file-item">
                                <div class="file-icon">
                                    <i class="far fa-file"></i>
                                </div>
                                <a class="file-link" href="{{ URL::asset('storage') }}/{{ $arquivo->path }}" target="_blank" title="Arquivo">
                                    {{ $arquivo->nome }}
                                </a>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty-state">Nenhum arquivo disponível</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
