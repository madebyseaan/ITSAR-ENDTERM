# Bookstore ERP Project Instructions

## 🛑 STRICT MANDATES
- **No Hallucinations:** Do not hallucinate when prompting. Always remember and focus strictly on the provided context and explicit user instructions.
- **Empirical Validation:** Never assume code works. Always verify by reading files, checking database schemas in `db-init/`, or running relevant shell commands.
- **Security First:** Never log, print, or commit secrets or API keys. Protect `.env` and `config.php` content.

## 🏗️ Architecture Overview
This is a microservices-based ERP system for a bookstore:
- **API Gateway:** Central entry point (`api-gateway/gateway.php`) handling routing and JWT verification.
- **Microservices:**
    - `auth-service`: Handles login and registration.
    - `inventory-service`: Manages books, authors, and stock.
    - `order-service`: Handles checkouts and order status updates.
    - `reporting-service`: Provides analytics.
- **Frontend:** Legacy PHP/jQuery interface with Bootstrap 3.
- **Database:** MySQL with per-service databases initialized via `db-init/`.

## 🛠️ Development Workflow
- **Environment Toggle:** The `$is_production` flag in `config.php` switches between Local XAMPP and Docker environments.
    - `false` (Default): Local XAMPP (localhost paths).
    - `true`: Docker (internal service names like `http://inventory-service`).
- **Docker:** Services are containerized. Use `docker-compose up --build` to start the full stack.
- **DB Initialization:** Database schemas are defined in `db-init/*.sql`. Refer to these files to understand the data structure.
- **Documentation:** Functional requirements and service details are located in text files at the root (e.g., `inventory-docs.txt`, `orders-docs.txt`). Consult these for business logic rules.

## 💻 Coding Standards
- **PHP Style:** Procedural PHP using `mysqli`.
- **Database:** ALWAYS use **prepared statements** for any queries involving user-supplied input to prevent SQL injection.
- **API Responses:** Services should return JSON with a consistent structure: `{"status": "success/error", "data": ..., "message": "..."}`.
- **Routing:** New routes must be added to `api-gateway/gateway.php`.
- **JWT:** Protected routes require a valid JWT passed in the `Authorization: Bearer <token>` header.

## 🔒 Security & Data Integrity
- Validate all input server-side.
- Ensure JWT verification is applied in the Gateway for all protected resources.
- Respect the separate database boundaries for each microservice.
