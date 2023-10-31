<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE PERFIL
$obRouter->get('/user/comercio', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Comercio::getComercio($request));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL (POST) 
$obRouter->post('/user/comercio' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Comercio::setComercio($request));
    }
]);

//ROTA DE PERFIL
$obRouter->get('/user/addProfList', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Comercio::getProfList($request));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL (POST) 
$obRouter->post('/user/addProfList' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Comercio::setProfList($request));
    }
]);