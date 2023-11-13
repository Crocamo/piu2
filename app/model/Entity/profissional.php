<?php

namespace App\model\Entity;

use \App\Db\Database;

class Profissional{

    /**
     * ID do profissional
     * @var interger
     */
    public $idProfissional;

    /**
     * Id do Usuário
     * @var interger
     */
    public $idUser;

    /**
     * idHorarios
     * @var String
     */
    public $idHorarios;

    /**
     * Função do Profissional
     * @var String
     */
    public $funcaoProfissional;


    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->idProfissional = (new Database('tbprofissional'))->insert([
            'idUser'            => $this->idUser,
            'idHorarios'        => $this->idHorarios,
            'funcaoProfissional'=> $this->funcaoProfissional
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbprofissional'))->update('idProfissional = '.$this->idProfissional,[
            'idUser'            => $this->idUser,
            'idHorarios'        => $this->idHorarios,
            'funcaoProfissional'=> $this->funcaoProfissional
        ]);
    }

    /**
     * Método responsável por excluir um profissional do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbprofissional'))->delete('idProfissional = '.$this->idProfissional);
    }

     /**
     * Método responsável por retornar um profissional com base em seu ID de usuario
     * @param interger $id
     * @return User
     */
    public static function getUserPById($id){
        return self::getUsers('idUser ='.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um profissional com base em seu ID Profissional
     * @param interger $id
     * @return User
     */
    public static function getProfissionalById($id){
        return self::getUsers('idProfissional ='.$id)->fetchObject(self::class);
    }
 
    /**
     * Método responsavel por retornar profissionais
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbprofissional'))->select($where,$order,$limit,$fields);
    }
}