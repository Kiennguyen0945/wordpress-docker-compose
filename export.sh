#!/bin/bash
# =====================================================
# Database Export Script
# Works on: Linux, macOS, Windows (Git Bash / WSL)
# =====================================================
set -e

_now=$(date +"%Y-%m-%d_%H-%M-%S")
_file="wp-data/export_$_now.sql"

echo "⏳ Exporting database to $__file ..."

# Run mysqldump inside the db container
EXPORT_COMMAND='exec mysqldump "$MYSQL_DATABASE" -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction --quick'
docker compose exec db sh -c "$EXPORT_COMMAND" > "$_file"

# Remove the password warning line 1 (works on Linux, macOS, and Git Bash on Windows)
if [[ "$OSTYPE" == "darwin"* ]]; then
  sed -i '.bak' '1d' "$_file"
  rm -f "${_file}.bak"
else
  sed -i '1d' "$_file"
fi

echo "✅ Database exported successfully: $_file"
echo "   To import: just copy this file into wp-data/ and restart containers."
echo ""
echo "   Or via CLI:"
echo "   docker compose exec -T db mysql -uroot -p\$MYSQL_ROOT_PASSWORD \$MYSQL_DATABASE < $_file"
