-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 02-Abr-2018 às 03:21
-- Versão do servidor: 10.1.31-MariaDB
-- PHP Version: 7.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zinnes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliar_comentario`
--

CREATE TABLE `avaliar_comentario` (
  `id_usuario` int(11) NOT NULL,
  `id_comentario` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Salvar avaliação de cada comentário.';

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliar_titulo`
--

CREATE TABLE `avaliar_titulo` (
  `id_usuario` int(11) NOT NULL,
  `id_titulo` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='salvar likes dados em titulos';

--
-- Extraindo dados da tabela `avaliar_titulo`
--

INSERT INTO `avaliar_titulo` (`id_usuario`, `id_titulo`, `data`) VALUES
(1, 1, '2018-03-13 13:45:44'),
(1, 5, '2018-03-26 17:50:10');

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentario_titulo`
--

CREATE TABLE `comentario_titulo` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_titulo` int(11) NOT NULL,
  `texto` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_referencia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Comentarios feitos em titulos';

--
-- Extraindo dados da tabela `comentario_titulo`
--

INSERT INTO `comentario_titulo` (`id`, `id_usuario`, `id_titulo`, `texto`, `data`, `id_referencia`) VALUES
(1, 1, 4, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Porro nisi adipisci, voluptatibus impedit molestiae. Quas animi alias nemo quidem beatae reiciendis aliquid sunt! Non, libero. Beatae facere nemo eum, illo.\r\n\r\n', '2018-03-23 17:22:41', NULL),
(5, 1, 4, 'teste 3 galera', '2018-03-26 12:31:11', NULL),
(6, 1, 4, 'caraca velho mais um comentario', '2018-03-26 12:32:33', NULL),
(13, 1, 4, 'asdf', '2018-03-26 16:43:48', NULL),
(14, 1, 4, 'asdffdsa', '2018-03-26 16:43:50', NULL),
(15, 1, 4, 'asdfasdfasdfasdf', '2018-03-26 16:43:53', NULL),
(16, 1, 4, 'asdf asdf asd fasdf asdf', '2018-03-26 16:43:55', NULL),
(17, 1, 4, 'asdfasdfasd as  asdf asdfa sdf', '2018-03-26 16:43:59', NULL),
(18, 1, 4, 'asdfa sdf asdf', '2018-03-26 16:44:06', NULL),
(19, 1, 4, 'asdfdsasdfasdf asdfa', '2018-03-26 16:44:09', NULL),
(20, 1, 4, 'caraca velhoooo\r\n', '2018-03-26 17:15:37', NULL),
(21, 1, 4, 'Que massa', '2018-03-26 17:15:43', 20),
(22, 1, 5, 'Um teste para este comic', '2018-03-26 17:44:19', NULL),
(23, 1, 5, 'teste', '2018-03-30 12:04:05', NULL),
(24, 1, 5, 'asdfasdf', '2018-03-30 12:06:54', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comentario_usuario`
--

CREATE TABLE `comentario_usuario` (
  `id` int(11) NOT NULL,
  `de` int(11) NOT NULL,
  `para` int(11) DEFAULT NULL,
  `texto` text NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_referencia` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='salva os comentários feitos no perfil do usuário.';

--
-- Extraindo dados da tabela `comentario_usuario`
--

INSERT INTO `comentario_usuario` (`id`, `de`, `para`, `texto`, `data`, `id_referencia`) VALUES
(1, 2, 1, 'teste', '2018-03-31 22:34:35', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `genero`
--

CREATE TABLE `genero` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Gêneros dos projetos';

--
-- Extraindo dados da tabela `genero`
--

INSERT INTO `genero` (`id`, `nome`) VALUES
(1, 'Comédia'),
(2, 'Drama'),
(3, 'Terror'),
(4, 'Ação'),
(5, 'Slice of Life');

-- --------------------------------------------------------

--
-- Estrutura da tabela `log_moderador`
--

CREATE TABLE `log_moderador` (
  `id` int(11) NOT NULL,
  `id_moderador` int(3) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_projeto` int(11) DEFAULT NULL,
  `id_titulo` int(11) DEFAULT NULL,
  `id_comentario` int(11) DEFAULT NULL,
  `descricao` text NOT NULL,
  `lido` int(1) NOT NULL DEFAULT '0',
  `data` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `log_moderador`
--

INSERT INTO `log_moderador` (`id`, `id_moderador`, `id_usuario`, `id_projeto`, `id_titulo`, `id_comentario`, `descricao`, `lido`, `data`) VALUES
(1, 9, 1, NULL, NULL, NULL, 'O moderador <b></b> excluiu seu comentario <b>#9!</b>!', 1, '2018-03-30 20:17:12.901552'),
(2, 1, 2, NULL, NULL, NULL, 'O moderador <b>Le Ninja</b> atualizou informações em seu perfil!', 1, '2018-03-09 17:24:34.671008'),
(3, 1, 2, NULL, NULL, NULL, 'O moderador <b>Le Ninja</b> atualizou informações em seu perfil!', 1, '2018-03-09 19:52:34.526365'),
(4, 0, 2, NULL, NULL, NULL, 'O moderador <b></b> excluiu seu comentario <b>#2!</b>!', 1, '2018-03-31 22:37:50.476680');

-- --------------------------------------------------------

--
-- Estrutura da tabela `moderador`
--

CREATE TABLE `moderador` (
  `id` int(11) NOT NULL,
  `administradorGeral` int(1) NOT NULL DEFAULT '0',
  `id_usuario` int(15) NOT NULL,
  `pin` varchar(4) NOT NULL DEFAULT 'aaaa'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `moderador`
--

INSERT INTO `moderador` (`id`, `administradorGeral`, `id_usuario`, `pin`) VALUES
(1, 1, 1, '1234'),
(4, 0, 2, '1111');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacao`
--

CREATE TABLE `notificacao` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_titulo` int(15) NOT NULL,
  `lido` int(1) NOT NULL DEFAULT '0',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `link` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Grava notificações para os usuários';

--
-- Extraindo dados da tabela `notificacao`
--

INSERT INTO `notificacao` (`id`, `id_usuario`, `id_titulo`, `lido`, `data`, `link`) VALUES
(2, 1, 4, 1, '2018-03-20 16:21:16', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `projeto`
--

CREATE TABLE `projeto` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` int(1) NOT NULL,
  `descricao` varchar(250) NOT NULL,
  `link_banner` varchar(100) NOT NULL,
  `tags` varchar(100) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `thumb_projeto` varchar(20) DEFAULT NULL,
  `banner_projeto` varchar(20) DEFAULT NULL,
  `estado_projeto` int(1) NOT NULL DEFAULT '0',
  `id_genero` int(1) NOT NULL,
  `data_criacao` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `projeto`
--

INSERT INTO `projeto` (`id`, `nome`, `tipo`, `descricao`, `link_banner`, `tags`, `id_usuario`, `thumb_projeto`, `banner_projeto`, `estado_projeto`, `id_genero`, `data_criacao`) VALUES
(1, 'Caraca Velho', 1, 'Lorem Ipsum é simplesmente uma simulação de texto da indústria tipográfica e de impressos, e vem sendo utilizado desde o século XVI, quando um impressor desconhecido pegou uma bandeja de tipos e os embaralhou para fazer um livro de modelos de tipos. ', '', 'malandro__teste__ação__caraca__mestre', 2, '21521137778.png', '21521137746.png', 0, 1, '2018-03-13 13:18:04.090907'),
(3, 'Os sonhos de uma rocha', 2, 'Esta é uma historia que conta os desesperos de ser uma rocha, ver o tempo passar e nunca poder interagir com o que acontece ao seu redor.', '', 'rocha__massa__lecal__tocha', 2, NULL, NULL, 0, 2, '2018-03-15 18:56:27.688138'),
(4, 'Teste de novel para o sistema', 2, 'Lorem Ipsum é simplesmente uma simulação de texto da indústria tipográfica e de impressos, e vem sendo utilizado desde o século XVI, quando um impressor desconhecido pegou uma bandeja de tipos e os embaralhou para fazer um livro de modelos de tipos. ', '', 'asdf__fdsa__qwer', 1, NULL, NULL, 0, 4, '2018-03-16 10:36:24.539947');

-- --------------------------------------------------------

--
-- Estrutura da tabela `seguir_projeto`
--

CREATE TABLE `seguir_projeto` (
  `id_usuario` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='salvar projetos seguidos pelos usuarios';

--
-- Extraindo dados da tabela `seguir_projeto`
--

INSERT INTO `seguir_projeto` (`id_usuario`, `id_projeto`, `data`) VALUES
(2, 4, '2018-04-01 20:31:35');

-- --------------------------------------------------------

--
-- Estrutura da tabela `slides`
--

CREATE TABLE `slides` (
  `link` varchar(200) NOT NULL,
  `numero` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `slides`
--

INSERT INTO `slides` (`link`, `numero`) VALUES
('http://www.mysqltutorial.org/mysql-primary-key/', 1),
('http://www.webtoons.com/en/', 2),
('https://fontawesome.com/icons?d=gallery&q=link', 3),
('http://www.webtoons.com/en/', 4),
('http://www.webtoons.com/en/', 5);

-- --------------------------------------------------------

--
-- Estrutura da tabela `titulo`
--

CREATE TABLE `titulo` (
  `id` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `estado_titulo` int(1) NOT NULL DEFAULT '0',
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(300) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `texto` text,
  `data_ultima_atualizacao` timestamp(6) NULL DEFAULT NULL,
  `visualizacoes` int(15) NOT NULL DEFAULT '0',
  `thumb_titulo` varchar(50) DEFAULT NULL,
  `rascunho` int(1) NOT NULL DEFAULT '1',
  `data_lancamento` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='titulos criados (comics e novels)';

--
-- Extraindo dados da tabela `titulo`
--

INSERT INTO `titulo` (`id`, `id_projeto`, `estado_titulo`, `nome`, `descricao`, `data`, `texto`, `data_ultima_atualizacao`, `visualizacoes`, `thumb_titulo`, `rascunho`, `data_lancamento`) VALUES
(4, 3, 0, 'Isto é um teste #1', 'afsdf ad fasdfh asjd fhasklh fasdf.', '2018-03-16 21:12:26', '<p>Lorem Ipsum é simplesmente uma simulação de texto da indústria tipográfica e de impressos, e vem sendo utilizado desde o século XVI, quando um impressor desconhecido pegou uma bandeja de tipos e os embaralhou para fazer um livro de modelos de tipos. Lorem Ipsum sobreviveu não só a cinco séculos, como também ao salto para a editoração eletrônica, permanecendo essencialmente inalterado. Se popularizou na década de 60, quando a Letraset lançou decalques contendo passagens de Lorem Ipsum, e mais recentemente quando passou a ser integrado a softwares de editoração eletrônica como Aldus PageMaker.</p><p><br></p>', '2018-03-22 21:46:58.000000', 5, '41521740818.jpg', 0, '2018-03-30 08:00:00.000000'),
(5, 1, 0, 'Que titulo massa esse aqui #1', 'lorem ipsum dollor asdfkja sdflkjasdlkçfjk lsadfj çksajdçkfjasjkdflkasdjflk asdlçkjfjaklsdfjlksdj lksj f dljfa jld fdjl kfdlaskdjfaklçsdjf. caraca velho.', '2018-03-22 17:55:17', NULL, '2018-03-22 23:20:41.000000', 0, '51521741351.jpg', 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(15) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `hash` varchar(15) NOT NULL,
  `estado_conta` int(1) NOT NULL,
  `confirmado` int(1) NOT NULL DEFAULT '0',
  `dias_bloqueio` timestamp(6) NULL DEFAULT NULL,
  `ultimo_login` timestamp(6) NULL DEFAULT CURRENT_TIMESTAMP(6),
  `facebook` int(1) NOT NULL DEFAULT '0',
  `google` int(1) NOT NULL DEFAULT '0',
  `link_twitter` varchar(100) DEFAULT NULL,
  `link_instagram` varchar(100) DEFAULT NULL,
  `link_facebook` varchar(100) DEFAULT NULL,
  `link_youtube` varchar(100) DEFAULT NULL,
  `link_apoiase` varchar(100) DEFAULT NULL,
  `link_padrim` varchar(100) DEFAULT NULL,
  `link_patreon` varchar(100) DEFAULT NULL,
  `link_site` varchar(100) DEFAULT NULL,
  `descricao` varchar(500) NOT NULL,
  `localizacao` varchar(100) NOT NULL,
  `foto_perfil` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `nickname`, `email`, `senha`, `hash`, `estado_conta`, `confirmado`, `dias_bloqueio`, `ultimo_login`, `facebook`, `google`, `link_twitter`, `link_instagram`, `link_facebook`, `link_youtube`, `link_apoiase`, `link_padrim`, `link_patreon`, `link_site`, `descricao`, `localizacao`, `foto_perfil`) VALUES
(1, 'Izac Galdino Lima Diniz', 'izac', 'isac_cedrim@hotmail.com', 'd36250ef7c7d0d696689d4ddddda90d7', '1520429428', 0, 1, NULL, '2018-04-01 12:16:26.000000', 0, 0, 'http://localhost/phpmyadmin/db_sql.php', NULL, '', NULL, NULL, NULL, NULL, 'https://br.lipsum.com/', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Cedro, Ceará', '11520528423.png'),
(2, 'Teste da Silva', 'Le Ninja', 'itsoftwares2016@gmail.com', 'a4798fc7d218d265d2d9970e1e8db9f8', '1520530720', 0, 1, '2018-03-08 23:50:19.000000', '2018-04-01 20:29:44.000000', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://fontawesome.com/icons?d=gallery&q=unlo', 'teste maluco 5', 'Seus sonhos', '21521134059.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comentario_titulo`
--
ALTER TABLE `comentario_titulo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comentario_usuario`
--
ALTER TABLE `comentario_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_moderador`
--
ALTER TABLE `log_moderador`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `moderador`
--
ALTER TABLE `moderador`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indexes for table `notificacao`
--
ALTER TABLE `notificacao`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projeto`
--
ALTER TABLE `projeto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`numero`);

--
-- Indexes for table `titulo`
--
ALTER TABLE `titulo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comentario_titulo`
--
ALTER TABLE `comentario_titulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `comentario_usuario`
--
ALTER TABLE `comentario_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `genero`
--
ALTER TABLE `genero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `log_moderador`
--
ALTER TABLE `log_moderador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `moderador`
--
ALTER TABLE `moderador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notificacao`
--
ALTER TABLE `notificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `titulo`
--
ALTER TABLE `titulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
