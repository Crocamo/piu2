<?php 

namespace App\Controller\User;

use \App\Utils\View;


class Home extends Page{

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function getHome($request){
        //CONTEÚDO DA HOME
        $content = View::render('user/modules/home/index',[]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp',$content,'home');

    }
}