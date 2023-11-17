-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 17-Nov-2023 às 00:53
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
-- Estrutura da tabela `emplistuser`
--

DROP TABLE IF EXISTS `emplistuser`;
CREATE TABLE IF NOT EXISTS `emplistuser` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idEmpre` int NOT NULL,
  `creationDate` date NOT NULL,
  `finalDate` date NOT NULL,
  `status` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  PRIMARY KEY (`idLista`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `profissionallistuser`
--

DROP TABLE IF EXISTS `profissionallistuser`;
CREATE TABLE IF NOT EXISTS `profissionallistuser` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idUser` int NOT NULL,
  `idProf` int NOT NULL,
  `creationDate` date NOT NULL,
  `finalDate` date NOT NULL,
  `status` tinyint NOT NULL,
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`idServ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `agendaHora` int NOT NULL,
  `agendaData` date NOT NULL,
  `status` tinyint NOT NULL,
  `motivo` varchar(255) NOT NULL,
  PRIMARY KEY (`idAgenda`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tbcomercio`
--

DROP TABLE IF EXISTS `tbcomercio`;
CREATE TABLE IF NOT EXISTS `tbcomercio` (
  `idEmpre` int NOT NULL AUTO_INCREMENT,
  `enderecoEmpre` varchar(255) NOT NULL,
  `cepEmpre` varchar(255) NOT NULL,
  `numEmpre` int NOT NULL,
  `telEmpre` varchar(255) NOT NULL,
  `nomeEmpre` varchar(255) NOT NULL,
  `siteEmpre` varchar(255) NOT NULL,
  `cpfEmpre` varchar(255) NOT NULL,
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
  `horario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `semana` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `feriadoEstadual` tinyint NOT NULL,
  `feriadoNacional` tinyint NOT NULL,
  PRIMARY KEY (`idHorarios`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
