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
     * Endereço do usuário
     * @var String
     */
    public $endereco;

    /**
     * CEP do usuário
     * @var interger
     */
    public $cep;

    /**
     * Numero do endereço do usuário
     * @var interger
     */
    public $numero;

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
     * Documento do usuário
     * @var interger
     */
    public $tipoConta;


    /**
     * Contato do usuário
     * @var interger
     */
    public $sexo;

    /**
     * Login do usuário
     * @var String
     */
    public $login;

    /**
     * Senha do usuário
     * @var String
     */
    public $senha;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->id = (new Database('users'))->insert([
            'nome'      => $this->nome,
            'email'     => $this->email,
            'endereco'  => $this->endereco,
            'cep'       => $this->cep,
            'numero'    => $this->numero,
            'telefone'  => $this->telefone,
            'cpf'       => $this->cpf,
            'tipoConta' => $this->tipoConta,
            'sexo'      => $this->sexo,
            'login'     => $this->login,            
            'senha'     => $this->senha
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
            'endereco'  => $this->endereco,
            'cep'       => $this->cep,
            'numero'    => $this->numero,
            'telefone'  => $this->telefone,
            'cpf'       => $this->cpf,
            'tipoConta' => $this->tipoConta,
            'sexo'      => $this->sexo,
            'login'     => $this->login,            
            'senha'     => $this->senha
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