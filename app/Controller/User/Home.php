<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\Servicos;
use \App\model\Entity\Profissional;
use \App\model\Entity\ProfListUser;
use \App\model\Entity\Agenda;
use App\model\Entity\Comercio;
use App\model\Entity\EmpListUser;
use \App\Utils\Pagination;

class Home extends Page
{
    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getItensAgendadosItens($request, &$obPaginationAgenda)
    {
        //DEPOIMENTOS
        $itens = '';

        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);
        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/');
        }

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = Agenda::getAgendas('idUser ="' . $obUser->id . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPaginationAgenda = new Pagination($quantidadetotal, $paginaAtual, 10);

        //RESULTADOS DA PÁGINA
        $results = Agenda::getAgendas('idUser ="' . $obUser->id . '"', null, $obPaginationAgenda->getLimit());

        //RENDERIZA O ITEM
        while ($agenda = $results->fetchObject(Agenda::class)) {
            if ($agenda->status == 1) {
                $obProf = Profissional::getProfissionalById($agenda->idProfissional);
                $obProfUser = User::getUserById($obProf->idUser);
                $obServico = Servicos::getServiceById($agenda->idProfissional);

                $itens .= View::render('user/modules/home/LikeList/itensAgendados', [
                    'nomeServ'      => $obServico->nomeServ,
                    'dataAgenda'    => $agenda->agendaData,
                    'agendaHora'    => $agenda->agendaHora,
                    'valorServ'     => $obServico->valorServ,
                    'profissional'  => $obProfUser->nome,
                    'statusAgenda'  => 'ativo'
                ]);
            }
        }
        return $itens;
    }

    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getItensProfItens($request, &$obPaginationProf)
    {
        //DEPOIMENTOS
        $itens = '';

        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);
        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/');
        }

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = ProfListUser::getUsers('idUser ="' . $obUser->id . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPaginationProf = new Pagination($quantidadetotal, $paginaAtual, 10);

        //RESULTADOS DA PÁGINA
        $results = ProfListUser::getUsers('idUser ="' . $obUser->id . '"', 'idUser DESC', $obPaginationProf->getLimit());

        //RENDERIZA O ITEM
        while ($obProfList = $results->fetchObject(ProfListUser::class)) {
            $obProf = Profissional::getProfissionalById($obProfList->idProf);
            $obProfUser = User::getUserById($obProf->idUser);

            $itens .= View::render('user/modules/home/LikeList/itensProf', [
                'Profissional'  => $obProfUser->nome,
                'Funcao'        => $obProf->funcaoProfissional,
                'idProf'        => $obProf->idProfissional
            ]);
        }
        return $itens;
    }

    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getItensEmpreItens($request, &$obPaginationEmp)
    {
        //DEPOIMENTOS
        $itens = '';

        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);
        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/');
        }

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EmpListUser::getComerciosList('idUser ="' . $obUser->id . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPaginationEmp = new Pagination($quantidadetotal, $paginaAtual, 10);

        //RESULTADOS DA PÁGINA
        $results = EmpListUser::getComerciosList('idUser ="' . $obUser->id . '"', 'idUser DESC', $obPaginationEmp->getLimit());

        //RENDERIZA O ITEM
        while ($obEmpList = $results->fetchObject(EmpListUser::class)) {
            $obEmp = Comercio::getComercioById($obEmpList->idEmpre);
            $enderecoNumeroEmpre = $obEmp->enderecoEmpre . ', N:' . $obEmp->numEmpre;
            $itens .= View::render('user/modules/home/LikeList/itensEmpre', [
                'nomeEmpre'             => $obEmp->nomeEmpre,
                'enderecoNumeroEmpre'   => $enderecoNumeroEmpre,
                'siteEmpre'             => $obEmp->siteEmpre ?? '',
                'idEmp'                 => $obEmp->idEmpre
            ]);
        }
        return $itens;
    }

    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getItensFinalizadosItens($request, &$obPaginationFinalizado)
    {
        //DEPOIMENTOS
        $itens = '';

        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);
        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/');
        }

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = Agenda::getAgendas('idUser ="' . $obUser->id . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPaginationFinalizado = new Pagination($quantidadetotal, $paginaAtual, 10);

        //RESULTADOS DA PÁGINA
        $results = Agenda::getAgendas('idUser ="' . $obUser->id . '"', null, $obPaginationFinalizado->getLimit());

        //RENDERIZA O ITEM
        while ($agenda = $results->fetchObject(Agenda::class)) {
            if ($agenda->status != 1) {
                $obProf = Profissional::getProfissionalById($agenda->idProfissional);
                $obProfUser = User::getUserById($obProf->idUser);
                $obServico = Servicos::getServiceById($agenda->idProfissional);

                $itens .= View::render('user/modules/home/LikeList/itensFinalizados', [
                    'nomeServ'      => $obServico->nomeServ,
                    'dataAgenda'    => $agenda->agendaData,
                    'agendaHora'    => $agenda->agendaHora,
                    'valorServ'     => $obServico->valorServ,
                    'profissional'  => $obProfUser->nome,
                    'statusAgenda'  => $agenda->status == 0 ? 'finalizado' : 'cancelado',
                    'motivo'        => $agenda->motivo
                ]);
            }
        }
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function getHome($request)
    {

        //CONTEÚDO DA HOME
        $content = View::render('user/modules/home/index', [
            'itensAgendados'        => self::getItensAgendadosItens($request, $obPaginationAgenda),
            'AgendaPagination'      => parent::getPagination($request, $obPaginationAgenda),

            'itensProf'             => self::getItensProfItens($request, $obPaginationProf),
            'ProfPagination'        => parent::getPagination($request, $obPaginationProf),

            'itensEmpre'            => self::getItensEmpreItens($request, $obPaginationEmp),
            'EmprePagination'       => parent::getPagination($request, $obPaginationEmp),

            'itensFinalizados'      => self::getItensFinalizadosItens($request, $obPaginationFinalizado),
            'concluidoPagination'   => parent::getPagination($request, $obPaginationFinalizado),

            'status'    => self::getStatus($request),

        ]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'home');
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function getLikeProfList($request)
    {
        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/home/LikeList/index', [
            'title'     => 'Adicionar Profissional',
            'empProf'   => 'Profissional',
            'nome'      => 'prof',
            'status'    => self::getStatus($request),
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'agendar');
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function setLikeProfList($request)
    {
        //POST VARS
        $nomeProf = $request->getPostVars();

        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        $TempProf = User::getUserByLogin($nomeProf['prof']);
        if (!$TempProf instanceof User) {
            $request->getRouter()->redirect('/user/likeListProf?status=Invalid');
        }

        $obProf = Profissional::getUserPById($TempProf->id);
        if (!$obProf instanceof profissional) {
            //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
            $request->getRouter()->redirect('/user/likeListProf?status=Invalid');
        }

        $results = ProfListUser::getUsers('idUser ="' . $id . '" AND idProf="' . $TempProf->id . '"');
        while ($obProfDuplicated = $results->fetchObject(ProfListUser::class)) {
            if ($obProfDuplicated instanceof ProfListUser) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/likeListProf?status=duplicated');
            }
        }

        //SALVA HORARIOS DE TRABALHO DO PROFISSIONAL
        $obListProf           = new ProfListUser;
        $obListProf->idUser   = $id;
        $obListProf->idProf   = $obProf->idProfissional;
        $obListProf->finalDate = '';
        $obListProf->status   = 1;

        $obListProf->cadastrar();
        $request->getRouter()->redirect('/user/likeListProf?status=success');
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function getLikeEmpList($request)
    {
        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/home/LikeList/index', [
            'title'     => 'Adicionar Comércio',
            'empProf'   => 'Comércio',
            'nome'      => 'emp',
            'status'    => self::getStatus($request),
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'agendar');
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function setLikeEmpList($request)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        //POST VARS
        $txtEmpre = $request->getPostVars();

        if ($txtEmpre==null||$txtEmpre=='') {
            $request->getRouter()->redirect('/user/likeListEmp?status=InvalidC');
        }
        $obTemp = '';
        
        //QUANTIDADE TOTAL DE REGISTROS
        $obEmp = Comercio::getComercios('nomeEmpre ="'.$txtEmpre['emp'].'"');
        if ($obEmp==null||$obEmp=='') {
            $request->getRouter()->redirect('/user/likeListEmp?status=InvalidC');
        }
        if (!$obEmp instanceof Comercio) {
                $request->getRouter()->redirect('/user/likeListEmp?status=InvalidC');
            }
                $obTemp = $obEmp;
        
        echo 'obTemp<pre>';
        print_r($obTemp);
        echo '</pre>'; exit;
        $results = EmpListUser::getComerciosList('idUser ="' . $id . '" AND idEmpre="' . $obTemp->idEmpre . '"');
        while ($obEmpDuplicated = $results->fetchObject(EmpListUser::class)) {
            if ($obEmpDuplicated instanceof EmpListUser) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/likeListEmp?status=duplicated');
            }
        }
        echo 'obTemp<pre>';
        print_r($obEmpDuplicated);
        echo '</pre>'; exit;
        //SALVA HORARIOS DE TRABALHO DO PROFISSIONAL
        $obEmpList           = new EmpListUser;
        $obEmpList->idUser   = $id;
        $obEmpList->idEmpre  = $obTemp->idEmpre;
        $obEmpList->finalDate = '';
        $obEmpList->status   = 1;

        $obEmpList->cadastrar();
        $request->getRouter()->redirect('/user/likeListEmp?status=success');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um profissonal da lista de preferidos
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getDeleteLikeProf($request, $idProf)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        $obProf =  Profissional::getProfissionalById($idProf);
        if (!$obProf instanceof Profissional) {
            //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
            $request->getRouter()->redirect('/user/likeListProf?status=Invalid');
        }
        $obProfUser =  User::getUserById($obProf->idUser);

        $results = ProfListUser::getUsers('idUser ="' . $id . '" AND idProf="' . $idProf . '"');
        while ($obProfDuplicated = $results->fetchObject(ProfListUser::class)) {
            if (!$obProfDuplicated instanceof ProfListUser) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/likeListProf?status=ProfNoExist');
            }
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/home/Delete/delete', [
            'nomeProf'      => $obProfUser->nome
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Excluir Profissional > PIUnivesp', $content, 'home');
    }

    /**
     * Método responsável por excluir um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setDeleteLikeProf($request, $idProf)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        $results = ProfListUser::getUsers('idUser ="' . $id . '" AND idProf="' . $idProf . '"');
        while ($obProfDelete = $results->fetchObject(ProfListUser::class)) {

            if (!$obProfDelete instanceof ProfListUser) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/likeListProf?status=ProfNoExist');
            }
            //EXCLUI O USUÁRIOS
            $obProfDelete->excluir();
        }

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/?status=deleted');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um profissonal da lista de preferidos
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getDeleteLikeEmp($request, $idEmp)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        $obEmp =  Comercio::getComercioById($idEmp);
        if (!$obEmp instanceof Comercio) {
            //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
            $request->getRouter()->redirect('/user/likeListEmp?status=InvalidC');
        }


        $results = EmpListUser::getComerciosList('idUser ="' . $id . '" AND idEmpre="' . $idEmp . '"');
        while ($obEmpDelete = $results->fetchObject(EmpListUser::class)) {

            if (!$obEmpDelete instanceof EmpListUser) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/likeListEmp?status=EmpNoExist');
            }
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/home/Delete/delete', [
            'nomeProf'      => $obEmp->nomeEmpre
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Excluir Profissional > PIUnivesp', $content, 'home');
    }

    /**
     * Método responsável por excluir um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setDeleteLikeEmp($request, $idEmp)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        $results = EmpListUser::getComerciosList('idUser ="' . $id . '" AND idEmpre="' . $idEmp . '"');
        while ($obEmpDelete = $results->fetchObject(EmpListUser::class)) {

            if (!$obEmpDelete instanceof EmpListUser) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/likeListProf?status=ProfNoExist');
            }
            //EXCLUI O USUÁRIOS
            $obEmpDelete->excluir();
        }

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/?status=empDeleted');
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
            case 'Invalid':
                return Alert::getSuccess('Nome de profissional não existe!');
                break;
            case 'success':
                return Alert::getSuccess('Cadastrado com sucesso!');
                break;
            case 'duplicated':
                return Alert::getSuccess('Este Profissional ja está cadastrado!');
                break;
            case 'ProfNoExist':
                return Alert::getSuccess('Este Profissional não foi encontrado em sua lista!');
                break;
            case 'deleted':
                return Alert::getSuccess('Profissional Removido da lista!');
                break;

            case 'empNoExist':
                return Alert::getSuccess('Este Comércio não foi encontrado em sua lista!');
                break;
            case 'empDeleted':
                return Alert::getSuccess('Comércio Removido da lista!');
                break;
            case 'InvalidC':
                return Alert::getSuccess('Nome do comércio não existe!');
                break;
            case 'duplicatedC':
                return Alert::getSuccess('Este Comércio já está cadastrado!');
                break;

            case 'InvalidSelected':
                return Alert::getSuccess('Selecione uma opção das listas e uma data!');
                break;

            case 'InvalidSelectedDate':
                return Alert::getSuccess('Selecione uma data!');
                break;
        }
    }
}
