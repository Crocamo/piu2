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

//ROTA DE EXCLUSÃO DE UM USUÁRIO 
$obRouter->get('/user/ProfPref/{id}/delete' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Home::getDeleteLikeProf($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM USUÁRIOS (POST)
$obRouter->post('/user/ProfPref/{id}/delete' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Home::setDeleteLikeProf($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM USUÁRIO 
$obRouter->get('/user/comercio/{id}/delete' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Home::getDeleteLikeEmp($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM USUÁRIOS (POST)
$obRouter->post('/user/comercio/{id}/delete' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Home::setDeleteLikeEmp($request,$id));
    }
]);