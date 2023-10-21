-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 21-Out-2023 às 02:30
-- Versão do servidor: 8.0.31
-- versão do PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pi2univesp`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresalistaprofissionais`
--

DROP TABLE IF EXISTS `empresalistaprofissionais`;
CREATE TABLE IF NOT EXISTS `empresalistaprofissionais` (
  `idLista` int NOT NULL AUTO_INCREMENT,
  `idEmpresa` int NOT NULL,
  `idProfissional` int NOT NULL,
  `dataInicio` datetime NOT NULL,
  `dataFim` datetime NOT NULL,
  `status` int NOT NULL,
  `funcao` varchar(255) NOT NULL,
  PRIMARY KEY (`idLista`),
  UNIQUE KEY `idEmpre` (`idEmpresa`),
  UNIQUE KEY `idProfissional` (`idProfissional`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `servico`
--

DROP TABLE IF EXISTS `servico`;
CREATE TABLE IF NOT EXISTS `servico` (
  `idServ` int NOT NULL AUTO_INCREMENT,
  `nomeServ` varchar(255) NOT NULL,
  `tempoMedioServ` double NOT NULL,
  `valorServ` double NOT NULL,
  `idProfissional` int NOT NULL,
  `status` tinyint NOT NULL,
  `dataInicioServ` datetime NOT NULL,
  `dataFimServ` datetime NOT NULL,
  PRIMARY KEY (`idServ`),
  UNIQUE KEY `idprofissional` (`idProfissional`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `servico`
--

INSERT INTO `servico` (`idServ`, `nomeServ`, `tempoMedioServ`, `valorServ`, `idProfissional`, `status`, `dataInicioServ`, `dataFimServ`) VALUES
(1, 'Unha basica', 1.3, 30, 3, 0, '2023-10-20 20:09:06', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbagenda`
--

DROP TABLE IF EXISTS `tbagenda`;
CREATE TABLE IF NOT EXISTS `tbagenda` (
  `idAgenda` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idServico` int NOT NULL,
  `idProfissional` int NOT NULL,
  `idComercio` int NOT NULL,
  `agendaHora` time NOT NULL,
  `agendaData` date NOT NULL,
  `status` tinyint NOT NULL,
  `motivo` varchar(255) NOT NULL,
  PRIMARY KEY (`idAgenda`),
  UNIQUE KEY `idUser` (`idUser`),
  UNIQUE KEY `idServico` (`idServico`),
  UNIQUE KEY `idProfissional` (`idProfissional`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbcomercio`
--

DROP TABLE IF EXISTS `tbcomercio`;
CREATE TABLE IF NOT EXISTS `tbcomercio` (
  `idEmpre` int NOT NULL AUTO_INCREMENT,
  `enderecoEmpre` varchar(255) NOT NULL,
  `cepEmpre` int NOT NULL,
  `numEmpre` int NOT NULL,
  `telEmpre` int NOT NULL,
  `nomeEmpre` varchar(255) NOT NULL,
  `siteEmpre` varchar(255) NOT NULL,
  `cpfEmpre` int NOT NULL,
  `idProfissional` int NOT NULL,
  PRIMARY KEY (`idEmpre`),
  UNIQUE KEY `idprofissional` (`idProfissional`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbhorarios`
--

DROP TABLE IF EXISTS `tbhorarios`;
CREATE TABLE IF NOT EXISTS `tbhorarios` (
  `idHorarios` int NOT NULL AUTO_INCREMENT,
  `segunda` varchar(255) NOT NULL,
  `terca` varchar(255) NOT NULL,
  `quarta` varchar(255) NOT NULL,
  `quinta` varchar(255) NOT NULL,
  `sexta` varchar(255) NOT NULL,
  `sabado` varchar(255) NOT NULL,
  `domingo` varchar(255) NOT NULL,
  `feriadoEstadual` tinyint NOT NULL,
  `feriadoNacional` tinyint NOT NULL,
  PRIMARY KEY (`idHorarios`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `tbhorarios`
--

INSERT INTO `tbhorarios` (`idHorarios`, `segunda`, `terca`, `quarta`, `quinta`, `sexta`, `sabado`, `domingo`, `feriadoEstadual`, `feriadoNacional`) VALUES
(4, '0830/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', 0, 0),
(3, '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', '0800/$/1200/$/1300/$/1800/$/', 1, 1),
(5, '0800/$/1000/$/1300/$/1800/$/', '0800/$/1030/$/1300/$/1800/$/', '0800/$/1015/$/1300/$/1800/$/', '0800/$/1045/$/1300/$/1800/$/', '0800/$/1045/$/1300/$/1800/$/', '0800/$/1000/$/1300/$/1800/$/', '0800/$/1000/$/1300/$/1800/$/', 1, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbprofissional`
--

DROP TABLE IF EXISTS `tbprofissional`;
CREATE TABLE IF NOT EXISTS `tbprofissional` (
  `idProfissional` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idHorarios` int NOT NULL,
  `funcaoProfissional` varchar(255) NOT NULL,
  PRIMARY KEY (`idProfissional`),
  UNIQUE KEY `idUser` (`idUser`),
  UNIQUE KEY `idHorarios` (`idHorarios`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `tbprofissional`
--

INSERT INTO `tbprofissional` (`idProfissional`, `idUser`, `idHorarios`, `funcaoProfissional`) VALUES
(1, 12, 3, 'cabelereiro'),
(2, 23, 4, 'cabeleireiro'),
(3, 21, 5, 'manicure');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `endereco` varchar(255) NOT NULL,
  `cep` varchar(255) NOT NULL,
  `numero` int NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `cpf` varchar(255) NOT NULL,
  `tipoConta` tinyint NOT NULL,
  `sexo` tinyint NOT NULL,
  `login` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `endereco`, `cep`, `numero`, `telefone`, `cpf`, `tipoConta`, `sexo`, `login`, `senha`) VALUES
(20, 'André', 'and@and.com', 'Rua teste', '08870570', 123, '11912345678', '00000000000', 0, 1, 'crocamo', '$2y$10$TirIh3qsGRTxtpvSEq2D3.oNezXUYqTgpmUY8/q08aodC4Mq72dT6'),
(21, 'mel', 'mel@mel.com', 'Rua teste2', '08475640', 123, '11987654321', '00000000000', 2, 0, 'mel', '$2y$10$dJGS7XbgbqFR7ozGJMfSZ.SVyxAQOTCan14QF/aV0HTgsPwWE8cna');
(22, 'ruan', 'ruan@ruan.com', 'Rua teste3', '05781650', 12345, '11912345678', '12345678910', 1, 0, 'ruanLPB', '$2y$10$c0TB/O0VfyGvumH/9XT2y.FUOM9JAM7Yi39LM.DlqOJ4DxRa6flju'),
(23, 'renan', 'renan@renan.com', 'Rua Rua teste4', '03546550', 123, '10987654321', '57056388261000', 1, 0, 'renanPPA', '$2y$10$kXZe/yZjYWvKjgunJwm3A.wHKsJipcbC/b82AdMmTJ4f3SGJKwsGu');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
