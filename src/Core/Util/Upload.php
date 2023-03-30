<?php

namespace Core\Util;

use Core\Exception\TechnicalException;
use Exception;

/**
 * @author Anderson Brandão Batistoti <batistoti@gmail.com>
 * @version <1.0>
 * @package util
 */
class Upload
{

    private $file = array();
    # Diretório para aonde ser� enviado os arquivos
    public $dir;

    # Extensões permitidas
    public $extension = array();

    # Tamanho máximo do arquivo, em MB
    public $size;

    # Nome(s) do(s) arquivo(s) enviado(s), já com rename
    public $name = array();

    /**
     * Repassa os valores da variável FILES para o file.
     *
     * @param String $file nome do campo file
     */
    public function __construct($file, $dir, $extensions = array('pdf', 'png', 'gif', 'jpg', 'jpeg', 'ico', 'doc', 'docx', 'xls', 'xlsx'))
    {
        $this->file = $_FILES[$file];
        $this->dir = $dir;
        $this->extension = $extensions;
    }

    /**
     * Faz o upload de arquivos.
     *
     * @return string Nome do arquivo se sucesso ou erro se insucesso
     * @throws TechnicalException
     * @throws Exception
     */
    public function upload($rename = true, $newname = null)
    {
        if ($rename) {
            $this->rename($newname);
        }
        # Verifica se é array, ou seja, se � multiplos arquivos a serem enviados.
        if (is_array($this->file["name"])) {
            foreach ($this->file["error"] as $key => $error) {
                # Verifica se existe algum erro no envio, se n�o houver, ele faz o upload
                if ($error == UPLOAD_ERR_OK && !empty($this->file["name"][$key])) {
                    if ($this->checkExtension($this->file["name"][$key])) {
                        if (move_uploaded_file($this->file["tmp_name"][$key], $this->getDir() . $this->file["name"][$key])) {
                            # Armazena o nome do arquivo para a variável name, aonde depois poderá ser inserido no banco de dados
                            $this->name[] = $this->file["name"][$key];
                        } else {
                            $this->verificaErro($key);
                        }
                    } else {
                        throw new TechnicalException('Aviso: Extensão inválida!');
                    }
                }
            }
        } elseif (!empty($this->file["name"])) {
            if ($this->checkExtension($this->file["name"])) {
                if (move_uploaded_file($this->file["tmp_name"], $this->getDir() . $this->file["name"])) {
                    # Armazena o nome do arquivo para a variável name, aonde depois poderá ser inserido no banco de dados
                    $this->name = $this->file["name"];
                } else {
                    $this->verificaErro();
                }
            } else {
                throw new TechnicalException('Aviso: Extensão inválida!');
            }
        }
        return $this->name;
    }

    private function verificaErro($key = null)
    {
        $_UP = array();
        $_UP['erros'][0] = 'Diretório inválido.';
        $_UP['erros'][1] = 'O arquivo no upload e maior do que o limite do permitido.';
        $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML.';
        $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente.';
        $_UP['erros'][4] = 'Não foi feito o upload do arquivo.';
        if ($key !== null) {
            throw new Exception("ERRO:" . $_UP['erros'][$this->file['error'][$key]]);
        } else {
            throw new Exception("ERRO:" . $_UP['erros'][$this->file['error']]);
        }
    }

    /**
     * Verifica se a(s) extensão do(s) arquivo(s) enviado(s)
     * �(s�o) permitido(s)
     *
     * @return \Upload
     */
    public function checkExtension($file)
    {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $this->extension)) {
            return true;
        }
        return false;
    }

    /**
     * Verifica se o tamanho do arquivo é do
     * limite especificado
     *
     * @return \Upload
     */
    public function size()
    {
        $size = $this->convertMbToBt();

        if (is_array($this->file["size"])) {
            foreach ($this->file["size"] as $key => $sizes) {
                if ($sizes > $size) {
                    $this->file["name"][$key] = "";
                    $this->file["size"][$key] = "";
                }
            }
        } else {
            if ($this->file ["size"] > $size) {
                unset($this->file["size"]);
            }
        }

        return $this;
    }

    /**
     * Transaforma os MB para bits
     *
     * @return int
     */
    private function convertMbToBt()
    {
        $size = $this->getSize() * (1024 * 1024);
        return $size;
    }

    private function getExtensionFile($filename)
    {
        $exts = preg_split("[\.]", $filename);
        $n = count($exts) - 1;
        return $exts[$n];
    }

    /**
     * Renomeia o(s) nome(s) do(s) arquivo(s) enviado(s)
     * @return \Upload
     */
    protected function rename($new_name = null)
    {
        if (is_array($this->file["name"])) {
            foreach ($this->file["name"] as $key => $val) {
                if (!empty($this->file["name"][$key])) {
                    $exts = $this->getExtensionFile($this->file["name"][$key]);
                    $this->file["name"][$key] = date("YmdHisu") . "_" . uniqid() . "_" . date("usiHdmY") . "." . $exts;
                } else {
                    $this->file["name"][$key] = "";
                }
            }
        } else {
            $exts = $this->getExtensionFile($this->file["name"]);
            $this->file["name"] = $new_name == null ? date("YmdHisu") . uniqid() . date("usiHdmY") . "." . $exts : $new_name . "." . $exts;
        }

        return $this;
    }

    /**
     * Função para limpar string
     * @param type $string
     * @return type
     */
    private function remover_caracter($string)
    {
        $string = preg_replace("/[áàâãä]/", "a", $string);
        $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
        $string = preg_replace("/[éèê]/", "e", $string);
        $string = preg_replace("/[ÉÈÊ]/", "E", $string);
        $string = preg_replace("/[íì]/", "i", $string);
        $string = preg_replace("/[ÍÌ]/", "I", $string);
        $string = preg_replace("/[óòôõö]/", "o", $string);
        $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
        $string = preg_replace("/[úùü]/", "u", $string);
        $string = preg_replace("/[ÚÙÜ]/", "U", $string);
        $string = preg_replace("/ç/", "c", $string);
        $string = preg_replace("/Ç/", "C", $string);
        $string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
        $string = preg_replace("/ /", "_", $string);
        return $string;
    }

    public function getDir()
    {
        return $this->dir;
    }

    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

}
