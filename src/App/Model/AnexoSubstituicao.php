<?php

namespace App\Model;

use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="anexo_substituicao", indexes={@Index(name="exercicio_index", columns={"exercicio"}), @Index(columns={"descricao"}, flags={"fulltext"})})
 */
class AnexoSubstituicao extends AppModel {

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Column(type="boolean",name="is_digitalizado")
     */
    public $isDigitalizado;

    /**
     * @Column(type="boolean",name="is_auto_numeric", options={"default" : 0})
     */
    public $isAutoNumeric;

    /**
     * @Column(type="string",name="numero",nullable=true)
     */
    public $numero;

    /**
     * @Column(type="string",name="exercicio",nullable=true)
     */
    public $exercicio;

    /**
     * @type TipoAnexo
     * @ManyToOne(targetEntity="TipoAnexo")
     * @JoinColumn(name="tipo_anexo_id", referencedColumnName="id",nullable=true)
     */
    public $tipo;

    /**
     * @type Classificacao
     * @ManyToOne(targetEntity="Classificacao")
     * @JoinColumn(name="classificacao_id", referencedColumnName="id",nullable=true,onDelete="RESTRICT")
     */
    public $classificacao;

    /**
     * @type DateTime
     * @Column(type="date", nullable=true)
     */
    public $dataValidade;

    /**
     * @Column(type="string",name="descricao",nullable=true)
     */
    public $descricao;

    /**
     * @type string
     * @Column(type="string",name="arquivo",length=255,nullable=true)
     */
    public $arquivo;

    /**
     * @type Processo
     * @ManyToOne(targetEntity="Processo", inversedBy="processo")
     * @JoinColumn(name="processo_id", referencedColumnName="id",nullable=true,onDelete="SET NULL")
     */
    private $processo;

    /**
     * @type Usuario
     * @ManyToOne(targetEntity="Usuario")
     * @JoinColumn(name="usuario_id", referencedColumnName="id",nullable=true)
     */
    public $usuario;

    /**
     * @Column(type="date",name="data",nullable=false)
     */
    public $data;

    /**
     * @Column(type="datetime",name="data_cadastro",nullable=false)
     */
    public $dataCadastro;

    /**
     * @Column(type="decimal",precision=10, scale=2,name="valor",nullable=true)
     */
    public $valor;

    /**
     * @Column(type="integer",name="qtde_paginas",nullable=true)
     */
    public $qtdePaginas;

    /**
     * @Column(type="boolean",name="is_circulacao_interna", options={"default" : 0})
     */
    public $isCirculacaoInterna;

    function __construct()
    {
        $this->dataCadastro = new DateTime();
        $this->isDigitalizado = false;
        $this->isAutoNumeric = false;
        $this->isCirculacaoInterna = false;
    }

    /**
     * @param $isAutoNumeric
     */
    public function setIsAutoNumeric($isAutoNumeric): void{
            $this->isAutoNumeric = $isAutoNumeric;
    }

    /**
     * @return boolean
     */
    public function getIsAutoNumeric(): bool
    {
        return $this->isAutoNumeric;
    }
    /**
     * @param $isCirculacaoInterna
     */
    public function setIsCirculacaoInterna($isCirculacaoInterna): void
    {
        $this->isCirculacaoInterna = $isCirculacaoInterna;
    }

