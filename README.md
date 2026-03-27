# MagixMultiText

[![Release](https://img.shields.io/github/release/magix-cms/MagixMultiText.svg)](https://github.com/magix-cms/MagixMultiText/releases/latest)
[![License](https://img.shields.io/github/license/magix-cms/MagixMultiText.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-blue.svg)](https://php.net/)
[![Magix CMS](https://img.shields.io/badge/Magix%20CMS-4.x-success.svg)](https://www.magix-cms.com/)

**MagixMultiText** est un plugin d'extension de contenu pour Magix CMS 4.x. Il permet d'ajouter un nombre illimité de blocs de texte supplémentaires (onglets, réassurance, fiches techniques) directement au sein des modules natifs du CMS.

## 🌟 Fonctionnalités principales

* **Intégration Contextuelle** : S'injecte automatiquement sous forme d'onglet dans les modules **Produits, Pages, Catégories, News et About**.
* **Gestion Multilingue Native** : Interface d'édition avec onglets de langues synchronisés via `MagixTabManager`.
* **Édition Rich Text** : Intégration complète de `TinyMCE` pour chaque bloc de texte.
* **Interface Master-Detail AJAX** : Listing et formulaire d'édition ultra-fluides sans rechargement de page.
* **Drag & Drop** : Réorganisez l'ordre d'affichage de vos blocs de texte par simple glisser-déposer.
* **Architecture Propre** : Séparation stricte de la structure (base) et du contenu (traductions) en base de données.

## ⚙️ Installation

1. Téléchargez la dernière version.
2. Placez le dossier `MagixMultiText` dans le répertoire `plugins/`.
3. Dans l'administration, allez dans **Extensions > Plugins** et cliquez sur **Installer**.
4. Le plugin créera automatiquement les tables `mc_plug_textmulti` et `mc_plug_textmulti_content`.

## 🚀 Utilisation

### Côté Administration
Le plugin n'a pas de page de configuration dédiée. Il apparaît directement comme un nouvel onglet **"Textes Multilingues"** lorsque vous éditez un produit, une page ou une catégorie.
* Cliquez sur **"Ajouter un texte"** pour ouvrir le formulaire multilingue.
* Enregistrez : le contenu est sauvegardé en AJAX et rafraîchit la liste instantanément.

### Côté Public (Frontend)
Utilisez le hook de zone pour afficher les textes. Par défaut, le plugin est greffé sur :
* `displayPageBottom` (Pages)
* `displayProductExtraContent` (Produits)
* `displayCategoryBottom` (Catégories)

## 🛠️ Architecture Technique

* **Backend JS** : Utilise la classe globale `MagixAjaxManager.js` pour piloter les vues et la synchronisation `TinyMCE` via `FormData`.
* **Multi-Instance** : Conçu pour cohabiter avec d'autres plugins AJAX sur la même page sans conflits d'IDs grâce à un système de préfixage (`mt_`).
* **Performance** : Les données ne sont chargées en AJAX qu'au clic sur l'onglet pour optimiser le temps de chargement initial du backoffice.

## 📄 Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)