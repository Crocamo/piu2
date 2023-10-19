<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\profissional;
use \App\model\Entity\Horarios;

class Cadastro extends Page
{
    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getNewAccount($request, $errorMessage = null)
    {

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/cadastrar/index', [
            'title'     => 'Cadastrar usuário',
            'nome'      => '',
            'email'     => '',
            'login'     => '',
            'senha'     => '',
            'senhaConfirm' => '',
            'endereco'  => '',
            'numero'    => '',
            'cep'       => '',
            'telefone'  => '',
            'cpf'       => '',
            'status'    => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Cadastro > Univesp', $content);
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     * @param Request $request
     * @return string
     */
    public static function setNewAccount($request)
    {
        //POST VARS
        $postVars       = $request->getPostVars();
        $email          = $postVars['email'] ?? '';
        $nome           = $postVars['nome'] ?? '';
        $senha          = $postVars['senha'] ?? '';
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
            $request->getRouter()->redirect('/cadastro?status=invaliLenghtTel');
        } elseif (strlen($telefone) >= 14) {
            $request->getRouter()->redirect('/cadastro?status=invaliLenghtTel');
        }

        //VALIDA QTD DE CARACTERES DO CPF DO USUÁRIO
        if (strlen($cpf) != 11) {
            if (strlen($cpf) != 14) {
                //REDIRECIONA O USUÁRIO
                $request->getRouter()->redirect('/cadastro?status=invaliLenghtDoc');
            }
        }

        //VALIDA QTD DE CARACTERES DO CEP DO USUÁRIO
        if (strlen($cep) != 8) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/cadastro?status=cepLeng');
        }

        //VALIDA O E-MAIL DO USUÁRIO
        $obUserEmail =  User::getUserByEmail($email);

        if ($obUserEmail instanceof User) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/cadastro?status=duplicated');
        }

        //VALIDA O LOGIN DO USUÁRIO
        $obUserLogin =  User::getUserByLogin($login);

        if ($obUserLogin instanceof User) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/cadastro?status=LogDuplicated');
        }
        //ATENÇÃO ESTA GRAVANDO NUMERO DO TEL NO LUGAR DO CPF
        if ($senha === $senhaConfirm) {
            //NOVA INSTANCIA DE USUÁRIOS
            $obUser             = new User;
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
            $obUser->senha      = password_hash($senha, PASSWORD_DEFAULT);
            $obUser->cadastrar();

            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/');
        } else {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/cadastro?status=noEqualsPass');
        }
    }

    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getNewProfissionalAccount($request, $errorMessage = null)
    {

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('user/modules/cadastrarProfissional/index', [
            'title'             => 'Cadastro profissional',
            'FuncaoProfissional' => '',
            'horarios' => self::getTBHorarios(),
            'status'    => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Cadastro Profissional > Univesp', $content);
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     * @param Request $request
     * @return string
     */
    public static function setNewProfissionalAccount($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        
        $seg[0]   = $postVars['segIni'] ?? '';
        $seg[1]   = $postVars['segIda'] ?? '';
        $seg[2]   = $postVars['segVol'] ?? '';
        $seg[3]   = $postVars['segFim'] ?? '';

        $ter[0]   = $postVars['terIni'] ?? '';
        $ter[1]   = $postVars['terIda'] ?? '';
        $ter[2]   = $postVars['terVol'] ?? '';
        $ter[3]   = $postVars['terFim'] ?? '';

        $qua[0]   = $postVars['quaIni'] ?? '';
        $qua[1]   = $postVars['quaIda'] ?? '';
        $qua[2]   = $postVars['quaVol'] ?? '';
        $qua[3]   = $postVars['quaFim'] ?? '';

        $qui[0]   = $postVars['quiIni'] ?? '';
        $qui[1]   = $postVars['quiIda'] ?? '';
        $qui[2]   = $postVars['quiVol'] ?? '';
        $qui[3]   = $postVars['quiFim'] ?? '';

        $sex[0]   = $postVars['sexIni'] ?? '';
        $sex[1]   = $postVars['sexIda'] ?? '';
        $sex[2]   = $postVars['sexVol'] ?? '';
        $sex[3]   = $postVars['sexFim'] ?? '';

        $sab[0]   = $postVars['sabIni'] ?? '';
        $sab[1]   = $postVars['sabIda'] ?? '';
        $sab[2]   = $postVars['sabVol'] ?? '';
        $sab[3]   = $postVars['sabFim'] ?? '';

        $dom[0]   = $postVars['domIni'] ?? '';
        $dom[1]   = $postVars['domIda'] ?? '';
        $dom[2]   = $postVars['domVol'] ?? '';
        $dom[3]   = $postVars['domFim'] ?? '';

        $funcao   = $postVars['FuncaoProfissional'] ?? '';
        $feriadoNacio  = ($postVars['feriadoNacio'] ?? '0');
        $feriadoEstad  = $postVars['feriadoEstad'] ?? '0';

        $segunda=self::timeCombine($seg);
        $terca  =self::timeCombine($ter);
        $quarta =self::timeCombine($qua);
        $quinta =self::timeCombine($qui);
        $sexta  =self::timeCombine($sex);
        $sabado =self::timeCombine($sab);
        $domingo=self::timeCombine($dom);

        $idUser=$_SESSION['user']['usuario']['id'];

        $obProfi = Profissional::getUserPById($idUser);
        
        //VALIDA SE USUARIO É PROFISSIONAL CADASTRADO
        if (!$obProfi instanceof Profissional) {
            //SALVA HORARIOS DE TRABALHO DO PROFISSIONAL
            $obHorario          = new Horarios;
            $obHorario->segunda =$segunda;
            $obHorario->terca   =$terca;
            $obHorario->quarta  =$quarta;
            $obHorario->quinta  =$quinta;
            $obHorario->sexta   =$sexta;
            $obHorario->sabado  =$sabado;
            $obHorario->domingo =$domingo;
            $obHorario->feriadoEstadual  =$feriadoNacio;
            $obHorario->feriadoNacional  =$feriadoEstad;
            $obHorario->cadastrar();

            // SALVA OS DADOS DO PROFISSIONAL
            $obProfi                = new profissional;
            $obProfi->idUser        =$idUser;
            $obProfi->idHorarios    =$obHorario->idHorarios;
            $obProfi->funcaoProfissional=$funcao;
            $obProfi->cadastrar();
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/cadastroProfissional?status=workCreated');
        } else {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/cadastro?status=error');
        }
    }

    /**
     * Método responsável por unificar os horarios em uma variavel diarios para armazenamento.
     * @param Array $array
     * @return String
     */
    private static function timeCombine($array){
        $horarios ='';
        for ($d = 0; $d < 4; $d++) {
            $horarios .=$array[$d].'/$/';
        }
        return $horarios;
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
            $dias .= View::render('user/modules/cadastrarProfissional/box', [
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
                $options .= View::render('user/modules/cadastrarProfissional/option', [
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
            case 'duplicated':
                return Alert::getError('O e-mail digitado já está sendo utilizado por outro usuário!');
                break;
            case 'noEqualsPass':
                return Alert::getError('Senha não confere!');
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
