<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\Servicos;
use \App\model\Entity\Profissional;
use \App\Utils\Pagination;


class Servico extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */     
    private static function getServicosItens($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';
         
        //RECEBE ID DO PROFISSIONAL LOGADO
        $id = $_SESSION['user']['usuario']['id'];
        $obProf = Profissional::getUserPById($id);
        $idProf=$obProf->idProfissional;/**ATENÇÃO COLOCAR ID DO PROFISSIONAL NO SESSION */
        
        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = Servicos::getServices('idProfissional ="'.$idProf.'"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
        
        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;
        
        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = Servicos::getServices('idProfissional ="'.$idProf.'"', 'idServ DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obServico = $results->fetchObject(Servicos::class)) {
             
            $itens .= View::render('user/modules/services/item', [
                'nomeServ'          => $obServico->nomeServ,//ATENÇÃO CRIAR FUNÇÃO
                'tempoMedioServ'    => $obServico->tempoMedioServ,//ATENÇÃO CRIAR FUNÇÃO
                'valorServ'         => $obServico->valorServ,//ATENÇÃO CRIAR FUNÇÃO
                'status'            => $obServico->status,//ATENÇÃO CRIAR FUNÇÃO
                'dataInicioServ'    => date('d/m/Y h:i:s', strtotime($obServico->dataInicioServ))
            ]);
        }
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de depoimentos
     * @return string
     */
    public static function getServicos($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('user/modules/services/index', [
            'itens'      => self::getServicosItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Serviços > Univesp', $content, 'servicos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     * @param Request $request
     * @return string
     */
    public static function getNewService($request)
    {

        //CONTEÚDO DA HOME
        $content = View::render('user/modules/services/form', [
            'title'         => 'Cadastrar Serviços',
            'nomeServ'      => '',
            'tempoMedioServ'=> self::getTempoMedioServ('0130'),
            'valorServ'     => '',
            'status'        => ''
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cadastrar Serviços > Univesp', $content, 'servicos');
    }

    private static function getTempoMedioServ($selecao)
    {
        $options = '';
        $hora = 0;
        $min = '00';
        
        //for de hora
        for ($h = 0; $h <= 8; $h++) {

            
            //for de min
            for ($m = 0; $m < 4; $m++) {
                if($hora==8&&$min==15){
                    break;
                }
                //coloca 0 na frente do numero abaixo do 10
                    $value = '0' . $hora . $min;
                    $label = '0' . $hora . ':' . $min;
                    
                $min += 15;

                //CONTROLE DO VALOR SELECIONADO
                if ($selecao == $value) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }

                //RENDERIZA AS OPÇÕES DO SELECT
                $options .= View::render('user/modules/services/option', [
                    'value'     => $value,
                    'selected'  => $selected ? 'selected' : '',
                    'nome'      =>'tempoMedioServ',
                    'label'     => $label
                ]);
                
            } //fim for min
 
            //INCREMENTA UMA HORA ZERA MINUTO
            $hora += 1;
            $min = '00';
            
        } //fim for hora

        //RETORNA A RENDERIZAÇÃO DO MENU
        return  $options;
    }

    /**
     * Método responsável por cadastrar um depoimento no banco
     * @param Request $request
     * @return string
     */
    public static function setNewService($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        
        $id = $_SESSION['user']['usuario']['id'];

        $obProf = Profissional::getUserPById($id);
        if(!$obProf instanceof profissional){
            //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
            $request->getRouter()->redirect('/cadastroProfissional');
        }

        $idHorario=$obProf->idHorarios;
        
        //NOVA INSTANCIA DE DEPOIMENTO
        $obServ                 = new Servico;
        echo "<pre>";      
    print_r($obServ);      
    echo "</pre>";exit;
        $obServ->nomeServ       =$postVars['nomeServ'] ?? '';
        $obServ->tempoMedioServ =$postVars['tempoMedioServ'] ?? '';
        $obServ->valorServ      =$postVars['valorServ'] ?? '';
        $obServ->idProfissional =$idHorario ?? '';
        $obServ->status         =$postVars['status'] ?? 1 ;
        $obServ->dataFimServ    ='';
        $obServ->cadastrar();
    

/*
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/testimonies/' . $obTestimony->id . '/edit?status=created');
    */
    }

    /**
     * Método responsável por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        //  QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if (!isset($queryParams['status'])) return '';

        //MENSAGEM DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Depoimento criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Depoimento deletado com sucesso!');
                break;

        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    /*public static function getEditTestimony($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimoniesById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/testimonies/form', [
            'title'     => 'Editar depoimento',
            'nome'      => $obTestimony->nome,
            'mensagem'  => $obTestimony->mensagem,
            'status'    => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Editar depoimento > WDEV', $content, 'testimonies');
    }*/

    /**
     * Método responsável por gravar a atualização de um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    /*public static function setEditTestimony($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimoniesById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTANCIA
        $obTestimony->nome = $postVars['nome'] ?? $obTestimony->nome;
        $obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;
        $obTestimony->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/testimonies/' . $obTestimony->id . '/edit?status=updated');
    }*/

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    /*public static function getDeleteTestimony($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimoniesById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/testimonies/delete', [
            'nome'      => $obTestimony->nome,
            'mensagem'  => $obTestimony->mensagem
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Excluir depoimento > WDEV', $content, 'testimonies');
    }*/
    
    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    /*public static function setDeleteTestimony($request, $id)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimoniesById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin/testimonies');
        }

        //EXCLUI O DEPOIMENTO
        $obTestimony->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/testimonies?status=deleted');
    }*/
}
