<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.delete-professor-externo {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    opacity: 0.7;
    transition: all 0.2s ease;
}

.delete-professor-externo:hover {
    opacity: 1;
    transform: scale(1.1);
}

.form-check.d-flex {
    margin-bottom: 8px;
    padding: 4px;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

.form-check.d-flex:hover {
    background-color: #f8f9fa;
}
</style>

<div class="modal fade" id="createBanca" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Cadastrar banca</h5>
                <button type="button" class="close btn btn-lg" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal_banca_success" class="alert alert-success rounded-3" style="display:none;"></div>
                <div id="modal_banca_errors" class="alert alert-danger rounded-3" style="display:none;"></div>
                
                <div id="form_create_banca_content">
                    <div class="form-group">
                    <label for="data">Data da banca</label>
                    <input type="date" name="data" id="data" class="form-control">
                    <label for="local">Local</label>
                    <input type="text" name="local" id="local" class="form-control" placeholder="Local da banca">

                    <div class="mb-3 row">
                        <label for="" class="form-label col-sm-2 col-form-label">Presidente:</label>
                        <div class="col-sm-9">
                            <input id="textPresidente" type="text" readonly class="form-control-plaintext" value="">
                        </div>
                        <span class="text-danger">O presidente da banca é o orientador selecionado anteriormente</span>
                    </div>

                    <div class="form-group" id="professores">
                        <label for="professores">Professores internos</label>
                        @foreach ($professores as $professor_interno)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="professores_internos[]" id="professor_{{$professor_interno->id}}" value="{{$professor_interno->id}}">
                            <label for="professor_{{$professor_interno->id}}" class="form-check-label text-wrap">{{$professor_interno->nome}} </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="form-group" id="professores_externos">
                        <label for="professores">Professores externos</label>

                        @foreach ($professores_externos as $professor_externo)
                        <div class="form-check d-flex align-items-center" id="professor_externo_container_{{$professor_externo->id}}">
                            <input type="checkbox" class="form-check-input me-2" name="professores_externos[]" id="professor_externo_{{$professor_externo->id}}" value="{{$professor_externo->id}}">
                            <label for="professor_externo_{{$professor_externo->id}}" class="form-check-label text-wrap flex-grow-1">{{$professor_externo->nome}} - {{$professor_externo->filiacao}}</label>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2 delete-professor-externo" 
                                    data-professor-id="{{$professor_externo->id}}" 
                                    data-professor-nome="{{$professor_externo->nome}}"
                                    title="Excluir professor externo">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <a href="" id="cadastrarProfessorExternoModal" class=" modal-trigger" data-bs-toggle="modal" data-bs-target="#createProfessorExterno" data-return-to-modal="#createBanca">Cadastrar professor externo</a>
                </div>
                </div>

            </div>
            <div class="modal-footer" id="buttons">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="buttonCancelBanca">Cancelar</button>
                <button type="button" class="btn custom-button" id="cadastrarBancaButton">
                    Cadastrar
                </button>
                <button type="button" class="btn custom-button" id="cadastrandoBancaButton" hidden disabled>
                    <span id="iconLoadingBanca" class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    Cadastrando
                </button>
            </div>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#professor_id').on('change', function() {
            var orientadorNome = $('#professor_id option:selected').text();
            var orientadorId = $('#professor_id option:selected').val();

            $('#textPresidente').val(orientadorNome);

            $('input[name="professores_internos[]"]').prop('checked', false);
            $('input[name="professores_internos[]"]').prop('disabled', false);

            // Marque o checkbox correspondente ao presidente selecionado
            $('#professor_' + orientadorId).prop('checked', true);
            $('#professor_' + orientadorId).prop('disabled', true);
        });

        $('#cadastrarBancaButton').click(function() {
        var buttonCadastrar = $('#cadastrarBancaButton');
        var buttonCancelar = $('#buttonCancelBanca');
        var buttonCadastrando = $('#cadastrandoBancaButton');
        var presidente = $('#professor_id option:selected').val();

        if (!presidente) {
            alert("Por favor, selecione um orientador");
            $('#createBanca').modal('hide');
            return;
        }

        loading();

        var data = $('#data').val();
        var local = $('#local').val();


        var professoresInternos = [];
        $('input[name="professores_internos[]"]:checked').each(function() {
            professoresInternos.push($(this).val());
        });

        var professoresExternos = [];
        $('input[name="professores_externos[]"]:checked').each(function() {
            professoresExternos.push($(this).val());
        });


        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        var data = {
            _token: csrfToken,
            data: data,
            local: local,
            professores_internos: professoresInternos,
            professores_externos: professoresExternos,
            presidente: presidente,
            contexto: 'modal'
        };

        $.ajax({
            type: 'POST',
            url: "{{ route('banca.store') }}",
            data: data,
            success: function(response) {
                loading();
                console.log(response);
                
                if (response.error) {
                    loaded();
                    $('#modal_banca_errors').html('<ul><li>' + response.error + '</li></ul>').show();
                    return;
                }
                
                // Esconde o formulário e os botões
                $('#form_create_banca_content').hide();
                $('#buttons').hide();
                
                // Mostra a mensagem de sucesso
                $('#modal_banca_success').html('<strong>Sucesso!</strong> Banca cadastrada com sucesso.').show();

                //Atualizar select de bancas
                $selectBanca = $('#banca_id');
                $selectBanca.empty();

                $.each(response.bancas, function(index, banca) {
                    var dataFormatada = new Date(banca.data);
                    var options = {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    };
                    var dataFormatadaTexto = dataFormatada.toLocaleDateString('pt-BR', options);

                    var professoresExternosTexto = "";
                    if (banca.professores_externos && banca.professores_externos.length > 0) {
                        var professoresArray = banca.professores_externos.map(function(professor) {
                            return professor.nome + " - " + professor.filiacao;
                        });

                        professoresExternosTexto = professoresArray.join(", ");
                    }

                    var professoresInternosTexto = "";
                    if (banca.professores && banca.professores.length > 0) {
                        var professoresInternosArray = banca.professores.map(function(professor) {
                            return professor.servidor.nome + " - IFNMG";
                        });

                        professoresInternosTexto = professoresInternosArray.join(", ");
                    }

                    var texto = dataFormatadaTexto + " - " + banca.local + " - MEMBROS: " + professoresExternosTexto + ", " + professoresInternosTexto;
                    $selectBanca.append($('<option>', {
                        value: banca.id,
                        text: texto
                    }));
                });
                
                // Espera 1.5 segundo e fecha o modal
                setTimeout(function() {
                    // Esconde a mensagem de sucesso
                    $('#modal_banca_success').hide();
                    
                    // Limpa os campos
                    $('#data').val('');
                    $('#local').val('');
                    $('input[name="professores_internos[]"]').prop('checked', false);
                    $('input[name="professores_externos[]"]').prop('checked', false);
                    $('#textPresidente').val('');
                    
                    
                    // Fecha o modal
                    $('#createBanca').modal('hide');
                }, 1500);
                
                loaded();
            },
            error: function(jqXHR) {
                var errorHtml = '<ul>';
                if (jqXHR.status === 422) {
                    var errors = jqXHR.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value + '</li>';
                    });
                } else {
                    var serverMessage = 'Por favor, tente novamente.';
                    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        serverMessage = jqXHR.responseJSON.message;
                    }
                    errorHtml += '<li>Ocorreu um erro inesperado. Detalhe: ' + serverMessage + '</li>';
                }
                errorHtml += '</ul>';
                $('#modal_banca_errors').html(errorHtml).show();
                loaded();
            }
        });

        function loading() {
            buttonCadastrar.prop('hidden', true);
            buttonCancelar.prop('disabled', true);
            buttonCadastrando.prop('hidden', false);
        }

        function loaded() {
            buttonCadastrar.prop('hidden', false);
            buttonCancelar.prop('disabled', false);
            buttonCadastrando.prop('hidden', true);
        }
    });
    
    $('#createBanca').on('hidden.bs.modal', function () {
        if (!$(this).data('returning-from-modal')) {
            $('#modal_banca_errors').hide();
            $('#modal_banca_success').hide();
            $('#data').val('');
            $('#local').val('');
            $('input[name="professores_internos[]"]').prop('checked', false);
            $('input[name="professores_externos[]"]').prop('checked', false);
            $('#textPresidente').val('');
            $('#form_create_banca_content').show();
            $('#buttons').show();
        } else {
            // Remove a flag após usar
            $(this).removeData('returning-from-modal');
        }
    });

    $('#createBanca').on('show.bs.modal', function () {
        if (event.relatedTarget && $(event.relatedTarget).data('return-to-modal')) {
            $(this).data('returning-from-modal', true);
        }
    });

    // Funcionalidade para excluir professor externo
    $(document).on('click', '.delete-professor-externo', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var professorId = $(this).data('professor-id');
        var professorNome = $(this).data('professor-nome');
        var containerElement = $('#professor_externo_container_' + professorId);
        var deleteButton = $(this);
        
        // Criar modal de confirmação similar ao do TCC
        var confirmModal = `
            <div class="modal fade" id="confirmDeleteProfessorModal" tabindex="-1" aria-labelledby="confirmDeleteProfessorModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="confirmDeleteProfessorModalLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Confirmar Exclusão
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                            </div>
                            <h6 class="mb-3">Tem certeza que deseja excluir este Professor Externo?</h6>
                            <p class="text-muted mb-0">
                                <strong>${professorNome}</strong>
                            </p>
                            <p class="text-muted small">
                                Esta ação não pode ser desfeita.
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-danger" id="confirmDeleteProfessorBtn">
                                <i class="fas fa-trash me-1"></i>
                                Sim, Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove modal anterior se existir
        $('#confirmDeleteProfessorModal').remove();
        
        // Adiciona o modal ao body
        $('body').append(confirmModal);
        
        // Mostra o modal
        var modalInstance = new bootstrap.Modal(document.getElementById('confirmDeleteProfessorModal'));
        modalInstance.show();
        
        // Handler para o botão de confirmação
        $('#confirmDeleteProfessorBtn').off('click').on('click', function() {
            // Fecha o modal de confirmação
            modalInstance.hide();
            
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            // Desabilita o botão durante a requisição
            deleteButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: '/professor-externo/' + professorId,
                type: 'DELETE',
                data: {
                    _token: csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        // Remove o elemento da interface
                        containerElement.fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Mostra mensagem de sucesso
                        setTimeout(function() {
                            if (typeof showSuccessMessage === 'function') {
                                showSuccessMessage('Professor externo excluído com sucesso!');
                            } else {
                                alert('Professor externo excluído com sucesso!');
                            }
                        }, 400);
                    } else {
                        alert('Erro ao excluir professor: ' + (response.message || 'Tente novamente.'));
                        // Reabilita o botão em caso de erro
                        deleteButton.prop('disabled', false).html('<i class="fas fa-times"></i>');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Erro ao excluir professor externo.';
                    if (xhr.status === 422) {
                        errorMessage = 'Não é possível excluir este professor pois ele está sendo usado em uma banca.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    alert(errorMessage);
                    
                    // Reabilita o botão em caso de erro
                    deleteButton.prop('disabled', false).html('<i class="fas fa-times"></i>');
                }
            });
        });
        
        // Remove o modal quando for fechado
        $('#confirmDeleteProfessorModal').on('hidden.bs.modal', function () {
            $(this).remove();
        });
    });
});
</script>
