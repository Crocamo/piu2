<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\model\Entity\User;
use \App\model\Entity\Servicos;
use \App\model\Entity\Profissional;
use \App\model\Entity\ProfListUser;
use \App\model\Entity\Agenda;
use \App\model\Entity\Comercio;

class Agendar extends Page
{
    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function getAgendar($request, $disp = NULL)
    {
        $idServ = 0;
        $helper = '';
        $disp != NULL ?: '';
        if ($disp != NULL) {
            $helper = self::getAgenda($request, $disp);
            $help = explode('_', $disp);
            $idServ  =   $help[1]; //id do serviço escolhido
        }
        //CONTEÚDO DA AGENDA
        $content = View::render('user/modules/agendar/index', [
            'title'     => 'Agendar',
            'boxS'      => self::getBox($idServ),
            'status'    => self::getStatus($request),
            'Agenda'    => $helper
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'agendar');
    }

    private static function getAgenda($request, $disp, $get=NULL)
    {
        
        $help = explode('_', $disp);
        $servicoID  =   $help[1]; //id do serviço escolhido
        $ReqData    =   $help[0]; //data escolhida 

        $obService      = Servicos::getServiceById($servicoID); //RECEBE OBJETO DO SERVIÇO ESCOLHIDO 
        $obProf         = Profissional::getProfissionalById($obService->idProfissional);
        $obProfUser     = User::getUserById($obProf->idUser);

        $data = explode('-', $ReqData);

        $dia = $data[2];
        $mes = $data[1];
        $ano = $data[0];
        $data = $dia . '/' . $mes . '/' . $ano . '. Profissional: ' . $obProfUser->nome . '. Valor Médio R$:' . $obService->valorServ;

        $itens = View::render('user/modules/agendar/agenda', [
            'itensAgendados' => self::getItensAgendados($request, $servicoID, $ReqData, $get),
            'date'           => $data
        ]);

        return $itens;
    }

    private static function getItensAgendados($request, $servicoID, $ReqData, $get=NULL ,$idAgenda=NULL)
    {
        $itens = '';

        //se serviço vazio redireciona
        if (($servicoID == '0' || $servicoID == '')) {
            $request->getRouter()->redirect('/user/agendar/' . $ReqData . '_' . $servicoID . '/new?status=InvalidSelected');
        }

        //se data vazia redireciona
        if ($servicoID != '') {
            if ($ReqData == '') {
                $request->getRouter()->redirect('/user/agendar/' . $ReqData . '_' . $servicoID . '/new?status=InvalidSelectedDate');
            }
        }

        $obService      = Servicos::getServiceById($servicoID); //RECEBE OBJETO DO SERVIÇO ESCOLHIDO 
        $obServices     = []; //RECEBE OBJETOS DOS SERVIÇOS DO PROFISSIONAL 
        $agendamentoDia = []; //RECEBE LISTA DE HORARIOS DOS SERVIÇOS DO PROFISSIONAL PARA DIA ESCOLHIDO 
        $obProf         = Profissional::getProfissionalById($obService->idProfissional);
        $obProfUser     = User::getUserById($obProf->idUser);

        //SERVIÇOS DO PROFISSIONAL
        $obProfServ = Servicos::getservices('idProfissional ="' . $obService->idProfissional . '" AND status =" 0"');
        $s = 0;
        while ($servicos = $obProfServ->fetchObject(Servicos::class)) {
            $obServices[$s] = $servicos;
            $s += 1;
        }

        //LISTA DE HORARIOS DOS SERVIÇOS AGENDAOS 
        $obAgendaList = Agenda::getAgendas('idProfissional ="' . $obService->idProfissional . '"  AND status ="1"', 'agendaData ASC');
        $p = 0;
        while ($obAgenda = $obAgendaList->fetchObject(Agenda::class)) {
            if ($ReqData == $obAgenda->agendaData) {
                $agendamentoDia[$p] = $obAgenda;
                $p++;
            }
        }

        //horarios do dia
        $horario[] = self::getHorario();

        $agenda = array_fill(0, count($horario[0]), 0);

        for ($ag = 0; $ag < count($agendamentoDia); $ag++) {

            $tempService = Servicos::getServiceById($agendamentoDia[$ag]->idServico);
            $tempoMedioServ =  $tempService->tempoMedioServ;
            $tempSlot = self::getSlot($tempoMedioServ);

            $tempMin = substr($agendamentoDia[$ag]->agendaHora, -2, 2);
            $helpperHora = explode($tempMin, $agendamentoDia[$ag]->agendaHora);
            $tempHora = ($helpperHora[0] < 10) ? '0' . $helpperHora[0] . ':' . $tempMin : $helpperHora[0] . ':' . $tempMin;

            for ($a = 0; $a < count($horario[0]);) {
                if (!isset($agenda[$a])) {
                    $agenda[$a] = 0;
                }

                if ($agenda[$a] == 0 || $agenda[$a] == null || $agenda[$a] == '') {
                    if ($horario[0][$a] == $tempHora) {
                        for ($t = 0; $t < $tempSlot; $t++) {
                            $agenda[$a + $t] = 1;
                        }
                        $a += $tempSlot;
                    } else {
                        $a++;
                    }
                } else {
                    $a++;
                }
            }
        }

        $countSlot = 0;
        $slot = self::getSlot($obService->tempoMedioServ);
        for ($i = 0; $i < count($agenda) - $slot; $i++) {

            if ($agenda[$i] == 0) { //disponivel

                for ($x = $i; $x <= $i + $slot; $x++) {

                    if ($agenda[$x] == 1) {
                        $countSlot = 0;
                        break;
                    }

                    if ($countSlot == $slot) {

                        $agenda[$i] = 2;
                        $countSlot = 0;
                        break;
                    }
                    $countSlot++;
                }
            } else {
                $countSlot = 0;
            }
        }

        for ($i = 0; $i < count($agenda); $i++) {
            $hora = 0;

            if ($agenda[$i] == 2) {
                if ($i % 2 == 0) {
                    $itens .= '<tr>';
                } else {
                    $itens .= '<th></th>';
                }
                $hora = $horario[0][$i];
                
                $diaDataServico = '';

                switch ($get) {
                    case 'reagendar':
                        $diaDataServico = $ReqData . '_' . $idAgenda . '_' . $hora . '/edit';
                        break;
                    default:
                        $diaDataServico = $ReqData . '_' . $servicoID . '_' . $hora . '/Confirm';
                        break;
                }

                $itens .= View::render('user/modules/agendar/itemAgendar', [
                    'Hora'              => $hora,
                    'Disponibilidade'   => 'Disponivel',
                    'diaDataServico'    =>  $diaDataServico
                ]);

                if ($i % 2 != 0) {
                    $itens .= '</tr>';
                }
            }
        }

        return $itens;
    }

    private static function getBox($idServ = null)
    {
        $dataVal = date('Y-m-d');
        $content = View::render('user/modules/agendar/box', [
            'filtro'     => 'Serviço',
            'filtroName' => 'Servico',
            'option'    => self::getOption($idServ),
            'onchange'  => '',
            'dataVal'   => $dataVal
        ]);
        return $content;
    }

    /**
     * Método responsável receber os dados necessarios para renderizar os selects da página
     * @return Array $agendamento
     */
    private static function getOption($idServ = null)
    {
        //RECEBE ID DO USUARIO LOGADO
        $id = $_SESSION['user']['usuario']['id'];

        // OBTÉM A LISTA DE PROFISSIONAIS LIGADOS AO USUÁRIO
        $obPrefProf = ProfListUser::getUsers('idUser =' . $id);

        $options = "<option value='0' name='servico' class='0' selected>Selecionar Serviço</option>";
        //RENDERIZA O ITEM
        while ($obProfList = $obPrefProf->fetchObject(ProfListUser::class)) {

            if ($obProfList instanceof ProfListUser) {
                $obProf = Profissional::getProfissionalById($obProfList->idProf);

                $obProfUser = User::getUserById($obProf->idUser);
                $obProfServ = Servicos::getservices('idProfissional ="' . $obProf->idProfissional . '" AND status =" 0"');

                while ($obService = $obProfServ->fetchObject(Servicos::class)) {

                    $label = $obService->nomeServ . ' (' . $obProfUser->nome . ')';
                    $selected = $obService->idServ == $idServ ? 'selected' : '';
                    $options .= View::render('user/modules/agendar/option', [
                        'value' => $obService->idServ,
                        'name'  => $obProfUser->nome,
                        'class' => $obProfUser->nome,
                        'label' => $label,
                        'selected' => $selected
                    ]);
                }
            }
        }

        return $options;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     * @param Request
     * @return string
     */
    public static function setAgendar($request)
    {
        $selectDate = $request->getPostVars();

        $servicoID =   $selectDate['Servico']     ?? ''; //id do serviço escolhido
        $ReqData    =   $selectDate['DataServico'] ?? ''; //data escolhida 2023-11-10

        //se serviço vazio redireciona
        if (($servicoID == '0' || $servicoID == '')) {
            $request->getRouter()->redirect('/user/agendar/' . $ReqData . '_' . $servicoID . '/new?status=InvalidSelected');
        }

        //se data vazia redireciona
        if ($servicoID != '') {
            if ($ReqData == '') {
                $request->getRouter()->redirect('/user/agendar/' . $selectDate . '/new?status=InvalidSelectedDate');
            }
        }

        /*
        $obService  = Servicos::getServiceById($servicoID); //RECEBE OBJETO DO SERVIÇO ESCOLHIDO 
        $Profi      = Profissional::getProfissionalById($obService->idProfissional);
        $obServices  = []; //RECEBE OBJETOS DOS SERVIÇOS DO PROFISSIONAL 
        $agendamentoDia = []; //RECEBE LISTA DE HORARIOS DOS SERVIÇOS DO PROFISSIONAL PARA DIA ESCOLHIDO 

        //SERVIÇOS DO PROFISSIONAL
        $obProfServ = Servicos::getservices('idProfissional ="' . $obService->idProfissional . '" AND status =" 0"');
        $s = 0;
        while ($servicos = $obProfServ->fetchObject(Servicos::class)) {
            $obServices[$s] = $servicos;
            $s += 1;
        }

        //LISTA DE HORARIOS DOS SERVIÇOS AGENDAOS 
        $obAgendaList = Agenda::getAgendas('idProfissional ="' . $obService->idProfissional . '"  AND status ="1"', 'agendaData ASC');
        $p = 0;
        while ($obAgenda = $obAgendaList->fetchObject(Agenda::class)) {
            if ($ReqData == $obAgenda->agendaData) {
                $agendamentoDia[$p] = $obAgenda;
            }
            $p++;
        }

        //horarios do dia
        $horario[] = self::getHorario();


        $agenda = [];

        for ($ag = 0; $ag < count($agendamentoDia); $ag++) {
            $tempService = Servicos::getServiceById($agendamentoDia[$ag]->idServico);
            $tempoMedioServ =  $tempService->tempoMedioServ;
            $tempSlot = self::getSlot($tempoMedioServ);

            $tempMin = substr($agendamentoDia[$ag]->agendaHora, -2, 2);
            $helpperHora = explode($tempMin, $agendamentoDia[$ag]->agendaHora);
            $tempHora = ($helpperHora[0] < 10) ? '0' . $helpperHora[0] . ':' . $tempMin : $helpperHora[0] . ':' . $tempMin;

            for ($a = 0; $a < count($horario[0]);) {
                if (!isset($agenda[$a])) {
                    $agenda[$a] = [
                        'hora' => $horario[0][$a],
                        'disp' => 0
                    ];
                }

                if ($agenda[$a]['disp'] == 0 || $agenda[$a]['disp'] == null || $agenda[$a]['disp'] == '') {
                    if ($horario[0][$a] == $tempHora) {
                        for ($t = 0; $t < $tempSlot; $t++) {
                            $agenda[$a + $t] = [
                                'hora' => $horario[0][$a + $t],
                                'disp' => 1
                            ];
                        }
                        $a += $tempSlot;
                    } else {
                        $agenda[$a]['hora'] = $horario[0][$a];
                        $a++;
                    }
                } else {
                    $a++;
                }
            }
        }

        $slots_disponiveis = array_fill(0, count($agenda), 0); // Inicializa array com zeros

        $cont_disponiveis = 0;

        for ($a = 0; $a < count($agenda); $a++) {
            if ($agenda[$a]['disp'] == 0) {
                $cont_disponiveis++;
            } else {
                $cont_disponiveis = 0;
            }

            if ($cont_disponiveis == $tempSlot) {
                for ($i = 0; $i < $tempSlot; $i++) {
                    $slots_disponiveis[$a - $i] = 1;
                }
                $cont_disponiveis = 0;
            }
        }
        */
        // Exibir slots disponíveis como string
        $slots_disponiveis_string = $ReqData . '_' . $servicoID;

        $request->getRouter()->redirect('/user/agendar/' . $slots_disponiveis_string . '/new');
    }

    /**
     * Método responsável gravar o formulário de conclusão de agendamento de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setConfirmService($request, $dataIdHora)
    {
        $help = explode('_', $dataIdHora);
        $data = $help[0];
        $hora = intval(self::removeSpecialCaracter($help[2]));
        $idServ = $help[1];
        //RECEBE ID DO USUARIO LOGADO
        $idUser = $_SESSION['user']['usuario']['id'];
        $obService = Servicos::getServiceById($idServ);
        $obPro = Profissional::getProfissionalById($obService->idProfissional);
        $obComer = Comercio::getComercioByIdProfissional($obService->idProfissional);
        $idComercio = $obComer->idEmpre ?? '';

        $obAgenda = new Agenda;
        $obAgenda->idUser           = $idUser;
        $obAgenda->idServico        = $idServ;
        $obAgenda->idProfissional   = $obPro->idProfissional;
        $obAgenda->idComercio       = $idComercio;
        $obAgenda->agendaHora       = $hora;
        $obAgenda->agendaData       = $data;
        $obAgenda->status           = 1;
        $obAgenda->motivo           = '';

        $obAgenda->cadastrar();

        $request->getRouter()->redirect('/user');
    }

    /**
     * Método responsável por retornar o formulário de conclusão de agendamento de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getConfirmService($request, $dataIdHora)
    {
        $help = explode('_', $dataIdHora);
        $data = $help[0];
        $hora = $help[2];
        $idServ = $help[1];
        $obService = Servicos::getServiceById($idServ);

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('user/modules/agendar/confirmaAgenda', [
            'servico'   => $obService->nomeServ,
            'hora'      => $hora,
            'dia'       => $data
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Concluir Agendamento', $content, 'agendar');
    }









    /**
     * Método responsável gravar o formulário de conclusão de agendamento de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setEditService($request, $dataIdHora)
    {
        echo '<pre>';
        print_r($dataIdHora);
        echo '</pre>'; exit; 
      /*  $help = explode('_', $dataIdHora);
        $data = $help[0];
        $hora = intval(self::removeSpecialCaracter($help[2]));
        $idServ = $help[1];
        //RECEBE ID DO USUARIO LOGADO
        $idUser = $_SESSION['user']['usuario']['id'];
        $obService = Servicos::getServiceById($idServ);
        $obPro = Profissional::getProfissionalById($obService->idProfissional);
        $obComer = Comercio::getComercioByIdProfissional($obService->idProfissional);
        $idComercio = $obComer->idEmpre ?? '';

        $obAgenda = new Agenda;
        $obAgenda->idUser           = $idUser;
        $obAgenda->idServico        = $idServ;
        $obAgenda->idProfissional   = $obPro->idProfissional;
        $obAgenda->idComercio       = $idComercio;
        $obAgenda->agendaHora       = $hora;
        $obAgenda->agendaData       = $data;
        $obAgenda->status           = 1;
        $obAgenda->motivo           = '';

        $obAgenda->cadastrar();

        $request->getRouter()->redirect('/user');*/
    }

    /**
     * Método responsável por retornar o formulário de conclusão de agendamento de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getEditService($request, $IdServ)
    {
        $disp = '';
        $obAgenda   = Agenda::getAgendaPById($IdServ);
        $idServ     = $obAgenda->idServico;
        $agendaData = $obAgenda->agendaData;
        $idAgenda   = $obAgenda->idAgenda;
        $disp       = $agendaData . '_' . $idServ;

        $helper = '';
        $helper = self::getAgenda($request, $disp, 'reagendar', $idAgenda);

        //CONTEÚDO DA AGENDA
        $content = View::render('user/modules/agendar/index', [
            'title'     => 'Regendar',
            'boxS'      => self::getBox($idServ),
            'status'    => self::getStatus($request),
            'Agenda'    => $helper
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Reagendar > PIUnivesp', $content, 'agendar');
    }


    /*

        $helper = '';
        $box='';
        $disp != NULL ? $helper = self::getAgenda($request, $disp) : '';
        $disp != NULL ? $box = $disp : '';
        $box =
        //CONTEÚDO DA AGENDA
        $content = View::render('user/modules/agendar/index', [
            'title'     => 'Agendar',
            'boxS'      => self::getBox(),
            'status'    => self::getStatus($request),
            'Agenda'    => $helper
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('home > PIUnivesp', $content, 'agendar');


*/



    private static function getSlot($tempo)
    {
        $slot = 0; //a ideia é a cada 15 minutos é um slot

        // Contador de slots
        $horaS = 0;
        $minS = 0; // Inicializando os minutos com 0

        for ($h = 0; $h < 15; $h++) {
            // Loop de minutos
            for ($m = 0; $m < 4; $m++) {

                $valueS = sprintf('%02d%02d', $horaS, $minS);

                if ($valueS == $tempo) {
                    break 2; // Sair dos dois loops se a condição for atendida
                }

                $slot++;
                $minS += 15;
            }

            $horaS++;
            $minS = 0; // Reiniciar os minutos para 0 após cada hora
        } // Fim do contador de slots
        return $slot;
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

    private static function getHorario()
    {
        $horario = [];
        $hora = 8;
        $min = '00';
        $hCont = 0;
        //for de hora
        for ($h = 0; $h < 15; $h++) {

            //for de min
            for ($m = 0; $m < 4; $m++) {

                //coloca 0 na frente do numero abaixo do 10
                if ($hora < 10) {
                    $horario[$hCont] =  '0' . $hora . ':' . $min;
                } else {
                    $horario[$hCont] =  $hora . ':' . $min;
                }

                if ($hora == 22) {
                    break;
                }
                $hCont++;
                $min += 15;
            } //fim for min
            //INCREMENTA UMA HORA ZERA MINUTO
            $hora++;
            $min = '00';
        } //fim for hora

        return $horario;
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

            case 'InvalidSelected':
                return Alert::getSuccess('Selecione uma opção das listas e uma data!');
                break;

            case 'InvalidSelectedDate':
                return Alert::getSuccess('Selecione uma data!');
                break;
        }
    }
}
