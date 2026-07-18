# 🚂 Railpaths — Bangladesh Railway Ticket Booking System

> A full-featured web application for browsing, booking, and managing Bangladesh Railway train tickets — with real-time weather forecasts for departure and destination stations.

---

## 📌 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Architecture](#system-architecture)
- [User Roles](#user-roles)
- [Weather API Integration](#weather-api-integration)
- [Installation & Setup](#installation--setup)
- [Environment Variables](#environment-variables)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

---

## About the Project

**Railpaths** is a Laravel 10-powered web application designed to digitize and simplify the Bangladesh Railway ticketing experience. It provides passengers with a platform to search for trains, view schedules, book tickets, and receive real-time weather information for their travel destinations — helping them plan journeys more effectively.

The application enforces a multi-role system — **Admin**, **Station Master**, and **Passenger** — each with a dedicated portal and tailored set of capabilities.

---

## Features

### 🎫 Passenger Features
- Register/Login with secure authentication
- Search trains by source and destination station
- View schedules, seat availability, and fares
- Book tickets and download/print e-tickets
- View booking history and cancel tickets
- **Real-time weather forecast at departure & destination stations**

### 🛤️ Station Master Features
- Manage trains assigned to their station
- View and update live train schedules
- Manage platform assignments
- Monitor passenger manifests for upcoming trains

### 🛠️ Admin Features
- Full CRUD for Trains, Stations, Routes, and Schedules
- Manage all user accounts and roles
- View system-wide booking reports and revenue summaries
- Configure fare structure per route/class

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Backend Framework** | Laravel 10 (PHP 8.2) |
| **Frontend** | Blade Templates, Bootstrap 5, JavaScript |
| **Database** | MySQL 8 |
| **Authentication** | Laravel Breeze (multi-guard) |
| **Weather Integration** | OpenWeatherMap API |
| **HTTP Client** | Laravel HTTP Facade (Guzzle) |
| **Version Control** | Git / GitHub |
| **Local Server** | Apache (XAMPP / Laragon) |

---

## System Architecture

```
┌─────────────────────────────────────────────────┐
│                  Client Browser                  │
└──────────────────────┬──────────────────────────┘
                       │ HTTP
┌──────────────────────▼──────────────────────────┐
│              Laravel 10 Application              │
│                                                  │
│   ┌──────────┐   ┌──────────┐   ┌───────────┐  │
│   │  Routes  │──▶│Controllers│──▶│  Services  │  │
│   └──────────┘   └──────────┘   └─────┬─────┘  │
│                                        │         │
│   ┌───────────────┐    ┌──────────────▼──────┐  │
│   │ Blade Views   │    │  Eloquent Models    │  │
│   │ (Bootstrap 5) │    │  (MySQL Database)   │  │
│   └───────────────┘    └─────────────────────┘  │
│                                                  │
│   ┌──────────────────────────────────────────┐  │
│   │         WeatherService                   │  │
│   │   OpenWeatherMap API  ←→  HTTP Client    │  │
│   └──────────────────────────────────────────┘  │
└──────────────────────────────────────────────────┘
```

---

## User Roles

The application uses **separate authentication guards** for each role:

| Role | Guard | Default Route After Login |
|---|---|---|
| Admin | `admin` | `/admin/dashboard` |
| Station Master | `stationmaster` | `/stationmaster/dashboard` |
| Passenger | `web` | `/dashboard` |

Each role has its own login page, middleware protection, and dedicated set of routes.

---

## Weather API Integration

The `weather-api-add` branch introduces live weather data directly into the passenger journey experience.

### How it works

1. When a passenger searches for or books a train between two stations, the app fetches current weather conditions for both the **source** and **destination** stations via the **OpenWeatherMap API**.
2. Weather data is displayed on the train search results page and the ticket confirmation page.
3. A dedicated `WeatherService` class handles all API communication, caching, and error handling.

### What's displayed
- 🌡️ Temperature (°C)
- 🌤️ Weather condition (Clear, Rain, Fog, etc.)
- 💧 Humidity percentage
- 💨 Wind speed (km/h)
- Weather condition icon

### Caching
Weather responses are cached for **30 minutes** using Laravel's cache system to avoid hitting API rate limits on repeated searches for the same station.

---

## Installation & Setup

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL 8.0+
- Node.js & npm (for asset compilation)
- An OpenWeatherMap API key (free tier works)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/ullas-6575/Railpaths.git
cd Railpaths

# 2. Switch to the weather-api-add branch
git checkout weather-api-add

# 3. Install PHP dependencies
composer install

# 4. Install JS dependencies
npm install && npm run dev

# 5. Copy environment file
cp .env.example .env

# 6. Generate application key
php artisan key:generate

# 7. Configure your .env file (see below)

# 8. Run migrations and seed the database
php artisan migrate --seed

# 9. Start the development server
php artisan serve
```

---

## Environment Variables

Add the following to your `.env` file:

```env
APP_NAME=Railpaths
APP_ENV=local
APP_KEY=          # generated by artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=railpaths
DB_USERNAME=root
DB_PASSWORD=

# OpenWeatherMap API
OPENWEATHER_API_KEY=your_api_key_here
OPENWEATHER_BASE_URL=https://api.openweathermap.org/data/2.5

# Cache driver (use 'file' for local dev)
CACHE_DRIVER=file
CACHE_TTL=1800
```

> Get a free API key at [https://openweathermap.org/api](https://openweathermap.org/api)

---

## Database Setup

After running `php artisan migrate --seed`, the database will include:

- Default admin account: `admin@railpaths.com` / `password`
- Sample stations: Dhaka, Chittagong, Sylhet, Rajshahi, Khulna, etc.
- Sample train routes and schedules
- Sample fare structure per class (Shovan, Shovan Chair, Snigdha, AC)

To reset and re-seed:
```bash
php artisan migrate:fresh --seed
```

---

## Usage

### Accessing the App

| Role | URL | Default Credentials |
|---|---|---|
| Passenger | `http://localhost:8000/` | Register a new account |
| Station Master | `http://localhost:8000/stationmaster/login` | Seeded via DatabaseSeeder |
| Admin | `http://localhost:8000/admin/login` | `admin@railpaths.com` / `password` |

### Local Network Access (Mobile Testing)

To test on other devices on the same Wi-Fi network:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
Then access via `http://<your-local-ip>:8000` on your mobile device.

---

## Project Structure

```
Railpaths/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── StationMaster/
│   │   │   ├── Passenger/
│   │   │   └── WeatherController.php      ← New
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Train.php
│   │   ├── Station.php
│   │   ├── Route.php
│   │   ├── Schedule.php
│   │   └── Booking.php
│   └── Services/
│       └── WeatherService.php             ← New
├── config/
│   └── weather.php                        ← New
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── admin/
│       ├── stationmaster/
│       ├── passenger/
│       └── components/
│           └── weather-card.blade.php     ← New
├── routes/
│   └── web.php
└── .env.example
```

---

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you'd like to change.

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

---

## License

This project is developed as an academic project at **Khulna University of Engineering & Technology (KUET)** under the Department of Computer Science & Engineering. All rights reserved.

---

<p align="center">Made with ❤️ using Laravel 10 | Bangladesh Railway Ticketing System</p>