@extends('layouts.main')

@section('title', 'Editar Postagem')

@section('content')

    @include('layouts.flash-message')

<style>
    /* Estilos para títulos dentro do conteúdo do artigo */
    .note-editable h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .note-editable h2 {
        font-size: 2rem;
        font-weight: bold;
        margin-top: 1.2rem;
        margin-bottom: 0.4rem;
        line-height: 1.2;
    }

    .note-editable h3 {
        font-size: 1.75rem;
        font-weight: bold;
        margin-top: 1rem;
        margin-bottom: 0.3rem;
    }

    .note-editable ul {
        list-style: initial;
        margin-left: 20px;
        padding-left: 0;
    }

    .note-editable ol {
        list-style: decimal;
        margin-left: 20px;
        padding-left: 0;
    }

    .note-editable li {
        margin-bottom: 0.5rem;
    }

    .file-error-message {
        color: red;
        font-weight: bold;
        margin-top: 5px;
    }

    #main-image-error-message {
        color: red;
        font-weight: bold;
        margin-top: 5px;
    }

    #imagens-error-message {
        color: red;
        font-weight: bold;
        margin-top: 5px;
    }
</style>
    <div class="custom-container">
        <div>
            <div>
                <i class="fas fa-pen-to-square fa-2x"></i>
                <h3 class="smaller-font form-label">Editar Postagem</h3>
            </div>
        </div>
    </div>
    <div class="container">
        {{-- O ID 'postagemForm' é crucial para o JavaScript --}}
        <form method="post" action="{{ route('postagem.update', ['id' => $postagem->id]) }}" enctype="multipart/form-data" id="postagemForm">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="titulo" class="form-label"><br>Título*:</label>
                <input type="text" value="{{ old('titulo') ?? $postagem->titulo }}" name="titulo" id="titulo"
                    required class="form-control @error('titulo') is-invalid @enderror" placeholder="Título da postagem">

                @error('titulo')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="texto" class="form-label">Texto*:</label>
                <textarea name="texto" id="texto" class="form-control @error('texto') is-invalid @enderror"
                    placeholder="Texto da postagem" required>{{ old('texto') ?? $postagem->texto }}</textarea>
                @error('texto')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tipo_postagem" class="form-label">Tipo*:</label>
                <select name="tipo_postagem_id" id="tipo_postagem_id"
                    class="form-control @error('tipo_postagem_id') is-invalid @enderror" required>
                    @foreach ($tipo_postagens as $key => $value)
                        <option value="{{ $key }}" {{ $key == $postagem->tipo_postagem_id ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>

                @error('tipo_postagem_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="main-image" class="form-label">Capa da Postagem (2700 x 660)</label>
                @if($postagem->menu_inicial)
                    <img src="{{ asset('storage/'.$postagem->capa->imagem) }}" class="img-responsive"
                        style="max-height:100px; max-width:100px;">
                @endif
                <input type="file" name="main_image" id="main_image" class="form-control">
                <div id="main-image-error-message" class="file-error-message"></div> {{-- Mensagem de erro para capa --}}
            </div>

            <div class="form-group">
                <label for="imagens" class="form-label">Imagens:</label>
                @if(count($postagem->imagens) > 0)
                    @foreach($postagem->imagens as $img)
                        <div class="mb-2">
                            <button class="btn text-danger btn-sm" type="submit" form="deletar-imagens{{ $img->id }}">X</button>
                            <img src="{{ asset('storage/'.$img->imagem) }}" 
                                class="img-fluid" 
                                style="max-height:100px; max-width:100px;">
                        </div>
                    @endforeach
                @endif
                <input type="file" name="imagens[]" id="imagens" class="form-control" multiple>
                <div id="imagens-error-message" class="file-error-message"></div> {{-- Mensagem de erro para imagens adicionais --}}
            </div>

            <div class="form-group">
                <label for="arquivos" class="form-label">Arquivos: (Tamanho máximo permitido por arquivo 60MB)</label>
                @if (count($postagem->arquivos) > 0)
                    @foreach ($postagem->arquivos as $arquivo)
                        <button class="btn text-danger" type="submit"
                            form="deletar-arquivos{{ $arquivo->id }}">X</button>
                        <a download href="{{ asset('storage') }}/{{ $arquivo->path }}">{{ $arquivo->nome }}</a>
                    @endforeach
                @endif
                <input type="file" name="arquivos[]" id="arquivos" class="form-control" multiple>
                {{-- Div onde a mensagem de erro de arquivo será renderizada --}}
                <div id="file-error-message" class="file-error-message"></div>
            </div>

            <button type="submit" class="btn custom-button btn-default">Salvar</button>
            <a href="{{ route('postagem.index') }} "
                class="btn custom-button custom-button-castastrar-tcc btn-default">Cancelar</a>
        </form>

        @if (count($postagem->imagens) > 0)
            @foreach ($postagem->imagens as $img)
                <form id="deletar-imagens{{ $img->id }}"
                    action="{{ route('postagem.delete_imagem', ['id' => $img->id]) }}" method="post">
                    @csrf
                    @method('delete')
                </form>
            @endforeach
        @endif

        @if (count($postagem->arquivos) > 0)
            @foreach ($postagem->arquivos as $arquivo)
                <form id="deletar-arquivos{{ $arquivo->id }}"
                    action="{{ route('postagem.delete_arquivo', ['id' => $arquivo->id]) }}" method="post">
                    @csrf
                    @method('delete')
                </form>
            @endforeach
        @endif
    </div>

    <script>
        const maxFileSize = 60 * 1024 * 1024; // 60 MB em bytes
        
        const mainImageInput = document.getElementById('main_image');
        const mainImageErrorDiv = document.getElementById('main-image-error-message');

        const imagensInput = document.getElementById('imagens');
        const imagensErrorDiv = document.getElementById('imagens-error-message');

        const arquivosInput = document.getElementById('arquivos');
        const arquivosErrorDiv = document.getElementById('file-error-message');

        const postagemForm = document.getElementById('postagemForm');

        // Função genérica para validar arquivos (tamanho e tipo MIME para imagens)
        function validateFile(fileInput, errorDiv, isImage = false) {
            errorDiv.textContent = ''; // Limpa mensagens de erro anteriores

            const files = fileInput.files;

            if (files.length === 0) {
                return true; // Nenhum arquivo selecionado, considerado válido para o propósito de tamanho/tipo
            }

            const allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                // Validação de tipo MIME para imagens
                if (isImage && !allowedImageTypes.includes(file.type)) {
                    errorDiv.textContent = `O arquivo "${file.name}" não é um tipo de imagem permitido (apenas JPEG, PNG, GIF, SVG, WEBP).`;
                    fileInput.value = '';
                    return false;
                }

                // Validação de tamanho
                if (file.size > maxFileSize) {
                    errorDiv.textContent = `O arquivo "${file.name}" excede o tamanho máximo permitido de 60MB.`;
                    fileInput.value = '';
                    return false;
                }
            }
            return true; // Todos os arquivos são válidos
        }

        // Listener para o evento 'change' do input de capa
        if (mainImageInput) {
            mainImageInput.addEventListener('change', function() {
                validateFile(mainImageInput, mainImageErrorDiv, true);
            });
        }

        // Listener para o evento 'change' do input de imagens adicionais
        if (imagensInput) {
            imagensInput.addEventListener('change', function() {
                validateFile(imagensInput, imagensErrorDiv, true);
            });
        }

        // Listener para o evento 'change' do input de arquivos em geral
        if (arquivosInput) {
            arquivosInput.addEventListener('change', function() {
                validateFile(arquivosInput, arquivosErrorDiv, false);
            });
        }

        // Listener para o evento 'submit' do formulário
        if (postagemForm) {
            postagemForm.addEventListener('submit', function(event) {
                let isValid = true;

                // Revalida todos os campos de arquivo no submit
                if (mainImageInput && !validateFile(mainImageInput, mainImageErrorDiv, true)) {
                    isValid = false;
                }
                if (imagensInput && !validateFile(imagensInput, imagensErrorDiv, true)) {
                    isValid = false;
                }
                if (arquivosInput && !validateFile(arquivosInput, arquivosErrorDiv, false)) {
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        }
    </script>
    
@stop
