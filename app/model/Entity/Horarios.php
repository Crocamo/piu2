<?php

namespace App\model\Entity;

use \App\Db\Database;

class Horarios{

    /**
     * ID do horario
     * @var interger
     */
    public $idHorarios;

    /**
     * horarios de Segunda feira
     * @var String
     */
    public $segunda;

    /**
     * horarios de Terça feira
     * @var String
     */
    public $terca;

    /**
     * horarios de Quarta feira
     * @var String
     */
    public $quarta;

    /**
     * horarios de Quinta feira
     * @var String
     */
    public $quinta;

    /**
     * horarios de Sexta feira
     * @var String
     */
    public $sexta;

    /**
     * horarios de sabado
     * @var String
     */
    public $sabado;

    /**
     * horarios de Domingo
     * @var String
     */
    public $domingo;

    /**
     * feriadoEstadual
     * @var String
     */
    public $feriadoEstadual;

    /**
     * feriadoNacional
     * @var String
     */
    public $feriadoNacional;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     * @return boolean
     */
    public function cadastrar(){
        //INSERE A INSTANCIA NO BANCO
        $this->idHorarios = (new Database('tbhorarios'))->insert([
            'segunda'   => $this->segunda,
            'terca'     => $this->terca,
            'quarta'    => $this->quarta,
            'quinta'    => $this->quinta,
            'sexta'     => $this->sexta,
            'sabado'    => $this->sabado,
            'domingo'   => $this->domingo,
            'feriadoEstadual'   => $this->feriadoEstadual,
            'feriadoNacional'  => $this->feriadoNacional
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar no banco de dados
     * @return boolean
     */
    public function atualizar(){
        return (new Database('tbhorarios'))->update('idHorarios = '.$this->idHorarios,[
            'segunda'   => $this->segunda,
            'terca'     => $this->terca,
            'quarta'    => $this->quarta,
            'quinta'    => $this->quinta,
            'sexta'     => $this->sexta,
            'sabado'    => $this->sabado,
            'domingo'   => $this->domingo,
            'feriadoEstadual'   => $this->feriadoEstadual,
            'feriadoNacional'  => $this->feriadoNacional
        ]);
    }

    /**
     * Método responsável por excluir uma instancia de horário do banco de dados
     * @return boolean
     */
    public function excluir(){
       return (new Database('tbhorarios'))->delete('idHorarios = '.$this->idHorarios);
    }

    /**
     * Método responsável por retornar uma instancia de horário com base em seu ID
     * @param interger $id
     * @return User
     */
    public static function getTBHorariosById($id){
        return self::getHorarios('idHorarios ='.$id)->fetchObject(self::class);
    }
 
    /**
     * Método responsavel por retornar Todas instancias de horário
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getHorarios($where = null, $order= null, $limit = null, $fields ='*'){
        return (new Database('tbhorarios'))->select($where,$order,$limit,$fields);
    }
}