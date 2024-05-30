
import { generateFilePath } from '@nextcloud/router'
import { generateUrl } from '@nextcloud/router'

import Vue from 'vue'
import App from './App.vue'
import VueRouter from 'vue-router'
import TableContent from './components/TableContent.vue'
import Menu from './components/Menu.vue'
import NewUser from './components/NewUser.vue'
import vuetify from './plugins/vuetify';
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css';
import Search from './components/Search.vue'


Vue.use(VueGoodTablePlugin);
Vue.use(VueRouter)
Vue.directive('click-outside', {
  bind(el, binding, vnode) {
    el.clickOutsideEvent = function (event) {
      if (!(el === event.target || el.contains(event.target)) && event.target.closest('.vue-select') === null) {
        vnode.context[binding.expression](event);
      }
    };
    document.body.addEventListener('click', el.clickOutsideEvent);
  },
  unbind(el) {
    document.body.removeEventListener('click', el.clickOutsideEvent);
  },
});

const router = new VueRouter({
  base: generateUrl('/apps/kmaqlcb'),
  routes: [
    {
      path: '/timkiem', component: Search
    },
    {
      path: '/hoso', component: TableContent
    },
    { path: '/hoso/newuser', component: NewUser },
    {
      path: '/hoso/:user_id',
      name: 'Details',
      component: Menu,
      props: true
    },
  ]
})

router.push('/hoso')

__webpack_public_path__ = generateFilePath(appName, '', 'js/')

Vue.mixin({ methods: { t, n } })

export default new Vue({
  router,
  vuetify,
  el: '#content',
  render: h => h(App),
})