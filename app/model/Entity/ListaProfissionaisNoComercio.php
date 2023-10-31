<?php

namespace App\model\Entity;

use \App\Db\Database;

class ListaProfissionaisNoComercio{
    /**
     * ID da lista de profissionais cadastrados no comércio
     * @var interger
     */
    public $idLista;

    /**
     * ID do comércio
     * @var interger
     */
    public $idEmpresa;

    /**
     * ID do profissional filiado
     * @var interger
     */
    public $idProfissional;

    /**
     * data de inicio da filiação
     * @var DateTime
     */
    public $dataInicio;

    /**
     * data de final da filiação
     * @var DateTime
     */
    public $dataFim;

    /**
     * Status do profissional
     * @var interger
     */
    public $status;

    /**
     * função exercida do profissional
     * @var String
     */
    public $funcao;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->idLista = (new Database('empresalistaprofissionais'))->insert([
            'idEmpresa'     => $this->idEmpresa,
            'idProfissional'=> $this->idProfissional,
            'dataInicio'    => $this->dataInicio,
            'dataFim'       => $this->dataFim,
            'status'        => $this->status,
            'funcao'        => $this->funcao
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('empresalistaprofissionais'))->update('idLista = '.$this->idLista,[
            'idEmpresa'     => $this->idEmpresa,
            'idProfissional'=> $this->idProfissional,
            'dataInicio'    => $this->dataInicio,
            'dataFim'       => $this->dataFim,
            'status'        => $this->status,
            'funcao'        => $this->funcao
        ]);
    }

    /**
     * Método responsável por excluir um profissional da lista de filiado ao comércio do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('empresalistaprofissionais'))->delete('idLista = '.$this->idLista);
    }

    /**
     * Método responsável por retornar uma lista de profissionais filiado ao comércio com base no ID do comércio
     * @param interger $id
     * @return User
     */
    public static function getListProfissionalByEmprise($idEmpresa){
        return self::getListProf('idEmpresa ='.$idEmpresa)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar comércios que o profissional é filiado
     * @param string $email
     * @return User
     */
    public static function getListEmprisebyProfissional($idProfissional){
        return self::getListProf('idProfissional ="'.$idProfissional.'"')->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar listas de profissionais filiados a comércios
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getListProf($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('empresalistaprofissionais'))->select($where,$order,$limit,$fields);
    }
}