# API de Gerenciamento de Consultas e Serviços

Bem-vindo à API de Gerenciamento de Consultas e Serviços! Esta é uma API RESTful completa, construída com Laravel 12 e Docker, projetada para gerenciar consultas e serviços.

O projeto utiliza uma arquitetura limpa (`Controller` > `Service` > `Repository`), é totalmente testada (PHPUnit) e documentada (Swagger/OpenAPI).

## Principais Funcionalidades

* **Autenticação:** Sistema completo de registro (`/register`) e login (`/login`) usando Laravel Sanctum.
* **CRUD de Serviços:** Gerenciamento completo de serviços (nome, preço).
* **CRUD de Consultas:** Gerenciamento completo de consultas com cálculo de preço dinâmico.
* **Cálculo de Preço:** O `total_price` de um consultas é calculado automaticamente somando os preços dos serviços anexados.
* **Filas:** Envio de e-mails (como confirmação de consultas) de forma assíncrona usando Redis para alta performance.
* **Documentação:** Documentação OpenAPI (Swagger) completa e interativa.
* **Testes:** Cobertura robusta de testes de Feature (Integração) e Unitários.

## Tech Stack

* **Backend:** Laravel 12 (PHP 8.4)
* **Banco de Dados:** MySQL 8
* **Cache & Filas:** Redis
* **Servidor Web:** Nginx
* **Containerização:** Docker & Docker Compose
* **Autenticação:** Laravel Sanctum
* **Testes:** PHPUnit
* **Documentação:** `l5-swagger` (OpenAPI)
* **E-mail (Dev):** MailHog

---

## Instalação e Execução

Siga estes passos para configurar e rodar o ambiente de desenvolvimento localmente.

### 1. Pré-requisitos

* [Docker](https://www.docker.com/get-started)
* [Docker Compose](https://docs.docker.com/compose/install/)

### 2. Configuração do Ambiente

1.  **Clone o repositório:**
    ```bash
    git clone [URL_DO_SEU_REPOSITORIO]
    cd appointments_services_manager
    ```

2.  **Crie seu arquivo `.env`:**
    ```bash
    cp .env.example .env
    ```

3.  **Configure o `.env`:**
    Abra o arquivo `.env` e configure-o para se conectar aos serviços do Docker. O `docker-compose.yml` foi feito para que estas sejam as configurações corretas:

    ```env
    # Conexão com o Docker
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=laravel_db
    DB_USERNAME=root
    DB_PASSWORD=root

    REDIS_HOST=redis
    REDIS_PORT=6379

    # Configuração do MailHog
    MAIL_MAILER=smtp
    MAIL_HOST=mailhog
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    ```
    **Importante:** Os valores de `DB_DATABASE` e `DB_PASSWORD` no seu `.env` devem ser os mesmos que estão no seu arquivo `docker-compose.yml`.

### 3. Subindo os Containers

1.  **Buildar e Subir (em background):**
    ```bash
    docker compose up -d --build
    ```

2.  **Instalar Dependências (Composer):**
    ```bash
    docker compose exec app composer install
    ```

3.  **Gerar a Chave do Laravel:**
    ```bash
    docker compose exec app php artisan key:generate
    ```

4.  **Rodar as Migrations:**
    ```bash
    docker compose exec app php artisan migrate
    ```

### 4. Aplicação em Execução!

Seu ambiente está pronto!

* **API (Nginx):** [http://localhost:8000](http://localhost:8000)
* **MailHog (UI para ver e-mails):** [http://localhost:8025](http://localhost:8025)
* **Banco de Dados (Host):** Você pode se conectar ao MySQL com um cliente de BD (TablePlus, DBeaver) usando `Host: 127.0.0.1` e **Porta: `3305`**.

### 5. Processando as Filas

Para que os e-mails sejam enviados, você precisa iniciar o "worker" das filas. Em um novo terminal, execute:

```bash
docker compose exec app php artisan queue:work
```

### 6. Como rodar os Testes
O projeto está coberto por testes.

```bash
# Rodar todos os testes
docker compose exec app php artisan test

# Rodar apenas os testes de Feature (Integração)
docker compose exec app php artisan test --testsuite=Feature

# Rodar apenas os testes Unitários
docker compose exec app php artisan test --testsuite=Unit
```

### 7. Documentação da API (Swagger)
A API está 100% documentada usando OpenAPI (Swagger).

1. Gerar a documentação: Se você fizer qualquer alteração nos atributos #[OA] dos controllers (ou se o arquivo api-docs.json não estiver presente), gere-o novamente:
```bash
    docker compose exec app php artisan l5-swagger:generate
```
2. Acesse a Documentação: Abra no seu navegador para ver a interface interativa do Swagger: http://localhost:8000/api/documentation
