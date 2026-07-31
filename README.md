## Diagrama de Banco de Dados (ER) - Parte 1

Abaixo está o modelo Entidade-Relacionamento das tabelas da primeira parte do sistema, ilustrando como usuários, ações e instituições se conectam.

```mermaid
erDiagram
    ACAO ||--o{ EQUIPE_ACAO : "possui_membros"
    USUARIO ||--o{ EQUIPE_ACAO : "atua_como_membro"

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
        string email
        string senha
        boolean status
    }

    ACAO {
        uuid id PK
        string id_projeto
        int ano
        text titulo
        string modalidade_edital
        string bolsas_solicitadas
        string bolsas_concedidas
        string financiamento_interno
        string financiamento_externo
        string situacao
        date data_cadastro
        date data_inicio
        date data_fim
        date data_atualizacao
        string centro_departamento_sigla
        string tipo_acao
        string area_tematica
        text resumo
        text palavras_chave
        string ods
        string contexto
        boolean status
        text img
    }

    EQUIPE_ACAO {
        uuid id_acao PK, FK
        uuid id_usuario PK, FK
        string id_pessoa
        string tipo_membro
        string categoria_membro
        string status
        date data_inicio
        date data_fim
        string tipo_vinculo
    }

    USUARIO {
        int id PK
        uuid uuid PK
        string name
        string email
        datetime email_verified_at
        string password "Hash"
        boolean status "Sinaliza se a conta está ativa"
        string centro_departamento
        string matricula_siape
        string perfil_ativo
        string phone
        datetime last_login_at
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
        string observacao "Ex: Auditório com 50 lugares disponível"
    }

    INTERESSE_ACAO {
        uuid id_instituicao PK, FK
        uuid id_acao PK, FK
    }
```

## Diagrama de Banco de Dados (ER) - Parte 2

Abaixo está o modelo Entidade-Relacionamento das tabelas da segunda parte do sistema, ilustrando como irá funcionar o mini forms.

```mermaid
erDiagram
    FORMULARIO ||--o{ SECAO : possui
    SECAO ||--o{ PERGUNTA : contem
    PERGUNTA ||--o{ OPCAO_PERGUNTA : "tem (select, radio, checkbox)"
    FORMULARIO ||--o{ RELATORIO : recebe
    RELATORIO ||--o{ SUBMISSAO : recebe
    SUBMISSAO ||--o{ RESPOSTA : contem
    PERGUNTA ||--o{ RESPOSTA : "referencia a"
    OPCAO_PERGUNTA ||--o{ RESPOSTA : "vinculada a (se aplicavel)"
    
    PERGUNTA ||--o{ VALIDACAO_RESPOSTA :tem
    RESPOSTA ||--o{ VALIDACAO_RESPOSTA :tem

    FORMULARIO {
        uuid id PK
        string titulo
        text descricao
        boolean status
        boolean published
    }

    SECAO {
        uuid id PK
        uuid id_formulario FK
        string titulo
        text descricao
        int ordem "Controla a sequência das páginas"
    }

    PERGUNTA {
        uuid id PK
        uuid id_secao FK
        uuid id_pergunta_pai FK
        string tipo "Ex: text, textarea, select, checkbox, radio, file, number"
        text enunciado
        boolean obrigatoria
        double min "Tamanho mínimo do texto ou número"
        double max "Tamanho máximo do texto ou número"
        double step "Tamanho máximo do texto ou número"
        double step "Intervalo numérico"
        string accept "Extensões permitidas"
        string regex "Padrão de validação customizado"
    }

    OPCAO_PERGUNTA {
        uuid id PK
        uuid id_pergunta FK
        string rotulo
        string valor
    }

    RELATORIO {
        uuid id PK
        uuid id_formulario FK
        string titulo
        datetime data_inicio
        datetime prazo
        boolean status
    }

    SUBMISSAO {
        uuid id PK
        uuid id_relatorio FK 
        uuid id_acao FK
        datetime finalizada_em
    }

    RESPOSTA {
        uuid id PK
        uuid id_submissao FK
        uuid id_pergunta FK
        text valor
        int indice_grupo
    }

    VALIDACAO_RESPOSTA{
        uuid id PK
        uuid id_resposta FK
        uuid id_avaliador FK
        boolean status 
        text correcao
    }
```
---

## Referência da API

### Listar Ações
Retorna todas as ações cadastradas no sistema com paginação.

```http
GET /api/acoes
```

### Filtrar Ações
Você pode passar parâmetros na URL (Query Params) para filtrar os resultados. Ideal para barras de pesquisa e filtros no front-end.

**Filtro Simples (Ex: por área temática)**
```http
GET /api/acoes?area_tematica=trabalho
```

**Filtros Combinados**
```http
GET /api/acoes?area_tematica=comunicacao&titulo=teste&modalidade=teste&centro_departamento=teste
```

---

## Deploy no Coolify

Use o arquivo `docker-compose.prod.yml` com o build pack `Docker Compose`.

Serviços do stack:

- `nginx`: serviço HTTP público na porta `80`
- `app`: PHP-FPM com bootstrap do Laravel
- `worker`: processamento das filas
- `db`: MySQL com volume persistente
- `redis`: Redis com volume persistente

O serviço `app` executa `php artisan migrate --force` no bootstrap quando `RUN_MIGRATIONS=true`.

Variáveis mínimas no Coolify:

```env
APP_KEY=base64:gere-uma-chave-valida
APP_URL=https://seu-dominio.example.com
DB_DATABASE=sigex
DB_USERNAME=sigex
DB_PASSWORD=troque-isto
DB_ROOT_PASSWORD=troque-isto-tambem
```

Para produção, mantenha:

```env
APP_ENV=production
APP_DEBUG=false
LOG_CHANNEL=stderr
DB_HOST=db
REDIS_HOST=redis
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
RUN_MIGRATIONS=true
```
