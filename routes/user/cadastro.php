<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE CADASTRO
$obRouter->get('/cadastro', [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) { 
        return new Response(200, User\Cadastro::getNewAccount($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO DEPOIMENTO (POST) 
$obRouter->post('/cadastro' , [
    'middlewares' => [
        'required-user-logout'
    ],
    function ($request) {
        return new Response(200,User\Cadastro::setNewAccount($request));
    }
]);

//ROTA DE CADASTRO
$obRouter->get('/cadastroProfissional', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Cadastro::getNewProfissionalAccount($request));
    }
]);

//ROTA DE CADASTRO
$obRouter->post('/cadastroProfissional', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Cadastro::setNewProfissionalAccount($request));
    }
]);