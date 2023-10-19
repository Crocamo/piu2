<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\profissional;
use \App\model\Entity\Horarios;

class Perfil extends Page
{

        /**
     * Módulos disponíveis no painel
     * @var array
     */
    private static $profModules = [
        'perfil' => [
            'label' =>  'perfil de usuario',
            'link'  =>  URL . '/user/perfil'
        ],
        'perfilProfissional' => [
            'label' =>  'Perfil Profissional',
            'link'  =>  URL . '/user/perfilProfissional'
        ]
    ];

    /**
     * Módulos disponíveis no painel
     * @var array
     */
    private static $comecialModules = [
        'perfil' => [
            'label' =>  'perfil de usuario',
            'link'  =>  URL . '/user/perfil'
        ],
        'perfilProfissional' => [
            'label' =>  'Perfil Profissional',
            'link'  =>  URL . '/user/perfilProfissional'
        ],
        'perfilComercial' => [
            'label' =>  'perfil comercial',
            'link'  =>  URL . '/user/perfilComercial'
        ]
    ];

    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getPerfil($request, $errorMessage = null)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);
        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/');
        }
        
        //RECEBE O MODULO DO MENU DE PERFIL DA URL
        $url=$request->getRouter()->getUri();
        $xUri = explode('/', $url);
        $currentModule=end($xUri);

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/perfil/index', [
            'title'     => 'Perfil de usuário',
            'perfilLink'=>self::getMenu($currentModule),
            'nome'      => $obUser->nome ?? '',
            'email'     => $obUser->email ?? '',
            'login'     => $obUser->login ?? '',
            'endereco'  => $obUser->endereco ?? '',
            'numero'    => $obUser->numero ?? '',
            'cep'       => $obUser->cep ?? '',
            'telefone'  => $obUser->telefone ?? '',
            'cpf'       => $obUser->cpf ?? '',
            'sexo'      => $obUser->sexo ?? '',
            'tipoConta' => $obUser->tipoConta ?? '',
            'senha'         => '************',
            'novaSenha'     => '************',
            'senhaConfirm'  => '************',
            'status'        => self::getStatus($request),
            'nivelOption'   => self::nivelOption(),
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Perfil > Univesp', $content, 'perfil');
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setPerfil($request)
    {

        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {

            $request->getRouter()->redirect('/user');
        }
        //POST VARS
        $postVars       = $request->getPostVars();
        $email          = $postVars['email'] ?? '';
        $nome           = $postVars['nome'] ?? '';
        $senha          = $postVars['senha'] ?? '';
        $novaSenha      = $postVars['novaSenha'] ?? '';
        $senhaConfirm   = $postVars['senhaConfirm'] ?? '';
        $login          = $postVars['login'] ?? '';
        $endereco       = $postVars['endereco'] ?? '';
        $numero         = $postVars['numero'] ?? '';
        $cep            = $postVars['cep'] ?? '';
        $telefone       = $postVars['telefone'] ?? '';
        $cpf            = $postVars['cpf'] ?? '';
        $tipoConta      = $postVars['tipoConta'] ?? '';
        $sexo           = $postVars['sexo'] ?? '';

        //SERIALIZA OS DADOS        
        $cpf = self::removeSpecialCaracter($cpf);
        $telefone = self::removeSpecialCaracter($telefone);
        $cep = self::removeSpecialCaracter($cep);

        //VALIDA QTD DE CARACTERES DO TELEFONE DO USUÁRIO
        if (strlen($telefone) <= 7) {
            $request->getRouter()->redirect('/user/perfil?status=invaliLenghtTel');
        } elseif (strlen($telefone) >= 14) {
            $request->getRouter()->redirect('/user/perfilstatus=invaliLenghtTel');
        }

        //VALIDA QTD DE CARACTERES DO CPF DO USUÁRIO
        if (strlen($cpf) != 11) {
            if (strlen($cpf) != 14) {
                //REDIRECIONA O USUÁRIO
                $request->getRouter()->redirect('/user/perfil?status=invaliLenghtDoc');
            }
        }

        //VALIDA QTD DE CARACTERES DO CEP DO USUÁRIO
        if (strlen($cep) != 8) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/user/perfil?status=cepLeng');
        }

        //VALIDA O E-MAIL DO USUÁRIO
        $obUserEmail =  User::getUserByEmail($email);
        if ($obUserEmail instanceof User && $obUserEmail->id != $id && $obUserEmail->id != $obUser->$id) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/user/perfil?status=emailDuplicated');
        }

        //VALIDA O LOGIN DO USUÁRIO
        $obUserlogin =  User::getUserByLogin($login);
        if ($obUserlogin instanceof User && $obUserlogin->id != $id && $obUserlogin->id != $obUser->$id) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/user/perfil?status=loginDuplicated');
        }

        //VALIDA SENHA CASO EXISTA VALOR
        if ($senha != '') {
            if (!password_verify($senha, $obUser->senha)) {
                //REDIRECIONA O USUÁRIO
                $request->getRouter()->redirect('/user/perfil?status=noEqualsPass');
            }

            if ($novaSenha != '' && $senhaConfirm != '') {
                if ($senhaConfirm !== $novaSenha) {
                    //REDIRECIONA O USUÁRIO
                    $request->getRouter()->redirect('/user/perfil?status=noEqualsPass');
                }else{
                    //ATUALIZA A INSTANCIA ALTERANDO A SENHA
                    $obUser->nome       = $nome;
                    $obUser->email      = $email;
                    $obUser->login      = $login;
                    $obUser->endereco   = $endereco;
                    $obUser->numero     = $numero;
                    $obUser->cep        = $cep;
                    $obUser->telefone   = $telefone;
                    $obUser->cpf        = $cpf;
                    $obUser->tipoConta  = $tipoConta;
                    $obUser->sexo       = $sexo;
                    $obUser->senha      = password_hash($novaSenha, PASSWORD_DEFAULT);
                    $obUser->atualizar();
                }
            }
        }else{
            //ATUALIZA A INSTANCIA SEM ALTERAR A SENHA
            $obUser->nome       = $nome;
            $obUser->email      = $email;
            $obUser->login      = $login;
            $obUser->endereco   = $endereco;
            $obUser->numero     = $numero;
            $obUser->cep        = $cep;
            $obUser->telefone   = $telefone;
            $obUser->cpf        = $cpf;
            $obUser->tipoConta  = $tipoConta;
            $obUser->sexo       = $sexo;
            $obUser->senha      = $obUser->senha;
            $obUser->atualizar();
        }
        $request->getRouter()->redirect('/user/perfil?status=updated');
    }

    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getPerfilProfi($request, $errorMessage = null)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = User::getUserById($id);

        $funcaoProfissional="";
        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/');
        }
        $obProf = profissional::getUserPById($id);
        $funcaoProfissional=$obProf->funcaoProfissional;

        //RECEBE O MODULO DO MENU DA URL
        $url=$request->getRouter()->getUri();
        $xUri = explode('/', $url);
        $currentModule=end($xUri);

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/perfilProfissional/index', [
            'title'     => 'Perfil Profissional',
            'perfilLink'=>self::getMenu($currentModule),
            'FuncaoProfissional' =>$funcaoProfissional ?? '',
            'horarios' => self::getTBHorarios(),
            'status'        => self::getStatus($request),
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Perfil > Univesp', $content, 'perfil');
    }

    /**
     * Método responsável por retornar a renderização do option
     * @param String $selecao
     * @return String
     */
    private static function getTBHorarios()
    {
        $dias = '';
        $dia = ['segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $diap = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'];
        for ($i = 0; $i < 7; $i++) {
            $dias .= View::render('user/modules/perfilProfissional/box', [
                'dia'       => $dia[$i],
                'diaP'      => $diap[$i],
                'optionIni' => self::getSelect('0800', $diap[$i] . 'Ini'),
                'optionIda' => self::getSelect('1200', $diap[$i] . 'Ida'),
                'optionVol' => self::getSelect('1300', $diap[$i] . 'Vol'),
                'optionFim' => self::getSelect('1800', $diap[$i] . 'Fim')
            ]);
        }
        return $dias;
    }

