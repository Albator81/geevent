# Ge'Event

**An event management platform for a local tech community** — publish meetups and after-work events, let members sign up, showcase sponsors and keep everyone informed, without depending on meetup.com.

Designed for **Sacrée Tech**, a tech association based in Reims (France), and built with **Symfony 7.3**, **Doctrine / MySQL**, **EasyAdmin** and **Leaflet**.

![Ge'Event home page](docs/screenshots/home.jpg)

> The interface is in French, like the community it was made for.

## Features

**For visitors and members**

- **Events** — browse upcoming meetups and *apéros* with cover image, dates, address, organiser, sponsors and number of participants; a live search bar suggests events as you type.
- **Interactive map** — every geolocated event appears on an OpenStreetMap map of Reims, filterable by event type.
- **Sign-ups** — members join or leave an event in one click and find their upcoming events on their profile. Joining requires a verified email address.
- **Accounts** — registration with an email verification link, login with "remember me", profile editing (changing your email triggers a new verification).
- **Announcements** — news from the association, either public or reserved for logged-in members, also sent by email to verified members when published.
- **Sponsors** — a showcase page and a detail page per sponsor, with logo and website.
- **Chatbot** — an assistant backed by the Mistral AI API that answers questions using the site's current events, announcements and sponsors as context, with a per-session conversation history.

**For administrators**

- **Back office** built with EasyAdmin: manage events, users, sponsors and announcements.
- **Automatic geocoding** — type an address and let the OpenStreetMap Nominatim API fill in the coordinates that place the event on the map.
- **Uploads** — event covers and sponsor logos.

## Screenshots

| Events list | Interactive map |
| :---: | :---: |
| ![Events](docs/screenshots/events.jpg) | ![Map](docs/screenshots/map.jpg) |
| **Event page** | **Sponsors** |
| ![Event](docs/screenshots/event.jpg) | ![Sponsors](docs/screenshots/sponsors.jpg) |
| **Member profile** | **Administration (EasyAdmin)** |
| ![Profile](docs/screenshots/profile.png) | ![Admin](docs/screenshots/admin.png) |

## Tech stack

| Layer | Tools |
| :--- | :--- |
| Backend | PHP 8.2+, Symfony 7.3, Doctrine ORM, Symfony Security, Symfony Forms |
| Database | MySQL 8, Doctrine Migrations (SQLite for the test suite) |
| Administration | EasyAdmin 4 |
| Frontend | Twig, Bootstrap 5, vanilla JavaScript, AssetMapper (no Node build step) |
| Maps | Leaflet + OpenStreetMap, Nominatim geocoding |
| Email | Symfony Mailer |
| AI | Mistral AI chat completions API, Marked for rendering answers |
| Quality | Codeception functional tests, PHP-CS-Fixer, Twig-CS-Fixer, Foundry factories and fixtures |

## Data model

```mermaid
erDiagram
    USER ||--o{ EVENTS : organises
    USER }o--o{ EVENTS : "takes part in"
    USER ||--o{ ANNONCES : publishes
    EVENTS }o--o{ SPONSORS : "is backed by"
```

The code follows the standard Symfony layout: public controllers in `src/Controller`, EasyAdmin CRUD controllers in `src/Controller/Admin`, entities and repositories in `src/Entity` and `src/Repository`, and the Mistral client in `src/Service`.

## Getting started

### Requirements

- PHP 8.2 or newer with the `intl`, `pdo_mysql`, `mbstring`, `openssl` and `curl` extensions (plus `pdo_sqlite` to run the tests)
- [Composer](https://getcomposer.org/)
- MySQL 8
- [Symfony CLI](https://symfony.com/download) for the local web server
- Optional: an SMTP account for emails and a [Mistral AI](https://console.mistral.ai/) API key for the chatbot

### Installation

```bash
git clone https://github.com/Albator81/geevent.git
cd geevent
composer install
```

Create a `.env.local` file with your own settings:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/ge_event?serverVersion=8.0&charset=utf8mb4"

# Use null://null to disable outgoing email in development
MAILER_DSN="smtp://USER:PASSWORD@smtp.example.com:587"
CHATBOT_API_KEY="your-mistral-key"
```

Create the database, run the migrations and load the demo data, then start the server:

```bash
composer db
composer start
```

The app is now available at <http://127.0.0.1:8000>. Loading the fixtures needs an internet connection: event covers are downloaded from picsum.photos and addresses are looked up on Nominatim.

### Demo accounts

| Role | Username | Password |
| :--- | :--- | :--- |
| Administrator | `JeanJean` | `jean` |
| Other generated users | *(random)* | `test` |

The back office is at `/admin`. Fixture accounts start with an unverified email, and joining an event requires a verified one.

### Useful commands

| Command | Description |
| :--- | :--- |
| `composer start` | Start the Symfony server |
| `composer db` | Reset the database and reload the fixtures |
| `composer test` | Run code style checks and the Codeception test suite |
| `composer test:codeception` | Run the functional tests only |
| `composer fix` | Format PHP and Twig files |

## Documentation

A user guide (in French) covering accounts, event sign-ups and announcements is available in [docs/guide-utilisateur.md](docs/guide-utilisateur.md).

## About the project

Ge'Event was built as a team project during the second year of the BUT Informatique at the IUT de Reims (2025–2026), using feature branches and merge requests on the school's GitLab.

## Authors

- BUFFET Nathan
- ALFREDO Hugo
- BENAHMED Adam
- BETRY Eliott
