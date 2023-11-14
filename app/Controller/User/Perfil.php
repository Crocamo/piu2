<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\Profissional;
use \App\model\Entity\Horarios;

class Perfil extends Page
{
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
                    $obUser->senha = password_hash($novaSenha, PASSWORD_DEFAULT);
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

        $obValido = self::validaInstancia($id, "user");
        $obProf = $obValido ? $obValido : $request->getRouter()->redirect('/');

        $obValido = self::validaInstancia($id, "prof");
        $obProf = $obValido ? $obValido : $request->getRouter()->redirect('/cadastroProfissional');


        $idHorario = $obProf->idHorarios;
        $obHorario = Horarios::getTBHorariosById($idHorario);
        $est = $obHorario->feriadoEstadual ? 'checked' : '';
        $nac = $obHorario->feriadoNacional ? 'checked' : '';

        $day = $obHorario->semana;
        $dia = explode(',', $day);
        $hour = $obHorario->horario;
        $hora = explode('/$/', $hour);

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/perfilProfissional/index', [
            'title'         => 'Perfil Profissional',
            'FuncaoProfissional' => $obProf->funcaoProfissional ?? '',
            'seg'           => $dia[0] != 0 ? 'checked' : '',
            'ter'           => $dia[1] != 0 ? 'checked' : '',
            'qua'           => $dia[2] != 0 ? 'checked' : '',
            'qui'           => $dia[3] != 0 ? 'checked' : '',
            'sex'           => $dia[4] != 0 ? 'checked' : '',
            'sab'           => $dia[5] != 0 ? 'checked' : '',
            'dom'           => $dia[6] != 0 ? 'checked' : '',
            'optionIni' => self::getSelect($hora[0],  'Ini'),
            'optionIda' => self::getSelect($hora[1],  'Ida'),
            'optionVol' => self::getSelect($hora[2],  'Vol'),
            'optionFim' => self::getSelect($hora[3],  'Fim'),
            'feriadoNacio'  => $est,
            'feriadoEstad'  => $nac,
            //'horarios'      => //self::getTBHorarios($idHorario),
            'status'        => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Perfil > Univesp', $content, 'perfilProfissional');
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

        $obValido = self::validaInstancia($id, "prof");
        $obProf = $obValido ? $obValido : $request->getRouter()->redirect('/cadastroProfissional');

        $idHorario = $obProf->idHorarios;
        $obHorario = Horarios::getTBHorariosById($idHorario);
        $horario = $obHorario->horario;
        $horas = explode('/$/', $horario);


        $Ini = $horas[0];
        $ida = $horas[1];
        $vol = $horas[2];
        $fim = $horas[3];

        $hr[0]   = $postVars['Ini'] ?? $Ini;
        $hr[1]   = $postVars['Ida'] ?? $ida;
        $hr[2]   = $postVars['Vol'] ?? $vol;
        $hr[3]   = $postVars['Fim'] ?? $fim;

        for ($h = 1; $h < count($hr); $h++) {
            if ($hr[$h] < $hr[$h - 1]) {
                //REDIRECIONA O USUÁRIO PARA O CADASTRO DE PROFISSIONAL
                $request->getRouter()->redirect('/user/perfilProfissional?status=InvalidDateTime');
            }
        }


        $semana = $obHorario->semana;
        $dias = explode('/$/', $semana);

        $week[0] = $postVars['seg'] ?? $dias[0];
        $week[1] = $postVars['ter'] ?? $dias[1];
        $week[2] = $postVars['qua'] ?? $dias[2];
        $week[3] = $postVars['qui'] ?? $dias[3];
        $week[4] = $postVars['sex'] ?? $dias[4];
        $week[5] = $postVars['sab'] ?? $dias[5];
        $week[6] = $postVars['dom'] ?? $dias[6];

        $obHorario->semana = self::dayCombine($week);
        $obHorario->horario = self::timeCombine($hr);

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
            if ($d != 3) {
                $horarios .= $array[$d] . '/$/';
            } else {
                $horarios .= $array[$d];
            }
        }
        return $horarios;
    }

    private static function dayCombine($array)
    {
        $days = '';
        for ($d = 0; $d < 7; $d++) {
            if ($d != 6) {
                $days .= $array[$d] . ',';
            } else {
                $days .= $array[$d];
            }
        }
        return $days;
    }

    /**
     * Método responsável por retornar a renderização do option
     * @param String $selecao
     * @return String
     */
    private static function getTBHorarios($id)
    {

        $obHorario = Horarios::getTBHorariosById($id);

        $dias = '';
        $dia = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'];
        $diap = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'];

        for ($i = 0; $i < 1; $i++) {
            $week = $dia[$i];
            $day = $obHorario->$week;
            $hour = explode('/$/', $day);
            $Ini = $hour[0];
            $ida = $hour[1];
            $vol = $hour[2];
            $fim = $hour[3];

            $dias .= View::render('user/modules/perfilProfissional/box', [
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
    private static function getSelect($selecao)
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
                $obEmpre = EntityComercio::getComercioByIdProfissional($obProf->idProfissional);
                //VALIDA A INSTANCIA COMERCIAL
                if (!$obEmpre instanceof EntityComercio) {
                    return false;
                }
                return $obEmpre;

            case 'nome':

                //RECEBE DADOS DE USUÁRIO DO PROFISSIONAL
                $obUser = User::getUserByLogin($nome['nome']);

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
            case 'InvalidDateTime':
                return Alert::getSuccess('Horarios incompativeis!');
                break;
        }
    }
}
