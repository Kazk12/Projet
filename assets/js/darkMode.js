document.addEventListener('DOMContentLoaded', function() {
  // Récupérer l'élément HTML
  const htmlElement = document.documentElement;
  
  // Vérifier la préférence de l'utilisateur stockée dans le localStorage
  const darkModePreference = localStorage.getItem('darkMode') === 'true';
  
  // Appliquer le mode sombre si c'est la préférence
  if (darkModePreference) {
      htmlElement.classList.add('dark');
      updateDarkModeIcons(true);
  } else {
      updateDarkModeIcons(false);
  }
  
  // Ajouter un gestionnaire d'événements pour les boutons de dark mode
  const darkModeToggles = document.querySelectorAll('.dark-mode-toggle');
  darkModeToggles.forEach(toggle => {
      toggle.addEventListener('click', function() {
          // Basculer la classe dark sur l'élément HTML
          htmlElement.classList.toggle('dark');
          
          // Stocker la préférence dans localStorage
          const isDarkMode = htmlElement.classList.contains('dark');
          localStorage.setItem('darkMode', isDarkMode);
          
          // Mettre à jour les icônes
          updateDarkModeIcons(isDarkMode);
      });
  });
  
  // Fonction pour mettre à jour les icônes
  function updateDarkModeIcons(isDarkMode) {
      darkModeToggles.forEach(toggle => {
          const iconElement = toggle.querySelector('.material-icons');
          const labelElement = toggle.querySelector('.toggle-label');
          
          if (iconElement) {
              iconElement.textContent = isDarkMode ? 'light_mode' : 'dark_mode';
          }
          
          if (labelElement) {
              labelElement.textContent = isDarkMode ? 'Mode clair' : 'Mode sombre';
          }
      });
  }
});