<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE PERFIL
$obRouter->get('/user/perfil', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Perfil::getPerfil($request));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL (POST) 
$obRouter->post('/user/perfil' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Perfil::setPerfil($request));
    }
]);

//ROTA DE PERFIL PROFISSIONAL
$obRouter->get('/user/perfilProfissional', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Perfil::getPerfilProfi($request));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL PROFISSIONAL (POST) 
$obRouter->post('/user/perfilProfissional', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Perfil::setPerfilProfi($request));
    }
]);