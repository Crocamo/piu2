<?php

use \App\Http\Response;
use \App\Controller\User;

//ROTA USER
$obRouter->get('/user' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) {
        return new Response(200,User\Home::getHome($request));
    }
]);

//ROTA DE PERFIL PROFISSIONAL
$obRouter->get('/user/agendar/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Home::getAgendar($request));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL PROFISSIONAL (POST) 
$obRouter->post('/user/agendar/new', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Home::setAgendar($request));
    }
]);

//ROTA DE PERFIL PROFISSIONAL
$obRouter->get('/user/likeListProf', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Home::getLikeProfList($request));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL PROFISSIONAL (POST) 
$obRouter->post('/user/likeListProf', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Home::setLikeProfList($request));
    }
]);

//ROTA DE PERFIL PROFISSIONAL
$obRouter->get('/user/likeListEmp', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Home::getLikeEmpList($request));
    }
]);

//ROTA DE ATUALIZAÇÃO DE PERFIL PROFISSIONAL (POST) 
$obRouter->post('/user/likeListEmp', [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request) { 
        return new Response(200, User\Home::setLikeEmpList($request));
    }
]);