/**
     * Método responsável por retornar a renderização do select
     * @param String $selecao
     * @return string
     */
    private static function getSelect($selecao, $nome)
    {
        $options = '';
        $hora = 8;
        $min = '00';

        //for de hora
        for ($h = 0; $h < 15; $h++) {

            //for de min
            for ($m = 0; $m < 4; $m++) {

                //coloca 0 na frente do numero abaixo do 10
                if ($hora < 10) {
                    $value = '0' . $hora . $min;
                    $label = '0' . $hora . ':' . $min;
                } else {
                    $value = $hora . $min;
                    $label = $hora . ':' . $min;
                }

                //CONTROLE DO VALOR SELECIONADO
                if ($selecao == $value) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $min += 15;

                if ($hora == 22) {
                    break;
                }

                //RENDERIZA AS OPÇÕES DO SELECT
                $options .= View::render('user/modules/perfilProfissional/option', [
                    'value'     => $value,
                    'selected'  => $selected ? 'selected' : '',
                    'nome'      => $nome,
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
     * Método responsavel por renderizar a view do menu do perfil
     * @param string $currentModule
     * @return string
     */
    private static function getMenu($currentModule)
    {
        $nivel = $_SESSION['user']['usuario']['nivel'];
       
        //LINKS DO MENU
        $links = '';
        switch ($nivel) {
            case 1:
                //ITERA OS MÓDULOS
            foreach (self::$profModules as $hash => $module) {
                $links .= View::render('user/modules/perfil/menu/link', [
                    'label'     => $module['label'],
                    'link'      => $module['link'],
                    'current'   => $hash == $currentModule ? 'text-danger' : ''
                ]);
            }
                break;
            case 2:
                foreach (self::$comecialModules as $hash => $module) {
                    $links .= View::render('user/modules/perfil/menu/link', [
                        'label'     => $module['label'],
                        'link'      => $module['link'],
                        'current'   => $hash == $currentModule ? 'text-danger' : ''
                    ]);
                }
                break;
            default:
            $links='';
                break;
        }    
        //RETORNA A RENDERIZAÇÃO DO MENU
        return  $links;
    }

    /**
     * Método responsável por incrementar a opção nivel comercial para conta profissional
     * @param string $numero
     * @return int
     */
    private static function nivelOption()
    {
        //RETORNA A RENDERIZAÇÃO DO MENU
        return View::render('user/modules/perfil/nivelOption', []);
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
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário deletado com sucesso!');
                break;
            case 'emailDuplicated':
                return Alert::getError('O e-mail digitado já está sendo utilizado por outro usuário!');
                break;
            case 'loginDuplicated':
                return Alert::getError('O Login digitado já está sendo utilizado por outro usuário!');
                break;
            case 'noEqualsPass':
                return Alert::getError('nova Senha e confirmação de senha não confere!');
                break;
            case 'LogDuplicated':
                return Alert::getError('O Login digitado já está sendo utilizado por outro usuário!');
                break;
            case 'invaliLenghtDoc':
                return Alert::getError('Quantidade de caracteres Invalidos do campo CPF/CNPJ!');
                break;
            case 'cepLeng':
                return Alert::getError('Quantidade de caracteres Invalidos do campo CEP!');
                break;
            case 'invaliLenghtTel':
                return Alert::getError('Quantidade de caracteres Invalidos do campo Telefone!');
                break;
            case 'workCreated':
                return Alert::getSuccess('Conta Profissional criado com sucesso!');
                break;
            case 'error':
                return Alert::getSuccess('Não foi possivel criar Conta Profissional!');
                break;
        }
    }
}
