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

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->get('/user/servicos/{id}/edit' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Servico::getEditService($request,$id));
    }
]);

//ROTA DE EDIÇÃO DE UM SERVIÇO 
$obRouter->post('/user/servicos/{id}/edit' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Servico::setEditService($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTO 
$obRouter->get('/user/servicos/{id}/remove' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Servico::getRemoveService($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTO 
$obRouter->post('/user/servicos/{id}/remove' , [
    'middlewares' => [
        'required-user-login'
    ],
    function ($request,$id) {
        return new Response(200,User\Servico::setRemoveService($request,$id));
    }
]);


/*


//ROTA DE EXCLUSÃO DE UM DEPOIMENTO (POST)
$obRouter->post('/admin/testimonies/{id}/delete' , [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request,$id) {
        return new Response(200,Admin\Testimony::setDeleteTestimony($request,$id));
    }
]);
*/