<?php

namespace App\Model;

use Core\Exception\SecurityException;
use Core\Exception\TechnicalException;
use Core\Util\Functions;
use App\Controller\AnexoController;
use App\Controller\IndexController;
use App\Controller\TramiteController;
use App\Controller\UsuarioController;
use App\Enum\OrigemProcesso;
use App\Enum\SigiloProcesso;
use App\Enum\TipoLegado;
use App\Enum\TipoUsuario;
use App\Model\Dao\ProcessoDao;
use App\Util\CapaProcesso;
use Core\Controller\AppController;
use Core\Exception\BusinessException;
use Core\Model\AppModel;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use const APP_URL;
use const FILE_PATH;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="processo",uniqueConstraints={@UniqueConstraint(name="processo_unique",columns={"numero","exercicio","legado"})},indexes={@Index(columns={"objeto"},flags={"fulltext"}),@Index(name="exercicio_index", columns={"exercicio"}),@Index(name="numero_fase_index", columns={"numero_fase"}),@Index(name="is_arquivado_index", columns={"is_arquivado"})})
 * @property int $numero
 */
class Processo extends AppModel
{
    /**
     * @type int
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @type int
     * @Column(type="integer",name="codigo_nea",nullable=true)
     */
    private $codigoNea;

    /**
     * @type int
     * @Column(type="integer",name="numero",nullable=true)
     */
    private $numero;

    /**
     * @type int
     * @Column(type="integer",name="exercicio")
     */
    private $exercicio;

    /**
     * @type string
     * @Column(type="string", columnDefinition="ENUM('sistema','interna', 'externa','email','telefone','outros')")
     */
    private $origem;

    /**
     * @type DateTime
     * @Column(type="datetime",name="data_abertura",nullable=false)
     */
    private $dataAbertura;

    /**
     * @type string
     * @Column(type="string", columnDefinition="ENUM('sistema','fiorilli', 'fiorilli-rh','nea')")
     */
    private $legado;

    /**
     * @type DateTime
     * @Column(type="date",name="data_vencimento",nullable=false)
     */
    private $dataVencimento;

    /**
     * @type int
     * @Column(type="integer",name="numero_fase")
     */
    private $numeroFase;
    
    /**
     * @type bool
     * @Column(type="boolean",name="is_sigiloso",nullable=true)
     */
    private $isSigiloso;
     
    /**
     * @type string
     * @Column(type="string", name="sigilo",nullable=true, columnDefinition="ENUM('sem-restricao','sigiloso', 'anexos-sigilosos', 'privado')")
     */
    private $sigilo;

    /**
     * @type string
     * @Column(type="text",name="objeto",nullable=false)
     */
    private $objeto;

    /**
     * @type bool
     * @Column(type="boolean",name="is_arquivado",nullable=false)
     */
    private $isArquivado;

    /**
     * @type bool
     * @Column(type="boolean",name="is_externo",nullable=false, options={"default" : 0})
     */
    private $isExterno;

    /**
     * @type DateTime
     * @Column(type="datetime",name="data_arquivamento",nullable=true)
     */
    private $dataArquivamento;

    /**
     * @type string
     * @Column(type="text",name="justificativa_encerramento",nullable=true)
     */
    private $justificativaEncerramento;

    /**
     * @type LocalizacaoFisica
     * @ManyToOne(targetEntity="LocalizacaoFisica", cascade={"persist","remove"})
     * @JoinColumn(name="local_fisico_id", referencedColumnName="id",nullable=true)
     */
    private $localizacaoFisica;

    /**
     * @type Setor
     * @ManyToOne(targetEntity="Setor")
     * @JoinColumn(name="setor_origem_id", referencedColumnName="id",nullable=true)
     */
    private $setorOrigem;

    /**
     * @type Usuario
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_abertura_id", referencedColumnName="id",nullable=true)
     */
    private $usuarioAbertura;

    /**
     * @type Assunto
     * @ManyToOne(targetEntity="Assunto")
     * @JoinColumn(name="assunto_id", referencedColumnName="id",nullable=true)
     */
    private $assunto;

    /**
     * @type Interessado
     * @ManyToOne(targetEntity="Interessado")
     * @JoinColumn(name="interessado_id", referencedColumnName="id",nullable=true)
     */
    private $interessado;

    /**
     * @type Documento[]|Collection
     * @OneToMany(targetEntity="Documento", mappedBy="processo",cascade={"persist","remove"})
     * @OrderBy({"vencimento" = "ASC"})
     */
    private $documentos;

    /**
     * @type Tramite[]|Collection
     * @OneToMany(targetEntity="Tramite", mappedBy="processo",cascade={"persist","remove"})
     * @OrderBy({"dataEnvio" = "ASC"})
     */
    private $tramites;

    /**
     * @type Collection|Anexo[]
     * @OneToMany(targetEntity="Anexo", mappedBy="processo",cascade={"persist","remove"})
     * @OrderBy({"dataCadastro" = "ASC"})
     */
    private $anexos;
    
    private $componentes;
    
    /**
     * @type Collection|Assunto[]
     * Um processo pode ter vários processos apensados.
     * @OneToMany(targetEntity="AssuntoProcesso", mappedBy="processo",cascade={"persist","remove"})
     */
    private $assuntos;

    /**
     * @type Collection|Processo[]
     * Um processo pode ter vários processos apensados.
     * @OneToMany(targetEntity="Processo", mappedBy="apensado",cascade={"persist"})
     */
    private $apensos;

    /**
     * @type Processo
     * @ManyToOne(targetEntity="Processo", inversedBy="apensos", cascade={"persist"})
     * @JoinColumn(name="apensado_id", referencedColumnName="id",nullable=true)
     */
    private $apensado;

    /**
     * @type int
     * @Column(type="integer",name="numero_anexo",nullable=true)
     */
    private $numeroAnexo;

    /**
     * @type Collection|Usuario[]
     * @ManyToMany(targetEntity="Usuario")
     * @JoinTable(name="permissao_processo_sigiloso", joinColumns={@JoinColumn(name="processo_id",referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="usuario_id",referencedColumnName="id",unique= false)})
     */
    private $usuariosPermitidos;
    
    function __construct()
    {
        $this->tramites = new ArrayCollection();
        $this->anexos = new ArrayCollection();
        $this->apensos = new ArrayCollection();
        $this->documentos = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
        $this->componentes = new ArrayCollection();
        $this->legado = TipoLegado::SISTEMA;
        $this->isExterno = 0;
        $this->numeroAnexo = 0;
        $this->usuariosPermitidos = new ArrayCollection();
    }
    
