<h6 class="card-title">Requisitos do processo para encaminhar para o setor nesta fase:</h6>
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#checklistTab{$setor_fase->getId()}" role="tab" aria-controls="checklistTab{$setor_fase->getId()}"><i class="fa fa-list-ol"></i> Perguntas <span id="qtde_perguntas_{$setor_fase->getId()}" class="badge badge-success">{count($setor_fase->getPerguntas())}</span></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tarefasTab{$setor_fase->getId()}" role="tab" aria-controls="tarefasTab{$setor_fase->getId()}"><i class="fa fa-list-ul"></i> Tarefas <span id="qtde_tarefas_{$setor_fase->getId()}" class="badge badge-success">{count($setor_fase->getTarefas())}</span></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#camposTab{$setor_fase->getId()}" role="tab" aria-controls="camposTab{$setor_fase->getId()}"><i class="fa fa-address-card-o"></i> Campos <span id="qtde_campos_{$setor_fase->getId()}" class="badge badge-success">{count($setor_fase->getCampos())}</span></a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active" id="checklistTab{$setor_fase->getId()}" role="tabpanel">
                <div>
                    <button entidade="Pergunta" objeto_ref_id="{$setor_fase->getId()}" type="button" class="btn btn-primary btn-sm btn-cadastrar-entidade"><i class="fa fa-plus"></i> Nova Pergunta</button>
                </div>
                <br/>
                {$perguntas=$setor_fase->getPerguntas()}
                <div id="listaPergunta_{$setor_fase->getId()}">
                    {include file="../../Pergunta/Templates/tabela.tpl"}
                </div>
            </div>
            <div class="tab-pane" id="tarefasTab{$setor_fase->getId()}" role="tabpanel">
                <div>
                    <button entidade="Tarefa" objeto_ref_id="{$setor_fase->getId()}" type="button" class="btn btn-primary btn-sm btn-cadastrar-entidade"><i class="fa fa-plus"></i> Nova Tarefa</button>
                </div>
                <br/>
                {$tarefas=$setor_fase->getTarefas()}
                <div id="listaTarefa_{$setor_fase->getId()}">
                    {include file="../../Tarefa/Templates/tabela.tpl"}
                </div>
            </div>
            <div class="tab-pane" id="camposTab{$setor_fase->getId()}" role="tabpanel">
                <div class="alert alert-warning" role="alert">
                    Crie um formulário eletrônico que será carregado nesta fase para este setor.
                </div>
                <div>
                    <button entidade="Campo" objeto_ref_id="{$setor_fase->getId()}" type="button" class="btn btn-primary btn-sm btn-cadastrar-entidade"><i class="fa fa-plus"></i> Novo Campo</button>
                </div>
                <br/>
                {$campos=$setor_fase->getCampos()}
                <div id="listaCampo_{$setor_fase->getId()}">
                    {include file="../../Campo/Templates/tabela.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>
