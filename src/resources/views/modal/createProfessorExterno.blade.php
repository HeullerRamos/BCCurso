<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="modal fade" id="createProfessorExterno" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Cadastrar professor externo</h5>
                <button type="button" class="close btn btn-lg" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" name="nome-professor-externo" id="nome-professor-externo" class="form-control" placeholder="Nome do professor externo">
                    <label for="filiacao-professor-externo">Filiação</label>
                    <input type="text" name="filiacao" id="filiacao-professor-externo" class="form-control" placeholder="Nome da instituição de filiação">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn custom-button" data-dismiss="modal" id="cadastrarProfessoExternoButton">Cadastrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var isProcessing = false; // Flag para evitar múltiplas requisições

        $('#cadastrarProfessoExternoButton').click(function() {
            // Previne múltiplos cliques
            if (isProcessing) {
                return;
            }

            var nome = $('#nome-professor-externo').val();
            var filiacao = $('#filiacao-professor-externo').val();

            if (nome.trim() === '' || filiacao.trim() === '') {
                alert('Por favor, preencha todos os campos.');
                return;
            }

            // Desabilita o botão e mostra loading
            isProcessing = true;
            var $button = $(this);
            var originalText = $button.text();
            $button.prop('disabled', true).text('Cadastrando...');

            var professoresSelecionadosAntes = [];
            $('input[name="professores_externos[]"]:checked').each(function() {
                professoresSelecionadosAntes.push($(this).val());
            });

            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            var data = {
                _token: csrfToken,
                nome: nome,
                filiacao: filiacao,
                contexto: 'modal'
            };

            $.ajax({
                type: 'POST',
                url: "{{ route('professor-externo.store') }}",
                data: data,
                success: function(response) {
                    // Restaura o botão
                    isProcessing = false;
                    $button.prop('disabled', false).text(originalText);

                    console.log('Resposta do servidor:', response);

                    if (response.error) {
                        alert(response.error);
                    } else {
                        // Feche o modal
                        $('#createProfessorExterno').modal('hide');

                        // Limpa o formulário para evitar duplicações
                        $('#nome-professor-externo').val('');
                        $('#filiacao-professor-externo').val('');

                        // Força o refresh do Select2 para professores externos se existir
                        if ($('#professores_externos').length) {
                            var professor = response.professor_externo;
                            
                            console.log('Professor retornado:', professor);
                            console.log('Nome:', professor.nome);
                            console.log('Filiacao:', professor.filiacao);
                            
                            // Verifica se a opção já existe no Select2
                            var optionExists = $('#professores_externos option[value="' + professor.id + '"]').length > 0;
                            
                            if (!optionExists) {
                                // Só adiciona se não existir
                                var textoOpcao = professor.nome + ' - ' + professor.filiacao;
                                console.log('Texto da opção:', textoOpcao);
                                
                                var novaOpcao = new Option(
                                    textoOpcao, 
                                    professor.id, 
                                    true, 
                                    true
                                );
                                $('#professores_externos').append(novaOpcao);
                            } else {
                                // Se já existe, apenas seleciona
                                $('#professores_externos').val(function(index, currentVal) {
                                    if (Array.isArray(currentVal)) {
                                        return currentVal.includes(professor.id.toString()) ? currentVal : [...currentVal, professor.id.toString()];
                                    } else {
                                        return [professor.id.toString()];
                                    }
                                });
                            }
                            
                            $('#professores_externos').trigger('change');
                        }

                        var returnToModalSelector = $('#cadastrarProfessorExternoModal').data('return-to-modal');
                        if (returnToModalSelector) {
                            $(returnToModalSelector).modal('show');  // Mostrar o modal de retorno
                        }
                    }
                },
                error: function(xhr, status, error) {
                    // Restaura o botão
                    isProcessing = false;
                    $button.prop('disabled', false).text(originalText);

                    if (xhr.status === 422) {
                        // Erros de validação
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = 'Erro de validação:\n';
                        for (var field in errors) {
                            errorMessage += '- ' + errors[field][0] + '\n';
                        }
                        alert(errorMessage);
                    } else {
                        alert('Erro ao cadastrar professor externo. Tente novamente.');
                    }
                }
            });
        });

        // Fix para restaurar o scroll da página quando modal for fechado
        $('#createProfessorExterno').on('hidden.bs.modal', function () {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $('body').css('padding-right', '');
            $('body').css('overflow', '');
        });
    });
</script>
