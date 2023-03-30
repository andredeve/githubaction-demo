<div id="suporteModal" class="modal">
    <form id="formSuporte" class="form-horizontal " method="POST" enctype="multipart/form-data" action="{$app_url}src/Core/Ajax/enviar_email_suporte.php">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{$app['app_name']} - Suporte</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">Razão do contato:</label>
                        <select name="tipo" id="tipo_suporte" class="form-control" required="true">
                            <option value="">Selecione</option>
                            <option value="erro-bug">Erro/Bug</option>
                            <option value="duvida">Dúvida</option>
                            <option value="melhorias">Sugestão de Melhoria</option>
                            <option value="solicitacao">Solicitação</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Digite sua mensagem de uma forma bem clara: <span style="color: red">*</span></label>
                        <textarea id="descricao_suporte" name="descricao" class="form-control text-normal" rows="4" required="true"></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <div class="alert alert-warning alert-erro" style="display: none">
                                *Para agilizar o atendimento, pedimos que anexe o print da tela que está o ocorrendo o erro.
                            </div>
                            <label class="control-label">Anexos:</label>
                            <input type="file" name="anexos[]" multiple="true" class="form-control-file"/>
                            <br/>
                            <div class="progress invisible">
                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    <span id="porcentagem">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <div class="col col-xs-12 text-left">
                            <a target="_blank" href="mailto:suporte@rcmsuporte.com.br"><i class="fa fa-envelope-o"></i> suporte@rcmsuporte.com.br</a><br/>
                            <a target="_blank" href="tel:+556733270011"><i class="fa fa-phone"></i>(67) 3327-0011</a>
                        </div>
                        <div class="col col-xs-12 text-right">
                            <button type="button" class="btn btn-light border btn-sm" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i> Fechar</button>
                            <button type="button" class="btn btn-primary  btn-sm ladda-button" id="enviar_suporte" data-style="expand-right"><i class="fa fa-send-o"></i> Enviar</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>