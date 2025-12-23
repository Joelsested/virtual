-- TABLE: administradores
CREATE TABLE `administradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `ativo` varchar(5) NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `administradores` (`id`, `nome`, `cpf`, `telefone`, `email`, `foto`, `ativo`, `data`) VALUES ('1', 'Hugo Vasconcelos', '000.000.000-00', '(31) 00000-0000', 'contato@hugocursos.com.br', '16-05-2022-10-21-44-eu.jpeg', 'Sim', '2022-03-07');
INSERT INTO `administradores` (`id`, `nome`, `cpf`, `telefone`, `email`, `foto`, `ativo`, `data`) VALUES ('3', 'Admin Teste', '333.333.333-33', '(33) 33333-3333', 'admin@hotmail.com', '08-03-2022-22-21-20-02-03-2022-09-59-04-Arthur.jpg', 'Sim', '2022-03-08');

-- TABLE: alertas
CREATE TABLE `alertas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `link` varchar(50) DEFAULT NULL,
  `imagem` varchar(100) DEFAULT NULL,
  `video` varchar(100) DEFAULT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `alertas` (`id`, `titulo`, `descricao`, `link`, `imagem`, `video`, `data`) VALUES ('1', 'Lançamento Portal EAD', 'fadfa fdaf asfdsafasfdas fdas fasfa fad fasds fdas fasf sdaf a fa fas<br>', 'cursos-do-pacote-portal-ead', '16-05-2022-18-20-11-recursos-grafico.png', 'https://www.youtube.com/embed/OE8Wz0-v5nc', '2022-05-16');
INSERT INTO `alertas` (`id`, `titulo`, `descricao`, `link`, `imagem`, `video`, `data`) VALUES ('2', 'Site em Manutenção', 'Nosso site estará em manutenção ....<br>', '', 'sem-foto.png', '', '2022-05-17');
INSERT INTO `alertas` (`id`, `titulo`, `descricao`, `link`, `imagem`, `video`, `data`) VALUES ('4', 'Desconto Portal Immobiliario', 'fdafdsafdasfdsf fdafdsfasf afdasf af<br>', 'cursos-do-pacote-portal-ead', '16-05-2022-19-51-01-eu.jpeg', 'https://www.youtube.com/embed/OE8Wz0-v5nc', '2022-05-16');

