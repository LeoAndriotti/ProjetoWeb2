# 📰 CSL Times - Portal de Notícias

![CSL Times Logo](assets/img/logo2.png)

## 📋 Descrição

O **CSL Times** é um portal de notícias completo desenvolvido em PHP, que permite aos usuários publicar, gerenciar e visualizar notícias. O sistema oferece funcionalidades de cadastro de usuários, publicação de notícias com imagens, categorização de conteúdo e uma interface moderna e responsiva.

## ✨ Funcionalidades

### 🔐 Sistema de Autenticação
- **Cadastro de Usuários**: Registro com nome, sexo, telefone, email e senha
- **Login/Logout**: Sistema de autenticação seguro
- **Recuperação de Senha**: Alteração de senha via email
- **Perfil do Usuário**: Edição de dados pessoais

### 📰 Gestão de Notícias
- **Publicação**: Criação de notícias com título, conteúdo, data, autor e categoria
- **Upload de Imagens**: Suporte para imagens locais ou URLs externas
- **Categorização**: Organização por categorias
- **Edição**: Modificação de notícias existentes
- **Exclusão**: Remoção de notícias
- **Visualização**: Modal para leitura completa das notícias

### 🎨 Interface
- **Design Responsivo**: Adaptável a diferentes tamanhos de tela
- **Interface Moderna**: Design limpo e profissional
- **Modais Interativos**: Para login, cadastro e visualização de notícias
- **Navegação Intuitiva**: Menu de navegação fácil de usar

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 7.4+**: Linguagem principal do projeto
- **MySQL**: Banco de dados
- **PDO**: Conexão segura com banco de dados
- **Sessions**: Gerenciamento de sessões de usuário

### Frontend
- **HTML5**: Estrutura semântica
- **CSS3**: Estilização moderna e responsiva
- **JavaScript**: Interatividade e modais
- **Font Awesome**: Ícones
- **Google Fonts**: Tipografia (Poppins)

### Estrutura
- **Arquitetura MVC**: Organização em classes e arquivos separados
- **POO**: Programação Orientada a Objetos
- **CRUD**: Operações básicas de banco de dados

## 📁 Estrutura do Projeto

```
ProjetoFinalWEB/
├── assets/
│   └── img/
│       ├── logo.png
│       ├── logo2.png
│       └── linkedin.png
├── classes/
│   ├── BancoDeDados.php      # Classe de conexão com banco
│   ├── Usuario.php           # Classe para gestão de usuários
│   ├── Noticias.php          # Classe para gestão de notícias
│   └── Categoria.php         # Classe para gestão de categorias
├── config/
│   └── config.php            # Configurações do banco de dados
├── uploads/
│   └── style.css             # Arquivo de estilos
├── index.php                 # Página principal (público)
├── indexUsuario.php          # Página para usuários logados
├── portal.php                # Portal administrativo
├── registrar.php             # Cadastro de usuários
├── cadastrarNoticia.php      # Publicação de notícias
├── alterar.php               # Edição de perfil
├── alterarNoticia.php        # Edição de notícias
├── deletar.php               # Exclusão de usuários
├── deletarNoticia.php        # Exclusão de notícias
├── logout.php                # Logout do sistema
└── README.md                 # Este arquivo
```

## 🚀 Instalação e Configuração

### Pré-requisitos
- **XAMPP** ou servidor Apache com PHP
- **MySQL** 5.7+
- **PHP** 7.4+
- **Navegador web** moderno

### Passos para Instalação

1. **Clone o repositório**
   ```bash
   git clone [URL_DO_REPOSITORIO]
   cd ProjetoFinalWEB
   ```

2. **Configure o banco de dados**
   - Inicie o XAMPP (Apache e MySQL)
   - Acesse o phpMyAdmin: `http://localhost/phpmyadmin`
   - Crie um banco de dados chamado `bdcrud`

3. **Configure as credenciais**
   - Edite o arquivo `config/config.php`
   - Ajuste as configurações do banco se necessário:
   ```php
   private $servidor = "localhost";
   private $nome_banco = "bdcrud";
   private $usuario = "root";
   private $senha = "";
   ```

4. **Crie as tabelas no banco**
   ```sql
   -- Tabela de usuários
   CREATE TABLE usuarios (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nome VARCHAR(100) NOT NULL,
       sexo ENUM('M', 'F') NOT NULL,
       fone VARCHAR(15) NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       senha VARCHAR(255) NOT NULL
   );

   -- Tabela de categorias
   CREATE TABLE categorias (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nome VARCHAR(50) NOT NULL
   );

   -- Tabela de profissões
   CREATE TABLE profissao (
       id INT AUTO_INCREMENT PRIMARY KEY,
       nome VARCHAR(100) NOT NULL
   );

   -- Tabela de notícias
   CREATE TABLE noticias (
       id INT AUTO_INCREMENT PRIMARY KEY,
       titulo VARCHAR(200) NOT NULL,
       noticia TEXT NOT NULL,
       data DATE NOT NULL,
       autor INT NOT NULL,
       categoria INT NOT NULL,
       imagem VARCHAR(255),
       FOREIGN KEY (autor) REFERENCES usuarios(id),
       FOREIGN KEY (categoria) REFERENCES categorias(id)
   );

   -- Adicionar coluna profissao na tabela usuarios
   ALTER TABLE usuarios ADD COLUMN profissao INT;
   ALTER TABLE usuarios ADD CONSTRAINT fk_usuario_profissao 
   FOREIGN KEY (profissao) REFERENCES profissao(id);

   -- Inserir profissões padrão
   INSERT INTO profissao (nome) VALUES 
   ('Jornalista'),
   ('Anunciante'),
   ('Editor'),
   ('Repórter'),
   ('Fotógrafo'),
   ('Designer'),
   ('Administrador');

   -- Inserir categorias padrão
   INSERT INTO categorias (nome) VALUES 
   ('Política'),
   ('Economia'),
   ('Tecnologia'),
   ('Esportes'),
   ('Entretenimento'),
   ('Saúde'),
   ('Educação');
   ```

