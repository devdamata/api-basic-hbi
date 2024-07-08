# Api básica Contacts - Teste Prático HBI

Repositório focado em realizar o desafio de criar uma api completa com GET, POST, PUT e DELETE + enriquecimento de dados
com integração ViaCep, objetivo é demonstrar conhecimentos técnicos e conhecimentos conceituais sobre diversos temas
voltados a cultura back-end de desenvolvimento, como boas práticas, nível de segurança, velocidade de resposta entre outros.

Na sequencia teremos um passo a passo para a configuração do projeto e posteriomente passar por testes e avaliações.

## O que foi utilizado?

* [CodeIgniter 4](https://codeigniter.com/user_guide/intro/index.html)
* [Postman](https://www.postman.com/)
* [PHPUnit](https://phpunit.de/documentation.html)
* [Download Collection de teste para postman (arquivo JSON)](https://drive.google.com/file/d/1HiwXzzYpfIQytFt1KhYzi1EJyW96MlQw/view)
* [Link para collection diretamente do postman.com](https://www.postman.com/devdamata/workspace/api-bsica-hbi-teste-prtico/documentation/8035394-34721988-bf2e-450e-8108-5466444f66c8)

## Requisitos

PHP 8.1 ou superior

CodeIgniter 4

Composer version 2.4.4 ou superior

Mysql version 8.0.31

---------------------------------------------

## Instalação

### Passo 1: Clone do repositório no GitHub
```console
> git clone https://github.com/devdamata/api-basic-hbi.git
> cd api-basic-hbi
```
### Passo 2: Instalar as dependências

```console
> composer install
```
### Passo 3: Configurar o arquivo Database.php
app/Config/Database.php

```console
//Configuração do banco de produção
public array $default = [
    'DSN'          => '',
    'hostname'     => 'localhost',
    'username'     => 'root',
    'password'     => '',
    'database'     => 'api-basic-hbi',
    'DBDriver'     => 'MySQLi',
    'DBPrefix'     => '',
    'pConnect'     => false,
    'DBDebug'      => true,
    'charset'      => 'utf8mb4',
    'DBCollat'     => 'utf8mb4_general_ci',
    'swapPre'      => '',
    'encrypt'      => false,
    'compress'     => false,
    'strictOn'     => false,
    'failover'     => [],
    'port'         => 3306,
    'numberNative' => false,
    'dateFormat'   => [
        'date'     => 'Y-m-d',
        'datetime' => 'Y-m-d H:i:s',
        'time'     => 'H:i:s',
    ],
];
```
```console
//Configuração do banco de teste
public array $tests = [
    'DSN'          => '',
    'hostname'     => 'localhost',
    'username'     => 'root',
    'password'     => '',
    'database'     => 'api-basic-hbi-tests',
    'DBDriver'     => 'MySQLi',
    'DBPrefix'     => '',
    'pConnect'     => false,
    'DBDebug'      => true,
    'charset'      => 'utf8mb4',
    'DBCollat'     => 'utf8mb4_general_ci',
    'swapPre'      => '',
    'encrypt'      => false,
    'compress'     => false,
    'strictOn'     => false,
    'failover'     => [],
    'port'         => 3306,
    'numberNative' => false,
    'dateFormat'   => [
        'date'     => 'Y-m-d',
        'datetime' => 'Y-m-d H:i:s',
        'time'     => 'H:i:s',
    ],
];
```
Agora para criar as tabelas a partir do CodeIgniter 4 vamos precisar dos seguintes comandos:
```console
> php spark db:create api-basic-hbi
> php spark db:create api-basic-hbi-tests

> php spark migrate
```

### Passo 4: Subir o servidor embutido da aplicação
```console
> php spark serve
```
### Passo 5: Acessar a api
```console
> GET    - http://localhost:8080/api/contacts
> POST   - http://localhost:8080/api/contacts
> PUT    - http://localhost:8080/api/contacts{id}  
> DELETE - http://localhost:8080/api/contacts/{id}
```
### Passo 6: Rodar testes PhpUnit
```console
> ./vendor/bin/phpunit
```
## Reforçando a documentação dos endpois
Estou disponibilizando 2 maneiras de acesso a essa documentação, download + import do arquivo json da collection ou o 
link da collection direto do postman.com

* [Download Collection de teste para postman (arquivo JSON)](https://drive.google.com/file/d/1HiwXzzYpfIQytFt1KhYzi1EJyW96MlQw/view)
* [Link para collection diretamente do postman.com](https://www.postman.com/devdamata/workspace/api-bsica-hbi-teste-prtico/documentation/8035394-34721988-bf2e-450e-8108-5466444f66c8)

