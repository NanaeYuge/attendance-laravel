set -euo pipefail

GREEN="\033[32m"; YELLOW="\033[33m"; RED="\033[31m"; NC="\033[0m"
say() { echo -e "${YELLOW}==>${NC} $*"; }
ok()  { echo -e "${GREEN}OK${NC} $*"; }
fail(){ echo -e "${RED}ERROR${NC} $*"; exit 1; }
command -v docker >/dev/null || fail "Docker is not installed"
command -v docker compose >/dev/null || command -v docker-compose >/dev/null || fail "docker compose is not available"

[ -f "./docker-compose.yml" ] || fail "Run from repo root (docker-compose.yml not found)."
say "1/8 Copy env: src/.env.example -> src/.env (if missing)"
if [ ! -f "src/.env" ]; then
  [ -f "src/.env.example" ] || fail "src/.env.example not found"
  cp src/.env.example src/.env
  ok "created src/.env"
else
  ok "src/.env exists"
fi
say "2/8 Up containers"
if command -v docker compose >/dev/null; then
  docker compose up -d --build
else
  docker-compose up -d --build
fi
ok "containers up"
say "3/8 composer install"
if command -v docker compose >/dev/null; then
  docker compose exec -T php composer install --no-interaction --prefer-dist
else
  docker-compose exec -T php composer install --no-interaction --prefer-dist
fi
ok "composer install done"
say "4/8 artisan key:generate"
if command -v docker compose >/dev/null; then
  docker compose exec -T php php artisan key:generate --force
else
  docker-compose exec -T php php artisan key:generate --force
fi
ok "APP_KEY set (or already set)"
say "5/8 wait for MySQL"
DB_DSN="mysql:host=mysql;port=3306;dbname=laravel_db"
DB_USER="laravel_user"
DB_PASS="laravel_pass"
for i in {1..60}; do
  if command -v docker compose >/dev/null; then
    if docker compose exec -T php php -r "try{ new PDO('$DB_DSN','$DB_USER','$DB_PASS'); }catch(Exception \$e){ exit(1);}"; then ok "MySQL ready"; break; fi
  else
    if docker-compose exec -T php php -r "try{ new PDO('$DB_DSN','$DB_USER','$DB_PASS'); }catch(Exception \$e){ exit(1);}"; then ok "MySQL ready"; break; fi
  fi
  sleep 2
  [ "$i" -eq 60 ] && fail "MySQL not ready in time"
done
say "6/8 migrate --seed"
if command -v docker compose >/dev/null; then
  docker compose exec -T php php artisan migrate --seed --force
else
  docker-compose exec -T php php artisan migrate --seed --force
fi
ok "migrations & seeders done"
say "7/8 quick checks"
if command -v docker compose >/dev/null; then
  docker compose exec -T php php artisan --version || true
  docker compose exec -T php php -v || true
  docker compose exec -T php php artisan route:list | grep -E 'login|verify' || true
fi

say "8/8 HTTP smoke test"
HTTP1=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/login || echo "000")
HTTP2=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/admin/login || echo "000")
echo " /login       -> HTTP $HTTP1"
echo " /admin/login -> HTTP $HTTP2"
[ "$HTTP1" = "200" ] || [ "$HTTP1" = "302" ] || echo "Hint: check nginx/logs"
[ "$HTTP2" = "200" ] || [ "$HTTP2" = "302" ] || echo "Hint: check admin route/nginx"
ok "Done. Open: http://localhost/login  phpMyAdmin: http://localhost:8080"
