# 🏋️ Fitness Tracker

A full-stack PHP web application for tracking workouts, water intake, fitness goals, and community challenges. The backend is a JSON REST API and the frontend is a decoupled Vanilla JavaScript client using PDO/MySQL and session-based authentication.

---

## Features

- **User Authentication** — Secure registration & login with `password_hash` / `password_verify`, session fixation prevention, and CSRF protection on every form
- **Workout Logger** — Log strength and cardio sessions; view recent history
- **Workout Plans** — Browse and follow structured training plans
- **Water Intake Tracker** — Log daily hydration and track progress toward daily goal
- **Goal Setting** — Create personal fitness goals and mark them complete
- **Dashboard** — Weekly and 4-week trend charts; lifetime stats; achievement badges
- **Community Challenges** — Create and join step challenges with a live leaderboard

---

## Tech Stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Backend    | PHP 8.1+                          |
| Database   | MySQL 8 via PDO (prepared statements) |
| Frontend   | HTML5, CSS3, vanilla JS           |
| Auth       | PHP Sessions + `password_hash`    |
| Pattern    | REST API + static frontend         |

---

## Project Structure

```
fitness_tracker/
├── config/
│   └── config.php          # DB connection and session bootstrap
├── frontend/               # Static browser UI (HTML, CSS, and shared JavaScript)
├── public/
│   └── api/                # JSON-only PHP REST endpoints
├── .env.example            # Environment variable template
└── .gitignore
```

---

## Getting Started

### Prerequisites
- PHP 8.1+
- MySQL 8+
- A local server (XAMPP, Laragon, or Apache configured like the included vhost)
- Docker Desktop with Docker Compose (recommended)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/fitness_tracker.git
cd fitness_tracker

# 2. Create your environment file
cp .env.example .env
# Edit .env with your database credentials

# 3. Import the database schema
mysql -u your_user -p fitness_tracker1 < database/schema.sql
```

Configure Apache's document root as `frontend/`, add an `/api/` alias to
`public/api/` (the provided `docker/apache-vhost.conf` does this), and visit
`http://localhost:8080/login.html`.

### Run with Docker Compose

Docker Compose starts Apache/PHP, MySQL, and phpMyAdmin with the correct
service-to-service database host:

```bash
docker compose up --build
```

- Application: `http://localhost:8080/login.html`
- API base: `http://localhost:8080/api/` (the existing PHP API URLs are unchanged)
- phpMyAdmin: `http://localhost:8081`

The database is persisted in the `db_data` volume. To recreate it from
`database/schema.sql`, run `docker compose down --volumes` before starting the
stack again.

### CI/CD

The [local_CICD.yml](.github/workflows/local_CICD.yml) workflow runs on pushes
and pull requests. It validates the Compose file, builds the application image,
lint-checks every PHP file, starts the complete stack, and smoke-tests the login
page.

---

## Security Highlights

- All user input goes through PDO prepared statements — no raw SQL
- Passwords hashed with `PASSWORD_BCRYPT` via `password_hash()`
- CSRF tokens on every state-changing form
- `session_regenerate_id(true)` called on login to prevent session fixation
- Credentials kept out of version control via `.env` + `.gitignore`
- DB errors logged server-side; generic message shown to users

---

## Screenshots

> *(Add screenshots of Dashboard, Workout Logger, and Challenges pages here)*

---

## License

MIT
