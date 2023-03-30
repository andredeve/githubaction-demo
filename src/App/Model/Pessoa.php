<?php /** @noinspection PhpUnused */

namespace App\Model;

use App\Enum\TipoPessoa;
use Core\Interfaces\EntityInterface;
use Core\Model\AppModel;
use Core\Util\Functions;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use phpDocumentor\Reflection\Types\Collection;

/**
 * @Entity
 * @HasLifecycleCallbacks
 * @Table(name="pessoa",uniqueConstraints={@UniqueConstraint(name="pessoa_unique", columns={"cpf","cnpj"})})
 */
class Pessoa extends AppModel implements EntityInterface
{
    /**
     * @Id
     * @Column(type="integer",name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Column(type="string",name="nome",nullable=false)
     */
    private $nome;
    /**
     * @Column(type="text",name="shadow_nome",nullable=true)
     */
    private $shadowNome;
    /**
     * @Column(type="string",name="cnpj",nullable=true, unique=true)
     */
    private $cnpj;

    /**
     * @Column(type="string",name="ie",nullable=true)
     */
    private $ie;
    /**
     * @Column(type="string",name="email",nullable=true)
     */
    private $email;

    /**
     * @Column(type="string",name="telefone",nullable=true)
     */
    private $telefone;

    /**
     * @Column(type="string",name="celular",nullable=true)
     */
    private $celular;
    /**
     * @Column(type="string",name="cpf",nullable=true, unique=true)
     */
    private $cpf;

    /**
     * @Column(type="string",name="rg", length=50,nullable=true)
     */
    private $rg;

    /**
     * @Column(type="string",name="nacionalidade",nullable=true)
     */
    private $nacionalidade;

    /**
     * @Column(type="date",name="data_nascimento",nullable=true)
     */
    private $dataNascimento;

    /**
     * @Column(type="string", name="sexo", columnDefinition="ENUM('m', 'f')")
     */
    private $sexo;
    /**
     * @Column(type="string", name="tipo_pessoa", columnDefinition="ENUM('fisica', 'juridica')")
     */
    private $tipoPessoa;
    /**
     * @Column(type="string", name="estado_civil", columnDefinition="ENUM('solteiro', 'casado','separado','divorciado','viuvo')")
     */
    private $estadoCivil;

    /**
     * @OneToOne(targetEntity="Endereco",cascade={"persist"})
     * @JoinColumn(name="endereco_id", referencedColumnName="id",nullable=true,onDelete="CASCADE")
     */
    private $endereco;

    /**
     * @OneToMany(targetEntity="Usuario", mappedBy="pessoa",cascade={"persist","remove"})
     * @var ArrayCollection<Usuario> $usuarios
     */
    private $usuarios;

    /**
     * @OneToMany(targetEntity="Interessado", mappedBy="pessoa",cascade={"persist","remove"})
     * @var ArrayCollection<Interessado> $interessados
     */
    private $interessados;

    /**
     * @Column(type="datetime",name="ultima_alteracao",nullable=true)
     */
    private $ultimaAlteracao;

    /**
     * @Column(type="date",name="data_cadastro")
     */
    private $dataCadastro;


    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
        $this->interessados = new ArrayCollection();
        $this->dataCadastro = new DateTime();
        $this->tipoPessoa = TipoPessoa::FISICA;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @param bool $formatar
     * @return DateTime|string|null
     */
    function getDataCadastro(bool $formatar = false)
    {
        if (!empty($this->dataCadastro) && $formatar) {
            return $this->dataCadastro->format('d/m/Y - H:i:s');
        }
        return $this->dataCadastro;
    }

