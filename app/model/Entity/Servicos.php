<?php

namespace App\model\Entity;

use \App\Db\Database;

class Servicos{
    /**
     * ID do Serviço
     * @var interger
     */
    public $idServ;

    /**
     * Nome do Serviço
     * @var String
     */
    public $nomeServ;

    /**
     * tempo médio do Serviço
     * @var Double
     */
    public $tempoMedioServ;

    /**
     * Valor médio do serviço
     * @var Double
     */
    public $valorServ;

    /**
     * ID do profissional
     * @var interger
     */
    public $idProfissional;

    /**
     * Status do serviço
     * @var tynuint
     */
    public $status;

    /**
     * data de criação do serviço
     * @var datetime
     */
    public $dataInicioServ;

    /**
     * data de suspensão do serviço
     * @var datetime
     */
    public $dataFimServ;


    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        $this->dataInicioServ = date('Y-m-d H:i:s');
        //INSERE A INSTANCIA NO BANCO
        $this->idServ = (new Database('servico'))->insert([
            'nomeServ'          => $this->nomeServ,
            'tempoMedioServ'    => $this->tempoMedioServ,
            'valorServ'         => $this->valorServ,
            'idProfissional'    => $this->idProfissional,
            'status'            => $this->status,
            'dataInicioServ'    => $this->dataInicioServ,
            'dataFimServ'       => $this->dataFimServ
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('servico'))->update('idServ = '.$this->idServ,[
            'nomeServ'          => $this->nomeServ,
            'tempoMedioServ'    => $this->tempoMedioServ,
            'valorServ'         => $this->valorServ,
            'idProfissional'    => $this->idProfissional,
            'status'            => $this->status,
            'dataInicioServ'    => $this->dataInicioServ,
            'dataFimServ'       => $this->dataFimServ
        ]);
    }

    /**
     * Método responsável por excluir um Serviços do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('servico'))->delete('idServ = '.$this->idServ);
    }

    /**
     * Método responsável por retornar um Serviços com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getServiceById($idServ){
        return self::getservices('idServ ='.$idServ)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um Serviços com base em seu Profissional
     * @param string $email
     * @return User
     */
    public static function getServiceByProfissional($idProfissional){
        return self::getservices('idProfissional ="'.$idProfissional.'"')->fetchObject(self::class);
    }

    /**
     * Método responsavel por retornar Serviços
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getservices($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('servico'))->select($where,$order,$limit,$fields);
    }
}