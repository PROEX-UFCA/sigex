

# 🚀 Projeto Laravel com Docker

Este projeto utiliza Docker para criar um ambiente de desenvolvimento Laravel completo, com os seguintes serviços:

- PHP (com Laravel)
- MySQL
- Nginx
- Redis (opcional)
- phpMyAdmin

## 📦 Requisitos

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- Laravel já configurado no diretório do projeto

## ⚙️ Configuração inicial

### 1. Clonar o repositório

```bash
git clone https://github.com/Otavio-Ferreira/Docker-Laravel.git
````

### 2. Copiar o `.env`

Se ainda não tiver um `.env`, copie:

```bash
cp .env.example .env
```

E ajuste as seguintes variáveis de conexão com o banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=docker-laravel
DB_USERNAME=phpmyadmin
DB_PASSWORD=root
```

### 3. Criar os containers

```bash
sudo docker-compose up -d --build
```

Este comando irá:

* Criar os containers definidos no `docker-compose.yml`
* Instalar dependências PHP no container
* Levantar o ambiente completo

### 4. Acessar o container da aplicação

```bash
sudo docker-compose exec app bash
```

### 5. Instalar dependências PHP (dentro do container)

```bash
composer install
```

### 6. Rodar comandos Artisan (dentro do container)

```bash
php artisan key:generate
```
```bash
php artisan migrate --seed
```

### 7. Instalar dependências JS (caso use frontend)

```bash
npm install && npm run dev
```

### 8. Rodar os testes (se houver)

```bash
php artisan test
```

## 🔍 Acessos úteis

* Aplicação Laravel: [http://localhost:8000](http://localhost:8000)
* phpMyAdmin: [http://localhost:8080](http://localhost:8080)

  * Servidor: `db`
  * Usuário: `phpmyadmin`
  * Senha: `root`

## ✅ Checklist ao levantar o ambiente

* [x] Subiu os containers com `docker-compose up -d`
* [x] Acessou o container com `docker-compose exec app bash`
* [x] Rodou `composer install`
* [x] Rodou `php artisan key:generate`
* [x] Rodou `php artisan migrate`
* [x] Verificou o site em [http://localhost:8000](http://localhost:8000)


# Docker - Comandos úteis

Aqui estão os principais comandos Docker e `docker-compose` para gerenciar seu ambiente Laravel:

---

### 🔨 Buildar os containers (construir imagens)

```bash
sudo docker-compose build
````

Esse comando **reconstrói as imagens** com base nas instruções do `Dockerfile`, sem subir os containers.

---

### 🚀 Subir os containers

```bash
sudo docker-compose up -d
```

`-d` significa "detached", ou seja, roda em segundo plano. Usa o `docker-compose.yml` para levantar todos os serviços definidos.

> Dica: combine com `--build` se quiser buildar e subir ao mesmo tempo:

```bash
sudo docker-compose up -d --build
```

---

### 🛑 Parar os containers (sem remover)

```bash
sudo docker-compose stop
```

Isso apenas pausa os containers, mantendo-os disponíveis para restart.

---

### ▶️ Iniciar os containers que estão parados

```bash
sudo docker-compose start
```

Reinicia os containers que foram pausados com `stop`.

---

### ❌ Parar e remover todos os containers


```bash
sudo docker-compose down
```

Remove os containers criados, mas mantém as imagens, volumes e redes (a menos que você diga o contrário).

---

### ❌🧹 Parar e remover containers + volumes + redes

```bash
sudo docker-compose down -v --remove-orphans
```

`-v`: remove volumes (ex: banco de dados) `--remove-orphans`: remove containers que não estão mais no `docker-compose.yml`

> Use com cuidado, pois **apaga dados persistentes** como banco MySQL se estiver usando volumes locais.

---

### 🐚 Acessar o terminal dentro do container da aplicação Laravel

```bash
sudo docker-compose exec app bash
```

Depois de entrar, você pode rodar comandos PHP/Artisan, por exemplo:

```bash
php artisan migrate
```

---

### 📦 Ver containers em execução

```bash
sudo docker ps
```

---

### 🔍 Ver todos os containers (mesmo os parados)

```bash
sudo docker ps -a
```

---

### 🗑️ Remover containers parados

```bash
sudo docker container prune
```

---

### 🗑️ Remover imagens que não estão sendo usadas

```bash
sudo docker image prune
```

### ER das prmeiras tabelas

```mermaid
erDiagram
    USUARIO ||--o{ ACAO : "coordena"
    ACAO ||--o{ EQUIPE_ACAO : "possui_membros"
    USUARIO ||--o{ EQUIPE_ACAO : "atua_como_membro"
    INSTITUICAO_EXTERNA ||--o{ USUARIO : "possui_representantes"

    ACAO ||--o{ AGENDA_ACAO : "possui_eventos"
    INSTITUICAO_EXTERNA ||--o{ AGENDA_INSTITUICAO_EXTERNA : "informa_disponibilidade"
    INSTITUICAO_EXTERNA ||--o{ INTERESSE_ACAO : "demonstra_interesse"
    ACAO ||--o{ INTERESSE_ACAO : "recebe_interesse"

    INSTITUICAO_EXTERNA {
        uuid id PK
        string nome
        string cnpj
        string cep
        string logradouro
        string numero
        string complemento
        string telefone_contato
    }

    ACAO {
        uuid id PK
        uuid id_coordenador FK "Refere-se a USUARIO(id)"
        string id_atividade "Opcional se a ação for subdividida"
        string id_projeto "Opcional se pertencer a um projeto maior"
        string titulo
        string centro_departamento "Ex: Faculdade de Medicina"
        date data_inicio
        date data_fim
        int ano
        string tipo_acao "Ex: Curso, Evento"
        string area_tematica "Ex: Educação, Saúde"
        string modalidade "Ex: Prope, Ampla Concorrência"
    }

    EQUIPE_ACAO {
        uuid id_acao PK, FK
        uuid id_usuario PK, FK
        string categoria "Ex: Bolsista, Voluntário, Colaborador (VERIFICAR)"
    }

    USUARIO {
        uuid id PK
        uuid id_instituicao FK "Nulo se for usuário interno da universidade"
        string nome
        string email
        string senha "Hash"
        string cpf
        string centro_departamento
        string matricula_siape "Nulo se for representante externo"
        string perfil_ativo "Sinalizador: 'ADMIN', 'COORDENADOR', 'ESTUDANTE'"
        boolean status "Sinaliza se a conta está ativa"
    }

    AGENDA_ACAO {
        uuid id PK
        uuid id_acao FK
        string titulo_evento "Ex: Palestra Magna, Oficina 1"
        datetime data_hora_inicio
        datetime data_hora_fim
        string local_formato "Ex: Auditório Principal, Google Meet"
        string descricao
    }

    AGENDA_INSTITUICAO_EXTERNA {
        uuid id PK
        uuid id_instituicao FK
        date data_disponivel
        time hora_inicio
        time hora_fim
        string observacao "Ex: Auditório com 50 lugares disponível"
    }

    INTERESSE_ACAO {
        uuid id_instituicao PK, FK
        uuid id_acao PK, FK
    }
```