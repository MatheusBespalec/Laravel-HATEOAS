# :UPD8 Teste

## Requisitos
- Git
- PHP e extensões basicas do laravel
- Composer
- node/npm
- MySql

## Instalação
1. Clone este repositório
<pre>
```bash
git clone https://github.com/MatheusBespalec/cadastro-clientes-laravel
```
</pre>

2. Baixe as dependências do composer.json
    <pre>
```bash
composer install
```
</pre>

3. Baixe as dependências do package.json
    <pre>
```bash
npm install
```
</pre>

4. Realize o build com npm
    <pre>
```bash
npm run build
```
</pre>

5. Faça uma cópia do arquivo .env.exemplo colocando o nome da cópia como .env
    <pre>
```bash
cp .env.exemplo .env
```
</pre>

6. Gere a chave do projeto
    <pre>
```bash
php artisan key:generate
```
</pre>

7. Gere as migrações e popule o banco de dados (Para este passo é necessario realizar a conexão com um banco de dados MySql, providencie o mesmo e edite as credenciais e nomde da base de dados no arquivo .env, se preferir pode usar o arquivo docker-compose.yml na aplicação para subir seu banco de dados, já estara devidamente configurado nas variáveis de ambiente, bastando apenas executar o comando "docker compose up")
    <pre>
```bash
php artisan migrate --seed
```
</pre>

8. Inicie o servidor da aplicação
    <pre>
```bash
php artisan serve
```
</pre>

9. Agora você já pode acessar a aplicação acessando http://127.0.0.1:8000 no navegador de sua preferência