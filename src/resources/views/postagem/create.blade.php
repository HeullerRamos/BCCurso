@extends('layouts.main')

@section('title', 'Criar Postagem')

@section('content')

<style>
    /* Estilos para títulos dentro do conteúdo do artigo */
    .note-editable h1 {
        font-size: 2.5rem; /* Exemplo: ajuste o tamanho conforme sua necessidade */
        font-weight: bold;
        margin-top: 1.5rem; /* Espaçamento acima do título */
        margin-bottom: 0.5rem; /* Espaçamento abaixo do título */
        line-height: 1.2;
    }

    .note-editable h2 {
        font-size: 2rem; /* Exemplo: ajuste o tamanho conforme sua necessidade */
        font-weight: bold;
        margin-top: 1.2rem;
        margin-bottom: 0.4rem;
        line-height: 1.2;
    }

    /* Adicione regras para h3, h4, h5, h6 se necessário */
    .note-editable h3 {
        font-size: 1.75rem;
        font-weight: bold;
        margin-top: 1rem;
        margin-bottom: 0.3rem;
    }

    /* Estilos para listas (ordenadas e não ordenadas) dentro do conteúdo do artigo */
    .note-editable ul {
        list-style: initial; /* Restaura o estilo padrão (disco para ul) */
        margin-left: 20px;   /* Adiciona um recuo para os marcadores serem visíveis */
        padding-left: 0;     /* Garante que não haja padding extra que esconde o marcador */
    }

    .note-editable ol {
        list-style: decimal; /* Restaura o estilo padrão (numeração decimal para ol) */
        margin-left: 20px;   /* Adiciona um recuo para os números serem visíveis */
        padding-left: 0;     /* Garante que não haja padding extra que esconde o marcador */
    }

    /* Ajuste para itens de lista, se necessário */
    .note-editable li {
        margin-bottom: 0.5rem; /* Espaçamento entre os itens da lista */
    }
</style>

    <div class="custom-container">
        <div>
            <div>
                <i class="fas fa-pen-to-square fa-2x"></i>
                <h3 class="smaller-font" class="form-label">Criar Postagem</h3>
            </div>
        </div>
    </div>
    <div class="container">
        <form method="post" action="{{ route('postagem.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="titulo" class="form-label"><br>Título*:</label>
                <input value="{{ old('titulo', isset($postagem) ? $postagem['titulo'] : '') }}" type="text" name="titulo"
                    id="titulo" class="form-control" placeholder="Título da postagem" required>
            </div>

            <div class="form-group">
                <label for="texto" class="form-label">Texto*:</label>
                <textarea name="texto" id="texto" class="form-control" placeholder="Texto da postagem">{{ old('texto', isset($postagem) ? $postagem['texto'] : '') }}</textarea>
            </div>

            <div class="form-group">
                <label for="tipo_postagem" class="form-label">Tipo*:</label>
                <select name="tipo_postagem_id" id="tipo_postagem_id" class="form-control" required>
                    @foreach ($tipo_postagens as $key => $value)
                        <option value="{{ $key }}" {{ $key == old('tipo_postagem_id', $id) ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tipo_postagem" class="form-label">Exibir na tela inicial com destaque? (necessário cadastrar imagem)</label>
                <input type="checkbox" name="menu_inicial" id="menu_inicial" {{ old('menu_inicial') ? 'checked' : '' }}>
            </div>

            <div class="form-group">
                <label for="imagens" class="form-label">Imagens (caso for exibir na tela inicial, a primeira imagem deve ter a dimensão: 2700 x 660):</label>
                <input type="file" name="imagens[]" id="imagens" class="form-control" multiple>
                
            </div>

            <div class="form-group">
                <label for="arquivos" class="form-label">Arquivos:</label>
                <input type="file" name="arquivos[]" id="arquivos" class="form-control" multiple>
            </div>

            <button type="submit" class="btn custom-button btn-default">Cadastrar</button>
            <a href="{{ route('postagem.index') }} "
                class="btn custom-button custom-button-castastrar-tcc btn-default">Cancelar</a>
        </form>
    </div>

    
@stop
