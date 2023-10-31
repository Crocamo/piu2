<?php

namespace App\model\Entity;

use \App\Db\Database;

class ProfListUser{
    /**
     * ID da lista de empresas preferidas do usuário
     * @var interger
     */
    public $id;

    /**
     * ID do usuário
     * @var interger
     */
    public $idUser;

    /**
     * ID do comércio
     * @var interger
     */
    public $idProf;


    /**
     * data de inicio da filiação
     * @var DateTime
     */
    public $creationDate;

    /**
     * data de final da filiação
     * @var DateTime
     */
    public $finalDate;

    /**
     * Status do profissional
     * @var interger
     */
    public $status;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        $this->creationDate = date('Y-m-d H:i:s');
        //INSERE A INSTANCIA NO BANCO
        $this->id = (new Database('profissionallistuser'))->insert([
            'id'            => $this->id,
            'idUser'        => $this->idUser,
            'idProf'        => $this->idProf,
            'creationDate'  => $this->creationDate,
            'finalDate'     => $this->finalDate,
            'status'        => $this->status
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('profissionallistuser'))->update('id = '.$this->id,[
            'idUser'        => $this->idUser,
            'idProf'        => $this->idProf,
            'creationDate'  => $this->creationDate,
            'finalDate'     => $this->finalDate,
            'status'        => $this->status
        ]);
    }

    /**
     * Método responsável por excluir um profissional da lista de filiado ao comércio do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('profissionallistuser'))->delete('id = '.$this->id);
    }

    /**
     * Método responsável por retornar uma lista de profissionais filiado ao comércio com base no ID do comércio
     * @param interger $id
     * @return User
     */
    public static function getListProfbyUser($idUser){
        return self::getUsers('idUser ='.$idUser)->fetchObject(self::class);
    }

    /** ATENÇÃO INCREMENTAR && IDUSER
     * Método responsável por retornar comércios que o profissional é filiado
     * @param string $email
     * @return User
     */
    public static function getListProfbyId($idProf){
        return self::getUsers('idProf ="'.$idProf.'"')->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar listas de profissionais filiados a comércios
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('profissionallistuser'))->select($where,$order,$limit,$fields);
    }
}