    public function getDataVencimentoAtualizada($formatar = false){
        $ultimoAnexo = null;
        if($this->id){
            foreach($this->anexos as $anexo){
                if($anexo->getNovoVencimentoProcesso()){
                    if(!$ultimoAnexo || $anexo->getData() > $ultimoAnexo->getData()){
                        $ultimoAnexo = $anexo;
                    }
                }
            }
        }  
        $dataVencimento =  $ultimoAnexo? $ultimoAnexo->getNovoVencimentoProcesso():$this->dataVencimento;
        if (!empty($dataVencimento && $formatar)) {
            return $dataVencimento->format('d/m/Y');
        }
        return $dataVencimento;
    }

    /**
     * @return bool
     */
    public function interessadoPodeInteragir(): bool
    {
        if($this->getSetorAtual()->getIsExterno()){
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getTokenInterecao(): string
    {
        return md5($this->getId().'.'. $this->getSetorAtual()->getId(). '.'. $this->getInteressado()->getId());
    }
    public function getAnexosConsultaPublica(){
        $anexos = array();
        foreach($this->getAnexos() as $anexo){
            if($anexo->getIsCirculacaoInterna()){
                continue;
            }
            $anexos[] =$anexo;
        }
        return $anexos;
    } 
    /**
     * @return Componente[]|Collection
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws BusinessException
     * @throws \Doctrine\DBAL\Exception*@throws TechnicalException
     * @throws ORMException
     * @throws TechnicalException
     * @throws SecurityException
     */
    function getComponentes(bool $tramite = false, $paginado = false): ?Array
    {
        $this->componentes = new ArrayCollection();
        $qtdPaginas = (isset($_POST["qtdPaginas"]) && !empty($_POST["qtdPaginas"])) ? $_POST["qtdPaginas"] : 1;
        $config = AppController::getClienteConfig();
        /**
         * @var Componente $c
         */
        foreach($this->getComponentesOrdenadoPorData() as $c){
            $componente = new Componente();
            $componente->setProcesso($this);
            if ($c instanceof Anexo) {
                $componente->setQntdePaginas($c->getQtdePaginas());
                $componente->setAnexo($c);
                $assinatura = new Assinatura();
                $assinatura = $assinatura->buscarPorAnexo($c);
                if(!empty($assinatura)){
                    $app = AppController::getConfig();
                    $url = $app["lxsign_url"]."documento/versaoImpressa/".$assinatura->getLxsign_id();
                    $opts = array(
                        "ssl"=>array(
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        )
                    );
                    $context = stream_context_create($opts);
                    $filePdf = file_get_contents($url, false, $context);
                    if($filePdf && !str_contains($filePdf, "Documento cancelado ou excluído")){
                        $copyPath = "/tmp/anexo_{$c->getId()}.pdf";
                        file_put_contents($copyPath, $filePdf);
                        $pdf_path = $c->getArquivo(false, false, true, true);
                        copy($copyPath, $pdf_path);
                    } else {
                        $pdf_path = $c->getArquivo(false, false, true);
                    }
                } else {
                    $pdf_path = $c->getArquivo(false, false, true);
                }
                if ($paginado && file_exists($pdf_path)) {
                    //Não numera de novo, caso a opção seja de download dos arquivos
                    if(isset($_POST['download']) && !empty($_POST['download'])){
                        $this->componentes->add($componente);
                        continue;
                    }
                    if (Functions::isDocument($pdf_path)) {
                        if (pathinfo($pdf_path, PATHINFO_EXTENSION) === 'doc') {
                        // Não suportado
                            continue;
                        }
                        $newFilePdf = Functions::docToPdf($pdf_path);
                        $c->setArquivo(basename($newFilePdf));
                        $c->atualizar();
                    } else if (Functions::isImage($pdf_path)) {
                        $newFilePdf = Functions::imageToPdf($pdf_path);
                        $c->setArquivo(basename($newFilePdf));
                        $c->atualizar();
                    }
                    /*
                     * 1- Verificar se é paginado;
                     * 2- Caso paginado, verificar se a paginacao é correta;
                     * 3- Caso a paginação não seja correta, repaginar;
                     * 4- Caso não seja paginado, paginar.
                     */
                    if (empty($c->getPaginacao()) || file_exists($c->getArquivo(false, false, true, true))) {
                        try {
                            $pagInicial = $qtdPaginas + 1;
                            $qtdPaginas = $this->paginar($c, $qtdPaginas , $config);
                            $c->setPaginacao("{$pagInicial}-{$qtdPaginas}");
                            $c->atualizar(true, false);
                        } catch (Exception $e) {
                            Functions::escreverLogErro($e);
                        }
                    } else {
                        $numUltPag = $c->getNumeroUltimaPagina();
                        $totalPag = $qtdPaginas + Functions::getQntdePaginasPDF($c->getArquivo(false, false, true));
                        if ($numUltPag !== $totalPag) {
                            try {
                                $pagInicial = $qtdPaginas + 1;
                                $qtdPaginas = $this->paginar($c, $qtdPaginas, $config);
                                $c->setPaginacao("{$pagInicial}-{$qtdPaginas}");
                                $c->atualizar();
                            } catch (Exception $e) {
                                Functions::escreverLogErro($e);
                            }
                        } else {
                            $qtdPaginas = $totalPag;
                        }
                    }
                    $this->componentes->add($componente);
                }
            } else if ($tramite) {
                $componente->setTramite($c);
                if(isset($_POST['download']) && !empty($_POST['download'])){
                    $this->componentes->add($componente);
                    continue;
                }
                if ($c instanceof Tramite) {
                    $tramiteAux = $c;
                    $tramiteAux->gerarFormularioEletronico();
                    $arquivoComCaminhoCompleto = $this->getAnexosPath() . $tramiteAux->getNomeFormularioEletronico();
                    Functions::adicionarPaginacaoECarimbo($arquivoComCaminhoCompleto, IndexController::getClienteConfig(), $qtdPaginas);
                    $qtdPaginas += Functions::getQntdePaginasPDF($arquivoComCaminhoCompleto);
                }
                $this->componentes->add($componente);
            }
        }
        /*Seta uma variável POST para que os processos apensados
        continuem a partir da última folha do processo anterior
        */
        $_POST["qtdPaginas"] = $qtdPaginas;
        return $this->componentes->toArray();
    }

    function getComponentesDebug(bool $tramite = false, $paginado = false): ?Array
    {
        echo "<pre>";
        echo "============================ Debug de paginação - Início ============================" . PHP_EOL;
        $this->componentes = new ArrayCollection();
        $qtdPaginas = (isset($_POST["qtdPaginas"]) && !empty($_POST["qtdPaginas"])) ? $_POST["qtdPaginas"] : 1;
        $config = AppController::getClienteConfig();
        /**
         * @var Componente $c
         */
        foreach($this->getComponentesOrdenadoPorData() as $c){
            echo PHP_EOL;
            $componente = new Componente();
            $componente->setProcesso($this);
            echo "Iniciando análise de paginação." . PHP_EOL;
            if ($c instanceof Anexo) {
                echo "Componente contendo anexo: {$c->getId()}." . PHP_EOL;
                $componente->setQntdePaginas($c->getQtdePaginas());
                $componente->setAnexo($c);
                $assinatura = new Assinatura();
                $assinatura = $assinatura->buscarPorAnexo($c);
                if(!empty($assinatura)){
                    $app = AppController::getConfig();
                    $url = $app["lxsign_url"]."documento/versaoImpressa/".$assinatura->getLxsign_id();
                    $opts = array(
                        "ssl"=>array(
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        )
                    );
                    $context = stream_context_create($opts);
                    $filePdf = file_get_contents($url, false, $context);
                    if($filePdf && !str_contains($filePdf, "Documento cancelado ou excluído")){
                        $copyPath = "/tmp/anexo_{$c->getId()}.pdf";
                        file_put_contents($copyPath, $filePdf);
                        $pdf_path = $c->getArquivo(false, false, true, true);
                        copy($copyPath, $pdf_path);
                    } else {
                        $pdf_path = $c->getArquivo(false, false, true);
                    }
                } else {
                    $pdf_path = $c->getArquivo(false, false, true);
                }
                echo "Diretório do arquivo: $pdf_path." . PHP_EOL;
                if ($paginado && file_exists($pdf_path)) {
                    //Não numera de novo, caso a opção seja de download dos arquivos
                    if(isset($_POST['download']) && !empty($_POST['download'])){
                        $this->componentes->add($componente);
                        continue;
                    }
                    if (Functions::isDocument($pdf_path)) {
                        if (pathinfo($pdf_path, PATHINFO_EXTENSION) === 'doc') {
                            // Não suportado
                            continue;
                        }
                        $newFilePdf = Functions::docToPdf($pdf_path);
                        $c->setArquivo(basename($newFilePdf));
                        $c->atualizar();
                    } else if (Functions::isImage($pdf_path)) {
                        $newFilePdf = Functions::imageToPdf($pdf_path);
                        $c->setArquivo(basename($newFilePdf));
                        $c->atualizar();
                    }
                    /*
                     * 1- Verificar se é paginado;
                     * 2- Caso paginado, verificar se a paginacao é correta;
                     * 3- Caso a paginação não seja correta, repaginar;
                     * 4- Caso não seja paginado, paginar.
                     */
                    if (empty($c->getPaginacao()) || file_exists($c->getArquivo(false, false, true, true))) {
                        echo "Documento não paginado." . PHP_EOL;
                        try {
                            $pagInicial = $qtdPaginas + 1;
                            $qtdPaginas = $this->paginar($c, $qtdPaginas , $config);
                            echo "Arquivo paginado: $qtdPaginas no total." . PHP_EOL;
                            $c->setPaginacao("{$pagInicial}-{$qtdPaginas}");
                            $c->atualizar();
                        } catch (Exception $e) {
                            echo "A paginação falhou." . PHP_EOL;
                            Functions::escreverLogErro($e);
                        }
                    } else {
                        echo "Validando paginação atual... ";
                        $numUltPag = $c->getNumeroUltimaPagina();
                        $totalPag = $qtdPaginas + Functions::getQntdePaginasPDF($c->getArquivo(false, false, true));
                        if ($numUltPag !== $totalPag) {
                            echo "inválida." . PHP_EOL;
                            echo "Repaginando... ";
                            try {
                                $pagInicial = $qtdPaginas + 1;
                                $qtdPaginas = $this->paginar($c, $qtdPaginas, $config);
                                $c->setPaginacao("{$pagInicial}-{$qtdPaginas}");
                                $c->atualizar(true, false);
                                echo "ok." . PHP_EOL;
                            } catch (Exception $e) {
                                echo "falhou." . PHP_EOL;
                                echo $e->getMessage() . PHP_EOL;
                                echo $e->getTraceAsString() . PHP_EOL;
                                Functions::escreverLogErro($e);
                            }
                        } else {
                            echo "ok." . PHP_EOL;
                            $qtdPaginas = $totalPag;
                        }
                    }
                    echo "Concluído paginação de componente." . PHP_EOL;
                    $this->componentes->add($componente);
                } else {
                    echo "Arquivo inexistente." . PHP_EOL;
                }
            } else if ($tramite) {
                echo "Componente contendo trâmite: {$c->getId()}.";
                $componente->setTramite($c);
                if(isset($_POST['download']) && !empty($_POST['download'])){
                    $this->componentes->add($componente);
                    continue;
                }
                if ($c instanceof Tramite) {
                    $tramiteAux = $c;
                    $tramiteAux->gerarFormularioEletronico();
                    $arquivoComCaminhoCompleto = $this->getAnexosPath() . $tramiteAux->getNomeFormularioEletronico();
                    Functions::adicionarPaginacaoECarimbo($arquivoComCaminhoCompleto, IndexController::getClienteConfig(), $qtdPaginas);
                    $qtdPaginas += Functions::getQntdePaginasPDF($arquivoComCaminhoCompleto);
                }
                $this->componentes->add($componente);
            }
        }
        /*Seta uma variável POST para que os processos apensados
        continuem a partir da última folha do processo anterior
        */
        $_POST["qtdPaginas"] = $qtdPaginas;
        echo "============================ Debug de paginação - Fim ============================</pre>" . PHP_EOL;
        return $this->componentes->toArray();
    }

    function setComponentes($componentes) {
        $this->componentes = $componentes;
    }

    public function getCodigoNea()
    {
        return $this->codigoNea;
    }

    /**
     * @param $codigoNea
     */
    public function setCodigoNea($codigoNea)
    {
        $this->codigoNea = $codigoNea;
    }

    function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<Assunto>
     */
    public function getAssuntos(): ?Collection
    {
        return $this->assuntos;
    }

    /**
     * @param Assunto[]|Collection<Assunto> $assuntos
     */
    public function setAssuntos($assuntos)
    {
        if ($assuntos instanceof Collection) {
            $this->assuntos = $assuntos;
        } else {
            $this->assuntos = new ArrayCollection($assuntos);
        }
    }

    /**
     * @return Collection<Documento>
     */
    public function getDocumentos(): ?Collection
    {
        return $this->documentos;
    }

    /**
     * @param Documento[]|Collection $documentos
     */
    public function setDocumentos($documentos)
    {
        if ($documentos instanceof Collection) {
            $this->documentos = $documentos;
        } else {
            $this->documentos = new ArrayCollection($documentos);
        }
    }

    /**
     * @param bool $zeros_esquerda
     * @return int|string
     */
    function getNumero(bool $zeros_esquerda = false)
    {
        if ($zeros_esquerda) {
            return str_pad($this->numero, 8, "0", STR_PAD_LEFT);
        }
        return $this->numero;
    }

    function setNumeroAnexo($numeroAnexo){
        $this->numeroAnexo = $numeroAnexo;
    }

    function getNumeroAnexo(){
        return $this->numeroAnexo;
    }

    function getOrigem($formatar = false)
    {
        if ($formatar) {
            return OrigemProcesso::getDescricao($this->origem);
        }
        return $this->origem;
    }

    /**
     * @return mixed
     */
    public function getLegado()
    {
        return $this->legado;
    }

    /*
     * Deve retornar o trâmite "correto":
     * - Se o processo filho contém trâmite próprio, este deve ser retornado
     * - Caso não tenha tramitação própria, o pai deve ser considerado
     */
    private function getTramiteAConsiderarApensoEApensado(){

        if(!empty($this->apensado) && !empty($this->getTramiteAtualSemApenso())){
            return $this->getTramiteAtualSemApenso();
        }

        return $this->getTramiteAtual();
    }

    private function jaPassouPeloSetorUsuarioLogado(){
        $usuarioLogado = UsuarioController::getUsuarioLogadoDoctrine();
        foreach($this->getTramites() as $tramite){
            if($this->temAcessoAoSetor($tramite->getSetoresId(), $usuarioLogado->getSetoresIds())){
                return true;
            }
        }
        return false;
    }

    private function estaDesativadaRestricaoPorSetor(){
        return empty(AppController::processosSaoSigilosos());
    }

    private function estaRestricaoPorSetorDesativadaParaTipoDoUsuario(Usuario $usuarioLogado){
        $acessoForaDoSetor = array(
            TipoUsuario::INTERESSADO,
            TipoUsuario::ADMINISTRADOR
        );
        return in_array($usuarioLogado->getTipo(),$acessoForaDoSetor);
    }
    
    public function temRestricaoPorSetor(){

        if($this->estaDesativadaRestricaoPorSetor()){
            return false;
        }

        $usuarioLogado = UsuarioController::getUsuarioLogadoDoctrine();
        if($this->estaRestricaoPorSetorDesativadaParaTipoDoUsuario($usuarioLogado)){
            return false;
        }
        
        if($this->jaPassouPeloSetorUsuarioLogado()){
            return false;
        }

        return true;
    }

    public function contribuintePodeVerAnexos(){
        $usuario = UsuarioController::getUsuarioLogado();
        return $usuario->isInteressado() && $this->getUsuarioAbertura()->getId() == $usuario->getId() ;
    }

    public function usuarioTemPermissao(){
        $usuarioLogado = UsuarioController::getUsuarioLogadoDoctrine();
        if (is_null($usuarioLogado)) {
            return false;
        }
        if($usuarioLogado->getTipo() === TipoUsuario::MASTER){
            return true;
        }        
        $tramite = $this->getTramiteAConsiderarApensoEApensado();
        if(!empty($tramite->getUsuarioDestino()) && $usuarioLogado->getId() == $tramite->getUsuarioDestino()->getId()){
            return true;
        }
        if(!empty($tramite->getUsuarioRecebimento()) && $usuarioLogado->getId() == $tramite->getUsuarioRecebimento()->getId()){
            return true;
        }
        if ($this->usuariosPermitidos->exists(
            function ($key, $element) use ($usuarioLogado) {
                return $element->getId() === $usuarioLogado->getId();
            }
        )) {
            return true;
        }
        if($this->jaPassouPeloSetorUsuarioLogado()){
            return true;
        }
        if($this->contribuintePodeVerAnexos()){
            return true;
        }
        return false;
    }

    public function consultaPublicaTemAcessoAoProcesso(){
        $usuarioLogado = UsuarioController::getUsuarioLogado();
        if(empty($usuarioLogado) && $this->sigilo == SigiloProcesso::SEM_RESTRICAO){
            return true;
        }else if(empty($usuarioLogado) ){
            return false;
        }
    }

    public function usuarioTemPermissaoProcesso(){        
        if($this->sigilo != SigiloProcesso::SIGILOSO && AppController::processosSaoSigilosos() === false){
            return true;
        }
        if( $this->sigilo !== SigiloProcesso::SIGILOSO && !$this->temRestricaoPorSetor()){
            return true;
        }
        return $this->usuarioTemPermissao();
    }
    
    public function usuarioTemPermissaoAnexo(){

        //Verifica se os anexos possui algum sigilo
        if($this->sigilo != SigiloProcesso::ANEXOS_SIGILOSOS  && AppController::processosSaoSigilosos() === false){
            return true;
        } 

        return $this->usuarioTemPermissao();
        
    }

    public function temAcessoAoSetor($tramiteSetores, $usuarioSetores){
        foreach ($usuarioSetores as $usuarioSetor){
            if(in_array($usuarioSetor, $tramiteSetores, false)){
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param mixed $legado
     */
    public function setLegado($legado)
    {
        $this->legado = $legado;
    }

    function getDataAbertura($formatar = false)
    {
        if (!empty($this->dataAbertura) && $formatar) {
            return $this->dataAbertura->format('d/m/Y');
        }
        return $this->dataAbertura;
    }

    function getExercicio()
    {
        if (empty($this->exercicio) && !empty($this->dataAbertura)) {
            return $this->dataAbertura->format('Y');
        }
        return $this->exercicio;
    }

    function getIsSigiloso()
    {
        return $this->isSigiloso;
    }

    function getObjeto()
    {
        return $this->objeto;
    }

    function getIsArquivado()
    {
        return $this->isArquivado;
    }

    /**
     * @return bool
     */
    function getIsExterno()
    {
        return $this->isExterno;
    }

    function getDataArquivamento($formatar = false)
    {
        if (!empty($this->dataArquivamento) && $formatar) {
            return $this->dataArquivamento->format('d/m/Y');
        }
        return $this->dataArquivamento;
    }

    function getLocalizacaoFisica()
    {
        if ($this->localizacaoFisica == null) {
            return new LocalizacaoFisica();
        }
        return $this->localizacaoFisica;
    }

    public function bloquearTramiteParaUsuario(){
        $config = AppController::getConfig();
        
        if(isset($config['bloquear_tramite_para_usuario']) && !empty($config['bloquear_tramite_para_usuario'])){
            $excludentes = array(
                SigiloProcesso::ANEXOS_SIGILOSOS,
                SigiloProcesso::SIGILOSO
            );
            
            if(!in_array($this->sigilo, $excludentes)){
                return true;
            }
        }
        return false;
    }

    function setLocalizacaoFisica($localizacaoFisica)
    {
        $this->localizacaoFisica = $localizacaoFisica;
    }

    function getSetorOrigem()
    {
        if ($this->setorOrigem == null) {
            return new Setor();
        }
        return $this->setorOrigem;
    }

    function getUsuarioAbertura($string = false)
    {
        if ($string) {
            return $this->usuarioAbertura->getPessoa()->getNome();
        }
        return $this->usuarioAbertura;
    }

    function getAssunto($string = false)
    {
        if ($string) {
            $assuntos = array();
            foreach ($this->assuntos as $assuntoP) {
                $assuntos[] = $assuntoP->getAssunto()->getDescricao();
            }
            if (!empty($this->assunto))
                $assuntos[] = $this->assunto->getDescricao();
            return implode(" >> ", $assuntos);
        }
        if ($this->assunto == null) {
            return new Assunto();
        }
        return $this->assunto;
    }

    function getInteressado($string = false)
    {
        if ($string && !empty($this->interessado)) {
            return $this->interessado->getPessoa()->getNome();
        }
        if ($this->interessado == null) {
            return new Interessado();
        }
        return $this->interessado;
    }

    function getTramites($numero_fase = null, $desconsiderarApenso = false)
    {
        if (!$desconsiderarApenso && $this->tramites->count() == 0 && $this->apensado != null) {
            return $this->apensado->getTramites($numero_fase);
        }
        if ($numero_fase !== null) {
            $tramites = new ArrayCollection();
            foreach ($this->tramites as $tramite) {
                if ($tramite->getNumeroFase() == $numero_fase && $tramite->getAssunto() == $this->assunto && !$tramite->getIsCancelado()) {
                    $tramites->add($tramite);
                }
            }
            return $tramites->last();
        }
        return $this->tramites;
    }

    /**
     * @param $txt
     * @param $semApensos
     * @return Setor|array|string
     */
    function getSetorAtual($txt = true, $semApensos = false)
    {
        if($semApensos){
            
            if ($this->getApensado() != null) {
                return $this->getApensado()->getSetorAtual();
            }
        }
        $tramite_atual = $this->getTramiteAtual();
        if (empty($tramite_atual))
            $qtde = 0;
        else if(is_array($tramite_atual))
            $qtde = count($tramite_atual);
        else
            $qtde = 1;

        if ($qtde > 0) {
            if ($qtde > 1) {
                $setores = array();
                foreach ($tramite_atual as $tramite) {
                    $setores[] = $tramite->getSetorAtual();
                }
                return $txt ? implode( ", ",$setores) : $setores;
            }
            if (is_array($tramite_atual)) {
                return end($tramite_atual)->getSetorAtual();
            }
            if ($tramite_atual instanceof PersistentCollection) {
                return $tramite_atual->last()->getSetorAtual();
            }
            return $tramite_atual->getSetorAtual();
            //return is_array($tramite_atual) ? $tramite_atual[0]->getSetorAtual() : ($tramite_atual instanceof PersistentCollection ? $tramite_atual->first()->getSetorAtual() : $tramite_atual->getSetorAtual());
        }
        return $this->getSetorOrigem();

    }

    /**
     * @param $tramite
     * @return Tramite|null
     */
    function getTramiteAnterior($tramite = null)
    {
        if ($tramite != null) {
            $tramites = array();
            foreach ($this->tramites as $tramite_processo) {
                if ($tramite_processo->getId() < $tramite->getId()) {
                    $tramites[] = $tramite_processo;
                }
            }
        } else {
            $tramites = $this->tramites->toArray();
            array_pop($tramites);
        }
        return array_pop($tramites);

    }

    /**
     * @return false|Tramite
     */
    function getTramiteAtual(): ?Tramite
    {
        $result = $this->getTramites($this->getNumeroFase(true));
        if ($result) return $result;
        return null;
    }

    /**
     * @return null|false|Tramite
     */
    function getTramiteAtualSemApenso(){
        $desconsiderarApenso = true;
        return $this->getTramites(
            $this->getNumeroFase($desconsiderarApenso),
            $desconsiderarApenso
        );
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setNumero($numero)
    {
        $this->numero = $numero;
    }

    function setOrigem($origem)
    {
        $this->origem = $origem;
    }

    function setDataAbertura($dataAbertura)
    {
        $this->dataAbertura = $dataAbertura;
    }

    function setExercicio($exercicio)
    {
        $this->exercicio = $exercicio;
    }

    function setIsSigiloso($isSigiloso)
    {
        $this->isSigiloso = $isSigiloso;
    }

    function setObjeto($objeto)
    {
        $this->objeto = $objeto;
    }

    function setIsArquivado($isArquivado)
    {
        $this->isArquivado = $isArquivado;
    }

    /**
     * @param bool $isExterno
     */
    function setIsExterno($isExterno)
    {
        $this->isExterno = $isExterno;
    }

    function setDataArquivamento($dataArquivamento)
    {
        $this->dataArquivamento = $dataArquivamento;
    }

    /**
     * @param Setor $setorOrigem
     * @return void
     */
    function setSetorOrigem($setorOrigem)
    {
        $this->setorOrigem = $setorOrigem;
    }

    function setUsuarioAbertura($usuarioAbertura)
    {
        $this->usuarioAbertura = $usuarioAbertura;
    }

    function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    function setInteressado($interessado)
    {
        $this->interessado = $interessado;
    }

    function setTramites($tramites)
    {
        $this->tramites = $tramites;
    }

    function getJustificativaEncerramento()
    {
        return $this->justificativaEncerramento;
    }

    function setJustificativaEncerramento($justificativaEncerramento)
    {
        $this->justificativaEncerramento = $justificativaEncerramento;
    }

    /**
     * @throws Exception
     */
    function getDiasVencimento()
    {
        $timezone = new DateTimeZone('America/Campo_Grande');
        return (new DateTime(Date('Y-m-d'), $timezone))->diff($this->dataVencimento)->format('%r%a');

    }

    function getDataVencimento($formatar = false)
    {
        if (!empty($this->dataVencimento && $formatar)) {
            return $this->dataVencimento->format('d/m/Y');
        }
        return $this->dataVencimento;
    }

    function setDataVencimento($dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;
    }

    function getApensos()
    {
        return $this->apensos;
    }

    function getApensosIds()
    {
        $ids = array();
        foreach ($this->apensos as $apenso) {
            $ids[] = $apenso->getId();
        }
        return $ids;
    }

    /**
     * @return Processo
     */
    function getApensado()
    {
        return $this->apensado;
    }

    function setApensos($apensos)
    {
        $this->apensos = $apensos;
    }

    function setApensado($apensado)
    {
        $this->apensado = $apensado;
    }

    /**
     * @param boolean|false $necessitam_orc
     * @return Anexo[]|Collection
     */
    function getAnexos($necessitam_orc = false)
    {
        //OCR (Reconhecimento ótico de caracteres)
        if ($necessitam_orc) {
            $anexos = array();
            foreach ($this->anexos as $anexo) {
                if ($anexo->getIsDigitalizado() && $anexo->getTextoOCR() == "" || $anexo->getTextoOCR() == null) {
                    $anexos[] = $anexo;
                }
            }
            return $anexos;
        }
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if(!is_null($usuario) && $usuario->getTipo() === TipoUsuario::INTERESSADO){
            return (new Anexo())->listarPorCampos(array("processo"=>$this, "isCirculacaoInterna"=>0));
        }
        
        return $this->anexos;
    }

    /**
     * @param $id
     * @return Anexo
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    function getAnexo($id) {
        return (new Anexo())->buscar($id);
    }

    function setAnexos($anexos)
    {
        $this->anexos = $anexos;
    }

    /**
     * @param int $position
     * @param Anexo $anexo
     */
    function setAnexo($position, $anexo)
    {
        $this->anexos->set($position, $anexo);
    }

    function getResponsavel()
    {
        $tramites = $this->getTramiteAtual();
        if (is_array($tramites) || $tramites instanceof PersistentCollection) {
            $responsaveis = array();
            foreach ($tramites as $tramite) {
                $responsaveis[] = $tramite->getResponsavel()->getPessoa()->getNome();
            }
            return implode("<br/> -", $responsaveis);
        }
        return $tramites != null ? $tramites->getResponsavel() : null;
    }

    function getParecer()
    {
        $tramites = $this->getTramiteAtual();
        if (is_array($tramites) || $tramites instanceof PersistentCollection) {
            $parecer = "";
            foreach ($tramites as $tramite) {
                $parecer .= $tramite->getParecer();
            }
            return $parecer;
        }
        return $tramites != null ? $tramites->getParecer() : null;
    }

    function getPath($assunto = null)
    {
        if (!empty($this->numero) && !$this->getIsExterno()) {
            if (!is_int($this->numero)) {
                $this->numero = intval($this->numero);
            }
            $assunto = $assunto == null ? $this->getAssunto() : $assunto;
            return $this->getExercicio() . '/' . $assunto->getDescricao(true) . '/' . $this->numero . '/' . ($this->legado != TipoLegado::SISTEMA ? $this->legado . "/" : "");

            
        }else if($this->getIsExterno()){
            $assunto = $assunto == null ? $this->getAssunto() : $assunto;
            return $this->getExercicio() . '/externo/' . $this->getId() . '/'. $assunto->getDescricao(true) . '/' . ($this->legado != TipoLegado::SISTEMA ? $this->legado . "/" : "");
        }
        return 'temp/';
    }

    function getAnexoUrl($assunto = null)
    {
        return APP_URL . "_files/processos/" . $this->getPath($assunto);
    }

    static function getTempPath()
    {
        return FILE_PATH . 'processos/temp/';
    }

    static function getTempPathUrl()
    {
        return APP_URL . '_files/processos/temp/';
    }

    /**
     * Função que busca o diretório do processo
     * @return string
     */
    function getAnexosPath($assunto = null)
    {
        $path = FILE_PATH . 'processos/' . $this->getPath($assunto);
        if (!is_dir($path)) {
            $oldmask = umask(0);
            mkdir($path, 0766, true);
            umask($oldmask);
        }
        return $path;
    }

    /**
     * Função que buscar o maior numero no ano atual
     * @return int|null
     */
    function getMaiorNumero()
    {
        $result = $this->getDAO()->getMaiorNumero($this->exercicio);
        return $result != null ? (int)$result : null;
    }

    /**
     * @PostPersist
     */
    public function moverAnexos()
    {
        foreach ($this->anexos as $anexo) {
            $anexo->moverArquivos();
        }
    }

    /**
     * @PrePersist
     *
     */
    function gerarNumero($validarProcessoExterno = true)
    {
        $gerar = !($validarProcessoExterno) || !$this->isExterno;
        if(empty($this->exercicio)){
            $this->exercicio = Date('Y');
        }
        if (empty($this->numero) && $gerar) {
            $maior_numero = $this->getMaiorNumero();
            if ($maior_numero != null) {
                $this->numero = $maior_numero + 1;
            } else {
                $this->numero = 1;
            }
            $i = 1;
            foreach ($this->apensos as $apenso) {
                if ($apenso->getNumero() == null) {
                    $apenso->setNumero($this->numero + $i);
                    $i++;
                }
                $apenso->setExercicio($this->exercicio);
            }
        }
        if (empty($this->legado)) {
            $this->legado = TipoLegado::SISTEMA;
        }
    }

    /**
     * @return HistoricoProcesso[]|null
     * @throws ORMException
     */
    function getHistorico(): ?array
    {
        return (new HistoricoProcesso())->listarPorCampos(array("processo" => $this), array("id" => "DESC"));
    }

    function adicionarAssunto(AssuntoProcesso $assuntoProcesso)
    {
        if (!$this->assuntos->contains($assuntoProcesso)) {
            $this->assuntos->add($assuntoProcesso);
        }
        return $this;
    }

    function removerAssunto(AssuntoProcesso $assuntoProcesso)
    {
        if($this->assuntos->contains($assuntoProcesso)){
            $this->assuntos->removeElement($assuntoProcesso);
        }
    }

    function adicionaDocumento(Documento $documento)
    {
        if (!$this->documentos->contains($documento)) {
            $this->documentos->add($documento);
        }
        return $this;
    }

    function adicionaAnexo(Anexo $anexo)
    {
        if (!$this->anexos->contains($anexo)) {
            $this->anexos->add($anexo);
        }
        return $this;
    }

    function adicionaApenso(Processo $apenso)
    {
        if (!$this->apensos->contains($apenso)) {
            $this->apensos->add($apenso);
        }
        return $this;
    }

    function removeAnexo(Anexo $anexo)
    {
        $this->anexos->removeElement($anexo);
    }
    
    function removerApenso(Processo $apenso){
        $this->apensos->removeElement($apenso);
    }

    function adicionaTramite(Tramite $tramite)
    {
        if (!$this->tramites->contains($tramite)) {
            $this->tramites->add($tramite);
        }
        return $this;
    }

    function removeTramite(Tramite $tramite)
    {
        $this->tramites->removeElement($tramite);
    }

    function getNumeroFase($desconsiderarApenso = false)
    {
        if($desconsiderarApenso){
            return $this->numeroFase;
        }
        if (!is_null($this->apensado)) {
            return $this->apensado->getNumeroFase();
        }
        return $this->numeroFase;
    }

    function setNumeroFase($numeroFase)
    {
        $this->numeroFase = $numeroFase;
    }

    function getTramite($numero_fase = null)
    {
        $numero_verificar = $numero_fase == null ? $this->numeroFase : $numero_fase;
        $tramites = array();
        foreach ($this->tramites as $tramite) {
            if ($tramite->getNumeroFase() == $numero_verificar && $tramite->getAssunto() == $this->assunto) {
                $tramites[] = $tramite;
            }
        }
        return $tramites;
    }

    function listarEnviados()
    {
        return $this->getDAO()->listarEnviados();
    }

    function listarReceber()
    {
        return $this->getDAO()->listarReceber();
    }

    function listarEmAberto($commom_filter = true)
    {
        return $this->getDAO()->listarEmAberto($commom_filter);
    }

    function listarArquivados()
    {
        return $this->getDAO()->listarArquivados();
    }

    public
    function listarVencidos()
    {
        return $this->getDAO()->listarVencidos();
    }

    function listarGlobal($exercicio = null, $numero_processo = null, $origem = null, $status_id = null, $assunto_id = null, $interessado_id = null, $setor_origem_id = null, $setor_anterior_id = null, $setor_atual_id = null, $responsavel_abertura_id = null, $responsavel_atual_id = null, $data_abertura_ini = null, $data_abertura_fim = null, $data_arquivamento_ini = null, $data_arquivamento_fim = null, $data_tramite_ini = null, $data_tramite_fim = null, $texto = null, $tipo_texto = null, $ref_texto = null)
    {
        return $this->getDAO()->listarGlobal($exercicio, $numero_processo, $origem, $status_id, $assunto_id, $interessado_id, $setor_origem_id, $setor_atual_id, $responsavel_abertura_id, $data_abertura_ini, $data_abertura_fim, $data_arquivamento_ini, $data_arquivamento_fim, $data_tramite_ini, $data_tramite_fim, $texto, $tipo_texto, $ref_texto);
    }

    function getExercicios()
    {
        $exercicios = array();
        foreach ($this->getDAO()->getExercicios() as $i => $exercicio) {
            $exercicios[] = $exercicio['exercicio'];
        }
        return $exercicios;
    }

    function listarProcessosDisponiveis($busca, $pagina)
    {
        return $this->getDAO()->listarProcessosDisponiveis($busca, $pagina);
    }

    function listarQtdeProcessos($referencia, $responsavel_id, $assunto_id, $interessado_id)
    {
        return $this->getDAO()->listarQtdeProcessos($referencia, $responsavel_id, $assunto_id, $interessado_id);
    }

    public function buscarQuantidadePorMes($ano, $assunto_id)
    {
        return $this->getDAO()->buscarQuantidadePorMes($ano, $assunto_id);
    }

    public function getCapa($comCarimbo = true)
    {
        if($comCarimbo && file_exists( $this->getAnexosPath()."capa_{$this->numero}_{$this->exercicio}_carimbado.pdf")){
    
            return "capa_{$this->numero}_{$this->exercicio}_carimbado.pdf";
        }
                    
        return "capa_{$this->numero}_{$this->exercicio}.pdf";
    }

    public function gerarFormularioEletronicos()
    {
        foreach ($this->tramites as $tramite) {
            $tramite->gerarFormularioEletronico();
        }
    }

    public function gerarCapa()
    {
        $capa = new CapaProcesso($this);
        $capa->gerar('F', $this->getAnexosPath() . $this->getCapa());
        $cliente = \App\Controller\IndexController::getClienteConfig();
        if($cliente['adicionar_paginacao']){
            if (isset($_POST["qtdPaginas"]) && !empty($_POST["qtdPaginas"])){
                \Core\Util\Functions::adicionarPaginacaoECarimbo(
                    $this->getAnexosPath() . $this->getCapa(false),
                    $cliente,$_POST["qtdPaginas"]);
            } else {
                \Core\Util\Functions::adicionarPaginacaoECarimbo(
                    $this->getAnexosPath() . $this->getCapa(false),
                    $cliente);
            }
            $_POST['processo_id'] = $this->getId();
            $processoController = new \App\Controller\ProcessoController();
            $processoController->gerarArquivosParaVisualizacaoDigital();
        }
    }

    public function listarTramitesVencidos()
    {
        return $this->getDAO()->listarTramitesVencidos();
    }

    public function listarQtdeTramitesVencidos($referencia, $setorAtual = null, $assunto = null, $interessado = null, $responsavel = null, $vencimentoIni = null, $vencimentoFim = null)
    {
        return $this->getDAO()->listarQtdeTramitesVencidos($referencia, $setorAtual, $assunto, $interessado, $responsavel, $vencimentoIni, $vencimentoFim);
    }

    /**
     * @throws exception
     */
    public function gerarArquivoDigital()
    {
        include APP_PATH . 'lib/pdf-merger/PDFMerger.php';
        $pdf = new \PDFMerger();
        $pdf->addPDF($this->getAnexosPath() . "capa_{$this->getNumero()}_{$this->getExercicio()}.pdf");
        foreach ($this->getComponentes() as $componente) {
            if ($componente instanceof Anexo && pathinfo($componente->getArquivo())['extension'] == 'pdf') {
                $file = $componente->getArquivo(true);
            } else if ($componente instanceof Tramite) {
                $file = $componente->getNomeFormularioEletronico();
            }
            if (is_file($file)) {
                $pdf->addPDF($this->getAnexosPath() . $file, 'all');
            }
        }
        $pdf->merge('file', $this->getAnexosPath() . "Processo_{$this->numero}_{$this->exercicio}.pdf");
    }

    /**
     * @param int $dias
     * @return Processo[]
     * @throws Exception
     */
    static function listarVencimentoProximos($dias = 15)
    {
        $dataAtual = new DateTime(Date('Y-m-d'));
        $dataFim = new DateTime(Date('Y-m-d'));
        $dataFim->add(new \DateInterval('P'.$dias.'D'));
        return (new Processo())->getDAO()->listarVencimentoProximos($dataAtual, $dataFim);
    }

    /**
     * @throws Exception
     */
    function getArquivoDigital()
    {
        $file = "Processo_{$this->numero}_{$this->exercicio}.pdf";
        if (!is_file($this->getAnexosPath() . $file)) {
            $this->gerarArquivoDigital();
        }
        return "Processo_{$this->numero}_{$this->exercicio}.pdf";
    }

    static function getQtdeListagem($tipo)
    {
        /**
         * @var ProcessoDao $dao
         */
        $dao = (new Processo())->getDAO();
        $result = $dao->buscarQuantidade($tipo);
        return !empty($result) ? $result[0]['qtde'] : 0;
    }

    function listarSelect2($busca, $pagina, $disponiveis = false)
    {
        return $this->getDAO()->listarSelect2($busca, $pagina, $disponiveis);
    }
    
    /**
     * 
     * @return mixed|Tramite[]|Anexo[]
     */
    function getComponentesOrdenadoPorData(): Array {
        $componentes = array();
        
        foreach ($this->anexos as $anexo) {
            $anexo->timestamp = $anexo->getDataCadastro()->getTimestamp();
            $componentes[] = $anexo;
        }
        foreach ($this->tramites as $tramite) {
            if(!$tramite->gerarFormularioEletronico()){
                continue;
            }
            $tramite->timestamp = $tramite->getDataEnvio() != null ? $tramite->getDataEnvio()->getTimestamp() : null;
            $componentes[] = $tramite;
        }
        usort($componentes, function ($a, $b) {
            if ($a->timestamp === $b->timestamp && $a instanceof Anexo && $b instanceof Anexo) {
                return $a->getId() > $b->getId();
            }
            return $a->timestamp > $b->timestamp;
        });
        return $componentes;
    }

    function listarMovimentacao($dataIni, $dataFim, $setor = null, $assunto = null, $interessado = null)
    {
        return $this->getDAO()->listarMovimentacao($dataIni, $dataFim, $setor, $assunto, $interessado);
    }

    function listarTramites($dataIni, $dataFim, $setor = null, $assunto = null, $usuario = null)
    {
        return $this->getDAO()->listarTramites($dataIni, $dataFim, $setor, $assunto, $usuario);
    }

    public function listarQtdeAgrupada($agrupado, $dataInicio = null, $dataFim = null, $limite = 10)
    {
        return $this->getDAO()->listarQtdeAgrupada($agrupado, $dataInicio, $dataFim, $limite);
    }

    public function listarQtdeDeTramitesForaDoFluxo($dataInicio = null, $dataFim = null,  $assunto_id = null, $interessado_id = null){
        return $this->getDAO()->listarQtdeDeTramitesForaDoFluxo($dataInicio, $dataFim, $assunto_id, $interessado_id);
    }

    public function listarProcessosPorDataAbertura($dataInicio = null, $dataFim = null){
        return $this->getDAO()->listarProcessosPorDataAbertura($dataInicio, $dataFim);
    }
    
    public function __toString()
    {
        if (!empty($this->numero) && !empty($this->exercicio)) {
            return $this->numero . "/" . $this->exercicio;
        }
        return "-";
    }
  
    function getSigilo() {
        return $this->sigilo;
    }

    function setSigilo($sigilo) {
        $this->sigilo = $sigilo;
    }

    /**
     * @param int|null $processoId
     * @return int[]|null
     */
    public function buscarLxSignIdDosAnexos($processoId = null) {
        if (is_null($processoId)) {
            return $this->getDAO()->buscarLxSignIdDosAnexos($this->id);
        } else {
            return $this->getDAO()->buscarLxSignIdDosAnexos($processoId);
        }
    }

    /**
     * @return bool
     */
    public function usuarioPossuiPermissao($usuario) {
        return $this->usuariosPermitidos->contains($usuario);
    }

    /**
     * @return Collection
     */
    public function getUsuariosPermitidos()
    {
        return $this->usuariosPermitidos;
    }

    /**
     * @param Collection $usuariosPermitidos
     */
    public function setUsuariosPermitidos($usuariosPermitidos)
    {
        $this->usuariosPermitidos = $usuariosPermitidos;
    }

    /**
     * @param $usuario
     */
    public function addUsuarioPermitido($usuario)
    {
        $this->usuariosPermitidos->add($usuario);
    }

    /**
     * @return void
     */
    public function contribuinteMarcarComoRecebido(): void
    {
        $usuarioLogado = UsuarioController::getUsuarioLogadoDoctrine();
        $tramiteAtual = $this->getTramiteAtualSemApenso();
        if ($usuarioLogado && $tramiteAtual && !is_null($tramiteAtual->getSetorAtual()) && $usuarioLogado->isInteressado()){
            $tramiteAtualNoContribuinte = $tramiteAtual->getSetorAtual()->getId() == AppController::getParametosConfig()['processo_setor_contribuinte_id'];
            if(!empty($this->getInteressado()) && !empty($usuarioLogado->getPessoa()->getInteressados()[0]->getId())){
                if ($tramiteAtualNoContribuinte && $usuarioLogado->getPessoa()->getInteressados()[0]->getId() === $this->getInteressado()->getId() && !$tramiteAtual->getIsRecebido()) {
                    $_POST['tramite_id'] = $tramiteAtual->getId();
                    (new TramiteController())->receber();
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function contribuintePodeAbrirTramitacao(): bool
    {
        if($this->getAssunto()->getFluxograma() === null){
            return false;
        }
        $tramiteAtual = $this->getTramiteAtualSemApenso();
        $tramiteAtualNoContribuinte = $tramiteAtual->getSetorAtual()->getId() == AppController::getParametosConfig()['processo_setor_contribuinte_id'];
        $precisaTramitar = ($tramiteAtual->getNumeroFase() < count($this->getAssunto()->getFluxograma()->getFases()));
        return  $precisaTramitar && $tramiteAtualNoContribuinte && $tramiteAtual->getIsRecebido() && !$this->getIsArquivado();
    }

    private function paginar(Anexo $anexo, int $pagInicial, $config): int
    {
        $filename_assinado = $anexo->getArquivo(false, false, true, true);
        if (file_exists($filename_assinado)) {
            $filename = $filename_assinado;
        } else {
            $filename = $anexo->getArquivo(false, false, true);
        }
        Functions::adicionarPaginacaoECarimbo($filename, $config, $pagInicial, false, 'F');
        return $pagInicial + Functions::getQntdePaginasPDF($filename);
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "codigo_nea" => $this->codigoNea,
            "numero" => $this->numero,
            "exercicio" => $this->exercicio,
            "origem" => $this->origem,
            "data_abertura" => Functions::formatarData($this->dataAbertura),
            "legado" => $this->legado,
            "data_vencimento" => Functions::formatarData($this->dataVencimento),
            "numero_fase" => $this->numeroFase,
            "is_sigiloso" => $this->isSigiloso,
            "sigilo" => $this->sigilo,
            "objeto" => $this->objeto,
            "is_arquivado" => $this->isArquivado,
            "is_externo" => $this->isExterno,
            "data_arquivamento" => Functions::formatarData($this->dataArquivamento),
            "justificativa_encerramento" => $this->justificativaEncerramento,
            "local_fisico_id" => is_null($this->localizacaoFisica) ? "" : $this->localizacaoFisica->getId(),
            "setor_origem_id" => is_null($this->setorOrigem) ? "" : $this->setorOrigem->getId(),
            "usuario_abertura" => is_null($this->usuarioAbertura) ? "" : $this->usuarioAbertura->getId(),
            "assunto" => is_null($this->assunto) ? "" : $this->assunto->getId(),
            "interessado" => is_null($this->interessado) ? "" : $this->interessado->getId(),
            "documentos" => empty($this->documentos) ? "" : array_map(function($item) {
                return $item->getId();
            }, $this->documentos),
            "tramites" => empty($this->tramites) ? "" : array_map(function ($item) {
                return $item->getId();
            }, $this->tramites),
            "anexos" => empty($this->anexos) ? "" : array_map(function ($item) {
                return $item->getId();
            }, $this->anexos),
            "assuntos" => empty($this->assuntos) ? "" : array_map(function ($item) {
                return $item->getId();
            }, $this->assuntos),
            "apensos" => empty($this->apensos) ? "" : array_map(function ($item) {
                return $item->getId();
            }, $this->apensos),
            "apensado" => is_null($this->apensado) ? "" : $this->apensado->getId(),
            "numero_anexo" => $this->numeroAnexo,
            "usuariosPermitidos" => empty($this->usuariosPermitidos) ? "" : array_map(function ($item) {
                return $item->getId();
            }, $this->usuariosPermitidos),
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}