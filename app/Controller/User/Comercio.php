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
    private static function getItensProf($request, &$profPagination)
    {
        //DEPOIMENTOS
        $itens = '';

         //RECEBE ID DO USUARIO LOGADO
         $id = $_SESSION['user']['usuario']['id'];
    
         $valido = self::validaInstancia($id, "emp");

         $obLista = EntityLista::getListProfissionalByEmprise($valido->idEmpre);
         //VALIDA A INSTANCIA
        if (!$obLista instanceof EntityLista) {
            $obLista='';

        }

       
/*
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
        }*/
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
    
        $valido = self::validaInstancia($id, "emp");

        $obEmpre = $valido ? $valido : '';

        //RECEBE O MODULO DO MENU DE PERFIL DA URL
        $url = $request->getRouter()->getUri();
        $xUri = explode('/', $url);
        $currentModule = end($xUri);

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/comercio/index', [
            'title'     => 'Perfil Comercial',
            'perfilLink' => parent::getSubMenu($currentModule),
            'enderecoEmpre' => $obEmpre->enderecoEmpre ?? '',
            'cepEmpre'      => $obEmpre->cepEmpre   ?? '',
            'nomeEmpre'     => $obEmpre->nomeEmpre  ?? '',
            'telEmpre'      => $obEmpre->telEmpre   ?? '',
            'numEmpre'      => $obEmpre->numEmpre   ?? '',
            'siteEmpresa'   => $obEmpre->siteEmpre  ?? '',
            'cpfEmpre'      => $obEmpre->cpfEmpre   ?? '',

            'itensProfissionais'   =>'',// self::getItensProf($request, $profPagination),
            'profPagination'       => '',//parent::getPagination($request, $profPagination),
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
        $valido = self::validaInstancia($id, "emp");
        $obEmpre = $valido ? $valido : '';
        
        if ($obEmpre == '') {
            //ATUALIZA INSTANCIA DE USUÁRIO
            $obUser->tipoConta=2;
            $obuser->atualizar();

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
    }

    /**
     * Método responsável por gravar um profissional em um comercio
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function ProfList($request)
    {

    }

    /**
     * Método responsável por validações de campos da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    private static function getValidaCampos($type, $val, $request)
    {
     if($val== ''|| $val==NULL) {
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
        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            return false;
        }

        if ($instancia == 'user') {
            return $obUser;
        }

        //VALIDA A INSTANCIA PROFISSIONAL
        $obProf = Profissional::getUserPById($id);
        if (!$obProf instanceof profissional) {
            return false;
        }

        if ($instancia == 'prof') {
            return $obProf;
        }

        $obEmpre = EntityComercio::getComercioByIdProfissional($obProf->idProfissional);

        if (!$obEmpre instanceof EntityComercio) {
            return false;
        }
        if ($instancia == 'emp') {

            return $obEmpre;
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
        }
    }
}
