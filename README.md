# 🗨️ Forum API – Symfony 6 + JWT Authentication

A simple RESTful forum API built with Symfony 6. Features include:

- User registration and login
- JWT-based authentication
- Role-based access control (User/Admin)
- Post, Comment, and Group management
- Voters and `@IsGranted` for resource protection
- Unified JSON responses via `ApiResponseFormatter`

---

## 📋 Requirements

- PHP >= 8.1  
- Composer  
- Symfony CLI (optional)  
- Docker (optional but recommended)  

---

## ⚙️ Installation

```bash
# 1. Clone the repository
git clone https://github.com/kwiatkowskijan/mushroom-forum-api
cd mushroom-forum-api

# 2. Install dependencies
composer install

# 3. Copy environment config
cp .env .env.local
```

---

## 🔐 JWT Key Generation

If not generated yet:

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Then add to `.env.local`:

```env
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase_here
```

---

## 🧪 Database Setup

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Optional: load fixtures (if any)
php bin/console doctrine:fixtures:load
```

---

## ▶️ Running the App

**Using Docker**:

```bash
docker compose up -d --build
```

---

## 🔐 Authentication Endpoints

- `POST /api/register` – Register new users  
- `POST /api/login` – Log in and receive a JWT token

Use the token in all protected requests via the `Authorization` header:

```http
Authorization: Bearer <your_token_here>
```

---

## 📚 API Endpoints Overview

| Endpoint              | Method | Access         | Description              |
|-----------------------|--------|----------------|--------------------------|
| `/api/posts`          | GET    | Public         | List all posts           |
| `/api/posts`          | POST   | Authenticated  | Create a post            |
| `/api/posts/{id}`     | PUT    | Post Author    | Edit a post              |
| `/api/posts/{id}`     | DELETE | Post Author    | Delete a post            |
| `/api/comments`       | POST   | Authenticated  | Add a comment            |
| `/api/comments/{id}`  | DELETE | Comment Author | Delete a comment         |
| `/api/groups`         | GET    | Public         | List all groups          |
| `/api/groups`         | POST   | Admin Only     | Create a group           |
| `/api/groups/{id}`    | DELETE | Admin Only     | Delete a group           |

---

## 🔒 Roles & Permissions

- `ROLE_USER` – Default user role  
- `ROLE_ADMIN` – Full access (e.g. delete groups)  

### Voters are used to protect resources:

- Only post authors can edit or delete their own posts  
- Only comment authors can delete their comments  
- Only admins can delete groups  

Symfony’s `#[IsGranted()]` attribute is used for method-level access control.

---

## 🧪 Testing with Postman

1. Log in at `/api/login` to get a JWT token  
2. Use the token in your request headers:

```http
Authorization: Bearer <your_token>
```

3. Access protected routes and test permissions (e.g., try editing or deleting another user's post)

---

## 📦 API Response Format

All API responses follow this standard structure using `ApiResponseFormatter`:

**Success response:**

```json
{
  "success": true,
  "data": { ... },
  "errors": null
}
```

**Error response:**

```json
{
  "success": false,
  "data": null,
  "errors": [ "Something went wrong." ]
}
```

---

## 🛠 Tech Stack

- Symfony 6  
- Doctrine ORM  
- LexikJWTAuthenticationBundle  
- Symfony Security (Voters, Roles)  
- Docker (optional)  
- Postman (for API testing)  
