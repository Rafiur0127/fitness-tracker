# Repository Cleanup Checklist

This checklist documents the staged cleanup of the legacy PHP route layer.
The verified legacy browser clients and original MVC tree have now been
removed; the verification record below explains why.

## Current architecture

| Area | Current role | Classification |
| --- | --- | --- |
| `frontend/` | Static HTML, CSS, and Vanilla JavaScript client | **Keep** |
| `public/api/` | JSON-only PHP REST endpoints | **Keep** |
| `config/` | PDO configuration and session bootstrap | **Keep** |
| `database/` | Database schema and initialization files | **Keep** |
| `docker/`, `Dockerfile`, `docker-compose.yml` | Runtime and Apache routing configuration | **Keep** |
| `README.md`, `.github/` | Deployment and CI documentation | **Keep and update as needed** |
| `public/*.php` | Legacy compatibility pages and old browser clients | **Verified and removed** |
| `public/assets/app-shell.js` | Navigation shell for the legacy PHP pages | **Verified and removed** |
| `app/` | Original MVC controllers, models, and views | **Verified and removed** |

## Active files to keep

These files are part of the intended decoupled application and must not be
removed during legacy cleanup:

- `frontend/*.html`
- `frontend/css/style.css`
- `frontend/js/app.js`
- `frontend/.htaccess`
- `public/api/*.php`
- `config/config.php`
- `database/schema.sql`
- `docker/apache-vhost.conf`
- `Dockerfile`
- `docker-compose.yml`

## Legacy files removed in this cleanup

The following compatibility files were removed after the static frontend,
authentication, navigation, API, and logout checks passed:

- `public/login.php`
- `public/signup.php`
- `public/dashboard.php`
- `public/workout_logger.php`
- `public/watertracker.php`
- `public/goals.php`
- `public/nutrition.php`
- `public/Workout_plans.php`
- `public/friend_challenges.php`
- `public/ajax_challenge.php`
- `public/assets/app-shell.js`

The original MVC tree and challenge AJAX compatibility file were removed
after all active API imports were migrated and the static frontend was
verified. Each replacement feature is available through the API and static
frontend:

- Authentication
- Dashboard
- Workouts
- Water tracking
- Goals
- Nutrition
- Workout plans
- Friend challenges

## Verification completed before deletion

### 1. Static frontend smoke test

With Docker running, verify that each page loads successfully:

- `/login.html`
- `/signup.html`
- `/dashboard.html`
- `/workout_logger.html`
- `/watertracker.html`
- `/goals.html`
- `/nutrition.html`
- `/workout_plans.html`
- `/friend_challenges.html`

### 2. Authentication and session flow

- Create a new account from `/signup.html`.
- Log in from `/login.html`.
- Confirm the session remains active while navigating between all modules.
- Confirm logout returns to `/login.html`.
- Open a protected page while logged out and confirm it redirects to login.
- Confirm the `next` parameter returns the user to the requested static page
  after successful login.

### 3. API and data flow

Using the browser Network tab, confirm:

- Requests go to `/api/*.php`, not to the legacy page files.
- API responses have `Content-Type: application/json`.
- Successful responses use the documented JSON envelope.
- Unauthorized requests return HTTP 401.
- Create operations return HTTP 201 where applicable.
- Forms submit without a full page reload.
- CSRF-protected writes include the CSRF header or JSON field.

### 4. Legacy route compatibility decision

Old URLs continue to redirect to the static frontend:

- If compatibility is required, move the redirect rules to an Apache-level
  configuration and remove only the PHP compatibility implementations.
- If compatibility is not required, remove the legacy rewrite rules from
  `frontend/.htaccess` and verify that no documentation or deployment script
  advertises the old URLs.

This is a product/deployment decision and must be made before deletion.

### 5. Reference scan

Run a repository-wide search for every legacy filename. A file was removable only when remaining matches were limited to this
checklist or historical documentation:

```powershell
rg -n "login\.php|signup\.php|dashboard\.php|workout_logger\.php|watertracker\.php|goals\.php|nutrition\.php|Workout_plans\.php|friend_challenges\.php|ajax_challenge\.php|app-shell\.js" .
```

Also search for direct includes or redirects into `app/` and `public/`.

### 6. Build and deployment checks

Run the repository's existing validation:

```powershell
docker compose config
docker compose build
docker compose up -d
```

Then smoke-test the application at `http://localhost:8080/login.html`.
After validation, stop the stack with:

```powershell
docker compose down
```

## Removal order after approval

For the verified legacy browser clients, the following cleanup is complete:

1. Remove references to legacy PHP routes from documentation and navigation.
2. Keep the legacy rewrite rules because old URLs are still required to
   redirect to the static frontend.
3. Remove the legacy navigation shell and browser clients.
4. Re-run the API and static frontend smoke tests.
5. Keep the MVC tree removed; future feature work belongs in `public/api/`
   and `frontend/`.