    function setDataCadastro($dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /**
     * @param bool $formatar
     * @return DateTime|string|null
     */
    public function getUltimaAlteracao(bool $formatar = false)
    {
        if (!empty($this->ultimaAlteracao) && $formatar) {
            return $this->ultimaAlteracao->format('d/m/Y - H:i:s');
        }
        return $this->ultimaAlteracao;
    }

    public function setUltimaAlteracao($ultimaAlteracao): void
    {
        $this->ultimaAlteracao = $ultimaAlteracao;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome($nome): void
    {
        $this->nome = $nome;
    }

    public function getPrimeiroNome(): string
    {
        $nome = explode(" ", $this->nome);
        return ucfirst($nome[0]);
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj($cnpj): void
    {
        if (!empty($cnpj)){
            $this->cnpj = Functions::sanitizeNumber(
                filter_var($cnpj,FILTER_SANITIZE_NUMBER_INT)            
            );
        }
    }

    public function getIe(): ?string
    {
        return $this->ie;
    }

    public function setIe($ie): void
    {
        $this->ie = $ie;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone($telefone): void
    {
        $this->telefone = Functions::sanitizeNumber(
            filter_var($telefone,FILTER_SANITIZE_NUMBER_INT)
        );
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular($celular): void
    {
        $this->celular = Functions::sanitizeNumber(
            filter_var($celular,FILTER_SANITIZE_NUMBER_INT)
        );
    }

    public function getShadowNome(): ?string
    {
        return $this->shadowNome;
    }

    public function setShadowNome($shadowNome): void
    {
        $this->shadowNome = $shadowNome;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf($cpf): void
    {
        if (!empty($cpf)){
            $this->cpf = Functions::sanitizeNumber(
                filter_var($cpf,FILTER_SANITIZE_NUMBER_INT)
            );
        }
        else
        {
            $this->cpf = null;
        }
    }

    public function getRg(): ?string
    {
        return $this->rg;
    }

    public function setRg($rg): void
    {
        $this->rg = $rg;
    }

    public function getNacionalidade(): ?string
    {
        return $this->nacionalidade;
    }

    public function setNacionalidade($nacionalidade): void
    {
        $this->nacionalidade = $nacionalidade;
    }

    public function getDataNascimento(bool $formatar = false)
    {
        if (!empty($this->dataNascimento) && $formatar) {
            return $this->dataNascimento->format('d/m/Y');
        }
        return $this->dataNascimento;
    }

    public function setDataNascimento($dataNascimento): void
    {
        $this->dataNascimento = $dataNascimento;
    }

    public function getSexo()
    {
        return $this->sexo;
    }

    public function setSexo($sexo): void
    {
        $this->sexo = $sexo;
    }

    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil($estadoCivil): void
    {
        $this->estadoCivil = $estadoCivil;
    }

    public function getEndereco(): Endereco
    {
        return $this->endereco ?? new Endereco();
    }

    /**
     * @param mixed $endereco
     */
    public function setEndereco($endereco): void
    {
        $this->endereco = $endereco;
    }

    public function getTipo(): ?string
    {
        return $this->tipoPessoa;
    }

    public function setTipo($tipoPessoa): void
    {
        $this->tipoPessoa = $tipoPessoa;
    }

    /**
     * @return Usuario[]|ArrayCollection|null
     */
    public function getUsuarios(): ?ArrayCollection
    {
        return $this->usuarios;
    }

    /**
     * @return Interessado[]|ArrayCollection|null
     */
    public function getInteressados()
    {
        return $this->interessados;
    }

    public function buscarPorNome($nome)
    {
        return $this->getDAO()->buscarPorNome($nome);
    }

    public function buscaPorEmail($email)
    {
        return $this->getDAO()->buscaPorEmail($email);
    }

    public function __toString()
    {
        return strtoupper($this->getNome());
    }

    ################################################
    ################### PERSIST ####################

    /** @PrePersist */
    public function inserirShadowNome(): void
    {
        $this->shadowNome = Functions::sanitizeString($this->nome);
    }

    /** @PreUpdate */
    public function atualizarShadowNome(): void
    {
        $this->shadowNome = Functions::sanitizeString($this->nome);
    }

    public function validarCpfCnpj(): bool
    {
        if (!empty($this->getCpf())) {
            $cpf = Functions::sanitizeNumber($this->getCpf());
            return empty($this->buscarPorCampos(['cpf' => $cpf]));
        }

        if (!empty($this->getCnpj())) {
            $cnpj = Functions::sanitizeNumber($this->getCnpj());
            return empty($resultado = $this->buscarPorCampos(['cnpj' => $cnpj]));
        }

        return true;
    }
}
