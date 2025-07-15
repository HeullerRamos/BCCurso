@extends('layouts.main')
@section('title', 'Ciência da Computação')
@section('content')

<style>

article > header > h1 {
    font-size: 3rem; /* Aumente o tamanho conforme desejar */
    font-weight: bolder; /* Ou 'bold' */
    line-height: 1.1;
    margin-bottom: 0.5rem;
}
    /* Estilos para títulos dentro do conteúdo do artigo */
.card-text h1 {
    font-size: 2.5rem; /* Exemplo: ajuste o tamanho conforme sua necessidade */
    font-weight: bold;
    margin-top: 1.5rem; /* Espaçamento acima do título */
    margin-bottom: 0.5rem; /* Espaçamento abaixo do título */
    line-height: 1.2;
}

.card-text h2 {
    font-size: 2rem; /* Exemplo: ajuste o tamanho conforme sua necessidade */
    font-weight: bold;
    margin-top: 1.2rem;
    margin-bottom: 0.4rem;
    line-height: 1.2;
}

/* Adicione regras para h3, h4, h5, h6 se necessário */
.card-text h3 {
    font-size: 1.75rem;
    font-weight: bold;
    margin-top: 1rem;
    margin-bottom: 0.3rem;
}

/* Estilos para listas (ordenadas e não ordenadas) dentro do conteúdo do artigo */
.card-text ul {
    list-style: initial; /* Restaura o estilo padrão (disco para ul) */
    margin-left: 20px;   /* Adiciona um recuo para os marcadores serem visíveis */
    padding-left: 0;     /* Garante que não haja padding extra que esconde o marcador */
}

.card-text ol {
    list-style: decimal; /* Restaura o estilo padrão (numeração decimal para ol) */
    margin-left: 20px;   /* Adiciona um recuo para os números serem visíveis */
    padding-left: 0;     /* Garante que não haja padding extra que esconde o marcador */
}

/* Ajuste para itens de lista, se necessário */
.card-text li {
    margin-bottom: 0.5rem; /* Espaçamento entre os itens da lista */
}
</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <article>
                <header class="mb-4">
                    <h1 class="fw-bolder mb-1 text-wrap">{{ $postagem->titulo }}</h1>
                    <div class="text-muted small fst-italic mb-2 text-wrap">Publicado em {{ \Carbon\Carbon::parse($postagem->created_at)->isoFormat('DD [de] MMMM [de] YYYY, HH[h]mm') }} | Última atualização em {{ \Carbon\Carbon::parse($postagem->updated_at)->isoFormat('DD [de] MMMM [de] YYYY, HH[h]mm') }}</div>
                    <div class="badge bg-secondary text-decoration-none link-light text-wrap">{{ $tipo_postagem->nome }}</div>
                </header>

                @if ($postagem->menu_inicial)
                <figure class="mb-4">
                    @php $capa = $postagem->capa; @endphp
                    @if (Storage::disk('public')->exists($capa->imagem))
                    <img class="img-fluid rounded" src="{{ URL::asset('storage') }}/{{ $capa->imagem }}" alt="{{ $postagem->titulo }}">
                    @endif
                </figure>
                @endif

                <section class="mb-5">
                    <div class="card-text text-wrap">{!! $postagem->texto !!}</div>
                </section>
            </article>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Imagens</div>
                <div class="card-body">
                    @if (count($postagem->imagens) > 0)
                    <div class="row">
                        @foreach ($postagem->imagens as $imagem)
                        @if (Storage::disk('public')->exists($imagem->imagem))
                        <div class="col-4 mb-4">
                            <div class="square-image-container" style="width: 100%; padding-bottom: 100%; position: relative;">
                                <a href="{{ URL::asset('storage') }}/{{ $imagem->imagem }}" target="_blank">
                                    <img class="img-fluid rounded" src="{{ URL::asset('storage') }}/{{ $imagem->imagem }}" alt="{{ $postagem->titulo }}" style="object-fit: cover; position: absolute; width: 100%; height: 100%;">
                                </a>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @else
                    <div>Nenhuma imagem disponível.</div>
                    @endif
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">Arquivos</div>
                <div class="card-body">
                    <div class="row">
                        @if (count($postagem->arquivos) > 0)
                        @foreach ($postagem->arquivos as $arquivo)
                        <div class="text-wrap">
                            <a class="text-wrap" href="{{ URL::asset('storage') }}/{{ $arquivo->path }}" target="_blank" title="Arquivo">{{ $arquivo->nome }}</a>
                        </div>
                        @endforeach
                        @else
                        <div>Nenhum arquivo disponível.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