    /**
     * @return boolean
     */
    public function getIsCirculacaoInterna(): bool
    {
        return $this->isCirculacaoInterna;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function setId(?int $id): void {
        $this->id = $id;
    }

    /**
     * @return boolean
     */
    public function getIsDigitalizado(): bool
    {
        return $this->isDigitalizado;
    }

    /**
     * @param boolean $isDigitalizado
     */
    public function setIsDigitalizado(bool $isDigitalizado): void
    {
        $this->isDigitalizado = $isDigitalizado;
    }

    /**
     * @return string
     */
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    /**
     * @param string $numero
     */
    public function setNumero(string $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return string
     */
    public function getExercicio(): string
    {
        return $this->exercicio;
    }

    /**
     * @param string $exercicio
     */
    public function setExercicio(string $exercicio): void
    {
        $this->exercicio = $exercicio;
    }

    /**
     * @return TipoAnexo
     */
    public function getTipo(): TipoAnexo
    {
        return $this->tipo;
    }

    /**
     * @param TipoAnexo $tipo
     */
    public function setTipo(TipoAnexo $tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return Classificacao|null
     */
    public function getClassificacao(): ?Classificacao
    {
        return $this->classificacao;
    }

    /**
     * @param Classificacao|null $classificacao
     */
    public function setClassificacao(?Classificacao $classificacao)
    {
        $this->classificacao = $classificacao;
    }

    /**
     * @return DateTime
     */
    public function getDataValidade(): ?DateTime
    {
        return $this->dataValidade;
    }

    /**
     * @param DateTime $dataValidade
     */
    public function setDataValidade(DateTime $dataValidade)
    {
        $this->dataValidade = $dataValidade;
    }

    /**
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * @param string|null $descricao
     */
    public function setDescricao(?string $descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return ?string
     */
    public function getArquivo(): ?string
    {
        return $this->arquivo;
    }

    /**
     * @param ?string $arquivo
     */
    public function setArquivo(?string $arquivo): void
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @return Processo
     */
    public function getProcesso(): Processo
    {
        return $this->processo;
    }

    /**
     * @param Processo $processo
     */
    public function setProcesso(Processo $processo): void
    {
        $this->processo = $processo;
    }

    /**
     * @return Usuario
     */
    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    /**
     * @param Usuario $usuario
     */
    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    function getData($formatar = false)
    {
        if (!empty($this->data) && $formatar) {
            return $this->data->format('d/m/Y');
        }
        return $this->data;
    }

    /**
     * @param DateTime $data
     */
    public function setData(DateTime $data): void
    {
        $this->data = $data;
    }

    /**
     * @return DateTime
     */
    public function getDataCadastro(): DateTime
    {
        return $this->dataCadastro;
    }

    /**
     * @param DateTime $dataCadastro
     */
    public function setDataCadastro(DateTime $dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @return float|string
     */
    function getValor($formatar = false)
    {
        if (!empty($this->valor) && $formatar)
        {
            return Functions::decimalToReal($this->valor);
        }
        return $this->valor;
    }

    /**
     * @param float $valor
     */
    public function setValor(float $valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return int|null
     */
    public function getQtdePaginas(): ?int
    {
        return $this->qtdePaginas;
    }

    /**
     * @param int|null $qtdePaginas
     */
    public function setQtdePaginas(?int $qtdePaginas)
    {
        $this->qtdePaginas = $qtdePaginas;
    }

    function getPath($fullPath = true)
    {
        if ($this->processo->getId() == null)
        {
            $path = Processo::getTempPath();
        }
        else
        {
            $assunto = null;
            if ($this->processo->getAssuntos()->count() > 0)
            {
                foreach ($this->processo->getAssuntos() as $assuntoFluxograma)
                {
                    $file = $this->processo->getAnexosPath($assuntoFluxograma->getAssunto()) . $this->getTipo()->getDescricao(true) . '/' . $this->getArquivo();

                    if (is_file($file))
                    {
                        $assunto = $assuntoFluxograma->getAssunto();
                        break;
                    }
                }
            }
            $tipo = $this->tipo != null ? $this->getTipo()->getDescricao(true) . '/' : "";
            $path = $this->processo->getAnexosPath($assunto) . $tipo;
        }

        if (!is_dir($path))
        {
            $oldmask = umask(0);
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
            umask($oldmask);
        }

        $search = $path.'/' . $this->getArquivo();
        if (!file_exists($search)) {
            $processoPath = FILE_PATH . 'processos/'.$this->processo->getPath();
            $arquivo = $this->getArquivo();

            $result = $this->searchFile($processoPath, $arquivo);
            if($result){
                $path = $result;
            }
        }
        if($fullPath){
            return $path;
        }else{
            return str_replace(FILE_PATH . 'processos/','',$path);
        }

    }

    private function searchFile($dir, $file)
    {
        $dir = rtrim($dir,"/");
        $search = $dir.'/'.$file;
        // Verifica se o arquivo existe no diretório
        if ((!is_dir($search)) && (file_exists($search))) {
            return $dir;
        } else {
            $scan = scandir($dir);
            //Percorre todos os arquivos e diretórios
            foreach ($scan as $path) {
                $newDir = "$dir/$path";
                if (is_dir($newDir) && $path != '.' && $path != '..') {
                    $result = $this->searchFile($newDir, $file);
                    if($result !== false){
                        return $result;
                    }
                }
            }
            return false; //Retorna falso caso não encontre
        }
    }

    function getArquivoUrl(): ?string
    {
        if ($this->arquivo != null)
        {
            return $this->getPathUrl() . $this->getArquivoTratado();
        }
        return null;
    }

    function getArquivoPath()
    {
        return $this->getPath() . $this->arquivo;
    }


    function convert(): AnexoSubstituicao {
        $anexo = new AnexoSubstituicao();
        $anexo->setTipo($this->tipo);
        $anexo->setExercicio($this->exercicio);
        $anexo->setProcesso($this->processo);
        $anexo->setQtdePaginas($this->qtdePaginas);
        $anexo->setValor($this->valor);
        $anexo->setNumero($this->numero);
        $anexo->setData($this->data);
        $anexo->setClassificacao($this->classificacao);
        $anexo->setIsAutoNumeric($this->isAutoNumeric);
        $anexo->setArquivo($this->arquivo);
        $anexo->setDataCadastro($this->dataCadastro);
        $anexo->setDataValidade($this->dataValidade);
        $anexo->setDescricao($this->descricao);
        $anexo->setIsCirculacaoInterna($this->isCirculacaoInterna);
        $anexo->setIsDigitalizado($this->isDigitalizado);
        $anexo->setUsuario($this->usuario);
        return $anexo;
    }

    function getPathUrl($fullPath = true)
    {
        if (is_file(Processo::getTempPath() . $this->arquivo) || empty($this->id)) {
            return Processo::getTempPathUrl();
        }
        $assunto = null;
        if ($this->processo->getAssuntos()->count() > 0) {
            foreach ($this->processo->getAssuntos() as $assuntoFluxograma) {
                $file = $this->processo->getAnexosPath($assuntoFluxograma->getAssunto()) . $this->getTipo()->getDescricao(true) . '/' . $this->getArquivo();
                if (is_file($file)) {
                    $assunto = $assuntoFluxograma->getAssunto();
                    break;
                }
            }
        }

        if ($fullPath) {
            if (date_format($this->getdataCadastro(),"Y") < $this->processo->getExercicio()){
                $path = str_replace('/' . $this->processo->getExercicio() . '/', '/' . date_format($this->getdataCadastro(),"Y") . '/', $this->processo->getAnexoUrl($assunto));
                return $path . $this->getTipo()->getDescricao(true) . '/';
            }
            return $this->processo->getAnexoUrl($assunto) . $this->getTipo()->getDescricao(true) . '/';
        } else {
            if (date_format($this->getdataCadastro(),"Y") < $this->processo->getExercicio()){
                $path = str_replace('/' . $this->processo->getExercicio() . '/', '/' . date_format($this->getdataCadastro(),"Y") . '/', $this->processo->getAnexoUrl());
                return $path . $this->getTipo()->getDescricao(true) . '/';
            }
            return $this->processo->getAnexoUrl() . $this->getTipo()->getDescricao(true) . '/';
        }

    }

    private function getArquivoTratado(): ?string
    {
        $nome_arquivo_sem_extensao = pathinfo($this->arquivo)['filename'];
        $arquivo = $this->getNomeArquivo($this->getPath(), $nome_arquivo_sem_extensao);
        if ($arquivo == null)
        {
            return $this->getNomeArquivo(self::getTempPath(), $nome_arquivo_sem_extensao);
        }
        else
        {
            return $arquivo;
        }
    }

    private function getNomeArquivo($path, $arquivo): ?string
    {
        if (is_file($path . $arquivo . ".pdf"))
        {
            return $arquivo . ".pdf";
        }
        elseif (is_file($path . $arquivo . '.PDF'))
        {
            return $arquivo . '.PDF';
        }
        return $this->arquivo;
    }

    private static function getTempPath(): ?string
    {
        return Processo::getTempPath();
    }

    function isImage()
    {
        $file = $this->getPath() . $this->arquivo;
        if (is_file($file)) {
            return is_array(getimagesize($file));
        }
        return false;
    }

    public function jsonSerialize(): ?array
    {
        return [
            "id" => $this->id,
            "is_digitalizado" => $this->isDigitalizado,
            "is_auto_numeric" => $this->isAutoNumeric,
            "numero" => $this->numero,
            "exercicio" => $this->exercicio,
            "tipo_anexo_id" => is_null($this->tipo) ? "" : $this->tipo->getId(),
            "classificacao_id" => is_null($this->classificacao) ? "" : $this->classificacao->getId(),
            "dataValidade" => Functions::formatarData($this->dataValidade),
            "descricao" => $this->descricao,
            "arquivo" => $this->arquivo,
            "processo_id" => is_null($this->processo) ? "" : $this->processo->getId(),
            "usuario_id" => is_null($this->usuario) ? "" : $this->usuario->getId(),
            "data" => Functions::formatarData($this->data),
            "data_cadastro" => Functions::formatarData($this->dataCadastro),
            "valor" => $this->valor,
            "qtde_paginas" => $this->qtdePaginas,
            "is_circulacao_interna" => $this->isCirculacaoInterna,
        ];
    }

    public function imprimir(): string
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}