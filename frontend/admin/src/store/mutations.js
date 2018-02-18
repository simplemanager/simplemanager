import * as types from './mutation-types'
import $ from 'jquery'

// Ajoute les paramètres des items parents dans
// les items fils du menu
let buildMenu = function (menu) {
  if (typeof menu !== 'object') { return null }
  for (var item in menu) {
    if (!menu[item]) {
      delete menu[item]
      continue
    }
    if (!menu[item] || !menu[item].items) { continue }
    for (var subitem in menu[item].items) {
      if (!menu[item].items[subitem]) {
        delete menu[item].items[subitem]
        continue
      }
      if (!menu[item].items[subitem].params) { continue }
      menu[item].items[subitem].params =
        Object.assign({},
          menu[item].params,
          menu[item].items[subitem].params
          )
    }
  }
  return menu
}

// Ajout des données au menu puis le reconstruit
let extendData = function (data, toUpdate) {
  data = $.extend(true, data, toUpdate)
  return data
}

// Copie d'objet (voir comment s'en passer...)
let copy = function (data) {
  return JSON.parse(JSON.stringify(data))
}

// let updateButton = function (buttonState, buttonUpdate) {
//  if (buttonUpdate.content && buttonUpdate.content.length) {
//    console.log(buttonUpdate.content)
//    for (var k in buttonUpdate.content) {
//      console.log(k)
//    }
//    buttonUpdate.content.forEach(function (val, key, merde) {
//      console.log('key: ' + key)
//      console.log(val)
//      console.log(merde)
//      console.log(buttonState.content)
//      buttonState.content.
//    })
//  }
//  console.log(buttonState)
//  return buttonState
// }

export const mutations = {
  [types.WRITE] (state, layout) {
    if (layout.sidebar.menu) {
      state.sidebar.menu = buildMenu(copy(layout.sidebar.menu))
    }
    if (layout.sidebar.registration) {
      state.sidebar.registration = layout.sidebar.registration
    }
    if (layout.header) {
      state.header = layout.header
    }
    if (layout.page) {
      state.page = layout.page
    }
    if (layout.footer) {
      state.footer = layout.footer
    }
  },
  [types.EXTEND] (state, updates) {
    if (!updates) {
      return
    }
    if (updates.sidebar && updates.sidebar.menu) {
      state.sidebar.menu = buildMenu(extendData(copy(state.sidebar.menu), updates.sidebar.menu))
    }
    if (updates.sidebar && 'registration' in updates.sidebar) {
      if (updates.sidebar.registration !== null) {
        state.sidebar.registration = extendData(copy(state.sidebar.registration), updates.sidebar.registration)
      } else {
        state.sidebar.registration = null
      }
    }
    if (updates.header) {
      state.header = extendData(copy(state.header), updates.header)
//      if ('title' in updates.header) {
//        state.header.title = updates.header.title
//      }
//      if (updates.header.user) {
//        state.header.user = extendData(copy(state.header.user), updates.header.user)
//      }
//      if (updates.header.settings) {
//        state.header.settings = extendData(copy(state.header.settings), updates.header.settings)
//      }
//      if (updates.header.buttons === null) {
//        console.log('cleanbuttons')
//        state.header.buttons = {}
//      }
//      if (updates.header.buttons) {
//        if (updates.header.buttons.ntf) {
//          if (updates.header.buttons.ntf.content) {
//            if (state.header.buttons.ntf) {
//              for (var k in updates.header.buttons.ntf.content) {
//                console.log('k: ' + k)
//                console.log(state.header.buttons.ntf.content)
//                state.header.buttons.ntf.content.push(updates.header.buttons.ntf.content[k])
//              }
//            } else {
//              state.header.buttons.ntf = updates.header.buttons.ntf
//            }
//          }
// //          state.header.buttons.ntf = updateButton(copy(state.header.buttons.ntf), updates.header.buttons.ntf)
//        }
// state.header.buttons = extendData(copy(state.header.buttons), updates.header.buttons)
//        }
//        if (updates.header.buttons.msg) {
//          state.header.buttons.msg = extendData(copy(state.header.buttons.msg), updates.header.buttons.msg)
//        }
//        if (updates.header.buttons.ntf) {
//          state.header.buttons.ntf = extendData(copy(state.header.buttons.ntf), updates.header.buttons.ntf)
//        }
//        if (updates.header.buttons.alr) {
//          state.header.buttons.alr = extendData(copy(state.header.buttons.alr), updates.header.buttons.alr)
//        }
//      }
    }
    if (updates.page) {
//      console.log('page update...')
      if ('title' in updates.page) {
        state.page.title = updates.page.title
      }
      if ('links' in updates.page) {
        state.page.links = updates.page.links // extendData(copy(state.page.links), updates.page.links)
      }
      if (('content' in updates.page)) {
        state.page.content = updates.page.content // extendData(copy(state.page.content), updates.page.content)
        if (updates.page.content !== null) {
          state.page.alerts = [] // Réinitialisation des alertes quand on change de page
        }
      }
      if (('alerts' in updates.page)) { // && state.page.alerts) {
        if (!state.page.alerts || updates.page.alerts === null) {
          state.page.alerts = []
        }
        if (typeof updates.page.alerts === 'object') {
          for (let alert in updates.page.alerts) {
            state.page.alerts.unshift(updates.page.alerts[alert])
          }
        }
//        console.log('alert updated')
//        console.log(state.page.alerts)
      }
      if (('scripts' in updates.page)) {
        state.page.scripts = updates.page.scripts // extendData(copy(state.page.scripts), updates.page.scripts)
      }
    }
    if (updates.footer) {
      state.footer = extendData(copy(state.footer), updates.footer)
    }
  },
  [types.CLEAN_ALERTS] (state, data) {
    state.page.alerts = []
  },
  [types.UPDATE_NOTIFS] (state, data) {
    state.header.buttons.ntf.content = data
    state.header.buttons.ntf.label = data.length
  }
}
