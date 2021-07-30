import Vue from "vue";
import VueCookies from "vue-cookie";
import axios from "axios";
import { stat } from "fs";

Vue.use(VueCookies);

const state = {};

const getters = {};

const actions = {
  // get notification label

  getNotificationLabel({ commit }, jwtToken) {
    let token = jwtToken;
  }
};

const mutations = {};

export default {
  state,
  getters,
  actions,
  mutations
};
