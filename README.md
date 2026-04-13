# EmpreenderRH

Bem-vindo ao **EmpreenderRH**, um sistema moderno e completo para recrutamento e seleção, voltado tanto para empresas que buscam os melhores talentos quanto para candidatos à procura de novas oportunidades. Construído com PHP 8+, PDO e Tailwind CSS, o sistema é resiliente, seguro e possui uma interface premium.

---

## 📋 Índice

- [Módulos do Sistema](#-módulos-do-sistema)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação e Configuração](#-instalação-e-configuração)
  - [1. Configurando o Servidor Web](#1-configurando-o-servidor-web)
  - [2. Configurando o Banco de Dados](#2-configurando-o-banco-de-dados)
  - [3. Arquivo de Configuração](#3-arquivo-de-configuração)
- [Acesso Incial](#-acesso-inicial)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Desenvolvimento](#-desenvolvimento)

---

## 🧩 Módulos do Sistema

O EmpreenderRH está dividido em 3 áreas principais:

1. **🧑‍💼 Candidato (`/candidato`)**
   - Criação de perfil, currículo, portfólio.
   - Busca por vagas.
   - Candidatura e acompanhamento de status.
   - Favoritar vagas.

2. **🏢 Empresa (`/empresa`)**
   - Perfil corporativo.
   - Criação e disparo de vagas.
   - Gestão de candidaturas (recebimento e alteração de status).

3. **👑 Admin (`/admin`)**
   - Visão global do sistema.
   - Gerenciamento de acessos e banimentos.
   - Métricas de uso da plataforma.

---

## ⚙️ Pré-requisitos

Para rodar o sistema, sua máquina / servidor precisará de:

- **PHP**: Versão 8.0 ou superior (Devido à exigência de recursos modernos).
- **Banco de Dados**: MySQL (5.7+ / 8.0+) ou MariaDB.
- **Servidor Web**: Apache ou Nginx (ou utilizar o embutido do PHP para desenvolvimento).
- **PDO Extension**: Habilitado no `php.ini` (`extension=pdo_mysql`).

> **Recomendação para Windows:** Instalar o pacote **XAMPP** ou **Laragon**, que já contêm todas as ferramentas necessárias.

---

## 🚀 Instalação e Configuração

Siga os passos abaixo para ter a plataforma funcionando localmente:

### 1. Configurando o Servidor Web

Se estiver utilizando ferramentas como XAMPP, coloque a pasta `EmpreenderRH` dentro diretório público (`htdocs` para XAMPP ou `www` para WAMP).

Outra opção mais rápida para dev é usar o servidor embutido do PHP:
1. Abra o Terminal/Prompt de Comando.
2. Navegue até a pasta do projeto: `cd c:\EmpreenderRH`
3. Inicie o servidor, rodando:
   ```bash
   php -S localhost:8000
   ```
4. Acesse o sistema via `http://localhost:8000`.

### 2. Configurando o Banco de Dados

1. Abra o painel de gerenciamento do seu banco de dados, como o phpMyAdmin (geralmente em `http://localhost/phpmyadmin`).
2. Vá até a aba "Importar".
3. Selecione o arquivo de schema contido neste projeto:
   `database/schema.sql`
4. Clique em executar. Isso irá criar o banco de dados `empreender_rh`, criar as tabelas e o administrador inicial.

Se preferir via linha de comando:
```bash
mysql -u root -p < database/schema.sql
```

### 3. Arquivo de Configuração

Revise as chaves de acesso ao banco de dados abrindo o arquivo `config/db.php`.

```php
// config/db.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'empreender_rh');
define('DB_USER', 'root'); // Mude se o seu usuário não for 'root'
define('DB_PASS', '');     // Mude se houver uma senha na sua base local
```
Altere `DB_USER` e `DB_PASS` caso seu ambiente exija senha!

---

## 🔑 Acesso Inicial

O schema inicial do banco também já cria o **Administrador Padrão** visando agilizar o uso da plataforma em um novo setup. 

Após subir o servidor e configurar o banco, você pode acessar a página de admin (`/admin`) ou logar via `index.php` informando:

- **Login / E-mail:** `cadu@empreenderrh.com`
- **Senha:** `caducadu`

Você pode então aproveitar a ferramenta para ver o funcionamento da aplicação ou gerenciar outras funções. Para testar o fluxo comum, experimente criar novas contas do tipo "Candidato" e "Empresa" na página de cadastro (`signup.php` ou acessando via front-end).

---

## 📁 Estrutura do Projeto

```text
C:\EmpreenderRH
├── index.php             # Página inicial e Landing Page com rota de login
├── login.php             # Sistema de Autenticação universal (processador)
├── logout.php            # Destrói a sessão e cookies
├── signup.php            # Registro universal (candidato ou empresa)
├── config/               
│   └── db.php            # Conexão centralizada com o banco de BD via PDO
├── database/             
│   └── schema.sql        # Modelagem do bd, tabelas e seeds iniciais
├── includes/             
│   └── alerts.php        # Componentes de alertas pro UI, etc...
├── admin/                # Retaguarda Administrativa (Restrita ao role 'admin')
├── empresa/              # Área Logada para as empresas (Restrito a 'empresa')
└── candidato/            # Área Logada para postulantes (Restrito a 'candidato')
```

---

## 🧑‍💻 Desenvolvimento

O projeto faz uso intenso do **Tailwind CSS**. 
Na versão de desenvolvimento rápida, o script do Tailwind está sendo carregado via CDN para facilitar protótipos visuais dinâmicos. 

**Segurança Utilizada:**
- Tratamento contra IDOR checando o User ID de quem solicita nas queries.
- Prevenção a SQL Injection atráves de PDO e "Prepared Statements".
- Emissão de hashs criptografados do tipo BCRYPT via `password_hash()` da própria stack do PHP para senhas.
