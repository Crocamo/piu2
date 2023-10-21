<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\Profissional;
use \App\model\Entity\Horarios;

class Perfil extends Page
{

    /**
     * Módulos disponíveis no painel
     * @var array
     */
    private static $profModules = [
        'perfil' => [
            'label' =>  'Perfil de Usuário',
            'link'  =>  URL . '/user/perfil'
        ],
        'perfilProfissional' => [
            'label' =>  'Perfil Profissional',
            'link'  =>  URL . '/user/perfilProfissional'
        ],
        'Servicos' => [
            'label' =>  'Gerenciar Serviços',
            'link'  =>  URL . '/user/servicos'
        ],
        'criarComercial' => [
            'label' =>  'Criar Comercio!',
            'link'  =>  URL . '/user/criarComercial'
        ]
    ];

    /**
     * Módulos disponíveis no painel
     * @var array
     */
    private static $comecialModules = [
        'perfil' => [
            'label' =>  'perfil de Usuário',
            'link'  =>  URL . '/user/perfil'
        ],
        'perfilProfissional' => [
            'label' =>  'Perfil Profissional',
            'link'  =>  URL . '/user/perfilProfissional'
        ],
        'Servicos' => [
            'label' =>  'Gerenciar Serviços',
            'link'  =>  URL . '/user/servicos'
        ],
        'perfilComercial' => [
            'label' =>  'Adminstrar Comércio',
            'link'  =>  URL . '/user/perfilProfissional'
        ]
    
    ];

