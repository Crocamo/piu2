<?php

namespace App\model\Entity;

use \App\Db\Database;

class Comercio{

    /**
     * ID do comércio
     * @var interger
     */
    public $idEmpre;

    /**
     * endereço do comércio
     * @var String
     */
    public $enderecoEmpre;

    /**
     * cep do comércio
     * @var interger
     */
    public $cepEmpre;

    /**
     * Nome do comércio
     * @var String
     */
    public $nomeEmpre;

    /**
     * telefone do comércio
     * @var interger
     */
    public $telEmpre;

    /**
     * numero do comércio
     * @var interger
     */
    public $numEmpre;

    /**
     * Site do comércio
     * @var String
     */
    public $siteEmpre;

    /**
     * documento do comércio/responsável
     * @var String
     */
    public $cpfEmpre;

    /**
     * ID do responsável pelo comércio
     * @var String
     */
    public $idProfissional;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->idEmpre = (new Database('tbcomercio'))->insert([
            'enderecoEmpre' => $this->enderecoEmpre,
            'cepEmpre'      => $this->cepEmpre,
            'nomeEmpre'     => $this->nomeEmpre,
            'telEmpre'      => $this->telEmpre,
            'numEmpre'      => $this->numEmpre,
            'siteEmpre'     => $this->siteEmpre,
            'cpfEmpre'      => $this->cpfEmpre,
            'idProfissional'=> $this->idProfissional
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbcomercio'))->update('idEmpre = '.$this->idEmpre,[
            'enderecoEmpre' => $this->enderecoEmpre,
            'cepEmpre'      => $this->cepEmpre,
            'nomeEmpre'     => $this->nomeEmpre,
            'telEmpre'      => $this->telEmpre,
            'numEmpre'      => $this->numEmpre,
            'siteEmpre'     => $this->siteEmpre,
            'cpfEmpre'      => $this->cpfEmpre,
            'idProfissional'=> $this->idProfissional
        ]);
    }

    /**
     * Método responsável por excluir uma instancia de comércio do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbcomercio'))->delete('idEmpre = '.$this->idEmpre);
    }

    /**
     * Método responsável por retornar uma instancia de comércio com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getComercioById($idEmpre){
        return self::getComercios('idEmpre ='.$idEmpre)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instancia de comércio com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getComercioByName($nomeEmpre){
        return self::getComercios('nomeEmpre ='.$nomeEmpre)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instancia de comércio com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getComercioByIdProfissional($idProfissional){
        return self::getComercios('idProfissional ='.$idProfissional)->fetchObject(self::class);
    }
 
    /**
     * Método responsavel por retornar Todas instancias de comércio
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getComercios($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbcomercio'))->select($where,$order,$limit,$fields);
    }
}