<?php

namespace App\Controller\User;


use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\Profissional;
use App\model\Entity\Comercio as EntityComercio;
use App\model\Entity\ListaProfissionaisNoComercio as EntityLista;
use \App\Utils\Pagination;

class Comercio extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de Serviços para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getItensProf($request, $obEmpre, &$profPagination)
    {
        //DEPOIMENTOS
        $itens = '';

        $EmpreId = $obEmpre->idEmpre;

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityLista::getListProf('idEmpresa ="' . $EmpreId . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $profPagination = new Pagination($quantidadetotal, $paginaAtual, 10);

        //RESULTADOS DA PÁGINA
        $results = EntityLista::getListProf('idEmpresa ="' . $EmpreId . '"', 'funcao DESC', $profPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obProfList = $results->fetchObject(EntityLista::class)) {
            //VALIDA A INSTANCIA PROFISSIONAL
            $obProf = Profissional::getProfissionalById($obProfList->idProfissional);
            $obProfUser = user::getUserById($obProf->idUser);

            $itens .= View::render('user/modules/comercio/itensProfissionais', [
                'nomeProf'    => $obProfUser->nome,
                'dataEntrada' => date('d/m/Y h:i:s', strtotime($obProfList->dataInicio)),
                'dataSaida'   => $obProfList->dataFim,
                'funcao'      => $obProfList->funcao ?? '',
                'statusProf'  => $obProfList->status >= 1 ? 'Ativo' : 'Inativo' //,
                // 'Motivo'    => $obProfList->motivo>=1 ??'',
            ]); //**ATENÇÃO CRIAR CONTROLE BOTÃO EDITAR */
        }
        return $itens;
    }

    private static function  getTabPrefProf($request, $obEmpre)
    {
        $itens = View::render('user/modules/comercio/tabelaPrefProf', [
            'itensProfissionais'   =>  self::getItensProf($request, $obEmpre, $profPagination),
            'profPagination'       =>  parent::getPagination($request, $profPagination)
        ]);

        return $itens;
    }


    /**
     * Método responsável por retornar a renderização da página de perfil
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getComercio($request, $errorMessage = null)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];
        $obValidoU = self::validaInstancia($id, "user");

        $obUser = $obValidoU->tipoConta == 0 ? $request->getRouter()->redirect('/user/?status=RequirePermission'):$obValidoU;

        $obValidoP = self::validaInstancia($id, "prof");
        $obValido = self::validaInstancia($obValidoP->idProfissional, "emp");
        $obEmpre = $obValido ? $obValido : '';

        //RECEBE O MODULO DO MENU DE PERFIL DA URL
        $url = $request->getRouter()->getUri();
        $xUri = explode('/', $url);
        $currentModule = end($xUri);

        $tabelaPrefProf = '';
        $obEmpre != NULL || $obEmpre != '' ? $tabelaPrefProf = self::getTabPrefProf($request, $obEmpre) : '';


        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/comercio/index', [
            'title'     => 'Perfil Comercial',
            'enderecoEmpre' => $obEmpre->enderecoEmpre ?? '',
            'cepEmpre'      => $obEmpre->cepEmpre   ?? '',
            'nomeEmpre'     => $obEmpre->nomeEmpre  ?? '',
            'telEmpre'      => $obEmpre->telEmpre   ?? '',
            'numEmpre'      => $obEmpre->numEmpre   ?? '',
            'siteEmpresa'   => $obEmpre->siteEmpre  ?? '',
            'cpfEmpre'      => $obEmpre->cpfEmpre   ?? '',

            'tabelaPrefProf' => $tabelaPrefProf,
            'status'        => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Perfil Comercial > Univesp', $content, 'comercio');
    }

    /**
     * Método responsável por gravar a atualização de um comercio
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setComercio($request)
    {
        //POST VARS
        $postVars      = $request->getPostVars();
        $nomeEmpre     = $postVars['nomeEmpre']    ?? '';
        $enderecoEmpre = $postVars['enderecoEmpre'] ?? '';
        $numEmpre      = $postVars['numEmpre']     ?? '';
        $CEP           = $postVars['cepEmpre']     ?? '';
        $siteEmpre     = $postVars['siteEmpresa']  ?? '';
        $telEmpre      = $postVars['telEmpre']     ?? '';
        $cpfEmpre      = $postVars['cpfEmpre']     ?? '';

        //SERIALIZA OS DADOS        
        $cpfEmpre = self::removeSpecialCaracter($cpfEmpre);
        $telEmpre = self::removeSpecialCaracter($telEmpre);

        //VALIDA CAMPOS
        $telEmpre = (self::getValidaCampos('tel', $telEmpre, $request));
        $cpfEmpre = (self::getValidaCampos('cpf', $cpfEmpre, $request));
        $cepEmpre = (self::getValidaCampos('cep', $CEP, $request));


        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        //VALIDA INSTANCIAS
        $obUser = self::validaInstancia($id, "user");
        $obProf = self::validaInstancia($id, "prof");
        $valido = self::validaInstancia($obProf->idProfissional, "emp");
        $obEmpre = $valido ? $valido : '';

        if ($obEmpre == '') {

            //ATUALIZA INSTANCIA DE USUÁRIO
            $obUser->tipoConta = 2;
            $obUser->atualizar();

            //CRIA INSTANCIA COMERCIAL
            $obEmpre          = new EntityComercio;
            $obEmpre->nomeEmpre      = $nomeEmpre;
            $obEmpre->enderecoEmpre  = $enderecoEmpre;
            $obEmpre->numEmpre       = $numEmpre;
            $obEmpre->cepEmpre       = $cepEmpre;
            $obEmpre->siteEmpre    = $siteEmpre;
            $obEmpre->telEmpre       = $telEmpre;
            $obEmpre->cpfEmpre       = $cpfEmpre;
            $obEmpre->idProfissional = $obProf->idProfissional;

            $obEmpre->cadastrar();
        } else {

            //ATUALIZA A INSTANCIA 
            $obEmpre->nomeEmpre      = $nomeEmpre;
            $obEmpre->enderecoEmpre  = $enderecoEmpre;
            $obEmpre->numEmpre       = $numEmpre;
            $obEmpre->cepEmpre       = $cepEmpre;
            $obEmpre->siteEmpre      = $siteEmpre;
            $obEmpre->telEmpre       = $telEmpre;
            $obEmpre->cpfEmpre       = $cpfEmpre;

            $obEmpre->atualizar();
        }

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/comercio?status=updated');
    }

    /**
     * Método responsável por retornar a renderização da página adicionar profissional
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getProfList($request, $errorMessage = null)
    {

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/comercio/LikeList/index', [
            'title'  => 'Adicionar Profissional',
            'empProf' => 'Profissional',
            'nome'   => 'nome',
            'status' => self::getStatus($request),
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'comercio');
    }

    /**
     * Método responsável por gravar um profissional em um comercio
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function SetProfList($request)
    { {
            //RECEBE ID DO USUARIO LOGADO
            $id = $_SESSION['user']['usuario']['id'];
            $obValidoP = self::validaInstancia($id, "prof");
            $obValido = self::validaInstancia($obValidoP->idProfissional, "emp");
            $obEmpre = $obValido ? $obValido : '';
            $obEmpreId = $obEmpre->idEmpre;

            //POST VARS
            $nomeProf = $request->getPostVars();

            //RECEBE DADOS DE USUÁRIO DO PROFISSIONAL
            $obValido = self::validaInstancia($nomeProf['nome'], "nome");
            $TempProf = $obValido ? $obValido : $request->getRouter()->redirect('/user/addProfList?status=Invalid');

            //RECEBE DADOS DO PROFISSIONAL

            $obValido = self::validaInstancia($TempProf->id, "prof");
            $obProf = $obValido ? $obValido : '';


            //QUANTIDADE TOTAL DE REGISTROS
            $quantidadetotal = EntityLista::getListProf('idEmpresa ="' . $obEmpreId . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            //PÁGINA ATUAL
            $queryParams = $request->getQueryParams();
            $paginaAtual = $queryParams['page'] ?? 1;

            //INSTANCIA DE PAGINAÇÃO
            $obPaginationProf = new Pagination($quantidadetotal, $paginaAtual, 10);

            //RESULTADOS DA PÁGINA
            $results = EntityLista::getListProf('idEmpresa ="' . $obEmpreId . '" AND idProfissional="' . $obProf->idProfissional . '"', null, $obPaginationProf->getLimit());
            $obProfDuplicated = $results->fetchObject(EntityLista::class);

            if ($obProfDuplicated instanceof EntityLista) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/likeListProf?status=duplicated');
            }

            //SALVA HORARIOS DE TRABALHO DO PROFISSIONAL
            $obProfList                 = new EntityLista;
            $obProfList->idEmpresa      = $obEmpreId;
            $obProfList->idProfissional = $obProf->idProfissional;
            $obProfList->dataFim        = '';
            $obProfList->funcao         = $obProf->funcaoProfissional;
            $obProfList->status         = 1;

            $obProfList->cadastrar();
            $request->getRouter()->redirect('/user/likeListProf?status=success');
        }
    }

    /**
     * Método responsável por validações de campos da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    private static function getValidaCampos($type, $val, $request)
    {
        if ($val == '' || $val == NULL) {
            return '';
        }
        switch ($type) {
            case 'tel':
                //VALIDA QTD DE CARACTERES DO TELEFONE DO USUÁRIO
                if (strlen($val) <= 7) {
                    $request->getRouter()->redirect('/user/comercio?status=invaliLenghtTel');
                } elseif (strlen($val) >= 14) {
                    $request->getRouter()->redirect('/user/comercio?status=invaliLenghtTel');
                }
                return $val;
                break;

            case 'cep':

                $cep = self::removeSpecialCaracter($val);

                //VALIDA QTD DE CARACTERES DO CEP DO USUÁRIO
                if (strlen($cep) != 8) {
                    if (substr($cep, 0, 1) != substr($val, 0, 1) && strlen($cep) == 7) {
                        return $cep = substr($val, 0, 1) . $cep;
                    } else {
                        //REDIRECIONA O USUÁRIO
                        $request->getRouter()->redirect('/user/comercio?status=cepLeng');
                    }
                    //return $cep;
                }
                return $cep;

            case 'cpf':
                //VALIDA QTD DE CARACTERES DO CPF DO USUÁRIO
                if (strlen($val) != 11) {
                    if (strlen($val) != 14) {
                        //REDIRECIONA O USUÁRIO
                        $request->getRouter()->redirect('/user/comercio?status=invaliLenghtDoc');
                    }
                }
                return $val;
                break;


            default:
                # code...
                break;
        }
    }

    /**
     * Método responsável por remover qualquer caracter, deixando somente numeros Inteiros
     * @param string $numero
     * @return int
     */
    private static function removeSpecialCaracter($numero)
    {
        return preg_replace('/[^0-9]/', '', $numero);
    }

    /**
     * Método responsável por validar instancia do DB baseado no ID do usuario Logado.
     * @param Request $request
     * @param interger $id
     * @return Array de objeto ou false
     */
    private static function validaInstancia($id, $instancia)
    {

        switch ($instancia) {

            case 'user':

                // OBTÉM O USUÁRIOS DO BANCO DE DADOS
                $obUser = User::getUserById($id);

                //VALIDA A INSTANCIA
                if (!$obUser instanceof User) {
                    return false;
                }
                return $obUser;

            case 'prof':

                // OBTÉM O USUÁRIOS DO BANCO DE DADOS
                $obProf = Profissional::getUserPById($id);
                //VALIDA A INSTANCIA PROFISSIONAL
                if (!$obProf instanceof profissional) {
                    return false;
                }
                return $obProf;

            case 'emp':

                // OBTÉM O USUÁRIOS DO BANCO DE DADOS
                $obEmpre = EntityComercio::getComercioByIdProfissional($id);

                //VALIDA A INSTANCIA COMERCIAL
                if (!$obEmpre instanceof EntityComercio) {
                    return false;
                }
                return $obEmpre;

            case 'nome':

                //RECEBE DADOS DE USUÁRIO DO PROFISSIONAL
                $obUser = User::getUserByLogin($id);

                if (!$obUser instanceof User) {
                    return false;
                }
                return $obUser;

            default:
                # code...
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
            case 'invaliLenghtDoc':
                return Alert::getError('Quantidade de caracteres Invalidos do campo CPF/CNPJ!');
                break;
            case 'cepLeng':
                return Alert::getError('Quantidade de caracteres Invalidos do campo CEP!');
                break;
            case 'invaliLenghtTel':
                return Alert::getError('Quantidade de caracteres Invalidos do campo Telefone!');
                break;
            case 'Invalid':
                return Alert::getSuccess('Nome de profissional não existe!');
                break;
            case 'InvalidAccount':
                return Alert::getSuccess('Precisa Ser um Profissional para acessar a página Comercial!');
                break;
            case 'success':
                return Alert::getSuccess('Cadastrado com sucesso!');
                break;
            case 'duplicated':
                return Alert::getSuccess('Este Profissional ja está cadastrado!');
                break;
                case 'RequirePermission':
                    return Alert::getSuccess('Requer conta Profissional para acessar');
                    break;

        }
    }
}
