@extends('layouts.main')

@section('title', 'Criar Postagem')

@section('content')

@include('layouts.flash-message')

<style>
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
                <h3 class="smaller-font form-label">Criar Postagem</h3>
            </div>
        </div>
    </div>
    <div class="container">
        <form method="post" action="{{ route('postagem.store') }}" enctype="multipart/form-data" id="postagemForm">
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
                <label for="main-image" class="form-label">Capa da Postagem (2700 x 660)</label>
                <input type="file" name="main_image" id="main_image" class="form-control">
                <div id="main-image-error-message" class="file-error-message"></div>
            </div>

            <div class="form-group">
                <label for="imagens" class="form-label">Imagens:</label>
                <input type="file" name="imagens[]" id="imagens" class="form-control" multiple>
                <div id="imagens-error-message" class="file-error-message"></div>
            </div>

            <div class="form-group">
                <label for="arquivos" class="form-label">Arquivos: (Tamanho máximo permitido por arquivo 60MB)</label>
                <input type="file" name="arquivos[]" id="arquivos" class="form-control" multiple>
                <div id="file-error-message" class="file-error-message"></div>
            </div>

            <button type="submit" class="btn custom-button btn-default">Cadastrar</button>
            <a href="{{ route('postagem.index') }} "
                class="btn custom-button custom-button-castastrar-tcc btn-default">Cancelar</a>
        </form>
    </div>

    <script>
        const maxFileSize = 60 * 1024 * 1024;
        
        const mainImageInput = document.getElementById('main_image');
        const mainImageErrorDiv = document.getElementById('main-image-error-message');

        const imagensInput = document.getElementById('imagens');
        const imagensErrorDiv = document.getElementById('imagens-error-message');

        const arquivosInput = document.getElementById('arquivos');
        const arquivosErrorDiv = document.getElementById('file-error-message');

        const postagemForm = document.getElementById('postagemForm');

        function validateFile(fileInput, errorDiv, isImage = false) {
            errorDiv.textContent = '';
            const files = fileInput.files;

            if (files.length === 0) {
                return true;
            }

            const allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (isImage && !allowedImageTypes.includes(file.type)) {
                    errorDiv.textContent = `O arquivo "${file.name}" não é um tipo de imagem permitido (apenas JPEG, PNG, GIF, SVG, WEBP).`;
                    fileInput.value = '';
                    return false;
                }

                if (file.size > maxFileSize) {
                    errorDiv.textContent = `O arquivo "${file.name}" excede o tamanho máximo permitido de 60MB.`
                    fileInput.value = '';
                    return false;
                }
            }
            return true;
        }

        if (mainImageInput) {
            mainImageInput.addEventListener('change', function() {
                validateFile(mainImageInput, mainImageErrorDiv, true);
            });
        }

        if (imagensInput) {
            imagensInput.addEventListener('change', function() {
                validateFile(imagensInput, imagensErrorDiv, true);
            });
        }

        if (arquivosInput) {
            arquivosInput.addEventListener('change', function() {
                validateFile(arquivosInput, arquivosErrorDiv, false);
            });
        }

        if (postagemForm) {
            postagemForm.addEventListener('submit', function(event) {
                let isValid = true;

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
