<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA DE PERFIL PROFISSIONAL
$obRouter->get('/user/agendar/{disponibilidade}/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$disponibilidade) { 
        return new Response(200, User\Agendar::getAgendar($request,$disponibilidade));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL PROFISSIONAL (POST) 
$obRouter->post('/user/agendar/{disponibilidade}/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$disponibilidade) { 
        return new Response(200, User\Agendar::setAgendar($request,$disponibilidade));
    }
]);

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->get('/user/agendar/{disponibilidade}/Confirm' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$disponibilidade) {
        return new Response(200, User\Agendar::getConfirmService($request,$disponibilidade));
    }
]);

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->post('/user/agendar/{disponibilidade}/Confirm' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$disponibilidade) {
        return new Response(200, User\Agendar::setConfirmService($request,$disponibilidade));
    }
]);

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->get('/user/agendar/{disponibilidade}/edit' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$disponibilidade) {
        return new Response(200, User\Agendar::getEditService($request,$disponibilidade));
    }
]);

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->post('/user/agendar/{disponibilidade}/edit' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$disponibilidade) {
        return new Response(200, User\Agendar::setEditService($request,$disponibilidade));
    }
]);