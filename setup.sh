#!/usr/bin/env bash
# One-shot dev environment bootstrap: creates .env if missing, builds and
# starts the Docker Compose stack, and waits for the app to answer.
set -euo pipefail
cd "$(dirname "$0")"

if ! command -v docker >/dev/null 2>&1; then
    echo "Docker is required. Install Docker Desktop and re-run this script." >&2
    exit 1
fi

if [ ! -f .env ]; then
    cp .env.example .env
    if command -v openssl >/dev/null 2>&1; then
        password=$(openssl rand -hex 16)
    else
        password=$(head -c 16 /dev/urandom | od -An -tx1 | tr -d ' \n')
    fi
    sed -i.bak "s/DB_PASSWORD=change-me/DB_PASSWORD=${password}/" .env && rm -f .env.bak
    echo "Created .env with a generated DB_PASSWORD."
else
    echo ".env already exists, leaving it as-is."
fi

set -a
# shellcheck disable=SC1091
source .env
set +a

echo "Building and starting the stack (dev mode: APP_DEBUG=true, live code reload via bind mount)..."
docker compose up --build -d

echo "Waiting for the app to become healthy..."
for _ in $(seq 1 30); do
    if curl -fsS "http://localhost:${APP_PORT:-8080}/health" >/dev/null 2>&1; then
        echo "App is up."
        break
    fi
    sleep 1
done

cat <<EOF

Ready:
  App:      http://localhost:${APP_PORT:-8080}
  Adminer:  http://localhost:${ADMINER_PORT:-8081}  (system: PostgreSQL, server: db, credentials from .env)

Only submitters can self-register through the web UI. Create a reviewer/admin with:
  docker compose exec app php bin/create-actor.php reviewer prof.smith secret Jane Smith 3
  docker compose exec app php bin/create-actor.php admin admin secret Site Admin

Logs:      docker compose logs -f app
Stop:      docker compose down
Reset DB:  docker compose down -v
EOF
