# Sistema de Autenticação com CodeIgniter 4 e JWT

Este projeto é um sistema de autenticação com `JWT` desenvolvido usando o framework PHP CodeIgniter 4. Ele implementa funcionalidades completas de registro, login, verificação de sessão com tokens JWT, logout, recuperação e redefinição de senha.

---

## **Pré-requisitos**

- PHP >= 7.4
- Composer
- Servidor web (Apache/Nginx)
- MySQL ou outro banco de dados compatível com CodeIgniter 4

---

## **Instalação**

1. Clone este repositório:

   ```bash
   git clone https://github.com/devfelipelimabr/login_system_backend.git
   cd login_system_backend
   ```

2. Instale as dependências usando o Composer:

   ```bash
   composer install
   ```

3. Copie o arquivo `.env.example` para `.env`:

   ```bash
   cp .env.example .env
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
   JWT_ISSUER = "nome_do_projeto"
   JWT_AUDIENCE = "usuarios_do_projeto"
   ```

6. Execute as migrações para criar as tabelas necessárias:

   ```bash
   php spark migrate
   ```

---

## **Funcionalidades**

- **Cadastro de Usuário**: Permite registrar novos usuários.
- **Login de Usuário**: Gera um token JWT para usuários autenticados.
- **Verificação de Token**: Verifica se um token JWT é válido.
- **Logout de Usuário**: "Invalida" um token adicionando-o a uma lista de tokens revogados (blacklist).
- **Recuperação de Senha**: Envia um e-mail para redefinição de senha com um link/token.
- **Redefinição de Senha**: Permite ao usuário redefinir sua senha utilizando um token válido.

---

## **Endpoints**

### **1. Registro de Usuário**

**Rota**: `/api/v2/register`  
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
- `405 Method Not Allowed`: Método HTTP não permitido.  
- `409 Conflict`: E-mail já está registrado.

---

### **2. Login de Usuário**

**Rota**: `/api/v2/login`  
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
    "token": "seu_token_jwt",
    "expires_in": "timestamp_do_tempo_de_expiração"
  }
  ```

- `401 Unauthorized`: Credenciais inválidas.
- `405 Method Not Allowed`: Método HTTP não permitido.  

---

### **3. Verificação de Token**

**Rota**: `/api/v2/verify`  
**Método**: `POST`  
**Corpo da Requisição**:

```json
{
  "token": "seu_token_jwt"
}
```

**Resposta**:

- `200 OK`: Token válido.
- `401 Unauthorized`: Token não fornecido ou inválido.
- `401 Unauthorized`: Token revogado.
- `405 Method Not Allowed`: Método HTTP não permitido.  

---

### **4. Logout de Usuário**

**Rota**: `/api/v2/logout`  
**Método**: `POST`  
**Cabeçalho da Requisição**:  
Inclua o token JWT no cabeçalho da requisição utilizando o formato padrão:

```http
Authorization: Bearer <seu_token_jwt>
```

**Resposta**:

- `200 OK`: Logout realizado com sucesso.  
- `401 Unauthorized`: Token inválido, ausente ou já revogado.  
- `405 Method Not Allowed`: Método HTTP não permitido.  

---

### **5. Recuperação de Senha**

**Rota**: `/api/v2/recovery`  
**Método**: `POST`  
**Corpo da Requisição**:

```json
{
  "email": "usuario@exemplo.com"
}
```

**Resposta**:

- `200 OK`: E-mail de recuperação enviado com sucesso.
- `401 Unauthorized`: E-mail inválido.
- `405 Method Not Allowed`: Método HTTP não permitido.  

---

### **6. Verificação de Token de Redefinição**

**Rota**: `/api/v2/reset-confirm/{token}`  
**Método**: `GET`  

**Resposta**:

- `200 OK`: Token de redefinição válido.
- `401 Unauthorized`: Token expirado ou inválido.
- `405 Method Not Allowed`: Método HTTP não permitido.  

---

### **7. Redefinição de Senha**

**Rota**: `/api/v2/reset`  
**Método**: `POST`  
**Corpo da Requisição**:

```json
{
  "reset_token": "token_de_redefinicao",
  "password": "nova_senha"
}
```

**Resposta**:

- `200 OK`: Nova senha salva com sucesso.
- `401 Unauthorized`: Token inválido ou expirado.
- `405 Method Not Allowed`: Método HTTP não permitido.  

---

## **Modelos**

### **UserModel**

Gerencia os dados do usuário, incluindo armazenamento de senhas com hash e recuperação de tokens de redefinição.

### **BlacklistedTokenModel**

Gerencia tokens JWT revogados para logout e controle de sessão.

---

## **Segurança**

- **Armazenamento de senhas**: As senhas são armazenadas usando `bcrypt`.
- **Validação JWT**: Tokens são validados e autenticados em cada requisição protegida.
- **Lista negra de tokens**: Tokens podem ser revogados com a lista de tokens na blacklist.
- **Tokens de redefinição**: Gerados e validados com prazo de expiração.

---

## **Configuração de Ambiente**

Configure corretamente o arquivo `.env` com as variáveis abaixo:

- `JWT_SECRET`: Chave secreta para gerar os tokens JWT.
- `JWT_ISSUER`: Emissor do token.
- `JWT_AUDIENCE`: Público-alvo do token.

---

## **Licença**

Este projeto está licenciado sob a [MIT License](LICENSE).
