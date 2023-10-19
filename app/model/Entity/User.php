<?php

namespace App\model\Entity;

use \App\Db\Database;

class User{

    /**
     * ID do usuário
     * @var interger
     */
    public $id;

    /**
     * Nome do usuário
     * @var String
     */
    public $nome;

    /**
     * Email do usuário
     * @var String
     */
    public $email;

    /**
     * Senha do usuário
     * @var String
     */
    public $senha;

    /**
     * Login do usuário
     * @var String
     */
    public $login;

    /**
     * Endereço do usuário
     * @var String
     */
    public $endereco;

    /**
     * Numero do endereço do usuário
     * @var interger
     */
    public $numero;

    /**
     * CEP do usuário
     * @var interger
     */
    public $cep;

    /**
     * Contato do usuário
     * @var interger
     */
    public $telefone;

    /**
     * Documento do usuário
     * @var interger
     */
    public $cpf;

    /**
     * Contato do usuário
     * @var interger
     */
    public $sexo;

    /**
     * Documento do usuário
     * @var interger
     */
    public $tipoConta;



    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->id = (new Database('users'))->insert([
            'nome'      => $this->nome,
            'email'     => $this->email,
            'senha'     => $this->senha,
            'login'     => $this->login,
            'endereco'  => $this->endereco,
            'numero'    => $this->numero,
            'cep'       => $this->cep,
            'telefone'  => $this->telefone,
            'cpf'       => $this->cpf,
            'sexo'      => $this->sexo,
            'tipoConta' => $this->tipoConta
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('users'))->update('id = '.$this->id,[
            'nome'      => $this->nome,
            'email'     => $this->email,
            'senha'     => $this->senha,
            'login'     => $this->login,
            'endereco'  => $this->endereco,
            'numero'    => $this->numero,
            'cep'       => $this->cep,
            'telefone'  => $this->telefone,
            'cpf'       => $this->cpf,
            'sexo'      => $this->sexo,
            'tipoConta' => $this->tipoConta
        ]);
    }

    /**
     * Método responsável por excluir um usuario do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('users'))->delete('id = '.$this->id);
    }

     /**
     * Método responsável por retornar um usuário com base em seu e-mail
     * @param interger $id
     * @return User
     */
    public static function getUserById($id){
        return self::getUsers('id ='.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um usuário com base em seu e-mail
     * @param string $email
     * @return User
     */
    public static function getUserByEmail($email){
        return self::getUsers('email ="'.$email.'"')->fetchObject(self::class);
    }

     /**
     * Método responsável por retornar um usuário com base em seu Login
     * @param string $login
     * @return User
     */
    public static function getUserByLogin($login){
        return self::getUsers('login ="'.$login.'"')->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar usuários
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('users'))->select($where,$order,$limit,$fields);
    }
}