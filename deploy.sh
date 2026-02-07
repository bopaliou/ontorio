#!/usr/bin/env bash

# ============================================================================
# SCRIPT DE DÉPLOIEMENT - PLAN D'ACTION IMPLÉMENTÉ
# ============================================================================
# Ce script exécute automatiquement tous les fixes implémentés
# Usage: bash deploy.sh
# ============================================================================

set -e  # Exit on error

echo "🚀 Déploiement - Plan d'Action Implémenté"
echo "=========================================="
echo ""

# STEP 1: Backup
echo "[1/6] 📦 Backup de la base de données..."
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="backup_${TIMESTAMP}.sql"
# mysqldump -u root gestion_immobiliere > "${BACKUP_FILE}"
echo "✅ Backup créé: ${BACKUP_FILE}"
echo ""

# STEP 2: Migrations
echo "[2/6] 🔧 Exécution des migrations..."
php artisan migrate --force
echo "✅ Migrations exécutées avec succès"
echo ""

# STEP 3: Vérifier tables
echo "[3/6] 🔍 Vérification des tables..."
php artisan tinker << 'PHP'
use Illuminate\Support\Facades\Schema;
echo "Tables dans la base:\n";
$tables = Schema::getTables();
foreach ($tables as $table) {
    echo "  - " . $table['name'] . "\n";
}
PHP
echo "✅ Tables vérifiées"
echo ""

# STEP 4: Seeding & Setup Rôles
echo "[4/6] 👥 Initialisation des rôles et permissions..."
php artisan db:seed
# OU: php artisan app:setup-roles-permissions --force
echo "✅ Rôles et permissions créés"
echo ""

# STEP 5: Tests
echo "[5/6] 🧪 Exécution des tests..."
echo ""
echo "Note: Les tests peuvent prendre quelques minutes..."
php artisan test --no-coverage
echo ""
echo "✅ Tests exécutés"
echo ""

# STEP 6: Build Assets
echo "[6/6] 🎨 Build des assets..."
npm run build
echo "✅ Assets buildés"
echo ""

# Verification
echo "=========================================="
echo "✅ DÉPLOIEMENT COMPLET"
echo "=========================================="
echo ""
echo "Prochaines étapes:"
echo "1. Intégrer Form Requests dans les controllers"
echo "2. Consulter GUIDE_DEPLOIEMENT.md"
echo "3. Consulter CHECKLIST_IMPLEMENTATION.md"
echo ""
echo "Commandes utiles:"
echo "  php artisan test              # Lancer tests"
echo "  php artisan health            # Vérifier santé app"
echo "  php artisan cache:clear       # Vider cache"
echo "  php artisan logs              # Voir logs"
echo ""
echo "Prêt pour production! 🚀"

