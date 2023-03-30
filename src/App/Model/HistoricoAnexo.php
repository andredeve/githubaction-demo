<?php

namespace App\Model;

use App\Controller\UsuarioController;
use App\Enum\TipoLog;
use Core\Util\Functions;
use Exception;

/**
 * @Entity
 * @Table(name="historico-anexo")
 */
class HistoricoAnexo extends Log
{

    /**
     * @ManyToOne(targetEntity="Anexo")
     * @JoinColumn(name="anexo_id", referencedColumnName="id",nullable=true,onDelete="CASCADE")
     */
    private $anexo;

	/**
	 * @return Anexo
	 */
	public function getAnexo(): ?Anexo
    {
		return $this->anexo;
	}

	public function setAnexo(?Anexo $anexo)
	{
		$this->anexo = $anexo;
	}

	/**
	 * @param $tipo
	 * @param $mensagem
	 * @param Anexo|null $antigo
	 * @param Anexo|null $novo
	 * @param Usuario|null $usuario
	 * @throws Exception
	 */
    static function registrar($tipo, $mensagem, ?Anexo $antigo = null, ?Anexo $novo = null, ?Usuario $usuario = null)
    {
        $usuarioLog = $usuario == null ? (UsuarioController::getUsuarioLogadoDoctrine()) : $usuario;
        $registro = new HistoricoAnexo();
        $registro->setUsuario($usuarioLog);
        $registro->setNomeUsuario($usuarioLog != null ? $usuarioLog->getNome() : "Sistema Externo");
        $registro->setTipo($tipo);
        $registro->setAnexo($novo);
        $registro->setMensagem($mensagem);
        $registro->setTabela("anexo");
        if (!is_null($antigo)) {
            $registro->setAntigo($antigo->imprimir());
        }
        if (!is_null($novo)) {
            $registro->setNovo($novo->imprimir());
        }
        $registro->setIp(Functions::getUserIp());
        $registro->inserir();
    }

    /**
     * @throws Exception
     */
    static function registrarLogAnexoRemovido(Anexo $anexo, string $arquivo, string $motivo) {
        $mensagem = "Registro deletado. \nMotivo: $motivo \nArquivo: $arquivo.";
        HistoricoAnexo::registrar(TipoLog::ACTION_DELETE, $mensagem, $anexo, null, UsuarioController::getUsuarioLogadoDoctrine());
    }
}
