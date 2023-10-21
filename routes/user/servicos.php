<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE ADMINISTRAÇÃO DE SERVIÇOS
$obRouter->get('/user/servicos', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Servico::getServicos($request));
    }
]);

//ROTA DE CRIAÇÃO DE SERVIÇO
$obRouter->get('/user/servicos/new' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200, User\Servico::getNewService($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO SERVIÇO (POST) 
$obRouter->post('/user/servicos/new' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Servico::setNewService($request));
    }
]);