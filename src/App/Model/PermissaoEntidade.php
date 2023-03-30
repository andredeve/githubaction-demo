<?php /** @noinspection PhpUnused */

namespace App\Model;

use Core\Model\AppModel;

/**
 * @Entity
 * @Table(name="permissao_entidade")
 */
class PermissaoEntidade extends AppModel
{

    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Grupo")
     * @JoinColumn(name="grupo_id", referencedColumnName="id",nullable=false,onDelete="CASCADE")
     */
    private $grupo;

    /**
     * @Column(type="string",name="codigo_entidade")
     */
    private $codigoEntidade;

    /**
     * @Column(type="boolean",name="inserir")
     */
    private $inserir;

    /**
     * @Column(type="boolean",name="editar")
     */
    private $editar;

    /**
     * @Column(type="boolean",name="excluir")
     */
    private $excluir;

    private static $entidades = array(
        array('codigo' => 1, 'descricao' => 'Protocolos', 'classe' => 'Processo'),
        array('codigo' => 2, 'descricao' => 'Assuntos', 'classe' => 'Assunto'),
        array('codigo' => 3, 'descricao' => 'Setores', 'classe' => 'Setor'),
        array('codigo' => 4, 'descricao' => 'Interessados', 'classe' => 'Interessado'),
        array('codigo' => 5, 'descricao' => 'Status', 'classe' => 'StatusProcesso'),
        array('codigo' => 6, 'descricao' => 'Anexos', 'classe' => 'Anexo'),
        array('codigo' => 7, 'descricao' => 'Tipos de Anexos', 'classe' => 'TipoAnexo'),
        array('codigo' => 8, 'descricao' => 'Fluxograma', 'classe' => 'Fluxograma'),
        array('codigo' => 9, 'descricao' => 'Local Arquivamento', 'classe' => 'Local'),
        array('codigo' => 10, 'descricao' => 'Tipo de Local de Arquivamento', 'classe' => 'TipoLocal'),
        array('codigo' => 11, 'descricao' => 'Subtipo de Local de Arquivamento', 'classe' => 'SubTipoLocal'),
        array('codigo' => 12, 'descricao' => 'Localização Física', 'classe' => 'LocalizacaoFisica'),
        array('codigo' => 13, 'descricao' => 'Nofiticações', 'classe' => 'Notificacao'),
    );

    public function __construct()
    {
        $this->inserir = false;
        $this->editar = false;
        $this->excluir = false;
    }

    function getId(): ?int
    {
        return $this->id;
    }

    function getGrupo()
    {
        return $this->grupo;
    }

    function getCodigoEntidade()
    {
        return $this->codigoEntidade;
    }

    function getInserir()
    {
        return $this->inserir;
    }

    function getEditar()
    {
        return $this->editar;
    }

    function getExcluir()
    {
        return $this->excluir;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    function setGrupo($grupo)
    {
        $this->grupo = $grupo;
    }

    function setCodigoEntidade($codigoEntidade)
    {
        $this->codigoEntidade = $codigoEntidade;
    }

    function setInserir($inserir)
    {
        $this->inserir = $inserir;
    }

    function setEditar($editar)
    {
        $this->editar = $editar;
    }

    function setExcluir($excluir)
    {
        $this->excluir = $excluir;
    }

    public static function getCodigo($classe)
    {
        foreach (self::$entidades as $entidade) {
            if ($entidade['classe'] == $classe) {
                return $entidade['codigo'];
            }
        }
        return null;
    }

    public static function getEntidade($codigo)
    {
        foreach (self::$entidades as $entidade) {
            if ($entidade['codigo'] == $codigo) {
                return $entidade['descricao'];
            }
        }
        return null;
    }

    public static function getClasse($codigo)
    {
        foreach (self::$entidades as $entidade) {
            if ($entidade['codigo'] == $codigo) {
                return $entidade['classe'];
            }
        }
        return null;
    }

    public static function getEntidades(): array
    {
        return self::$entidades;
    }

}
