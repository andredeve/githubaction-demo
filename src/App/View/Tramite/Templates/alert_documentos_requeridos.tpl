{if $tramite->getDocumentosRequerimentosValidar() and  count( $tramite->getDocumentosRequerimentosValidar()) >0  }
        {if !empty($tramite->getRequirimentosSemObrigaroriedadeNaoCumpridos())}
            <div class="alert alert-warning">

                <strong>Os documento(s) foram solicitado(s) porem não é obrigatório:</strong>
                <ul>
                    {foreach $tramite->getRequirimentosSemObrigaroriedadeNaoCumpridos() as $documentoRequerido }
                        <li> 
                            <a href="javascript:" title="Clique aqui para completar o que foi requerido." anexo-id="{$documentoRequerido->getAnexo()->getId()}" processo-id="{$documentoRequerido->getAnexo()->getProcesso()->getId()}"  class="btn-completar-requerimento"> 
                                 {$documentoRequerido->getAnexo()}.
                            </a>

                        </li>
                    {/foreach}
                </ul>

            </div>
        {/if}
        {$requirimentosNaoCumpridos = $tramite->getRequirimentosObrigaroriosNaoCumpridos()}
        {if !empty($requirimentosNaoCumpridos) }
            <div class="alert alert-danger ">
                <strong>Atenção para continuar é necessário:</strong>
                <ul>
                    {foreach $requirimentosNaoCumpridos as $documentoRequerido }
                        {if $documentoRequerido->getIsObrigatorio() and !$documentoRequerido->getAnexo()->getArquivo()} 
                            <li> 
                                <strong>  Obrigatório inserir o arquivo no documento/anexo

                                </strong><a href="javascript:" title="Clique aqui para completar o que foi requerido." anexo-id="{$documentoRequerido->getAnexo()->getId()}" processo-id="{$documentoRequerido->getAnexo()->getProcesso()->getId()}"  class="btn-completar-requerimento"> {$documentoRequerido->getAnexo()}.</a>

                            </li>
                        {/if}
                        {if $documentoRequerido->getIsAssinaturaObrigatoria()}
                            <li>
                                <strong> Obrigatório assinar o doumento/anexo </strong><a href="javascript:" title="Clique aqui para completar o que foi requerido." anexo-id="{$documentoRequerido->getAnexo()->getId()}" processo-id="{$documentoRequerido->getAnexo()->getProcesso()->getId()}"  class="btn-completar-requerimento"> {$documentoRequerido->getAnexo()}.</a>
                            </li>  
                        {/if}
                    
                {/foreach}
                </ul>
                

            </div>
        {/if}
    
    
{/if}