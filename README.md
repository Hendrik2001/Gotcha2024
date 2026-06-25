# Gotcha 2024

A full-stack real-world elimination game platform, built and self-hosted for a university social event with **600 active participants** and **€5,500+ in prizes**.

## What Is Gotcha?

Gotcha is a live elimination game: every player is secretly assigned a *target*. To eliminate your target, you must take a candid photo with them — without them realizing they're being hunted. Submit the photo, inherit your target's target, and keep hunting until one player remains.

This platform was designed and built from scratch to run the entire game end-to-end: registration, target assignment, kill verification, round management, and prize tracking.

## Scale

- **600 active players** across multiple weeks of gameplay
- **10+ rounds** with automated elimination at each round end
- **€5,500+ in prizes** distributed to survivors
- Self-hosted on a university server with real-time notifications via Firebase

## Features

- **SSO login** — players authenticate via their university credentials
- **Automated target assignment** — unique codes generated and distributed to all players at game start
- **Kill verification** — entering a target's secret code triggers an instant chain update (you inherit their target)
- **Admin panel** — real-time kill feed, round state management, manual override controls
- **Round management** — automated elimination of inactive players at round end (cron-based)
- **Player dashboard** — shows current target, active status, and round history

## How a Kill Works

```
Player A (hunter) → knows Player B's secret code
Player A submits B's code
System verifies → sets B.is_playing = 0
Player A inherits B's target → A now hunts C
B is out of the game
```

## Tech Stack

- **PHP** backend with MySQL database
- **Firebase** for real-time push notifications
- **Docker** for containerized self-hosted deployment
- **Bootstrap** frontend

## Database Schema

The core schema (`tover_gotcha.sql`) models:
- `auth` — all university accounts that have authenticated
- `player` — active participants with `code`, `target_id`, `is_playing`, and kill history
- Round state via admin-controlled timestamps and cron-triggered elimination jobs

## Running Locally

```bash
docker-compose up
```

Import `tover_gotcha.sql` into MySQL and configure the Firebase project credentials in `init_firebase.php`.
