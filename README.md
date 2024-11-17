
# Open Foods API

Este projeto foi desenvolvido como parte de um desafio técnico para construir uma REST API que utiliza dados do Open Food Facts. O objetivo é fornecer suporte à equipe de nutricionistas da Open Foods LC, permitindo que revisem rapidamente as informações nutricionais de produtos alimentícios enviados pelos usuários.

---

## **Instalação**

### **Requisitos**
Certifique-se de que seu ambiente atenda aos seguintes requisitos antes de prosseguir:
- PHP 7.4.33
- Composer
- PostgreSQL
- Laravel 8

### **Passos para instalar**

1. **Clone o repositório**
   ```bash
   git clone https://github.com/grezlima18/open-foods-api.git
   cd open-foods-api
   ```

2. **Instale as dependências**
   ```bash
   composer install
   ```

3. **Configure o arquivo `.env`**
   Copie o exemplo de configuração:
   ```bash
   cp .env.example .env
   ```
   Atualize as variáveis no arquivo `.env` para refletir suas configurações locais:
   - `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `APP_URL` para refletir a URL do ambiente local.

4. **Gere a chave da aplicação**
   ```bash
   php artisan key:generate
   ```

5. **Configure o banco de dados**
   Crie o banco de dados no PostgreSQL e execute as migrações:
   ```bash
   php artisan migrate
   ```

6. **Inicie o servidor**
   ```bash
   php artisan serve
   ```
   Acesse a aplicação em [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## **Funcionalidades da API**

- **GET** `/products`: Lista todos os produtos disponíveis.
- **POST** `/products`: Adiciona um novo produto com informações nutricionais.
- **GET** `/products/{id}`: Obtém detalhes de um produto específico.
- **PUT** `/products/{id}`: Atualiza as informações nutricionais de um produto.
- **DELETE** `/products/{id}`: Remove um produto.

---

## **Configuração do Cron**

A aplicação inclui uma tarefa agendada para sincronizar os dados do Open Food Facts automaticamente. Este processo garante que as informações estejam sempre atualizadas.

### **Como funciona**
- O comando `sync:openfoodfacts` sincroniza dados do Open Food Facts com a base local.
- A sincronização é agendada para rodar diariamente à meia-noite utilizando o Laravel Scheduler.

### **Configuração no Servidor**
1. Configure o cron job para rodar o scheduler do Laravel. Edite o crontab:
   ```bash
   crontab -e
   ```

2. Adicione a seguinte linha ao arquivo do crontab:
   ```bash
   * * * * * cd /caminho/para/seu/projeto && php artisan schedule:run >> /dev/null 2>&1
   ```

3. O comando acima verifica a cada minuto se há tarefas agendadas para serem executadas.
