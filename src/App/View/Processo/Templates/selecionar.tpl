<div class="row">
    <div class="col-12">
        <form id="formPesquisaProcesso" >
            <input type="hidden" name="entidade_id" id="entidade_id">
            <input type="hidden" name="entidade_descricao" id="entidade_descricao"> 
            <p class="text-info lead">Selecione um processo da lista abaixo:</p>
                <table id="tabelaPesquisaSelecionarProcesso" class="table table-hover table-sm"
                       url="{$app_url}src/App/Ajax/Processo/listar_resumido.php?pesquisar=1" sorter="1"
                       cols_select="1,2,3,4,5"
                       cols_hidden="0,6,7,8,9,10"
                       cols_descricao="0,1"
                       string_implode="/"
                       style="width:100%;">
                    <thead class="bg-light">
                        <tr>
                            <th>Cód.</th>
                            <th col_name="numero_processo" style="width:5%;">Número</th>
                            <th col_name="exercicio" style="width:5%;">Exercício</th>
                            <th col_name="assunto_string" style="width:20%;">Assunto</th>
                            <th col_name="interessado_string" style="width:20%;">Interessado</th>
                            <th col_name="setor_atual_string" style="width:20%;">Setor Atual</th>
                            <th>Abertura</th>
                            <th>Vencimento</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <div class="clearfix"></div>
                        </tr>
                    </thead>
                    <tbody style="cursor:pointer;"></tbody>
                </table>
            
            <hr/>
            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> OK</button>
            <button type="button" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                        class="fa fa-times"></i> Cancelar
            </button>
        </form>
    </div>
</div>
