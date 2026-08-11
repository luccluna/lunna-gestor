#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_NAME="$(basename "$APP_DIR")"
DOMAIN_DIR="$(dirname "$APP_DIR")"
PUBLIC_HTML="${PUBLIC_HTML:-$DOMAIN_DIR/public_html}"
BRANCH="${DEPLOY_BRANCH:-main}"

log() {
    printf '\n==> %s\n' "$1"
}

fail() {
    printf 'ERRO: %s\n' "$1" >&2
    exit 1
}

cd "$APP_DIR"

[[ -f spark ]] || fail "Execute este script na raiz do projeto CodeIgniter."
[[ -d public ]] || fail "Pasta public nao encontrada."
[[ -f .env ]] || fail "Arquivo .env nao encontrado no servidor."

log "Atualizando codigo pelo Git"
git fetch origin "$BRANCH"
git pull --ff-only origin "$BRANCH"

log "Instalando dependencias de producao"
if command -v composer >/dev/null 2>&1; then
    COMPOSER_BIN="composer"
elif command -v composer2 >/dev/null 2>&1; then
    COMPOSER_BIN="composer2"
else
    fail "Composer nao encontrado no servidor."
fi

"$COMPOSER_BIN" install --no-dev --optimize-autoloader --no-interaction

log "Garantindo pastas gravaveis"
mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar writable/backups
chmod -R u+rwX writable || true

log "Rodando migrations"
php spark migrate

log "Limpando cache"
php spark cache:clear

log "Atualizando public_html"
mkdir -p "$PUBLIC_HTML"

if [[ ! -f "$PUBLIC_HTML/index.php" ]]; then
    cp public/index.php "$PUBLIC_HTML/index.php"
fi

if grep -q "../app/Config/Paths.php" "$PUBLIC_HTML/index.php"; then
    sed -i "s#require FCPATH \. '../app/Config/Paths.php';#require FCPATH . '../${APP_NAME}/app/Config/Paths.php';#" "$PUBLIC_HTML/index.php"
fi

if command -v rsync >/dev/null 2>&1; then
    rsync -av --delete --exclude=index.php public/ "$PUBLIC_HTML/"
else
    (
        cd public
        find . -type d -exec mkdir -p "$PUBLIC_HTML/{}" \;
        find . -type f ! -path './index.php' -exec cp "{}" "$PUBLIC_HTML/{}" \;
    )
fi

log "Deploy finalizado"
printf 'Teste: https://lunnavidracaria.site/\n'
