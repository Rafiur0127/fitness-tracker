# 🏋️ Fitness Tracker

A full-stack PHP web application for tracking workouts, water intake, fitness goals, and community challenges. Built with a clean MVC architecture, PDO/MySQL, and session-based authentication.

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
| Pattern    | MVC (Model / View / Controller)   |

---

## Project Structure

```
fitness_tracker/
├── config/
│   └── config.php          # DB connection, CSRF helpers (reads from .env)
├── app/
│   ├── model/              # Database queries (PDO, prepared statements)
│   ├── controller/         # Business logic & input validation
│   └── view/               # HTML templates (PHP views)
├── public/                 # Entry-point PHP files (URL endpoints)
├── .env.example            # Environment variable template
└── .gitignore
```

---

## Getting Started

### Prerequisites
- PHP 8.1+
- MySQL 8+
- A local server (XAMPP, Laragon, or PHP built-in server)
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

# 4. Start your local server
php -S localhost:8000 -t public/
```

Then visit `http://localhost:8000/login.php`.

### Run with Docker Compose

Docker Compose starts Apache/PHP, MySQL, and phpMyAdmin with the correct
service-to-service database host:

```bash
docker compose up --build
```

- Application: `http://localhost:8080/login.php`
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
