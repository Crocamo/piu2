<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\User;
use \App\Session\User\Login as SessionUserLogin;
use \App\model\Entity\profissional;


class Login extends Page
{

    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request, $errorMessage = null)
    {
        //STATUS
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/login', [
            'status' => $status
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Login > Univesp', $content);
    }

    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     */
    public static function setLogin($request)
    {
        //POST VARS
        $postVars   = $request->getPostVars();
        $login      = $postVars['login'] ?? '';
        $senha      = $postVars['senha'] ?? '';

        //BUSCA O USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($login);
        if (!$obUser instanceof User) {
            //VALIDA O LOGIN DO USUÁRIO
            $obUser =  User::getUserByLogin($login);
            if (!$obUser instanceof User) {
                //REDIRECIONA O USUÁRIO
                return self::getLogin($request, 'Usuário invalido');
            }
        }

        // VERIFICA A SENHA DO USUÁRIO
        if (!password_verify($senha, $obUser->senha)) {
            return self::getLogin($request, 'Senha invalida');
        }

        //CRIA A SESSÃO DE LOGIN
        SessionUserLogin::login($obUser);

        //TIPO DE CONTA (0 = USUARIO, 1=PROFISSIONAL)
        if ($obUser->tipoConta != 0) {
            //BUSCA DADOS DE PROFISSINAL
            $obProfi = Profissional::getUserPById($obUser->id);
            //VALIDA SE USUARIO É PROFISSIONAL CADASTRADO
            if (!$obProfi instanceof Profissional) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/cadastroProfissional');
            }
            //ATENÇÃO CRIAR REDIRECIONAMENTO COM MENU PROFISSIONAL
        }
            //REDIRECIONA O USUÁRIO PARA A HOME
            $request->getRouter()->redirect('/user');
    }

    /**
     * Método responsável por deslogar o usuário
     * @param Request $request
     */
    public static function setLogout($request)
    {

        //DESTROI A SESSÃO DE LOGIN
        SessionUserLogin::logout();

        //REDIRECIONA O USUÁRIO PARA A TELA DE LOGIN
        $request->getRouter()->redirect('/');
    }
}
