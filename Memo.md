# Design System

Ce document liste tous les éléments de design utilisés dans le projet pour faciliter la création de maquettes sur Figma.

## Couleurs

### Couleur principale - Indigo
- Indigo-50: `#f0f5ff`
- Indigo-100: `#e0eaff`
- Indigo-400: `#8da2fb`
- Indigo-500: `#6875f5`
- Indigo-600: `#4f46e5` (Couleur principale)
- Indigo-700: `#4338ca` (Hover)
- Indigo-800: `#3730a3`

### Tons de gris
- Gray-50: `#f9fafb`
- Gray-100: `#f3f4f6`
- Gray-200: `#e5e7eb`
- Gray-300: `#d1d5db`
- Gray-400: `#9ca3af`
- Gray-500: `#6b7280`
- Gray-600: `#4b5563`
- Gray-700: `#374151`
- Gray-800: `#1f2937`
- Gray-900: `#111827`

### Rouge (pour alertes et erreurs)
- Red-50: `#fef2f2`
- Red-100: `#fee2e2`
- Red-600: `#dc2626`
- Red-800: `#991b1b`

### Blanc
- White: `#ffffff`

## Typographie

### Famille de police
- Sans-serif: `ui-sans-serif`, `system-ui`, `sans-serif`

### Style de typographie
- Couleur de texte standard: `#374151` (gray-700)
- Largeur maximale de texte: `65ch`
- Interligne: `1.5`

### Dark mode
- Couleur texte: `#d1d5db` (gray-300)
- Liens: `#8da2fb` (indigo-400)
- Liens hover: `#6875f5` (indigo-500)
- Titres: `#e0eaff` (indigo-100)
- Texte en gras: `#d1d5db` (gray-300)
- Code: `#f3f4f6` (gray-100)

## Ombres

### Mode clair
- Petite: `0 1px 2px 0 rgba(0, 0, 0, 0.05)`
- Moyenne: `0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)`
- Grande: `0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)`

### Mode sombre
- Petite: `0 1px 2px 0 rgba(0, 0, 0, 0.4)`
- Moyenne: `0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.4)`
- Grande: `0 10px 15px -3px rgba(0, 0, 0, 0.6), 0 4px 6px -2px rgba(0, 0, 0, 0.5)`

## Coins arrondis
- Medium: `0.375rem` (6px)
- Large: `0.5rem` (8px)
- XLarge: `0.75rem` (12px)
- Complet: `9999px` (cercle parfait)

## Breakpoints responsive
- Mobile: jusqu'à 767px
- Tablette: 768px à 991px
- Desktop: 992px et plus

## Mode sombre
Le projet utilise une approche basée sur les classes CSS pour le dark mode (`class`). Pour activer le mode sombre sur Figma, créer des versions alternatives des composants avec les couleurs appropriées.

## Utilisation des couleurs par élément HTML

### Balises de titre
- **H1**: 
  - Mode clair: `text-gray-900` (#111827)
  - Mode sombre: `dark:text-gray-100` (#f3f4f6)
  - Utilisation spéciale: `text-indigo-600` (#4f46e5) / `dark:text-indigo-400` (#8da2fb) pour mettre en évidence certaines parties
  - Classes communes: `text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl`

- **H2**: 
  - Mode clair: `text-gray-900` (#1f2937)
  - Mode sombre: `dark:text-gray-100` (#f3f4f6)
  - Classes communes: `text-2xl font-bold`

- **H3**: 
  - Mode clair: `text-gray-900` (#1f2937)
  - Mode sombre: `dark:text-gray-100` (#f3f4f6)
  - Classes communes: `text-xl font-bold line-clamp-2`

- **H4**: 
  - Mode clair: `text-gray-900` (#1f2937)
  - Mode sombre: `dark:text-gray-100` (#f3f4f6)
  - Classes communes: `text-sm font-medium`

### Textes
- **Paragraphe standard**:
  - Mode clair: `text-gray-600` (#4b5563)
  - Mode sombre: `dark:text-gray-300` (#d1d5db)
  - Classes communes: `text-base`

- **Texte secondaire/descriptif**:
  - Mode clair: `text-gray-500` (#6b7280)
  - Mode sombre: `dark:text-gray-400` (#9ca3af)
  - Classes communes: `text-sm` ou `text-xs`

### Liens
- **Liens standard**:
  - Mode clair: `text-indigo-600` (#4f46e5)
  - Mode sombre: `dark:text-indigo-400` (#8da2fb)
  - Hover clair: `hover:text-indigo-500` (#6875f5)
  - Hover sombre: `dark:hover:text-indigo-300`

### Boutons
- **Bouton principal**:
  - Fond clair: `bg-indigo-600` (#4f46e5)
  - Fond sombre: `dark:bg-indigo-500` (#6875f5)
  - Hover clair: `hover:bg-indigo-700` (#4338ca)
  - Hover sombre: `dark:hover:bg-indigo-600` (#4f46e5)
  - Texte: `text-white` (#ffffff)
  - Classes communes: `px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm`

- **Bouton secondaire**:
  - Fond clair: `bg-white` (#ffffff)
  - Fond sombre: `dark:bg-gray-800` (#1f2937)
  - Bordure clair: `border-gray-300` (#d1d5db)
  - Bordure sombre: `dark:border-gray-700` (#374151)
  - Texte clair: `text-gray-700` (#374151)
  - Texte sombre: `dark:text-gray-300` (#d1d5db)
  - Classes communes: `px-3 py-1.5 rounded-md text-sm font-medium`

### Fonds
- **Fond principal de page**:
  - Mode clair: `bg-white` (#ffffff)
  - Mode sombre: `dark:bg-gray-900` (#111827)

- **Fond d'élément/carte**:
  - Mode clair: `bg-white` (#ffffff)
  - Mode sombre: `dark:bg-gray-800` (#1f2937)
  - Classes communes: `rounded-lg shadow-md dark:shadow-dark-md overflow-hidden`

- **Fond d'élément secondaire**:
  - Mode clair: `bg-gray-50` (#f9fafb)
  - Mode sombre: `dark:bg-gray-700` (#374151)

### Bordures
- **Bordure standard**:
  - Mode clair: `border-gray-100` (#f3f4f6)
  - Mode sombre: `dark:border-gray-700` (#374151)

### Badges
- **Badge principal**:
  - Fond clair: `bg-indigo-100` (#e0eaff)
  - Fond sombre: `dark:bg-indigo-900/50`
  - Texte clair: `text-indigo-800` (#3730a3)
  - Texte sombre: `dark:text-indigo-200` (#e0eaff)
  - Classes communes: `px-2.5 py-0.5 rounded-full text-xs font-medium`

### Formulaires
- **Input/Textarea**:
  - Fond clair: `bg-white` (#ffffff)
  - Fond sombre: `dark:bg-gray-700` (#374151)
  - Bordure clair: `border-gray-300` (#d1d5db)
  - Bordure sombre: `dark:border-gray-600` (#4b5563)
  - Texte clair: `text-gray-700` (#374151)
  - Texte sombre: `dark:text-gray-200` (#e5e7eb)
  - Focus: `focus:ring-indigo-500 focus:border-indigo-500` / `dark:focus:ring-indigo-400 dark:focus:border-indigo-400`