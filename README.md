# Sistema de Autenticação com CodeIgniter 4 e JWT

Este projeto é um sistema de autenticação com `JWT` desenvolvido usando o framework PHP CodeIgniter 4. Ele implementa funcionalidades básicas de registro, login, verificação de sessão com tokens JWT e logout.

## Pré-requisitos

- PHP >= 7.4
- Composer
- Servidor web (Apache/Nginx)
- MySQL ou outro banco de dados compatível com CodeIgniter 4

## Instalação

1. Clone este repositório:

   ```bash
   git clone https://github.com/devfelipelimabr/login_system_backend.git
   cd seuprojeto
   ```

2. Instale as dependências usando o Composer:

   ```bash
   composer install
   ```

3. Copie o arquivo `.env.example` para `.env`:

   ```bash
   cp env.example .env
   ```

4. Configure seu banco de dados no arquivo `.env`:

   ```plaintext
   database.default.hostname = localhost
   database.default.database = nome_do_banco
   database.default.username = usuario
   database.default.password = senha
   database.default.DBDriver = MySQLi
   ```

5. Configure a chave secreta para os tokens JWT no arquivo `.env`:

   ```plaintext
   JWT_SECRET = "sua_chave_secreta_aqui"
   ```

6. Execute as migrações para criar as tabelas necessárias:

   ```bash
   php spark migrate
   ```

## Funcionalidades

- **Cadastro de Usuário**: Permite registrar novos usuários.
- **Login de Usuário**: Gera um token JWT para usuários autenticados.
- **Verificação de Token**: Verifica se um token JWT é válido.
- **Logout de Usuário**: "Invalida" um token adicionando-o a uma lista de tokens revogados (blacklist).

## Endpoints

### 1. Registro de Usuário

**Rota**: `/register`  
**Método**: `POST`  
**Corpo da Requisição**:

```json
{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

**Resposta**:

- `201 Created`: Usuário registrado com sucesso.
- `400 Bad Request`: Falha na validação dos campos.
- `409 Conflict`: E-mail já está registrado.

### 2. Login de Usuário

**Rota**: `/login`  
**Método**: `POST`  
**Corpo da Requisição**:

```json
{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

**Resposta**:

- `200 OK`: Token JWT gerado com sucesso.
  ```json
  {
    "token": "seu_token_jwt"
  }
  ```
- `401 Unauthorized`: Credenciais inválidas.

### 3. Verificação de Token

**Rota**: `/verify`  
**Método**: `POST`  
**Corpo da Requisição**:

````json
{
  "token": "gjkjgJHGj687%87",
}`

**Resposta**:
- `200 OK`: Token válido.
- `401 Unauthorized`: Token não fornecido.
- `401 Unauthorized`: Token inválido ou revogado.

### 4. Logout de Usuário

**Rota**: `/logout`
**Método**: `POST`
**Corpo da Requisição**:

```json
{
  "token": "gjkjgJHGj687%87",
}`

**Resposta**:
- `200 OK`: Logout realizado com sucesso.
- `401 Unauthorized`: Token inválido ou ausente.

## Modelos

### UserModel

Modelo para gerenciar os dados do usuário.

### BlacklistedTokenModel

Modelo para gerenciar tokens JWT revogados.

## Segurança

- As senhas são armazenadas com hash utilizando `bcrypt`.
- Tokens JWT são verificados e validados em cada requisição protegida.
- Tokens JWT podem ser "revogados" com a abordagem de lista de tokens em blacklist para logout.

## Configuração de Ambiente

Certifique-se de configurar seu ambiente de desenvolvimento corretamente definindo as variáveis no arquivo `.env`.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).
````

### Observações:

- Personalize o `README.md` de acordo com o nome do seu repositório e outras informações específicas, como links e referências.
- Inclua informações adicionais se você modificar ou adicionar funcionalidades novas.
