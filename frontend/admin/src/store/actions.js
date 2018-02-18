import * as types from './mutation-types'

export const actions = {
  write: (store, data) => {
    store.commit(types.WRITE, data)
  },
  extend: (store, data) => {
    store.commit(types.EXTEND, data)
  },
  cleanAlerts: (store, data) => {
    store.commit(types.CLEAN_ALERTS, data)
  },
  updateNotifs: (store, data) => {
    store.commit(types.UPDATE_NOTIFS, data)
  }
}
