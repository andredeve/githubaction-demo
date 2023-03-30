<?php

/** @noinspection PhpUnused */

namespace App\Model;

use App\Interfaces\Arquivo;
use Core\Model\AppModel;
use Core\Util\Functions;
use Exception;
use JsonSerializable;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Controller\UsuarioController;
use DateTime;

/**
 * @Entity
 * @Table(name="modelo_documento")
 */
class ModeloDocumento extends AppModel implements Arquivo, JsonSerializable
{
    public function __construct() {
        $this->dataCadastro = new DateTime();
        $this->ativo = 0;
    }
    
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="boolean")
     */
    protected $ativo;
    
    /**
     * @Column(type="string",  unique=true, nullable=false)
     */
    private $nome;

    /**
     * @Column(type="text", nullable=true)
     */
    private $texto;

    /**
     * @Column(type="string",name="arquivo",length=80,nullable=true)
     */
    private $arquivo;

    /**
     * @Column(type="date",name="data_cadastro")
     */
    protected $dataCadastro;
    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    protected $ultimaAlteracao;

    /**
     * @ManyToOne(targetEntity="Usuario")
     */
    protected $usuarioCadastro;

    /**
     * @ManyToOne(targetEntity="Usuario")
     */
    protected $usuarioAlteracao;
    
    function getId(): ?int {
        return $this->id;
    }

    function getNome()
    {
        return $this->nome;
    }

    /**
     * @return mixed
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * @param mixed $texto
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setNome($nome)
    {
        $this->nome = $nome;
    }


    function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    function getVariaveis(): ?array
    {
        return (new TemplateProcessor($this->getArquivo(true)))->getVariables();

    }

    function getArquivo($fullpath = false, $fullpath_url = false): ?string
    {
        if ($fullpath) {
            return self::getPath() . $this->arquivo;
        }
        if ($fullpath_url) {
            return self::getPathUrl() . $this->arquivo;
        }
        return $this->arquivo;
    }

    static function getPath(): string
    {
        return APP_PATH . '_files/templates/';
    }

    static function getPathUrl(): string
    {
        return APP_URL . '_files/templates/';
    }

    function getExtensaoArquivo(): ?string
    {
        if (!empty($this->arquivo)) {
            $pathinfo = pathinfo($this->arquivo);
            return strtolower($pathinfo["extension"]);
        }
        return null;
    }

    public function getTamanhoArquivo()
    {
        return Functions::getTamanhoArquivo($this->getArquivo(true));
    }

    function getPreview(): ?string
    {
        return $this->arquivo != null ? $this->getArquivo(false, true) : null;
    }

    function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getDataCadastro($formatar = false)
    {
        return $this->getDataPadrao($this->dataCadastro, $formatar);
    }

    /**
     * @param mixed $dataCadastro
     */
    public function setDataCadastro($dataCadastro)
    {
        $this->dataCadastro = $dataCadastro;
    }

    public function getUltimaAlteracao($formatar = false)
    {
        return $this->getDataPadrao($this->ultimaAlteracao, $formatar);
    }

    public function setUltimaAlteracao($ultimaAlteracao)
    {
        $this->ultimaAlteracao = $ultimaAlteracao;
    }

    public function getUsuarioCadastro()
    {
        return $this->usuarioCadastro;
    }

    public function setUsuarioCadastro($usuarioCadastro)
    {
        $this->usuarioCadastro = $usuarioCadastro;
    }

    public function getUsuarioAlteracao()
    {
        return $this->usuarioAlteracao;
    }

    public function setUsuarioAlteracao($usuarioAlteracao)
    {
        $this->usuarioAlteracao = $usuarioAlteracao;
    }

    /**
     * @param $hora
     * @param $formatar
     * @return string|DateTime
     */
    protected function getHoraPadrao($hora, $formatar)
    {
        if ($formatar && $hora instanceof DateTime) {
            return $hora->format('H:i');
        }
        return $hora;
    }

    /**
     * @param $data
     * @param $formatar
     * @return DateTime|string
     */
    protected function getDataPadrao($data, $formatar)
    {
        if ($formatar && $data instanceof DateTime) {
            if ($data->format('H') > 0) {
                return $data->format('d/m/Y - H:i');
            }
            return $data->format('d/m/Y');
        }
        return $data;
    }

    /**
     * @throws Exception
     */
    protected function setHoraPadrao($hora): ?DateTime
    {
        if (empty($hora)) {
            return null;
        }
        if (is_string($hora)) {
            return new DateTime($hora);
        }
        return $hora;
    }

    /**
     * @throws Exception
     */
    protected function setDataPadrao($data): ?DateTime
    {
        if (empty($data)) {
            return null;
        }
        if (is_string($data)) {
            return new DateTime(Functions::converteDataParaMysql($data));
        }
        return $data;
    }

    public function getAtivo($formatar = false)
    {
        if ($formatar) {
            return $this->ativo ? 'Sim' : "Não";
        }
        return $this->ativo;
    }

    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    public function inserir($validarSomenteLeitura = true, bool $considerarPermissoes = true): ?int
    {
        $this->usuarioCadastro = UsuarioController::getUsuarioLogadoDoctrine();
        return parent::inserir();
    }


}
