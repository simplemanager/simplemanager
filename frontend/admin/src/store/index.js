import Vue from 'vue'
import Vuex from 'vuex'
import { state } from './state'
import { getters } from './getters'
import { mutations } from './mutations'
import { actions } from './actions'

Vue.use(Vuex)

export default new Vuex.Store({
  state: state,         // Propriétés qu'on pourra injecter dans nos composants
  getters: getters,     // bindés dans les propriétés computed des composants
  mutations: mutations, // Fonctions permettant de "muter" l'état du state (changement dans le state)
  actions: actions,     // Fonction qui va déclancher une mutation
  strict: true          // empêche de muter à la main dans les composants
})
