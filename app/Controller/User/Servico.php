<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\Servicos;
use \App\model\Entity\Profissional;
use \App\Utils\Pagination;


class Servico extends Page
{
    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
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
        $idProf = $obProf->idProfissional;
        /**ATENÇÃO COLOCAR ID DO PROFISSIONAL NO SESSION */

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = Servicos::getServices('idProfissional ="' . $idProf . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 10);

        //RESULTADOS DA PÁGINA
        $results = Servicos::getServices('idProfissional ="' . $idProf . '"', 'idServ DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obServico = $results->fetchObject(Servicos::class)) {

            $itens .= View::render('user/modules/services/item', [
                'id'                => $obServico->idServ,
                'nomeServ'          => $obServico->nomeServ, //ATENÇÃO CRIAR FUNÇÃO
                'tempoMedioServ'    => $obServico->tempoMedioServ, //ATENÇÃO CRIAR FUNÇÃO
                'valorServ'         => $obServico->valorServ, //ATENÇÃO CRIAR FUNÇÃO
                'statusValue'       => self::getStatusValue($obServico->status),
                'dataInicioServ'    => date('d/m/Y h:i:s', strtotime($obServico->dataInicioServ)),
                'estado'            => $obServico->status >= 2 ? 'disabled' : '',
            ]); //**ATENÇÃO CRIAR CONTROLE BOTÃO EDITAR */
        }
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de serviços
     * @return string
     */
    public static function getServicos($request)
    {
        //RECEBE O MODULO DO MENU DE PERFIL DA URL
        $url = $request->getRouter()->getUri();
        $xUri = explode('/', $url);
        $currentModule = end($xUri);

        //CONTEÚDO DA PÁGINA DE NOVO SERVIÇO
        $content = View::render('user/modules/services/index', [
            'title'      => 'Área de Administração de Serviço',
            'itens'      => self::getServicosItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Serviços > Univesp', $content, 'servicos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo Serviços
     * @param Request $request
     * @return string
     */
    public static function getNewService($request)
    {

        //CONTEÚDO DO FORMULÁRIO 
        $content = View::render('user/modules/services/form', [
            'title'         => 'Cadastrar Serviço',
            'nomeServ'      => '',
            'tempoMedioServ' => self::getTempoMedioServ('0130'),
            'valorServ'     => '',
            'statusValue'   => '',
            'optionStatus'  => self::getSelect(0),
            'status'        => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cadastrar Serviço > Univesp', $content, 'servicos');
    }

    /**
     * Método responsável por cadastrar um selecao no banco
     * @param Request $request
     * @return string
     */
    public static function setNewService($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        $id = $_SESSION['user']['usuario']['id'];

        $obProf = Profissional::getUserPById($id);
        if (!$obProf instanceof profissional) {
            //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
            $request->getRouter()->redirect('/cadastroProfissional');
        }

        $idProf = $obProf->idProfissional;

        //NOVA INSTANCIA DE DEPOIMENTO
        $obServ                 = new Servicos;
        $obServ->nomeServ       = $postVars['nomeServ'] ?? '';
        $obServ->tempoMedioServ = $postVars['tempoMedioServ'] ?? '';
        $obServ->valorServ      = $postVars['valorServ'] ?? '';
        $obServ->idProfissional = $idProf ?? '';
        $obServ->status         = $postVars['statusValue'] ?? 1;
        $obServ->dataFimServ    = '';

        $obServ->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/servicos/' . $obServ->idServ . '/edit?status=created');
    }

    /**
     * Método responsável por retornar o formulário de edição de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getEditService($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obServ = Servicos::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obServ instanceof Servicos) {
            $request->getRouter()->redirect('/user/servicos');
        }
        if ($obServ->status >= 2) {
            $request->getRouter()->redirect('/user/servicos?status=discart');
        }

        //CONTEÚDO DO FORMULÁRIO 
        $content = View::render('user/modules/services/form', [
            'title'             => 'Editar Serviço',
            'nomeServ'          => $obServ->nomeServ,
            'tempoMedioServ'    => self::getTempoMedioServ($obServ->tempoMedioServ),
            'valorServ'         => $obServ->valorServ,
            'status'            => self::getStatus($request),
            'optionStatus'      => self::getSelect($obServ->status)
        ]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cadastrar Serviço > Univesp', $content, 'servicos');
    }

    /**
     * Método responsável por gravar a atualização de um Serviços
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setEditService($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obServ = Servicos::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obServ instanceof Servicos) {
            $request->getRouter()->redirect('/user/servicos');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //Atualiza INSTANCIA 
        $obServ->nomeServ       = $postVars['nomeServ'] ?? $obServ->nomeServ;
        $obServ->tempoMedioServ = $postVars['tempoMedioServ'] ?? $obServ->tempoMedioServ;
        $obServ->valorServ      = $postVars['valorServ'] ?? $obServ->valorServ;
        $obServ->status         = $postVars['statusValue'] ?? $obServ->status;

        $obServ->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/servicos/' . $obServ->idServ . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getRemoveService($request, $id)
    {
        // OBTÉM O SERVIÇO DO BANCO DE DADOS
        $obServ = Servicos::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obServ instanceof Servicos) {
            $request->getRouter()->redirect('/user/servicos');
        }
        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/services/delete', [
            'nomeServ'      => $obServ->nomeServ
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Excluir Serviço > Univesp', $content, 'servicos');
    }

    /**
     * Método responsável por desativar um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setRemoveService($request, $id)
    { //ATENÇÃO NÃO EXCLUIR SERVIÇO. ALTERAR PARA DESATIVADO.
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obServ = Servicos::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obServ instanceof Servicos) {
            $request->getRouter()->redirect('/user/servicos');
        }

        $obServ->descartar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/servicos?status=deleted');
    }

    /**
     * Método responsável por excluir um depoimento
     * @param Request $request
     * @param interger $id
     * @return string
     */
    /* public static function setDeleteService($request, $id)
    { //ATENÇÃO NÃO EXCLUIR SERVIÇO. ALTERAR PARA DESATIVADO.
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obServ = Servicos::getServiceById($id);

        //VALIDA A INSTANCIA
        if (!$obServ instanceof Servicos) {
            $request->getRouter()->redirect('/user/servicos');
        }

        //EXCLUI O DEPOIMENTO
        $obServ->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/servicos?status=deleted');
    }*/

    /**
     * Método responsável por retornar a renderização do select
     * @param String $selecao
     * @return string
     */
    private static function getSelect($status)
    {
        $options = '';
        $estado = ['Ativo', 'Suspenso', 'Descartado'];
        $limit = 2;
        /*if ($status != 'new') {
            $limit = 3;
        }*/
        for ($h = 0; $h < $limit; $h++) {
            //RENDERIZA AS OPÇÕES DO SELECT
            $options .= View::render('user/modules/services/option', [
                'value'     => $h,
                'selected'  => $status == $h ? 'selected' : '',
                'label'     => $estado[$h]
            ]);
        }

        //RETORNA A RENDERIZAÇÃO DO MENU
        return  $options;
    }

    /**
     * Método responsável por retornar o o select do formulario de cadastro de Serviços
     * @param String $selecao
     * @return string
     */
    private static function getTempoMedioServ($selecao)
    {
        $options = '';
        $hora = 0;
        $min = '00';

        //for de hora
        for ($h = 0; $h <= 8; $h++) {


            //for de min
            for ($m = 0; $m < 4; $m++) {
                if ($hora == 8 && $min == 15) {
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
                    'nome'      => 'tempoMedioServ',
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
     * Método responsável por retornar a renderização do select
     * @param String $selecao
     * @return string
     */
    private static function getStatusValue($value)
    {
        switch ($value) {

            case '1':
                return 'Suspenso';
                break;

            case '2':
                return 'Descartado';
                break;

            default:
                return 'Ativo';
                break;
        }
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
            case 'discart':
                return Alert::getError('Serviço descartado!');
                break;
            case 'RequirePermission':
                return Alert::getSuccess('Requer conta Profissional para acessar');
                break;
        }
    }
}