-- TABLE: alternativas
CREATE TABLE `alternativas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso` int(11) NOT NULL,
  `pergunta` int(11) NOT NULL,
  `resposta` varchar(255) NOT NULL,
  `correta` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('10', '29', '5', 'Resposta 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('11', '29', '5', 'Resposta 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('12', '29', '5', 'Resposta 3', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('13', '29', '5', 'Resposta 4', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('14', '29', '6', 'aaaaaa', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('15', '29', '6', 'bbbb', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('16', '29', '6', 'cccc', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('17', '29', '6', 'dddd', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('18', '29', '7', 'fdafdaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('19', '29', '7', 'fdasfafasf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('20', '29', '7', 'fsfsdf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('22', '29', '7', 'fddfsfds', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('23', '31', '1', 'afsdafsd', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('24', '31', '1', 'fdasfas', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('25', '31', '1', 'fsdf', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('26', '31', '2', 'fdafdsaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('27', '31', '2', 'sdaafaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('28', '31', '2', 'dfadfasfas', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('29', '31', '8', 'afaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('30', '31', '8', 'fdafdasf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('31', '31', '8', 'dfsafsaf', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('32', '31', '9', 'sdafaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('33', '31', '9', 'fdsafdsaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('34', '31', '9', 'fdasfdafa', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('35', '26', '10', 'fdadfsa', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('36', '26', '10', 'dfsafsaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('37', '26', '10', 'fasdfasfs', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('38', '26', '10', 'fdasfasfas', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('39', '26', '11', 'dfsafsa', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('40', '26', '11', 'dasfasf', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('41', '26', '11', 'dfasfdsaf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('42', '26', '11', 'fdafasf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('43', '26', '12', 'dfafsdaf', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('44', '26', '12', 'dfsfafsa', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('45', '26', '12', 'dafdasfs', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('46', '26', '12', 'safsdafd', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('47', '26', '13', 'dfsasfsafa', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('48', '26', '13', 'dfadfasf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('49', '26', '13', 'dasfasf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('50', '26', '13', 'fdsafasf', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('51', '25', '14', 'a', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('52', '25', '14', 'b', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('53', '25', '14', 'c', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('54', '25', '15', 'a', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('55', '25', '15', 'b', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('56', '25', '15', 'c', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('57', '29', '5', 'resposta uu', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('58', '28', '16', 'resposta 1', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('59', '28', '16', 'resposta 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('60', '28', '16', 'resposta 3', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('61', '28', '17', 'resposta 1', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('62', '28', '17', 'resposta 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('63', '28', '17', 'resposta 3', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('64', '28', '18', 'resposta 1', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('65', '28', '18', 'resposta ', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('66', '28', '18', 'resposta 3', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('67', '27', '19', 'alt 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('68', '27', '19', 'alt 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('70', '27', '19', 'alt 3', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('71', '27', '20', 'alt 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('72', '27', '20', 'alt 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('73', '27', '20', 'alt 3', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('74', '27', '21', 'alt 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('75', '27', '21', 'alt 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('76', '27', '21', 'alt 3', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('77', '34', '22', 'alt 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('78', '34', '22', 'alt 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('79', '34', '22', 'alt 3', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('80', '34', '23', 'alt 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('81', '34', '23', 'alt 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('82', '34', '23', 'alt 3', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('83', '33', '24', 'alt 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('84', '33', '24', 'alt 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('85', '33', '24', 'alt 3', 'Sim');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('86', '33', '25', 'alt 1', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('87', '33', '25', 'alt 2', '');
INSERT INTO `alternativas` (`id`, `curso`, `pergunta`, `resposta`, `correta`) VALUES ('88', '33', '25', 'alt 3', 'Sim');

-- TABLE: alunos
CREATE TABLE `alunos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(100) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `foto` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `cartao` int(11) NOT NULL,
  `ativo` varchar(5) DEFAULT NULL,
  `arquivo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('12', 'Aluno Testes', '222.222.222-22', 'aluno@hotmail.com', '(01) 22222-2222', 'Rua 5', 'Belo Horizonte', '', '', '19-04-2022-21-25-52-02-03-2022-09-51-42-fabio.jpg', '2022-03-08', '22', 'Sim', '27-12-2023-16-10-24-sicoob_2023_12_17_12_41_41.pdf');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('13', 'Aluno Dois', '000.000.000-59', 'aluno2@hotmail.com', '(55) 55555-5555', 'Rua Guajajaras 140 Centro', 'Belo Horizonte', '', '', '08-03-2022-21-15-02-02-03-2022-09-51-42-fabio.jpg', '2022-03-08', '3', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('15', 'Aluno de Teste', '', 'alunodeteste@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-04-04', '0', '', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('16', 'Aluno de Teste4', '', 'alunodeteste4@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-04-04', '0', '', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('17', 'Aluno de Teste5', '', 'alunodeteste5@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-04-04', '0', '', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('18', 'Hugo Freitas', 'MG0125698875', 'hugovasconcelosf@hotmail.com', '31975275083', 'Rua X Número 50', 'Belo Horizonte', 'MG', 'Brasil', '13-04-2022-02-12-01-02-03-2022-09-51-42-fabio.jpg', '2022-04-04', '24', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('19', 'alunoparateste', '', 'alunoparateste@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-04-04', '0', '', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('21', 'Paula', '', 'paula@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-04-11', '0', '', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('22', 'Marcia Silva', '', 'marcia@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-04-12', '3', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('23', 'Aluno 3', '', 'aluno3@hotmail.com', '325555685222', '', '', '', '', '13-04-2022-02-15-17-02-03-2022-09-46-32-teste.jpg', '2022-04-12', '4', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('26', 'Marcelo Aluno Teste', '', 'alunotestemarcelo@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-04-20', '0', '', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('27', 'Aluno teste 5', '', 'aluno5@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-05-16', '8', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('28', 'aluno6', '', 'aluno6@hotmail.com', '', '', '', '', '', 'sem-perfil.jpg', '2022-05-16', '0', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('30', 'teste@hotmail.com', '555.555.555-55', 'teste@hotmail.com', '(55) 55555-5555', 'adfasfa', '', '', '', 'sem-perfil.jpg', '2022-05-17', '0', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('31', 'Hugo Freitas', '12117853600', 'hugovasconcelosf@hotmai.com', '', '', '', '', '', 'sem-perfil.jpg', '2023-06-12', '2', 'Sim', '');
INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('32', 'Hugo Freitas', '7474747425252', 'hugofreitas20@hotmail.com', '', '', '', '', '', '29-11-2023-20-27-36-6729a536a21c1359916ccd7115a177d5.jpg', '2023-06-12', '0', 'Sim', '');

-- TABLE: arquivos_alunos
CREATE TABLE `arquivos_alunos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aluno` int(11) NOT NULL,
  `arquivo` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `descricao` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `arquivos_alunos` (`id`, `aluno`, `arquivo`, `data`, `descricao`) VALUES ('6', '12', '04-01-2024-18-45-26-8b276351-3a52-401c-8dab-df2b81e4bf36.pdf', '2024-01-04', 'conta de agua');
INSERT INTO `arquivos_alunos` (`id`, `aluno`, `arquivo`, `data`, `descricao`) VALUES ('7', '12', '04-01-2024-19-11-04-vistoria-(1).pdf', '2024-01-04', 'notas');
INSERT INTO `arquivos_alunos` (`id`, `aluno`, `arquivo`, `data`, `descricao`) VALUES ('8', '12', '04-01-2024-19-18-19-vistoria-(1).pdf', '2024-01-04', 'multa');
INSERT INTO `arquivos_alunos` (`id`, `aluno`, `arquivo`, `data`, `descricao`) VALUES ('9', '12', '04-01-2024-19-19-43-vistoria-(1).pdf', '2024-01-04', 'Teste janeiro/2024');

-- TABLE: aulas
CREATE TABLE `aulas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num_aula` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `link` varchar(150) DEFAULT NULL,
  `curso` int(11) NOT NULL,
  `sessao` int(11) NOT NULL,
  `sequencia_aula` int(11) NOT NULL,
  `apostila` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('1', '1', 'Introdução ao curso', 'https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '28', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('2', '2', 'Rodando o projeto', 'https://drive.google.com/file/d/1VZnnCgz6lnuyZpgZ-22u2892lM1vKmis/preview?usp=sharing', '28', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('3', '1', 'Introdução ao curso', 'https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '27', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('4', '2', 'Rodando o projeto', 'https://drive.google.com/file/d/1VZnnCgz6lnuyZpgZ-22u2892lM1vKmis/preview?usp=sharing', '27', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('5', '1', 'Introdução ao curso', 'https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '26', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('6', '2', 'Rodando o projeto', 'https://drive.google.com/file/d/1VZnnCgz6lnuyZpgZ-22u2892lM1vKmis/preview?usp=sharing', '26', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('7', '1', 'Introdução ao curso', 'https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '25', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('8', '2', 'Rodando o projeto', 'https://drive.google.com/file/d/1VZnnCgz6lnuyZpgZ-22u2892lM1vKmis/preview?usp=sharing', '25', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('9', '1', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('10', '2', 'Introdução ao curso', ' https://drive.google.com/file/d/1WfUkS4Zjy7Ikxj2FQ_moxI0bLoqm__uD/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('11', '3', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('12', '4', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('13', '5', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('14', '6', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('15', '7', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('16', '8', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('17', '9', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('18', '10', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('19', '11', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('20', '12', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('21', '13', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('22', '14', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('23', '15', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('24', '16', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('25', '17', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('26', '18', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('27', '19', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('28', '20', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('29', '21', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('30', '22', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('31', '23', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('32', '24', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('33', '25', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('34', '26', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('35', '27', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('36', '28', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('37', '29', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('38', '30', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('39', '31', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('40', '32', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('41', '33', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('42', '34', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('43', '35', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('44', '36', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('45', '37', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('46', '38', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('47', '39', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('48', '40', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('49', '41', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('50', '42', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('51', '43', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('52', '44', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('53', '45', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('54', '46', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('55', '47', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('56', '48', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('57', '49', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('58', '50', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('59', '51', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('60', '52', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('61', '53', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('62', '54', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('63', '55', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('64', '56', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('65', '57', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('66', '58', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('67', '59', 'Introdução ao curso', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('68', '60', 'Issssssssssssssssssss', ' https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '24', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('69', '3', 'Introdução ao curso', 'https://drive.google.com/file/d/126acozvEg_-y9-e7bUgp-WA68F1yUIe6/preview?usp=sharing', '28', '0', '0', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('86', '1', 'Aula 1', '', '35', '0', '1', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('87', '2', 'Aula 2', '', '35', '0', '2', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('88', '3', 'Aula 3', '', '35', '0', '3', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('89', '1', 'Aula 1', '', '34', '0', '1', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('90', '2', 'Aula 2', '', '34', '0', '2', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('139', '1', '8', '', '29', '11', '1', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('140', '2', '9', '', '29', '11', '2', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('141', '3', '10', '', '29', '11', '3', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('142', '4', '11', '', '29', '11', '4', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('146', '3', 'victor', '', '34', '0', '3', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('147', '1', '8', '', '31', '0', '1', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('148', '2', '9', '', '31', '0', '2', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('149', '4', '4', '', '34', '0', '4', '27-12-2023-16-08-57-sicoob_2023_12_17_12_41_41.pdf');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('150', '1', '1', '', '33', '0', '1', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('151', '2', '2', '', '33', '0', '2', '');
INSERT INTO `aulas` (`id`, `num_aula`, `nome`, `link`, `curso`, `sessao`, `sequencia_aula`, `apostila`) VALUES ('152', '3', '3', '', '33', '0', '3', '');

-- TABLE: avaliacoes
CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nota` int(11) NOT NULL,
  `comentario` varchar(500) NOT NULL,
  `curso` int(11) NOT NULL,
  `aluno` int(11) NOT NULL,
  `data` date NOT NULL,
  `pacote` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `avaliacoes` (`id`, `nota`, `comentario`, `curso`, `aluno`, `data`, `pacote`) VALUES ('8', '5', 'Excelente curso!', '29', '25', '2022-04-13', '0');
INSERT INTO `avaliacoes` (`id`, `nota`, `comentario`, `curso`, `aluno`, `data`, `pacote`) VALUES ('14', '5', 'fdsfdsfdsfsadf', '26', '9', '2022-04-20', '0');
INSERT INTO `avaliacoes` (`id`, `nota`, `comentario`, `curso`, `aluno`, `data`, `pacote`) VALUES ('15', '5', 'Avaliação do curso', '29', '20', '2022-04-20', '0');
INSERT INTO `avaliacoes` (`id`, `nota`, `comentario`, `curso`, `aluno`, `data`, `pacote`) VALUES ('16', '2', 'curso top dms', '33', '9', '2023-11-30', '0');
INSERT INTO `avaliacoes` (`id`, `nota`, `comentario`, `curso`, `aluno`, `data`, `pacote`) VALUES ('17', '5', 'topzera', '33', '20', '2023-11-30', '0');
INSERT INTO `avaliacoes` (`id`, `nota`, `comentario`, `curso`, `aluno`, `data`, `pacote`) VALUES ('18', '4', 'legal', '32', '20', '2023-11-30', '0');

-- TABLE: banner_index
CREATE TABLE `banner_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(35) NOT NULL,
  `descricao` varchar(160) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `link` varchar(50) NOT NULL,
  `ativo` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `banner_index` (`id`, `titulo`, `descricao`, `foto`, `link`, `ativo`) VALUES ('5', 'Estude em casa!', 'Nossos cursos são On-line e com acesso vitalício, para você estudar quando e onde quiser!', '04-04-2022-11-43-13-banner-slide-1.jpg', '', 'Sim');
INSERT INTO `banner_index` (`id`, `titulo`, `descricao`, `foto`, `link`, `ativo`) VALUES ('6', 'Curso com Certificado', 'Disponibilizamos certificação profissionalizante em todos nossos cursos! Otimize seu currículo ou utilize como horas extra-curriculares! ', '04-04-2022-13-27-26-banner-slide-2.jpg', '', 'Sim');
INSERT INTO `banner_index` (`id`, `titulo`, `descricao`, `foto`, `link`, `ativo`) VALUES ('7', 'Suporte direto com Professor', 'Aqui você conta com exclusivo suporte direto com professor! Caminhamos com você, para que seu treinamento seja concluído com SUCESSO!', '04-04-2022-13-28-45-banner-slide-3.jpg', '', 'Sim');
INSERT INTO `banner_index` (`id`, `titulo`, `descricao`, `foto`, `link`, `ativo`) VALUES ('8', 'Programe seu Futuro! ', 'Já são mais de 30 mil alunos formados! Venha você também fazer parte deste time e aproveitar as melhores oportunidades do mercado de trabalho!', '04-04-2022-13-28-07-banner-slide-4.jpg', '', 'Sim');

-- TABLE: banner_login
CREATE TABLE `banner_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `link` varchar(100) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `ativo` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `banner_login` (`id`, `nome`, `link`, `foto`, `ativo`) VALUES ('1', 'Portal Imobiliário', 'http://hugocursos.com.br', '29-03-2022-00-36-54-banner-login.jpg', 'Sim');
INSERT INTO `banner_login` (`id`, `nome`, `link`, `foto`, `ativo`) VALUES ('2', 'Portal EAD', 'http://hugocursos.com.br', '29-03-2022-00-37-51-Almofada-lantejolas.jpg', 'Não');

-- TABLE: categorias
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(35) NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `imagem` varchar(100) DEFAULT NULL,
  `nome_url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('1', 'Programação WEB', 'Desenvolvimento WEB', '09-03-2022-00-02-54-02-03-2022-09-51-42-fabio.jpg', 'programacao-web');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('2', 'Design Gráfico', 'Tudo na área', '09-03-2022-00-08-20-02-03-2022-09-46-45-katia.jpg', 'design-grafico');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('5', 'WEB Design', 'Tudo em WEB', 'sem-foto.png', 'web-design');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('6', 'Aplicativos Mobile', 'Desenvolvedor de Aplicativos', 'sem-foto.png', 'aplicativos-mobile');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('7', 'fdsfs', 'fdsfasf', 'sem-foto.png', 'fdsfs');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('8', 'fdsafdsaf', 'fadfdasfa', 'sem-foto.png', 'fdsafdsaf');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('9', 'fdasfas', 'fsdafas', 'sem-foto.png', '');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('10', 'fdasfadsf', 'sadfasdfas', 'sem-foto.png', 'fdasfadsf');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('11', 'fasfasfasdf', 'asfsdafasf', 'sem-foto.png', 'fasfasfasdf');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('12', 'fsdafsaf', 'safdfaf', 'sem-foto.png', 'fsdafsaf');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('13', 'fdsafdsfsdf', 'asdfdsafas', 'sem-foto.png', 'fdsafdsfsdf');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('14', 'fafdsafas', 'fasfasdfas', 'sem-foto.png', 'fafdsafas');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('15', 'fdsafasf', 'fdsfasf', 'sem-foto.png', 'fdsafasf');
INSERT INTO `categorias` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('16', 'fdfasf', 'fdsfsaf', 'sem-foto.png', 'fdfasf');

-- TABLE: config
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_sistema` varchar(50) NOT NULL,
  `tel_sistema` varchar(20) NOT NULL,
  `email_sistema` varchar(50) NOT NULL,
  `cnpj_sistema` varchar(25) DEFAULT NULL,
  `tipo_chave_pix` varchar(25) DEFAULT NULL,
  `chave_pix` varchar(100) DEFAULT NULL,
  `logo` varchar(20) NOT NULL,
  `icone` varchar(20) NOT NULL,
  `logo_rel` varchar(20) NOT NULL,
  `qrcode_pix` varchar(20) DEFAULT NULL,
  `facebook` varchar(120) DEFAULT NULL,
  `instagram` varchar(120) DEFAULT NULL,
  `youtube` varchar(120) DEFAULT NULL,
  `itens_pag` int(11) NOT NULL,
  `video_sobre` varchar(100) DEFAULT NULL,
  `itens_relacionados` int(11) NOT NULL,
  `aulas_liberadas` int(11) NOT NULL,
  `desconto_pix` int(11) NOT NULL,
  `email_adm_mat` varchar(5) DEFAULT NULL,
  `cartoes_fidelidade` int(11) NOT NULL,
  `taxa_boleto` decimal(8,2) NOT NULL,
  `taxa_mp` decimal(8,2) NOT NULL,
  `taxa_paypal` decimal(8,2) NOT NULL,
  `valor_max_cartao` decimal(8,2) NOT NULL,
  `total_emails_por_envio` int(11) NOT NULL,
  `intervalo_envio_email` int(11) NOT NULL,
  `script_dia` date DEFAULT NULL,
  `dias_email_matricula` int(11) NOT NULL,
  `dias_excluir_matricula` int(11) NOT NULL,
  `professor_cad` varchar(5) NOT NULL,
  `comissao_professor` int(11) NOT NULL,
  `dia_pgto_comissao` int(11) NOT NULL,
  `questionario` varchar(5) NOT NULL,
  `media` int(11) NOT NULL,
  `verso` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `config` (`id`, `nome_sistema`, `tel_sistema`, `email_sistema`, `cnpj_sistema`, `tipo_chave_pix`, `chave_pix`, `logo`, `icone`, `logo_rel`, `qrcode_pix`, `facebook`, `instagram`, `youtube`, `itens_pag`, `video_sobre`, `itens_relacionados`, `aulas_liberadas`, `desconto_pix`, `email_adm_mat`, `cartoes_fidelidade`, `taxa_boleto`, `taxa_mp`, `taxa_paypal`, `valor_max_cartao`, `total_emails_por_envio`, `intervalo_envio_email`, `script_dia`, `dias_email_matricula`, `dias_excluir_matricula`, `professor_cad`, `comissao_professor`, `dia_pgto_comissao`, `questionario`, `media`, `verso`) VALUES ('1', 'Portal Hugo Cursos', '(31) 97527-5084', 'contato@hugocursos.com.br', '11.111.111/1111-11', 'CNPJ', 'contato@hugocursos.com.br', 'logo.png', 'favicon.ico', 'logo_rel.jpg', 'qrcode.jpg', 'https://www.facebook.com/hugo.vasconcelos.940', 'https://www.instagram.com/portal_hugo_cursos/', 'https://www.youtube.com/channel/UCawdQwVpEcbcVpEfwAmbx9Q', '12', 'https://www.youtube.com/embed/xP_0xUytm7c', '8', '2', '5', 'Sim', '5', '3.45', '5.00', '7.50', '65.00', '480', '70', '2024-01-09', '3', '30', 'Não', '20', '20', 'Sim', '60', 'Sim');

-- TABLE: cupons
CREATE TABLE `cupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(25) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- TABLE: cursos
CREATE TABLE `cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(35) NOT NULL,
  `desc_rapida` varchar(40) NOT NULL,
  `desc_longa` text DEFAULT NULL,
  `valor` decimal(8,2) NOT NULL,
  `professor` int(11) NOT NULL,
  `categoria` int(11) NOT NULL,
  `imagem` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `carga` int(11) NOT NULL,
  `mensagem` varchar(255) DEFAULT NULL,
  `arquivo` varchar(150) DEFAULT NULL,
  `ano` int(11) NOT NULL,
  `palavras` varchar(255) DEFAULT NULL,
  `grupo` int(11) NOT NULL,
  `nome_url` varchar(150) NOT NULL,
  `pacote` varchar(100) DEFAULT NULL,
  `sistema` varchar(5) NOT NULL,
  `link` varchar(150) DEFAULT NULL,
  `tecnologias` varchar(150) DEFAULT NULL,
  `promocao` decimal(8,2) NOT NULL,
  `matriculas` int(11) NOT NULL,
  `comissao` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('24', 'Gestão de Contratos', 'Portal Imobiliário', 'O curso de Gestão de Contratos possui 68 aulas, neste curso você vai aprender a gerar laudos de vistorias, contratos de compra e venda já com os dados dos compradores / proprietários e os dados dos imóveis, vai aprender também gerar recibos personalizados, ficha de captação de imóveis, proposta de compra e locação, este é o nono e último módulo do sistema imobiliário, concluindo assim todo o projeto, o ideal é fazer passo a passo para criar o sistema do zero.<br>', '249.99', '2', '1', '04-04-2022-15-52-34-80.jpg', 'Aprovado', '0', '', '', '2022', '', '2', 'gestao-de-contratos', '', 'Não', 'http://google.com', 'Php Pdo e mysql', '0.00', '20', '50');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('25', 'Site Imobiliário', 'Portal Imobiliário', 'O curso de Site para imobiliária com PDO e Mysql possui 55 aulas, neste curso você vai aprender a pegar tudo que cadastramos no banco de dados nos módulos anteriores e construir o site a partir destes dados, vamos colocar galeria de imagens, slideshow para banners, filtros de diversos tipos para os imóveis e muito mais, lembrando que este é o oitavo curso do projeto, no total serão 9 cursos, o ideal é acompanhar na sequência para poder criar o sistema e o site 100% do zero.', '209.99', '2', '1', '04-04-2022-15-55-25-79.jpg', 'Aprovado', '25', '', '', '2022', 'portal imobiliária, site imobiliária , sistema imobiliária , ', '2', 'site-imobiliario', '', 'Não', '', 'Php Pdo e mysql', '0.00', '12', '0');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('26', 'Boletos e Carnês', 'Portal Imobiliário', '<div>O curso de Boletos e Carnês para sistemas possui 60 aulas, neste curso vamos aprender a gerar boleto para as cobranças / contas à Receber e para as parcelas dos aluguéis, onde também teremos a opção do carnê de pagamento com todas as parcelas e forma de pagamento via transação bancária e pix, faremos toda a integração dos boletos com o sistema do gerencianet, faremos também a automatização para dar baixa nas parcelas e cobranças de forma automatizada quando o boleto for aprovado, ainda neste módulo do treinamento você também aprenderá a criar as fichas de captação dos imóveis com pdf, podendo assim usar como base para vários outros tipos de fichas, este é o sétimo módulo do projeto do sistema imobiliário, é ideal fazer na sequência para criar do zero todo o projeto.<br></div>', '189.99', '2', '1', '04-04-2022-15-57-14-78.jpg', 'Aprovado', '25', '', '', '2022', 'portal imobiliária, site imobiliária , sistema imobiliária , ', '2', 'boletos-e-carnes', '', 'Não', '', 'Php Pdo e mysql', '0.00', '14', '0');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('27', 'Gestao de Cobrancas', 'Portal Imobiliário', 'O Curso de Gestão de Cobranças com PHP e mysql possui 71 aulas, neste curso vamos aprender a filtrar os débitos / parcelas dos clientes, efetuar cobranças via whatsapp, lembretes de pagamentos, vamos fazer também diversos tipos de relatórios financeiros para o projeto, vendas, aluguéis, comissões, débitos, etc, lembrando que este é sexto módulo do projeto imobiliário, é ideal acompanhar o passo a passo para montar o sistema do zero.<br>', '149.99', '2', '1', '04-04-2022-15-59-04-77.jpg', 'Aprovado', '25', '', '', '2022', 'portal imobiliária, site imobiliária , sistema imobiliária , ', '2', 'gestao-de-cobrancas', '', 'Não', '', '', '0.00', '21', '10');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('28', 'Controle de Parcelas e Comissões', 'Portal Imobiliário', 'O curso de Controle de Parcelas e Comissões possui 62 aulas, neste curso vamos mostrar como gerar as vendas dos imóveis e os aluguéis, criando as contas a Pagar e Receber de forma automática, bem como as parcelas dos inquilino e as comissões dos corretores e da imobiliária, lembrando que este é o quinto módulo do projeto, o ideal é fazer na sequência para montar todo o sistema do zero.<br>', '119.99', '2', '1', '04-04-2022-16-00-57-76.jpg', 'Aprovado', '25', '', '', '2022', 'portal imobiliária, site imobiliária , sistema imobiliária , ', '2', 'controle-de-parcelas-e-comissoes', '', 'Não', '', 'Php Pdo e mysql', '0.00', '17', '0');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('29', 'Portal de Cursos', 'PHP PDO e Mysql', 'fdsafdafa', '300.00', '2', '1', '12-04-2022-23-18-09-10-03-2022-13-42-49-83.jpg', 'Aprovado', '25', '', 'http://google.com', '2022', '', '1', 'portal-de-cursos', '', 'Não', '', 'Html, css, bootstrap, php8, javascript, jquery', '250.00', '7', '10');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('30', 'Sistema Escritório', 'Somente Fontes', '', '270.00', '2', '1', 'sem-foto.png', 'Aprovado', '0', '', '', '2022', '', '1', 'sistema-escritorio', '', 'Sim', '', '', '250.00', '0', '0');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('31', 'Curso Boleto teste', '5 reais', '', '5.00', '2', '6', 'sem-foto.png', 'Aprovado', '0', '', '', '2022', '', '1', 'curso-boleto-teste', '', 'Não', '', '', '0.00', '18', '0');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('32', 'Curso MP 1 real', '1 real', '', '2.00', '2', '6', 'sem-foto.png', 'Aprovado', '0', '', '', '2022', '', '1', 'curso-mp-1-real', '', 'Não', '', '', '1.00', '4', '0');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('33', 'fafa', 'fdafdas', 'fadfa', '50.00', '2', '6', 'sem-foto.png', 'Aprovado', '0', '', '', '2022', '', '1', 'fafa', '', 'Não', '', '', '60.00', '5', '30');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('34', 'Curso Gratuito', 'Curso Gratis', '', '0.00', '2', '6', 'sem-foto.png', 'Aprovado', '0', '', '', '2022', '', '1', 'curso-gratuito', '', 'Não', '', '', '0.00', '0', '0');
INSERT INTO `cursos` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `categoria`, `imagem`, `status`, `carga`, `mensagem`, `arquivo`, `ano`, `palavras`, `grupo`, `nome_url`, `pacote`, `sistema`, `link`, `tecnologias`, `promocao`, `matriculas`, `comissao`) VALUES ('35', 'Curso Teste', 'teste', '', '60.00', '14', '6', 'sem-foto.png', 'Aprovado', '0', '', '', '2022', '', '1', 'curso-teste', '', 'Não', '', '', '0.00', '6', '20');

-- TABLE: cursos_pacotes
CREATE TABLE `cursos_pacotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) NOT NULL,
  `id_pacote` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('1', '1', '1');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('9', '6', '1');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('10', '1', '3');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('11', '3', '3');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('12', '4', '3');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('15', '24', '9');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('16', '25', '9');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('17', '26', '9');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('18', '27', '9');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('19', '28', '9');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('20', '28', '10');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('21', '24', '10');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('22', '27', '10');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('23', '26', '10');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('24', '25', '10');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('25', '28', '11');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('26', '27', '11');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('27', '26', '11');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('28', '24', '12');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('29', '25', '12');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('30', '24', '13');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('31', '26', '13');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('32', '28', '13');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('33', '24', '14');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('34', '25', '14');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('36', '31', '16');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('37', '33', '16');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('39', '32', '18');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('42', '28', '18');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('43', '29', '18');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('44', '27', '16');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('45', '28', '16');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('46', '24', '17');
INSERT INTO `cursos_pacotes` (`id`, `id_curso`, `id_pacote`) VALUES ('47', '26', '17');

-- TABLE: emails
CREATE TABLE `emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(55) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `enviar` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('1', 'joaopaulo@hotmail.com', 'João Paulo', 'sim');
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('2', 'joaopaulo2@hotmail.com', 'João Paulo x', 'sim');
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('3', 'aaa@hotmail.com', 'aaaaaaaaaa', 'sim');
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('4', 'alunotestemarcelo@hotmail.com', 'Marcelo Aluno Teste', 'sim');
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('5', 'hugovasconcelosf@hotmail.com', 'Hugo Freitas', 'não');
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('6', 'aluno5@hotmail.com', 'Aluno teste 5', 'sim');
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('7', 'aluno6@hotmail.com', 'aluno6', 'sim');
INSERT INTO `emails` (`id`, `email`, `nome`, `enviar`) VALUES ('8', 'hugofreitas20@hotmail.com', 'Hugo Freitas', 'sim');

-- TABLE: envios
CREATE TABLE `envios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `final` int(11) NOT NULL,
  `assunto` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `link` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `envios` (`id`, `data`, `final`, `assunto`, `mensagem`, `link`) VALUES ('1', '2022-04-20 12:49:53', '0', 'AAAAA', 'fsdfdsfdsfds', 'cursos-do-pacote-aplicativo-financeiro');

-- TABLE: grupos
CREATE TABLE `grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `grupos` (`id`, `nome`) VALUES ('1', 'Portal EAD');
INSERT INTO `grupos` (`id`, `nome`) VALUES ('2', 'Portal Imobiliário');

-- TABLE: linguagens
CREATE TABLE `linguagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `imagem` varchar(100) NOT NULL,
  `nome_url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `linguagens` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('2', 'Delphi', 'fdsadfas', '30-03-2022-00-30-24-10-03-2022-13-42-49-83.jpg', 'delphi');
INSERT INTO `linguagens` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('4', 'PHP', 'dfsafasfs', '30-03-2022-00-30-11-10-03-2022-13-42-49-83.jpg', 'php');
INSERT INTO `linguagens` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('6', 'fdsafda', 'aaaaaaaaaaa', '30-03-2022-00-29-34-10-03-2022-13-42-49-83.jpg', 'fdsafda');
INSERT INTO `linguagens` (`id`, `nome`, `descricao`, `imagem`, `nome_url`) VALUES ('7', 'CSharp', 'fafafsdfaf', '30-03-2022-00-30-37-10-03-2022-13-42-49-83.jpg', 'csharp');

-- TABLE: matriculas
CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) NOT NULL,
  `aluno` varchar(20) NOT NULL,
  `professor` varchar(20) NOT NULL,
  `aulas_concluidas` int(11) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `data` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `pacote` varchar(5) NOT NULL,
  `alertado` varchar(5) DEFAULT NULL,
  `valor_cupom` decimal(8,2) NOT NULL,
  `subtotal` decimal(8,2) NOT NULL,
  `forma_pgto` varchar(25) NOT NULL,
  `boleto` varchar(25) DEFAULT NULL,
  `id_pacote` int(11) NOT NULL,
  `data_conclusao` date DEFAULT NULL,
  `total_recebido` decimal(8,2) NOT NULL,
  `obs` varchar(255) DEFAULT NULL,
  `nota` decimal(8,2) DEFAULT NULL,
  `ref_api` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('6', '13', '34', '2', '1', '229.99', '2023-06-12', 'Matriculado', 'Sim', '', '0.00', '229.99', 'Pix', '', '0', '', '229.99', '', '', '59256024157');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('8', '25', '34', '2', '2', '209.99', '2023-06-12', 'Finalizado', 'Não', '', '0.00', '209.99', 'Pix', '', '0', '2023-06-12', '209.99', '', '100.00', '59338351678');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('9', '28', '34', '2', '3', '0.00', '2023-06-12', 'Finalizado', 'Não', '', '0.00', '0.00', '', '', '13', '', '0.00', 'Pacote', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('10', '26', '34', '2', '2', '0.00', '2023-06-12', 'Finalizado', 'Não', '', '0.00', '0.00', '', '', '13', '', '0.00', 'Pacote', '25.00', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('11', '24', '34', '2', '1', '0.00', '2023-06-12', 'Matriculado', 'Não', '', '0.00', '0.00', '', '', '13', '', '0.00', 'Pacote', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('15', '34', '9', '2', '4', '0.00', '2023-11-17', 'Finalizado', 'Não', '', '0.00', '0.00', 'Pix', '', '0', '2024-01-05', '0.00', '', '100.00', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('17', '32', '20', '2', '2', '1.00', '2023-11-30', 'Finalizado', 'Não', '', '0.00', '1.00', 'Pix', '', '0', '2023-11-30', '1.00', '', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('19', '28', '20', '2', '3', '119.99', '2023-11-30', 'Matriculado', 'Não', '', '0.00', '119.99', 'Pix', '', '0', '', '119.99', '', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('20', '27', '20', '2', '2', '149.99', '2023-11-30', 'Finalizado', 'Não', '', '0.00', '149.99', 'Pix', '', '0', '2023-11-30', '149.99', '', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('21', '16', '20', '2', '1', '999.99', '2023-11-30', 'Matriculado', 'Sim', '', '0.00', '999.99', 'Pix', '', '0', '', '999.99', '', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('22', '33', '20', '2', '3', '0.00', '2023-11-30', 'Finalizado', 'Não', '', '0.00', '0.00', '', '', '16', '2023-11-30', '0.00', 'Pacote', '30.00', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('23', '31', '20', '2', '9', '0.00', '2023-11-30', 'Matriculado', 'Não', '', '0.00', '0.00', '', '', '16', '', '0.00', 'Pacote', '25.00', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('24', '29', '9', '2', '4', '250.00', '2023-12-27', 'Finalizado', 'Não', '', '0.00', '250.00', 'Pix', '', '0', '2023-12-27', '250.00', '', '66.67', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('29', '16', '9', '2', '1', '999.99', '2023-12-27', 'Matriculado', 'Sim', '', '0.00', '999.99', 'Pix', '', '0', '', '999.99', '', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('30', '28', '9', '2', '3', '0.00', '2023-12-27', 'Finalizado', 'Não', '', '0.00', '0.00', '', '', '16', '2024-01-04', '0.00', 'Pacote', '100.00', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('31', '27', '9', '2', '2', '0.00', '2023-12-27', 'Finalizado', 'Não', '', '0.00', '0.00', '', '', '16', '2024-01-05', '0.00', 'Pacote', '66.67', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('32', '33', '9', '2', '3', '0.00', '2023-12-27', 'Finalizado', 'Não', '', '0.00', '0.00', '', '', '16', '2024-01-05', '0.00', 'Pacote', '100.00', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('33', '31', '9', '2', '2', '0.00', '2023-12-27', 'Finalizado', 'Não', '', '0.00', '0.00', '', '', '16', '2023-12-28', '0.00', 'Pacote', '75.00', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('34', '17', '9', '2', '1', '50.00', '2024-01-05', 'Matriculado', 'Sim', '', '0.00', '50.00', 'Pix', '', '0', '', '50.00', '', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('35', '26', '9', '2', '1', '0.00', '2024-01-05', 'Matriculado', 'Não', '', '0.00', '0.00', '', '', '17', '', '0.00', 'Pacote', '', '');
INSERT INTO `matriculas` (`id`, `id_curso`, `aluno`, `professor`, `aulas_concluidas`, `valor`, `data`, `status`, `pacote`, `alertado`, `valor_cupom`, `subtotal`, `forma_pgto`, `boleto`, `id_pacote`, `data_conclusao`, `total_recebido`, `obs`, `nota`, `ref_api`) VALUES ('36', '24', '9', '2', '1', '0.00', '2024-01-05', 'Matriculado', 'Não', '', '0.00', '0.00', '', '', '17', '', '0.00', 'Pacote', '', '');

-- TABLE: pacotes
CREATE TABLE `pacotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(35) NOT NULL,
  `desc_rapida` varchar(40) NOT NULL,
  `desc_longa` text DEFAULT NULL,
  `valor` decimal(8,2) NOT NULL,
  `professor` int(11) NOT NULL,
  `imagem` varchar(100) DEFAULT NULL,
  `grupo` int(11) NOT NULL,
  `ano` int(11) NOT NULL,
  `palavras` varchar(255) DEFAULT NULL,
  `nome_url` varchar(150) NOT NULL,
  `video` varchar(150) DEFAULT NULL,
  `linguagem` int(11) NOT NULL,
  `promocao` decimal(8,2) NOT NULL,
  `matriculas` int(11) NOT NULL,
  `comissao` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('9', 'Pacote Imobiliário', 'Portal Imobiliário', '<div><span style=\"background-color: rgb(253, 253, 253); color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont, \" segoe=\"\" ui\",=\"\" roboto,=\"\" \"helvetica=\"\" neue\",=\"\" arial,=\"\" sans-serif,=\"\" \"apple=\"\" color=\"\" emoji\",=\"\" \"segoe=\"\" ui=\"\" symbol\",=\"\" \"noto=\"\" emoji\";=\"\" font-size:=\"\" 14px;=\"\" text-align:=\"\" justify;\"=\"\">Está buscando desenvolver sistemas completos? do zero? ou está buscando um sistema completo voltado para imobiliárias? Se sim está no lugar certo, aqui você irá aprender a desenvolver um sistema e um site integrado do zero, passo a passo, bem detalhado, mesmo que não seja programador você irá conseguir seguir todo treinamento e construir seu sistema, lembrando que também já vem pronto todo o projeto caso queira apenas instalar para uso, este sistema tem toda gestão necessária para uma imobiliária, cadastro de corretores, imóveis, financeiro com contas a pagar e receber, controle de parcelas dos aluguéis, vendas, comissões, relatórios de diversos tipos, contratos, recibos, laudos, vistorias, tudo que você vai precisar para gerenciar de forma profissional uma imobiliária, assista ao vídeo demonstrativo para ver tudo que possui neste sistema, qualquer dúvida entre em contato conosco, adquira já nosso treinamento e comece a criar sistemas completos.</span><br></div>', '249.99', '2', '04-04-2022-16-38-34-PORTAL-IMOBILIARIO.jpg', '2', '2022', 'portal imobiliária, site imobiliária , sistema imobiliária , ', 'pacote-imobiliario', 'https://www.youtube.com/embed/_8k65K8PzAw', '4', '229.99', '4', '0');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('10', 'Pacote Igreja', 'Sistema Igreja', '<span style= color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont,   segoe=   ui ,=   roboto,=    helvetica=   neue ,=   arial,=   sans-serif,=    apple=   color=   emoji ,=    segoe=   ui=   symbol ,=    noto=   emoji ;=   font-size:=   14px;=   text-align:=   justify;=   background-color:=   rgb(253,=   253,=   253); =  >Está buscando um sistema completo para gerenciar igrejas? Então acaba de encontrar, com este sistema você vai poder gerenciar igrejas (Sede e Filiais), membros, pastores, área da secretaria, controle de células, grupos, eventos, pregações, controlar o financeiro (Dízimos, Ofertas, Doações, Vendas, Contas à Pagar), fazer todo o controle de patrimônio da igreja, envio e anexos de arquivos, prestações de contas, relatórios e muito mais, tudo que sua igreja necessita para que tenha um controle simples e bem organizado de tudo, tudo isso é montado em um treinamento do zero em 8 cursos sequenciais, onde com base neste treinamento você estará apto a desenvolver vários outros tipos de sistemas web, além das aulas você receberá o código fonte com o sistema já finalizado caso não tenha interesse em criá-lo passo a passo.</span>', '329.99', '2', '04-04-2022-16-56-32-PACOTE-SISTEMA-IGREJA.jpg', '1', '2022', 'sistemas para igreja, sistema web para igreja, criar sistemas web, desenvolvimento web, secretaria igreja, sistema secretaria igreja', 'pacote-igreja', 'https://www.youtube.com/embed/tFekNhJt3Lk', '4', '299.99', '1', '15');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('11', 'Pacote Escritório', 'Sistema Escritório', '<span style= color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont,   segoe=   ui ,=   roboto,=    helvetica=   neue ,=   arial,=   sans-serif,=    apple=   color=   emoji ,=    segoe=   ui=   symbol ,=    noto=   emoji ;=   font-size:=   14px;=   text-align:=   justify;=   background-color:=   rgb(253,=   253,=   253); =  >Está buscando um sistema completo para gerenciar Escritórios?</span><br style= color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont,   segoe=   ui ,=   roboto,=    helvetica=   neue ,=   arial,=   sans-serif,=    apple=   color=   emoji ,=    segoe=   ui=   symbol ,=    noto=   emoji ;=   font-size:=   14px;=   text-align:=   justify;=   background-color:=   rgb(253,=   253,=   253); =  ><span style= color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont,   segoe=   ui ,=   roboto,=    helvetica=   neue ,=   arial,=   sans-serif,=    apple=   color=   emoji ,=    segoe=   ui=   symbol ,=    noto=   emoji ;=   font-size:=   14px;=   text-align:=   justify;=   background-color:=   rgb(253,=   253,=   253); =  >Então acaba de encontrar, com este sistema você vai poder gerenciar Controle financeiro, Rh, gestão de tarefas, relatórios administrativos, gráficos Dashboards e muito mais, tudo que um escritório necessita para que tenha um controle simples e bem organizado de tudo!</span><br style= color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont,   segoe=   ui ,=   roboto,=    helvetica=   neue ,=   arial,=   sans-serif,=    apple=   color=   emoji ,=    segoe=   ui=   symbol ,=    noto=   emoji ;=   font-size:=   14px;=   text-align:=   justify;=   background-color:=   rgb(253,=   253,=   253); =  ><span style= color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont,   segoe=   ui ,=   roboto,=    helvetica=   neue ,=   arial,=   sans-serif,=    apple=   color=   emoji ,=    segoe=   ui=   symbol ,=    noto=   emoji ;=   font-size:=   14px;=   text-align:=   justify;=   background-color:=   rgb(253,=   253,=   253); =  >Tudo isso é montado em um treinamento do zero em 8 cursos sequenciais, onde com base neste treinamento você estará apto a desenvolver vários outros tipos de sistemas web, além das aulas você receberá o código fonte com o sistema já finalizado caso não tenha interesse em criá-lo passo a passo.</span>', '279.99', '2', '04-04-2022-17-00-12-PACOTE-ESCRITÓRIO.jpg', '1', '2022', 'curso escritorio , curso seguranca , curso web , curso escritorios , curso sistema , php ', 'pacote-escritorio', 'https://www.youtube.com/embed/4ZViRefVaTU', '4', '0.00', '3', '22');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('12', 'Pacote Portal EAD', 'Portal EAD', '<div><span style=\"background-color: rgb(253, 253, 253); color: rgb(33, 37, 41); font-family: -apple-system, BlinkMacSystemFont, \" segoe=\"\" ui\",=\"\" roboto,=\"\" \"helvetica=\"\" neue\",=\"\" arial,=\"\" sans-serif,=\"\" \"apple=\"\" color=\"\" emoji\",=\"\" \"segoe=\"\" ui=\"\" symbol\",=\"\" \"noto=\"\" emoji\";=\"\" font-size:=\"\" 14px;=\"\" text-align:=\"\" justify;\"=\"\">Deseja aprender programação na prática e montar um portal de cursos completo com um site e sistema integrado? Se sim está no lugar certo, neste pacote de cursos vou ensinar do zero como montar um portal EAD profissional, com diversos recursos para poder gerenciar da melhor forma possível seus cursos, faremos cadastros de alunos e professores, comissões por professores, relatórios de vendas, certificados, pagamentos com cartão, boleto ou pix, liberações automáticas, conteúdo digital, área do aluno / área do professor / área do administrador, email marketing automatizado e muito mais, assista ao vídeo demonstrativo do projeto para ver tudo que esse portal oferece, aprenda já como criar projetos grandes de forma profissional.</span><br></div>', '249.99', '2', '04-04-2022-17-03-02-PORTAL-EAD.jpg', '1', '2022', 'ead , portal ead', 'pacote-portal-ead', 'https://www.youtube.com/embed/nsn4jMFeta0', '4', '0.00', '1', '0');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('13', 'Pacote Aplicativo Financeiro', 'React Native com Mysql', 'Está querendo desenvolver um aplicativo financeiro completo com vendas, compras, estoque, controle de contas à pagar, contas à receber, movimentações do caixa, cartão de crédito, débito, controle de parcelas, recorrência de cobranças, notificações automatizadas, upload de fotos pela camera / galeria e muito mais? Se sim acaba de encontrar o projeto ideal, é um dos sistemas mais completos que temos, ele foi feito em React Native e Mysql, são 7 módulos criando do zero todo o Aplicativo, além das aulas também vem todos os arquivos fontes do projeto e o APP já finalizado pronto para uso caso não tenha interesse em acompanhar as aulas de criação. Assista ao vídeo demonstrativo do Aplicativo para ver tudo que contém nele, qualquer dúvida estou a disposição.', '229.99', '2', '04-04-2022-17-06-05-PACOTE-REACT.jpg', '1', '2022', 'curso de react native, curso de react, aulas de react, react com mysql, aplicativos com react, aplicativos react native', 'pacote-aplicativo-financeiro', 'https://www.youtube.com/embed/nUChYb3Wbd0', '4', '0.00', '3', '24');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('15', 'Formação PHP', 'Tudo em PHP', '', '600.00', '2', 'sem-foto.png', '1', '2022', '', 'formacao-php', '', '4', '0.00', '1', '10');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('16', 'Pacotão Cursos 2022', 'Todos deste Ano', 'fadfasfasfa', '999.99', '2', 'sem-foto.png', '1', '2022', '', 'pacotao-cursos-2022', '', '0', '0.00', '2', '15');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('17', 'dfafa', '4545', '', '50.00', '2', 'sem-foto.png', '1', '2022', '', 'dfafa', '', '0', '50.00', '1', '25');
INSERT INTO `pacotes` (`id`, `nome`, `desc_rapida`, `desc_longa`, `valor`, `professor`, `imagem`, `grupo`, `ano`, `palavras`, `nome_url`, `video`, `linguagem`, `promocao`, `matriculas`, `comissao`) VALUES ('18', 'Pacote Teste', 'teste', '', '250.00', '14', 'sem-foto.png', '1', '2022', '', 'pacote-teste', '', '0', '0.00', '1', '20');

-- TABLE: pagar
CREATE TABLE `pagar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(50) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `data` date NOT NULL,
  `vencimento` date NOT NULL,
  `pago` varchar(5) NOT NULL,
  `data_pgto` date NOT NULL,
  `arquivo` varchar(150) DEFAULT NULL,
  `professor` int(11) NOT NULL,
  `curso` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('3', 'Mensalidade Servidor', '25.00', '2022-04-19', '2022-04-19', 'Não', '0000-00-00', '19-04-2022-18-39-29-09-11-2021-09-21-26-conta3.jpg', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('4', 'Conta Teste', '600.00', '2022-04-19', '2022-04-19', 'Sim', '2022-04-19', '19-04-2022-18-16-43-09-11-2021-10-17-10-pdfteste.pdf', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('5', 'Conta de Luz', '550.00', '2022-04-19', '2022-04-20', 'Sim', '2022-04-19', '19-04-2022-18-17-15-09-11-2021-12-04-29-pdfteste.zip', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('7', 'Comissão', '15.00', '2022-05-16', '2022-06-05', 'Não', '0000-00-00', 'sem-foto.png', '2', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('12', 'Comissão', '15.00', '2022-05-16', '2022-06-05', 'Não', '0000-00-00', 'sem-foto.png', '2', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('13', 'Comissão', '15.00', '2022-05-16', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('14', 'Comissão', '61.60', '2022-05-16', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('15', 'Comissão', '61.60', '2022-05-16', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Pacote Escritório');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('16', 'Comissão', '12.00', '2022-05-16', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '14', 'Curso Teste');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('18', 'Comissão', '61.60', '2022-05-16', '2022-05-20', 'Sim', '2022-04-12', 'sem-foto.png', '14', 'Pacote Escritório');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('19', 'Comissão', '45.00', '2022-05-16', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Pacote Igreja');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('20', 'Comissão', '50.00', '2022-05-16', '2022-05-20', 'Sim', '2022-05-16', 'sem-foto.png', '14', 'Pacote Teste');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('21', 'Comissão', '12.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '14', 'Curso Teste');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('22', 'Comissão', '125.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('23', 'Comissão', '125.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('24', 'Comissão', '125.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('25', 'Comissão', '125.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('26', 'Comissão', '125.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('27', 'Comissão', '125.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('29', 'Conta Luz', '800.00', '2022-05-17', '2022-05-17', 'Sim', '0000-00-00', '17-05-2022-17-27-02-09-11-2021-13-07-27-conta3.jpg', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('30', 'Comissão', '125.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('31', 'Aaa', '45.00', '2022-05-17', '2022-05-17', 'Não', '0000-00-00', '65ce8755-75e7-48ac-b040-d95d7cc0a123.jpg', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('32', 'Vbb', '70.00', '2022-05-17', '2022-05-18', 'Não', '0000-00-00', 'sem-foto.png', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('33', 'Sxxzzzzz', '59.00', '2022-05-17', '2022-05-17', 'Não', '0000-00-00', '94552797-e3a5-4241-b767-ed2449fa8ad5.jpg', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('34', 'Ydg', '780.00', '2022-05-17', '2022-05-17', 'Não', '0000-00-00', 'fd01871c-f5c6-4851-8f15-a37d8d3dee3e.jpg', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('35', 'Cxxxz', '70.00', '2022-05-17', '2022-05-17', 'Não', '0000-00-00', '5065f9fa-3a67-4d5b-b9fd-b9600b59fae3.jpg', '0', '');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('36', 'Comissão', '25.00', '2022-05-17', '2022-05-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Portal de Cursos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('37', 'Comissão', '18.00', '2022-08-20', '2022-09-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'fafa');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('38', 'Comissão', '12.00', '2022-08-20', '2022-09-20', 'Não', '0000-00-00', 'sem-foto.png', '14', 'Curso Teste');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('39', 'Comissão', '12.00', '2022-08-20', '2022-09-20', 'Não', '0000-00-00', 'sem-foto.png', '14', 'Curso Teste');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('40', 'Comissão', '25.00', '2022-10-24', '2022-11-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Portal de Cursos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('41', 'Comissão', '25.00', '2022-10-24', '2022-11-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Portal de Cursos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('42', 'Comissão', '0.20', '2023-06-06', '2023-06-20', 'Não', '0000-00-00', 'sem-foto.png', '14', 'Curso Teste');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('43', 'Comissão', '15.00', '2023-06-12', '2023-06-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestao de Cobrancas');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('44', 'Comissão', '55.20', '2023-06-12', '2023-06-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Pacote Aplicativo Financeiro');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('45', 'Comissão', '18.00', '2023-11-17', '2023-11-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'fafa');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('46', 'Comissão', '15.00', '2023-11-30', '2023-12-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestao de Cobrancas');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('47', 'Comissão', '150.00', '2023-11-30', '2023-12-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Pacotão Cursos 2022');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('48', 'Comissão', '25.00', '2023-12-27', '2024-01-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Portal de Cursos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('49', 'Comissão', '15.00', '2023-12-27', '2024-01-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestao de Cobrancas');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('50', 'Comissão', '125.00', '2023-12-27', '2024-01-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Gestão de Contratos');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('51', 'Comissão', '150.00', '2023-12-27', '2024-01-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'Pacotão Cursos 2022');
INSERT INTO `pagar` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`, `professor`, `curso`) VALUES ('52', 'Comissão', '12.50', '2024-01-05', '2024-01-20', 'Não', '0000-00-00', 'sem-foto.png', '2', 'dfafa');

-- TABLE: perguntas
CREATE TABLE `perguntas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aula` int(11) NOT NULL,
  `pergunta` varchar(255) NOT NULL,
  `curso` int(11) NOT NULL,
  `aluno` int(11) NOT NULL,
  `data` date NOT NULL,
  `respondida` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('3', '5', 'Pergunta para o Curso de Site imobiliario', '25', '20', '2022-04-12', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('5', '8', 'Pergunta para o gestao', '24', '20', '2022-04-12', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('6', '5', 'aaaaaaaaaaaaa', '24', '20', '2022-04-12', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('10', '9', 'Nova pergunta fdaf dfdaf fsadfdsafsdafsdf afasf', '25', '20', '2022-04-13', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('12', '0', 'fdsafadsfasas', '25', '20', '2022-04-13', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('16', '0', 'fdafasfaf', '28', '20', '2022-04-13', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('18', '0', 'fafdafaf  fdfsfsf', '25', '20', '2022-04-13', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('19', '0', 'fdsafadsf', '29', '20', '2022-04-13', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('20', '6', 'dggdsgsfdgsdg', '25', '20', '2022-04-13', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('22', '0', 'dsafaafa  ', '29', '20', '2022-04-18', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('23', '0', 'fadfaf   fdsafsdfa      ', '25', '20', '2022-04-18', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('24', '0', 'fdasfdasf', '33', '25', '2022-04-19', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('25', '0', 'fdsfafa', '26', '9', '2022-05-16', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('26', '0', 'fdsafdasf', '35', '25', '2022-05-16', 'Sim');
INSERT INTO `perguntas` (`id`, `aula`, `pergunta`, `curso`, `aluno`, `data`, `respondida`) VALUES ('27', '0', 'fdsafdasf', '26', '20', '2022-05-16', 'Não');

-- TABLE: perguntas_quest
CREATE TABLE `perguntas_quest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `curso` int(11) NOT NULL,
  `pergunta` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('1', '31', 'Pergunta 1');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('2', '31', 'Pergunta 2');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('5', '29', 'Pergunta 1');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('6', '29', 'Pergunta 2');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('7', '29', 'Pergunta 3');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('8', '31', 'Nova pergunta');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('9', '31', 'aaaaaaaaaaaaaa');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('10', '26', 'Qual é ...?');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('11', '26', 'Pergunta 2??');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('12', '26', 'Pergunta 3');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('13', '26', 'Pergunta 4');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('14', '25', 'teste');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('15', '25', 'teste2');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('16', '28', 'pergunta 1');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('17', '28', 'pergunta 2');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('18', '28', 'pergunta 3');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('19', '27', 'pergunta 1');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('20', '27', 'pergunta 2');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('21', '27', 'pergunta 3');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('22', '34', 'pergunta 1');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('23', '34', 'pergunta 2');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('24', '33', 'pergunta 1');
INSERT INTO `perguntas_quest` (`id`, `curso`, `pergunta`) VALUES ('25', '33', 'pergunta 2');

-- TABLE: perguntas_respostas
CREATE TABLE `perguntas_respostas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_aluno` int(11) NOT NULL,
  `pergunta` int(11) NOT NULL,
  `resposta` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `letra` varchar(5) NOT NULL,
  `numeracao` int(11) NOT NULL,
  `correta` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('1', '9', '5', '13', '29', '', '0', '');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('2', '9', '6', '17', '29', '', '0', '');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('3', '9', '7', '18', '29', '', '0', '');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('4', '9', '1', '25', '31', 'C', '1', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('5', '9', '2', '28', '31', 'C', '2', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('6', '9', '8', '31', '31', 'C', '3', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('7', '9', '9', '32', '31', 'A', '4', 'Não');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('8', '9', '16', '58', '28', 'A', '1', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('9', '9', '17', '61', '28', 'A', '2', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('10', '9', '18', '64', '28', 'A', '3', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('11', '9', '19', '70', '27', 'C', '1', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('12', '9', '20', '73', '27', 'C', '2', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('13', '9', '21', '74', '27', 'A', '3', 'Não');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('14', '9', '22', '79', '34', 'C', '1', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('15', '9', '23', '82', '34', 'C', '2', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('16', '9', '24', '85', '33', 'C', '1', 'Sim');
INSERT INTO `perguntas_respostas` (`id`, `id_aluno`, `pergunta`, `resposta`, `id_curso`, `letra`, `numeracao`, `correta`) VALUES ('17', '9', '25', '88', '33', 'C', '2', 'Sim');

-- TABLE: professores
CREATE TABLE `professores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `ativo` varchar(5) NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `professores` (`id`, `nome`, `cpf`, `telefone`, `email`, `foto`, `ativo`, `data`) VALUES ('2', 'Professor Teste', '555.555.555-55', '(33) 33333-3333', 'professor@hotmail.com', '08-03-2022-22-21-57-02-03-2022-09-46-45-katia.jpg', 'Sim', '2022-03-08');
INSERT INTO `professores` (`id`, `nome`, `cpf`, `telefone`, `email`, `foto`, `ativo`, `data`) VALUES ('3', 'Professor Teste 2', '888.888.888-88', '(00) 00000-0000', 'professor2@hotmail.com', '09-03-2022-15-04-53-02-03-2022-09-46-32-teste.jpg', 'Sim', '2022-03-09');
INSERT INTO `professores` (`id`, `nome`, `cpf`, `telefone`, `email`, `foto`, `ativo`, `data`) VALUES ('4', 'Professor Teste 5', '', '', 'professor5@hotmail.com', 'sem-perfil.jpg', 'Sim', '2022-05-16');

-- TABLE: receber
CREATE TABLE `receber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(50) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `data` date NOT NULL,
  `vencimento` date NOT NULL,
  `pago` varchar(5) NOT NULL,
  `data_pgto` date NOT NULL,
  `arquivo` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `receber` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`) VALUES ('2', 'Conta teste', '60.00', '2022-04-19', '2022-04-19', 'Não', '0000-00-00', '19-04-2022-18-58-13-19-04-2022-18-17-15-09-11-2021-12-04-29-pdfteste.zip');
INSERT INTO `receber` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`) VALUES ('3', 'Conta x', '600.00', '2022-04-19', '2022-04-19', 'Sim', '2022-04-19', '19-04-2022-18-58-39-09-11-2021-10-17-10-pdfteste.pdf');
INSERT INTO `receber` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`) VALUES ('4', 'fdsfa', '60.00', '2022-04-19', '0000-00-00', 'Não', '0000-00-00', 'sem-foto.png');
INSERT INTO `receber` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`) VALUES ('7', 'Conta teste', '60.00', '2022-05-17', '2022-05-17', 'Não', '0000-00-00', 'sem-foto.png');
INSERT INTO `receber` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`) VALUES ('8', 'Conta Testess', '90.00', '2022-05-17', '2022-05-17', 'Não', '0000-00-00', '17-05-2022-18-18-42-09-11-2021-13-58-28-pdfteste.pdf');
INSERT INTO `receber` (`id`, `descricao`, `valor`, `data`, `vencimento`, `pago`, `data_pgto`, `arquivo`) VALUES ('9', 'Aaa', '70.00', '2022-05-17', '2022-05-17', 'Não', '0000-00-00', 'sem-foto.png');

-- TABLE: respostas
CREATE TABLE `respostas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resposta` varchar(500) NOT NULL,
  `curso` int(11) NOT NULL,
  `pessoa` int(11) NOT NULL,
  `data` date NOT NULL,
  `pergunta` int(11) NOT NULL,
  `funcao` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('12', 'fdafadf', '25', '20', '2022-04-13', '3', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('13', 'gdgdfgd', '25', '25', '2022-04-13', '3', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('14', 'fdsafsf', '25', '20', '2022-04-13', '3', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('15', 'fsdfsdf', '24', '20', '2022-04-13', '6', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('16', 'aaaa', '24', '20', '2022-04-13', '6', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('26', 'fdfdsfs', '0', '1', '2022-04-13', '16', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('27', 'fdfsfdsfds', '0', '1', '2022-04-13', '16', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('28', 'fdfdfa', '0', '1', '2022-04-13', '5', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('30', 'fdfdfds', '0', '1', '2022-04-13', '10', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('31', 'fdfdsf', '0', '1', '2022-04-13', '12', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('32', 'fdsfdsfs', '0', '1', '2022-04-13', '6', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('33', 'fdsfa\r\nfdsfasf\r\n\r\nfadf', '25', '20', '2022-04-13', '12', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('34', 'fafadf\r\nfdsaf\r\n\r\nfadfafa', '25', '20', '2022-04-13', '10', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('35', 'fsdafaffa gfgs', '25', '20', '2022-04-13', '18', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('36', 'fdsafdasfa', '29', '20', '2022-04-13', '19', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('37', 'afa', '29', '25', '2022-04-13', '19', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('39', 'tretretert', '25', '20', '2022-04-13', '20', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('40', 'tertertertert', '25', '20', '2022-04-13', '20', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('41', 'gfgfdggfdg', '0', '1', '2022-04-13', '18', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('42', 'tretretertre', '0', '1', '2022-04-13', '20', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('43', 'fdafdafafsfs', '0', '1', '2022-04-13', '19', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('44', 'fdsfdasf', '25', '20', '2022-04-18', '23', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('45', 'fdfdsfds', '0', '1', '2022-04-19', '24', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('46', 'sddfdf', '26', '9', '2022-05-16', '25', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('47', 'fsfafa', '25', '20', '2022-05-16', '23', 'Aluno');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('48', 'dfasfasf', '0', '2', '2022-05-16', '23', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('49', 'sfdsfdsfsfdsfdsf', '0', '2', '2022-05-16', '3', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('50', 'gfdgfgdg', '0', '2', '2022-05-16', '10', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('51', 'fsdfsfaf', '0', '2', '2022-05-16', '3', 'Professor');
INSERT INTO `respostas` (`id`, `resposta`, `curso`, `pessoa`, `data`, `pergunta`, `funcao`) VALUES ('52', 'fdsafdf', '0', '14', '2022-05-16', '26', 'Professor');

-- TABLE: secretarios
CREATE TABLE `secretarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(100) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `foto` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `cartao` int(11) NOT NULL,
  `ativo` varchar(5) DEFAULT NULL,
  `arquivo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `secretarios` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('2', 'robert', '000.000.000-00', 'victoraugustopixar@gmail.com', '(31) 99198-2754', '45454545454', '35345344', '5345345', '77777', 'sem-perfil.jpg', '2024-01-08', '0', 'Sim', '');

-- TABLE: sessao
CREATE TABLE `sessao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `curso` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('6', 'Módulo 1', '6');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('7', 'Módulo 2', '6');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('8', 'Módulo 3', '6');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('9', 'Básico', '14');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('10', 'Avançado', '14');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('11', 'Básico', '29');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('12', 'Avançado', '29');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('13', 'Outro', '29');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('16', 'aaa', '35');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('17', 'bbb', '35');
INSERT INTO `sessao` (`id`, `nome`, `curso`) VALUES ('18', 'cc', '35');

-- TABLE: tesoureiros
CREATE TABLE `tesoureiros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(100) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `foto` varchar(100) NOT NULL,
  `data` date NOT NULL,
  `cartao` int(11) NOT NULL,
  `ativo` varchar(5) DEFAULT NULL,
  `arquivo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `tesoureiros` (`id`, `nome`, `cpf`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `pais`, `foto`, `data`, `cartao`, `ativo`, `arquivo`) VALUES ('5', 'vd', '275.275.272-75', '7474747@dcdfs', '(72) 52752-7527', '74574', 'Belo Horizonte', 'MG', '77777', 'sem-perfil.jpg', '2024-01-09', '0', 'Sim', '');

-- TABLE: usuarios
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(20) NOT NULL,
  `senha_crip` varchar(150) NOT NULL,
  `nivel` varchar(20) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `id_pessoa` int(11) NOT NULL,
  `ativo` varchar(5) NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('2', 'Hugo Vasconcelos', '000.000.000-00', 'contato@hugocursos.com.br', '123', '202cb962ac59075b964b07152d234b70', 'Administrador', '16-05-2022-10-21-44-eu.jpeg', '1', 'Sim', '2022-03-07');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('9', 'Aluno Testes', '222.222.222-22', 'aluno@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', '19-04-2022-21-25-52-02-03-2022-09-51-42-fabio.jpg', '12', 'Sim', '2022-03-08');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('10', 'Aluno Dois', '000.000.000-59', 'aluno2@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', '08-03-2022-21-15-02-02-03-2022-09-51-42-fabio.jpg', '13', 'Sim', '2022-03-08');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('13', 'Admin Teste', '333.333.333-33', 'admin@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Administrador', '08-03-2022-22-21-20-02-03-2022-09-59-04-Arthur.jpg', '3', 'Sim', '2022-03-08');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('14', 'Professor Teste', '555.555.555-55', 'professor@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Professor', '08-03-2022-22-21-57-02-03-2022-09-46-45-katia.jpg', '2', 'Sim', '2022-03-08');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('16', 'Professor Teste 2', '888.888.888-88', 'professor2@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Professor', '09-03-2022-15-04-53-02-03-2022-09-46-32-teste.jpg', '3', 'Sim', '2022-03-09');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('17', 'Aluno de Teste', '', 'alunodeteste@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '15', 'Sim', '2022-04-04');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('18', 'Aluno de Teste4', '', 'alunodeteste4@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '16', 'Sim', '2022-04-04');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('19', 'Aluno de Teste5', '', 'alunodeteste5@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '17', 'Sim', '2022-04-04');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('20', 'Hugo Freitas', 'MG0125698875', 'hugovasconcelosf@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', '13-04-2022-02-12-01-02-03-2022-09-51-42-fabio.jpg', '18', 'Sim', '2022-04-04');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('21', 'alunoparateste', '', 'alunoparateste@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '19', 'Sim', '2022-04-04');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('22', 'Aluno X', '', 'alunotestex@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '20', 'Sim', '2022-04-04');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('23', 'Paula', '', 'paula@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '21', 'Sim', '2022-04-11');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('24', 'Marcia Silva', '', 'marcia@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '22', 'Sim', '2022-04-12');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('25', 'Aluno 3', '', 'aluno3@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', '13-04-2022-02-15-17-02-03-2022-09-46-32-teste.jpg', '23', 'Sim', '2022-04-12');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('28', 'Marcelo Aluno Teste', '', 'alunotestemarcelo@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '26', 'Sim', '2022-04-20');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('29', 'Professor Teste 5', '', 'professor5@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Professor', 'sem-perfil.jpg', '4', 'Sim', '2022-05-16');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('30', 'Aluno teste 5', '', 'aluno5@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '27', 'Sim', '2022-05-16');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('31', 'aluno6', '', 'aluno6@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '28', 'Sim', '2022-05-16');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('33', 'teste@hotmail.com', '555.555.555-55', 'teste@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '30', 'Sim', '2022-05-17');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('34', 'Hugo Freitas', '12117853600', 'hugovasconcelosf@hotmai.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', 'sem-perfil.jpg', '31', 'Sim', '2023-06-12');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('35', 'Hugo Freitas', '7474747425252', 'hugofreitas20@hotmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Aluno', '29-11-2023-20-27-36-6729a536a21c1359916ccd7115a177d5.jpg', '32', 'Sim', '2023-06-12');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('37', 'robert', '000.000.000-00', 'victoraugustopixar@gmail.com', '123', '202cb962ac59075b964b07152d234b70', 'Secretario', 'sem-perfil.jpg', '2', 'Sim', '2024-01-08');
INSERT INTO `usuarios` (`id`, `nome`, `cpf`, `usuario`, `senha`, `senha_crip`, `nivel`, `foto`, `id_pessoa`, `ativo`, `data`) VALUES ('40', 'vd', '275.275.272-75', '7474747@dcdfs', '123', '202cb962ac59075b964b07152d234b70', 'Tesoureiro', 'sem-perfil.jpg', '5', 'Sim', '2024-01-09');