5. **Configure as permissões**
   - Certifique-se de que a pasta `uploads/` tenha permissões de escrita

6. **Acesse o projeto**
   - Abra o navegador
   - Acesse: `http://localhost/ProjetoFinalWEB`

## 👥 Como Usar

### Para Visitantes
1. Acesse a página principal (`index.php`)
2. Visualize as notícias publicadas
3. Clique em uma notícia para ler o conteúdo completo
4. Use o botão "Entrar" para acessar o sistema

### Para Usuários
1. **Cadastro**: Acesse "Registre-se aqui" na página de login
2. **Login**: Use email e senha para acessar
3. **Portal**: Após o login, acesse o portal administrativo
4. **Publicar**: Use "Adicionar Notícia" para criar conteúdo
5. **Gerenciar**: Edite ou exclua suas notícias
6. **Perfil**: Atualize seus dados pessoais

### Funcionalidades do Portal
- **Dashboard**: Visão geral das suas notícias
- **Nova Notícia**: Formulário completo para publicação
- **Editar Notícia**: Modificar conteúdo existente
- **Excluir Notícia**: Remover publicações
- **Editar Perfil**: Atualizar dados pessoais

## 🔧 Classes e Métodos

### BancoDeDados
- `obterConexao()`: Estabelece conexão com o banco

### Usuario
- `registrar()`: Cadastra novo usuário
- `fazerLogin()`: Autentica usuário
- `ler()`: Lista todos os usuários
- `lerPorId()`: Busca usuário por ID
- `atualizar()`: Atualiza dados do usuário
- `deletar()`: Remove usuário
- `buscarPorEmail()`: Busca usuário por email

### Noticias
- `registrar()`: Publica nova notícia
- `ler()`: Lista todas as notícias
- `lerNoticia()`: Busca notícia específica
- `lerPorId()`: Busca notícia por ID
- `lerPorAutor()`: Lista notícias por autor
- `lerPorCategoria()`: Lista notícias por categoria
- `atualizar()`: Modifica notícia
- `deletar()`: Remove notícia

### Categoria
- `lerPorId()`: Busca categoria por ID
- `lerTodas()`: Lista todas as categorias

## 🎨 Personalização

### Estilos
- Edite o arquivo `uploads/style.css` para personalizar a aparência
- O projeto usa CSS moderno com variáveis CSS para fácil customização

### Imagens
- Substitua os logos em `assets/img/` pelos seus próprios
- As imagens das notícias são salvas em `uploads/`

### Configurações
- Modifique `config/config.php` para alterar configurações do banco
- Ajuste as categorias padrão no banco de dados

## 🔒 Segurança

### Implementado
- **Hash de Senhas**: Uso de `password_hash()` e `password_verify()`
- **Prepared Statements**: Prevenção de SQL Injection
- **Validação de Sessões**: Controle de acesso
- **Sanitização de Dados**: `htmlspecialchars()` para XSS
- **Controle de Upload**: Validação de tipos de arquivo

### Recomendações
- Configure HTTPS em produção
- Implemente rate limiting
- Adicione validação mais robusta
- Configure backup automático do banco

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
- Verifique se o MySQL está rodando
- Confirme as credenciais em `config/config.php`
- Certifique-se de que o banco `bdcrud` existe

### Erro de Tabela Profissão
Se você encontrar erro sobre a tabela `profissao` não existir:
1. Execute o arquivo `setup_profissao.php` no navegador
2. Ou execute manualmente os comandos SQL do arquivo `criar_tabela_profissao.sql`
3. Isso criará a tabela e adicionará a coluna necessária na tabela `usuarios`

### Erro de Upload de Imagem
- Verifique permissões da pasta `uploads/`
- Confirme se o PHP tem extensão GD habilitada
- Verifique o limite de upload no `php.ini`

### Página em Branco
- Verifique logs de erro do PHP
- Confirme se todas as classes estão sendo incluídas
- Verifique se o arquivo `BancoDeDados.php` existe

## 📝 Log de Alterações

### Versão 2.0 (Atual)
- ✅ Tradução completa para português
- ✅ Refatoração das classes
- ✅ Melhoria na estrutura do código
- ✅ Correção de bugs

### Versão 1.0
- ✅ Sistema básico de notícias
- ✅ Cadastro e login de usuários
- ✅ Interface responsiva

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 👨‍💻 Desenvolvedor

**CSL Times Team**
- Email: contato@csltimes.com
- Website: www.csltimes.com

## 🙏 Agradecimentos

- Font Awesome pelos ícones
- Google Fonts pela tipografia
- Comunidade PHP pelo suporte
- Todos os contribuidores do projeto

---

**CSL Times - Your window to the world!** 🌍
