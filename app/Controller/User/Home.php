<?php

namespace App\Controller\User;

use App\model\Entity\Comercio;
use App\model\Entity\EmpListUser;
use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\Profissional;
use \App\model\Entity\ProfListUser;
use \App\Utils\Pagination;

class Home extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getItensAgendadosItens($request, &$obPagination)
    {
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
            $obProfName = User::getUserById($obProf->idUser);

            $itens .= View::render('user/modules/home/LikeList/itensProf', [
                'Profissional'  => $obProfName->nome,
                'Funcao'        => $obProf->funcaoProfissional
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
           $enderecoNumeroEmpre= $obEmp->enderecoEmpre.', N:'.$obEmp->numEmpre;
           $itens .= View::render('user/modules/home/LikeList/itensEmpre', [
               'nomeEmpre'          => $obEmp->nomeEmpre,
               'enderecoNumeroEmpre'=> $enderecoNumeroEmpre,
               'siteEmpre'          => $obEmp->siteEmpre
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
    private static function getItensFinalizadosItens($request, &$obPagination)
    {
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
            'itensAgendados'        => self::getItensAgendadosItens($request, $obPagination),
            'AgendaPagination'      => 'parent::getPagination($request, $obPagination)',

            'itensProf'             => self::getItensProfItens($request, $obPaginationProf),
            'ProfPagination'        => parent::getPagination($request, $obPaginationProf),

            'itensEmpre'            => self::getItensEmpreItens($request, $obPaginationEmp),
            'EmprePagination'       => parent::getPagination($request, $obPaginationEmp),

            'itensFinalizados'      => self::getItensFinalizadosItens($request, $obPagination),
            'concluidoPagination'   => 'parent::getPagination($request, $obPagination)',

            'status'    => self::getStatus($request),


            'nomeServ'      => 'nomeServ',
            'dataAgenda'    => 'dataAgenda',
            'valorServ'     => 'valorServ',
            'profissional'  => 'profissional',
            'local'         => 'local',
            'statusAgenda'  => 'statusAgenda',
            'id'            => 'id',


            'nomeEmpre'             => 'nomeEmpre',
            'enderecoNumeroEmpre'   => 'enderecoNumeroEmpre',
            'siteEmpre'             => 'siteEmpre',


            'nomeServ'              => 'nomeServ',
            'dataAgenda'            => 'dataAgenda',
            'valorServ'             => 'valorServ',
            'profissional'          => 'profissional',
            'local'                 => 'local',
            'statusFinalizado'      => 'statusFinalizado',
            'Motivo'                => 'Motivo',

        ]);
        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'home');
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function getAgendar($request)
    {
        //CONTEÚDO DA HOME

        $content = View::render('user/modules/agendar/index', []);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'agendar');
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function setAgendar($request)
    {
        //CONTEÚDO DA HOME
        /*
        $content = View::render('user/modules/home/index',[]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp',$content,'home');
        */
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

        $obListProf = ProfListUser::getListProfbyId($obProf->idProfissional);

        if (!$obListProf instanceof ProfListUser) {
            //SALVA HORARIOS DE TRABALHO DO PROFISSIONAL
            $obListProf           = new ProfListUser;
            $obListProf->idUser   = $id;
            $obListProf->idProf   = $obProf->idProfissional;
            $obListProf->finalDate = '';
            $obListProf->status   = 1;

            $obListProf->cadastrar();
            $request->getRouter()->redirect('/user/likeListProf?status=success');
        } else {
            $request->getRouter()->redirect('/user/likeListProf?status=duplicated');
        }
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
        $nomeEmpre = $request->getPostVars();

        //QUANTIDADE TOTAL DE REGISTROS
        $obNome = comercio::getComercios('nomeEmpre ="' . $nomeEmpre['emp'] . '"');
        $obTemp='';
        while ($obEmp = $obNome->fetchObject(comercio::class)) {
            if (!$obEmp instanceof Comercio) {
                $request->getRouter()->redirect('/user/likeListEmp?status=InvalidC');
            }else{
                $obTemp=$obEmp;
            }
        }

        $obEmpList = EmpListUser::getListEmprisebyId($obTemp->idEmpre);

        if (!$obEmpList instanceof EmpListUser) {
            //SALVA HORARIOS DE TRABALHO DO PROFISSIONAL
            $obEmpList           = new EmpListUser;
            $obEmpList->idUser   = $id;
            $obEmpList->idEmpre  = $obTemp->idEmpre;
            $obEmpList->finalDate= '';
            $obEmpList->status   = 1;

            $obEmpList->cadastrar();
            $request->getRouter()->redirect('/user/likeListProf?status=success');
        } else {
            $request->getRouter()->redirect('/user/likeListProf?status=duplicated');
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
            case 'Invalid':
                return Alert::getSuccess('Nome de profissional não existe!');
                break;
            case 'success':
                return Alert::getSuccess('Cadastrado com sucesso!');
                break;
            case 'duplicated':
                return Alert::getSuccess('Este Profissional ja está cadastrado!');
                break;

            case 'InvalidC':
                return Alert::getSuccess('Nome do comércio não existe!');
                break;
            case 'duplicatedC':
                return Alert::getSuccess('Este Comércio já está cadastrado!');
                break;
        }
    }
}
