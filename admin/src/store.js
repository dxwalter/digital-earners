import Vue from "vue";
import Vuex from "vuex";
import LoginModule from "@/modules/LoginModule";
import AdminRegistration from "@/modules/AdminRegistration";

import NavigationState from "@/modules/NavigationState";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {},
  mutations: {},
  actions: {},
  modules: {
    LoginModule,
    AdminRegistration,
    NavigationState
  }
});
