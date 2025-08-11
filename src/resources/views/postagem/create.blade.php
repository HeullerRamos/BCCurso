@extends('layouts.main')

@section('title', 'Criar Postagem')

@section('content')


@include('layouts.flash-message')

<div class="custom-container">
    <div>
        <div>
            <i class="fas fa-pen-to-square fa-2x"></i>
            <h3 class="smaller-font form-label">Criar Postagem</h3>
        </div>
    </div>
</div>
<div class="container form-container-main">
    <form method="post" action="{{ route('postagem.store') }}" enctype="multipart/form-data" id="postagemForm">
    @csrf

        <div class="form-section-paper">
            <div class="form-group">
                <label for="titulo" class="form-label">Título*:</label>
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
                        <option value="{{ $key }}" {{ $key == old('tipo_postagem_id', $id ?? '') ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-section-paper">
            <h5>Capa da Postagem</h5>
            <p class="text-muted">A imagem de capa deve ter a proporção de 2700x660 pixels.</p>

            <div id="upload-container">
                <label for="main_image" class="custom-file-button">Procurar Capa</label>
                <input type="file" name="main_image" id="main_image" class="hidden-file-input" accept="image/*">
            </div>
        </div>   

        <div class="form-section-paper">
            <h5>Imagens Adicionais</h5>
            <label for="imagens" class="custom-file-button">Procurar Imagens</label>
            <input type="file" name="imagens[]" id="imagens" class="hidden-file-input" accept="image/*" multiple>
            <div id="imagens-preview-container" class="preview-container"></div>
        </div>

        <div class="form-section-paper">
            <h5>Anexar Arquivos</h5>
            <p class="text-muted">Tamanho máximo permitido por arquivo: 60MB.</p>
            <label for="arquivos" class="custom-file-button">Procurar Arquivos</label>
            <input type="file" name="arquivos[]" id="arquivos" class="hidden-file-input" multiple>
            <div id="arquivos-preview-container" class="preview-container"></div>
        </div>

        <button type="submit" class="btn custom-button btn-default">Cadastrar</button>
        <a href="{{ route('postagem.index') }}" class="btn custom-button custom-button-castastrar-tcc btn-default">Cancelar</a>
    </form>
</div>

<div id="image-viewer-modal" class="image-modal" onclick="this.style.display='none'">
    <img id="modal-image-content" class="image-modal-content">
</div>

<!-- Modal de Recorte -->
<div class="modal" id="cropper-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Imagem de Capa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <p class="crop-instructions mb-0">
                        Use as alças para ajustar o recorte. Arraste a imagem para reposicionar e use o scroll do mouse para dar zoom.
                    </p>
                </div>
                <div id="cropper-container" class="crop-container bg-light">
                    <img id="cropper-image" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-confirmar" id="crop-button">Confirmar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cropper = null;
    let originalFile = null;
    
    window.removeMainImage = function() {
        const mainImageInput = document.getElementById('main_image');
        const container = document.getElementById('upload-container');
        mainImageInput.value = '';
        container.innerHTML = '<label for="main_image" class="custom-file-button">Procurar Capa</label>';
    };

    document.addEventListener('DOMContentLoaded', function () {
        // --- VARIÁVEIS GLOBAIS ---
        const mainImageInput = document.getElementById('main_image');
        const imagesInput = document.getElementById('imagens');
        const filesInput = document.getElementById('arquivos');
        const mainImagePreviewContainer = document.getElementById('upload-container');
        const imagesPreviewContainer = document.getElementById('imagens-preview-container');
        const filesPreviewContainer = document.getElementById('arquivos-preview-container');
        const imageViewerModal = document.getElementById('image-viewer-modal');
        const modalImageContent = document.getElementById('modal-image-content');
        const cropperModalElement = document.getElementById('cropper-modal');
        const cropperContainer = document.getElementById('cropper-container');
        const cropperImage = document.getElementById('cropper-image');
        const cropButton = document.getElementById('crop-button');

        const dataTransferImages = new DataTransfer();
        const dataTransferFiles = new DataTransfer();
        
        // --- MANIPULADORES DE EVENTOS DO CROPPER ---
        
        // 1. QUANDO O USUÁRIO SELECIONA A IMAGEM DE CAPA
        mainImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (!file) return;

            originalFile = file;
            const reader = new FileReader();
            
            reader.onload = function(e) {
                if (cropperContainer) {
                    cropperContainer.innerHTML = '<img id="cropper-image" src="' + e.target.result + '">';
                    const bsModal = new bootstrap.Modal(cropperModalElement);
                    bsModal.show();
                }
            };
            
            reader.readAsDataURL(file);
        });

        // 2. QUANDO O MODAL DE RECORTE É MOSTRADO
        cropperModalElement.addEventListener('shown.bs.modal', function () {
            const image = document.getElementById('cropper-image');
            if (!image) return;

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            cropper = new Cropper(image, {
                aspectRatio: 2700 / 660,
                viewMode: 2,
                dragMode: 'move',
                background: true,
                responsive: true,
                modal: true,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: false,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                wheelZoomRatio: 0.1,
                zoomable: true,
                zoomOnTouch: true,
                zoomOnWheel: true,
                autoCropArea: 0.85
            });
        });

        // 3. QUANDO O MODAL DE RECORTE É FECHADO
        cropperModalElement.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            const container = document.getElementById('cropper-container');
            if (container) {
                container.innerHTML = '';
            }

            // Restaura o scroll e limpa classes/estilos do modal
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            document.documentElement.style.overflow = '';
            
            // Remove qualquer backdrop que possa ter ficado
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
        });

        // 4. QUANDO O USUÁRIO CLICA NO BOTÃO "RECORTAR E SALVAR"
        cropButton.addEventListener('click', function() {
            if (!cropper) {
                console.error('Cropper not initialized');
                return;
            }

            try {
                const canvas = cropper.getCroppedCanvas({
                    width: 2700,
                    height: 660,
                    imageSmoothingQuality: 'high'
                });

                if (!canvas) {
                    throw new Error('Failed to create canvas');
                }

                canvas.toBlob(
                    (blob) => {
                        if (!blob || !originalFile) {
                            throw new Error('Failed to create blob or missing original file');
                        }

                        const newFile = new File([blob], originalFile.name, { type: originalFile.type });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(newFile);
                        
                        if (mainImageInput) {
                            mainImageInput.files = dataTransfer.files;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const container = document.getElementById('upload-container');
                            if (container) {
                                container.innerHTML = `
                                    <label for="main_image" class="custom-file-button">Procurar Capa</label>
                                    <div class="preview-item mt-3">
                                        <img src="${e.target.result}" alt="Preview">
                                        <button type="button" class="remove-preview-btn" onclick="removeMainImage()">&times;</button>
                                    </div>
                                `;
                            }

                            // Limpa o cropper
                            if (cropper) {
                                cropper.destroy();
                                cropper = null;
                            }

                            // Fecha o modal
                            const modal = bootstrap.Modal.getInstance(cropperModalElement);
                            if (modal) {
                                modal.hide();
                            }

                            // Restaura o scroll e limpa estilos
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                            document.documentElement.style.overflow = '';

                            // Remove backdrop
                            const backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) {
                                backdrop.remove();
                            }

                            // Força atualização do scroll
                            setTimeout(() => {
                                window.scrollTo(window.scrollX, window.scrollY);
                            }, 100);
                        };

                        reader.readAsDataURL(newFile);
                    },
                    originalFile.type,
                    0.8
                );
            } catch (error) {
                console.error('Error during crop:', error);
                
                // Cleanup em caso de erro
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                const modal = bootstrap.Modal.getInstance(cropperModalElement);
                if (modal) {
                    modal.hide();
                }

                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                document.documentElement.style.overflow = '';
                
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }
        });

        // --- MANIPULADORES PARA IMAGENS E ARQUIVOS ADICIONAIS ---

        imagesInput.addEventListener('change', function(event) {
            handleFileUpload(event.target.files, imagesPreviewContainer, dataTransferImages, imagesInput, true);
        });
        
        filesInput.addEventListener('change', function(event) {
            handleFileUpload(event.target.files, filesPreviewContainer, dataTransferFiles, filesInput, false);
        });

        // --- FUNÇÕES AUXILIARES ---
        
        function handleFileUpload(newFiles, previewContainer, dataTransfer, inputElement, isImage) {
            Array.from(newFiles).forEach(file => {
                dataTransfer.items.add(file);
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = createPreviewItem(e.target.result, file.name, inputElement, isImage);
                    previewContainer.appendChild(previewItem);
                };
                
                if(isImage) {
                    reader.readAsDataURL(file);
                } else {
                    const previewItem = createPreviewItem(null, file.name, inputElement, isImage);
                    previewContainer.appendChild(previewItem);
                }
            });
            inputElement.files = dataTransfer.files;
        }

        function createPreviewItem(src, fileName, inputElement, isImage) {
            const previewItem = document.createElement('div');
            previewItem.className = isImage ? 'preview-item' : 'file-preview-item';
            
            if (isImage) {
                const img = document.createElement('img');
                img.src = src;
                img.alt = 'Preview';
                img.onclick = () => {
                    modalImageContent.src = src;
                    imageViewerModal.style.display = 'flex';
                };
                previewItem.appendChild(img);
            } else {
                const icon = document.createElement('i');
                icon.className = 'fas fa-file-alt file-icon';
                const info = document.createElement('span');
                info.className = 'file-info';
                info.textContent = fileName;
                previewItem.appendChild(icon);
                previewItem.appendChild(info);
            }
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-preview-btn';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = (e) => {
                e.stopPropagation();
                removeFileFromFileList(fileName, inputElement);
                previewItem.remove();
            };

            previewItem.appendChild(removeBtn);
            return previewItem;
        }

        function removeFileFromFileList(fileName, inputElement) {
            const dt = (inputElement.id === 'imagens') ? dataTransferImages : dataTransferFiles;
            const newDt = new DataTransfer();
            Array.from(dt.files).forEach(file => {
                if (file.name !== fileName) {
                    newDt.items.add(file);
                }
            });
            dt.items.clear();
            Array.from(newDt.files).forEach(file => dt.items.add(file));
            inputElement.files = dt.files;
        }
    });
</script>
@endpush
@stop
