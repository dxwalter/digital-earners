import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import "./registerServiceWorker";
import VueCookies from "vue-cookie";
import NProgress from "nprogress";
import CxltToastr from "cxlt-vue2-toastr";

import "../node_modules/nprogress/nprogress.css";
import "cxlt-vue2-toastr/dist/css/cxlt-vue2-toastr.css";

Vue.use(VueCookies);

Vue.config.productionTip = false;

var toastrConfigs = {
  position: "top right",
  showDuration: 5,
  progressBar: true,
  closeButton: true
};
Vue.use(CxltToastr, toastrConfigs);

router.beforeResolve((to, from, next) => {
  if (to.name) {
    NProgress.start();
  }
  next();
});

router.afterEach((to, from) => {
  NProgress.done();
});

Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: function(h) {
    return h(App);
  }
}).$mount("#app");
