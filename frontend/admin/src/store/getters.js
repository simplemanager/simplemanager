export const getters = {
  sidebarMenu: state => state.sidebar.menu,
  sidebarRegistration: state => state.sidebar.registration,
  header: state => state.header,
  headerButtons: state => state.header.buttons,
  headerTitle: state => state.header.title,
  headerUser: state => state.header.user,
  page: state => state.page,
  alerts: state => state.page.alerts,
  scripts: state => state.page.scripts,
  footer: state => state.footer
}
