<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE CADASTRO
$obRouter->get('/user/perfil', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Perfil::getPerfil($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO DEPOIMENTO (POST) 
$obRouter->post('/user/perfil' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Perfil::setPerfil($request));
    }
]);

//ROTA DE CADASTRO
$obRouter->get('/user/perfilProfissional', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Perfil::getPerfilProfi($request));
    }
]);
/*
//ROTA DE CADASTRO
$obRouter->post('/perfilProfissional', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Perfil::setPerfilProfi($request));
    }
]);*/