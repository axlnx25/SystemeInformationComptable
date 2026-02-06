# Guide de Test - Application SIC

## Accès à l'application

L'application est accessible à : **http://localhost:8000**

## Compte de test

Un utilisateur a été créé avec les droits administrateur :
- **Email** : (vérifier dans la base de données)
- **Mot de passe** : (celui que vous avez utilisé lors de l'inscription)

## Tests à effectuer

### 1. Connexion
1. Accédez à http://localhost:8000/login
2. Connectez-vous avec vos identifiants
3. Vous devriez être redirigé vers le dashboard

### 2. Créer un Journal
1. Cliquez sur "+ Nouveau Journal" ou accédez à http://localhost:8000/journals/create
2. Entrez une désignation, par exemple : "Journal des Ventes"
3. Cliquez sur "Créer le journal"
4. Vous devriez voir le journal dans la liste

### 3. Saisir une Opération
1. Dans la liste des journaux, cliquez sur "✏️ Saisir opérations"
2. Remplissez la première ligne :
   - Date : 2025-01-01
   - Numéro d'opération : 1
   - Référence : VENTE-001
   - N° Compte : 411
   - Libellé : Vente de marchandise à crédit
   - Débit : 1000000
3. Cliquez sur "+ Ajouter une ligne"
4. **Vérifiez que les champs sont pré-remplis** (date, numéro, référence, libellé)
5. Remplissez uniquement :
   - N° Compte : 707
   - Crédit : 1000000
6. Vérifiez que les totaux s'affichent et que le bouton "Enregistrer" est activé
7. Cliquez sur "Enregistrer"

### 4. Consulter l'Historique
1. Cliquez sur "📊 Historique"
2. Vous devriez voir l'opération enregistrée avec ses 2 lignes
3. Vérifiez les totaux

### 5. Gestion des Utilisateurs (Admin)
1. Accédez à http://localhost:8000/users
2. Créez un nouvel utilisateur
3. Modifiez un utilisateur

## Problèmes connus et solutions

### Impossible de créer un journal
✅ **CORRIGÉ** : Le champ 'id' a été retiré des fillable dans les modèles Journal et Operation.

### Les champs ne sont pas pré-remplis
- Vérifiez que le JavaScript est bien chargé (ouvrez la console du navigateur)
- Le fichier `/public/js/app.js` doit être accessible

### Erreur 500
- Vérifiez les logs : `tail -f storage/logs/laravel.log`
- Vérifiez que les migrations sont à jour : `php artisan migrate:status`

## Commandes utiles

```bash
# Voir les journaux créés
php artisan tinker --execute="echo \App\Models\Journal::count() . ' journaux' . PHP_EOL;"

# Voir les opérations
php artisan tinker --execute="echo \App\Models\Operation::count() . ' opérations' . PHP_EOL;"

# Créer un utilisateur admin
php artisan tinker --execute="\$user = \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => bcrypt('password'), 'is_admin' => true]); echo 'Admin créé' . PHP_EOL;"

# Nettoyer les logs
echo "" > storage/logs/laravel.log
```
