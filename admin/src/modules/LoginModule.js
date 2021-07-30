import Vue from "vue";
import VueCookies from "vue-cookie";
import axios from "axios";

Vue.use(VueCookies);

const state = {
  token: ""
};

const getters = {
  getUserToken: state => state.token
};

const actions = {
  // set jwt to cookey
  setJWTCookey({ commit }, data) {
    VueCookies.set("userToken", data);
    commit("setJWT", data);
  }
};

const mutations = {
  setJWT: (state, token) => (state.token = token)
};

export default {
  state,
  getters,
  actions,
  mutations
};
