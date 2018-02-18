export const state = {
  sidebar: {
    menu: {
      init: {}      // Menu initial
    },
    registration: {} // Pavé d'identification au dessus du menu de gauche
  },
  header: {
    title: [],
    buttons: {},     // Boutons de notification, messages, taches
    user: null,      // Informations sur l'utilisateur
    settings: {}     // Bouton 'settings'
  },
  page: {
    title: '',       // Titre à l'intérieur de la page (pavé blanc)
    links: [],       // Contenu du breadcrumb
    alerts: [],      // Messages d'information (erreur, warning, info, success)
    content: null,   // Contenu de la page récupéré via la requete ajax
    scripts: null    // Scripts JS à mettre dans la balise script commune en fin de page
  },
  footer: {
    content: '',
    links: []        // Liens de bas de page (faq, mentions)
  }
}
