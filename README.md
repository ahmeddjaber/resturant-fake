# Pure PHP Restaurant Backend

Professional backend starter built with pure PHP, PDO, OOP, and a custom MVC architecture.

## Folder Structure

```text
app/
  controllers/
    MenuController.php
    ReservationController.php
    ReviewController.php
  models/
    Menu.php
    Reservation.php
    Review.php
config/
  database.php
core/
  Controller.php
  Database.php
  Env.php
  Model.php
  Router.php
database/
  schema.sql
public/
  .htaccess
  index.php
routes/
  api.php
  web.php
storage/
  logs/
  uploads/
utils/
  helpers.php
.env.example
.htaccess
README.md
```

## Setup

1. Copy `.env.example` to `.env` and update database credentials.
2. Import `database/schema.sql` into MySQL.
3. Point Apache to the project root or keep the provided `.htaccess` rewrite rules enabled.
4. Open the app through `public/index.php`.

## API Endpoints

- `GET /api/menu`
- `POST /api/reservation`
- `GET /api/reviews`
- `POST /api/reviews`

## Notes

- Uses PDO prepared statements to prevent SQL injection.
- Includes basic validation and output sanitization.
- Supports `.env` loading without external packages.
