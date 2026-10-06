#!/bin/bash
# ============================================================
#  CREDISOPORTE - Instalador de base de datos (Linux / macOS)
#  Ejecutar desde la raiz del proyecto:
#      bash database/credisistema/instalar.sh
#  DB_NAME debe coincidir con DB_DATABASE de backend/.env
# ============================================================
set -e

DB_HOST="${DB_HOST:-localhost}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DB_NAME="${DB_NAME:-credisoportecom_credisopo}"
BASE="$(cd "$(dirname "$0")" && pwd)"

if [ -z "$DB_PASS" ]; then
  AUTH=(-h "$DB_HOST" -u "$DB_USER")
else
  AUTH=(-h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS")
fi

echo "[1/5] Creando base $DB_NAME ..."
mysql "${AUTH[@]}" -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8 COLLATE utf8_spanish_ci;"

echo "[2/5] Importando 01_schema.sql (puede tardar unos minutos) ..."
mysql "${AUTH[@]}" "$DB_NAME" < "$BASE/01_schema.sql"

echo "[3/5] Importando 02_procedimientos.sql ..."
mysql "${AUTH[@]}" "$DB_NAME" < "$BASE/02_procedimientos.sql"

echo "[4/5] Aplicando migraciones ..."
for f in 01_evaluacion_datetime 02_galeria_credito 03_home_stats 04_origen_cliente 05_produccion_consolidada 06_tipos_dirigido_a_imagen; do
  echo "  - $f"
  mysql "${AUTH[@]}" "$DB_NAME" < "$BASE/03_migraciones/$f.sql"
done

echo "[5/5] Ejecutando seed de autorizacion ..."
php "$BASE/04_seeds/seed_authorization.php"

echo ""
echo "=== BD $DB_NAME lista ==="