    /**
     * Método responsável por retornar a renderização da página de perfil
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
        $url = $request->getRouter()->getUri();
        $xUri = explode('/', $url);
        $currentModule = end($xUri);

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/perfil/index', [
            'title'     => 'Perfil de usuário',
            'perfilLink' => self::getMenu($currentModule),
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
        $email          = $postVars['email']        ?? '';
        $nome           = $postVars['nome']         ?? '';
        $senha          = $postVars['senha']        ?? '';
        $novaSenha      = $postVars['novaSenha']    ?? '';
        $senhaConfirm   = $postVars['senhaConfirm'] ?? '';
        $login          = $postVars['login']        ?? '';
        $endereco       = $postVars['endereco']     ?? '';
        $numero         = $postVars['numero']       ?? '';
        $cep            = $postVars['cep']          ?? '';
        $telefone       = $postVars['telefone']     ?? '';
        $doc            = $postVars['cpf']          ?? '';
        $tipoConta      = $postVars['tipoConta']    ?? '';
        $sexo           = $postVars['sexo']         ?? '';

        //SERIALIZA OS DADOS        
        $doc        = self::removeSpecialCaracter($doc);
        $telefone   = self::removeSpecialCaracter($telefone);
        $cep        = self::removeSpecialCaracter($cep);

        //VALIDA QTD DE CARACTERES DO TELEFONE DO USUÁRIO
        if (strlen($telefone) <= 7) {
            $request->getRouter()->redirect('/user/perfil?status=invaliLenghtTel');
        } elseif (strlen($telefone) >= 14) {
            $request->getRouter()->redirect('/user/perfilstatus=invaliLenghtTel');
        }

        //VALIDA QTD DE CARACTERES DO CPF DO USUÁRIO
        if (strlen($doc) != 11) {
            if (strlen($doc) != 14) {
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
        if ($obUser instanceof User && $obUser->id != $id) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/user/perfil?status=emailDuplicated');
        }

        //VALIDA O LOGIN DO USUÁRIO
        if ($obUser instanceof User && $obUser->id != $id) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/user/perfil?status=loginDuplicated');
        }

        //VALIDA SENHA CASO EXISTA VALOR
        if ($senha != '') {
            if (!password_verify($senha, $obUser->senha)) {
                //REDIRECIONA O USUÁRIO
                $request->getRouter()->redirect('/user/perfil?status=errorPass');
            }
            if ($novaSenha != '' && $senhaConfirm != '') {
                if ($senhaConfirm !== $novaSenha) {
                    //REDIRECIONA O USUÁRIO
                    $request->getRouter()->redirect('/user/perfil?status=noEqualsPass');
                } else {
                    $obUser->senha= password_hash($novaSenha, PASSWORD_DEFAULT);
                }
            }
        }
         //ATUALIZA A INSTANCIA 
        $obUser->nome       = $nome;
        $obUser->email      = $email;
        $obUser->login      = $login;
        $obUser->endereco   = $endereco;
        $obUser->numero     = $numero;
        $obUser->cep        = $cep;
        $obUser->telefone   = $telefone;
        $obUser->cpf        = $doc;
        $obUser->tipoConta  = $tipoConta;
        $obUser->sexo       = $sexo;
        $obUser->senha      = $obUser->senha;
        $obUser->atualizar();
        
        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/user/perfil?status=updated');
    }

    /**
     * Método responsável por retornar a renderização da página de perfil
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

        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/');
        }
        $obProf = Profissional::getUserPById($id);
        if(!$obProf instanceof profissional){
            //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
            $request->getRouter()->redirect('/cadastroProfissional');
        }

        $idHorario=$obProf->idHorarios;
        $obHorario = Horarios::getTBHorariosById($idHorario);
        $est=$obHorario->feriadoEstadual ? 'checked':'';
        $nac=$obHorario->feriadoNacional ? 'checked':'';
        
        //RECEBE O MODULO DO MENU DA URL
        $url = $request->getRouter()->getUri();
        $xUri = explode('/', $url);
        $currentModule = end($xUri);

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/perfilProfissional/index', [
            'title'     => 'Perfil Profissional',
            'perfilLink' => self::getMenu($currentModule),
            'FuncaoProfissional' => $obProf->funcaoProfissional ?? '',
            'feriadoNacio'=> $est,
            'feriadoEstad'=> $nac,
            'horarios' => self::getTBHorarios($idHorario),
            'status'        => self::getStatus($request),
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Perfil > Univesp', $content, 'perfil');
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     * @param Request $request
     * @return string
     */
    public static function setPerfilProfi($request)
    {
        //**ATENÇÃO CRIAR CONTROLE DE HORARIOS COMPLICADO ALGUEM VOLTAR DO ALMOÇO ANTES DE INICIAR O EXPEDIENTE */
        
        //POST VARS
        $postVars = $request->getPostVars();

        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        $obProf = Profissional::getUserPById($id);
        if(!$obProf instanceof profissional){
            //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
            $request->getRouter()->redirect('/cadastroProfissional');
        }

        $idHorario=$obProf->idHorarios;
        $obHorario = Horarios::getTBHorariosById($idHorario);

        $dia = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $diap = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'];
        
        for ($i = 0; $i < 7; $i++) 
        {
            $week=$dia[$i];
            $day=$obHorario->$week;
            $hour = explode('/$/', $day);
            
            $Ini=$hour[0];
            $ida=$hour[1];
            $vol=$hour[2];
            $fim=$hour[3];

            $hr[0]   = $postVars[$diap[$i].'Ini'] ?? $Ini;
            $hr[1]   = $postVars[$diap[$i].'Ida'] ?? $ida;
            $hr[2]   = $postVars[$diap[$i].'Vol'] ?? $vol;
            $hr[3]   = $postVars[$diap[$i].'Fim'] ?? $fim;
            $semana[$i]= self::timeCombine($hr);

            $obHorario->$week=$semana[$i];
        }
        $obProf->funcaoProfissional = $postVars['FuncaoProfissional'] ?? $obProf->funcaoProfissional;
        
        $feriadoNacio  = $postVars['feriadoNacio'] ?? '0';
        $feriadoEstad  = $postVars['feriadoEstad'] ?? '0';
        $obHorario->feriadoEstadual  = $feriadoNacio;
        $obHorario->feriadoNacional  = $feriadoEstad;
        $obHorario->atualizar();
        $obProf->atualizar();
        //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
        $request->getRouter()->redirect('/user/perfilProfissional?status=updated');
    }

    private static function timeCombine($array)
    {
        $horarios = '';
        for ($d = 0; $d < 4; $d++) {
            $horarios .= $array[$d] . '/$/';
        }
        return $horarios;
    }

    /**
     * Método responsável por retornar a renderização do option
     * @param String $selecao
     * @return String
     */
    private static function getTBHorarios($id)
    {

        $obHorario= Horarios::getTBHorariosById($id);
        $dias = '';
        $dia = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $diap = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'];
        
        for ($i = 0; $i < 7; $i++) {
            $week=$dia[$i];
            $day=$obHorario->$week;
            $hour = explode('/$/', $day);
            $Ini=$hour[0];
            $ida=$hour[1];
            $vol=$hour[2];
            $fim=$hour[3];

            $dias .= View::render('user/modules/perfilProfissional/box', [
                'dia'       => $dia[$i],
                'diaP'      => $diap[$i],
                'optionIni' => self::getSelect($Ini, $diap[$i] . 'Ini'),
                'optionIda' => self::getSelect($ida, $diap[$i] . 'Ida'),
                'optionVol' => self::getSelect($vol, $diap[$i] . 'Vol'),
                'optionFim' => self::getSelect($fim, $diap[$i] . 'Fim')
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
                $links = '';
                break;
        }
        //RETORNA A RENDERIZAÇÃO DO MENU
        return  $links;
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
            case 'errorPass':
                return Alert::getError('Senha atual esta incorreta!');
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
