# Audit complet des modals & formulaires (UI/UX)

## Périmètre audité
- `resources/views/dashboard/sections/biens.blade.php`
- `resources/views/dashboard/sections/locataires.blade.php`
- `resources/views/dashboard/sections/contrats.blade.php`
- `resources/views/dashboard/sections/depenses.blade.php`
- `resources/views/dashboard/sections/proprietaires.blade.php`
- `resources/views/dashboard/sections/utilisateurs.blade.php`
- `resources/views/dashboard/sections/paiements.blade.php`
- `resources/css/app.css`

## Problèmes identifiés
1. **Incohérence visuelle inter-modals**
   - couleurs d’overlay différentes, blur variable, rayons incohérents.
2. **Positionnement non harmonisé mobile/desktop**
   - certains modals collés bas écran, d’autres centrés, avec comportement différent.
3. **Maintenabilité faible**
   - classes Tailwind longues et dupliquées dans chaque section.
4. **Perception “qualité premium” inégale**
   - ombres, bordures et transitions non unifiées.

## Améliorations appliquées
1. **Standardisation Design System des modals**
   - création de classes utilitaires:
     - `.app-modal-root`
     - `.app-modal-overlay`
     - `.app-modal-panel` (+ variantes `-lg`, `-xl`, `-2xl`, `-soft`)
2. **Uniformisation du positionnement & de l’overlay**
   - overlays harmonisés (`bg-slate-900/55` + blur léger) et transitions uniformes.
3. **Refactor des sections dashboard**
   - migration des modals de: biens, locataires, contrats, dépenses, propriétaires, utilisateurs, paiements.
4. **Résultat attendu**
   - rendu plus cohérent, meilleure lisibilité des formulaires, maintenance simplifiée, look plus professionnel.
5. **Visibilité immédiate du contenu modal**
   - réduction des espacements verticaux formulaires (tokens),
   - augmentation de la largeur du modal dépenses,
   - suppression du scroll forcé sur le formulaire paiements,
   - contrainte de hauteur desktop du panel modal pour éviter les découpes visuelles.

## Recommandations supplémentaires (prochaine itération) — implémentées
- ✅ Focus trap + retour focus au déclencheur sur fermeture (`window.modalUX`).
- ✅ Header/footer sticky pour les formulaires longs sur mobile (`app-modal-header`, `app-modal-footer`).
- ✅ Validation visuelle inline uniforme (style `field-invalid` + message `form-error-message`).
- ✅ Tokens de spacing spécifiques formulaires (`--form-stack-gap`, `--field-gap`, classes `form-stack`, `field-gap`).
