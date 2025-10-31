# Gerenciamento de Consultas e Serviços

### Iniciando o projeto

- Acessar a pasta appointments_services_manager
- Criar o arquivo .env
- Copiar o arquivo .env.example para o .env
- Executar o comando abaixo para buildar o projeto:
````
docker compose build
````
- Executar o comando abaixo para subir o projeto:
```
docker compose up
```
- Em um novo terminal, executar o comando abaixo para executar as migrations:
```
docker compose exec app bash
php artisan migrate
```
