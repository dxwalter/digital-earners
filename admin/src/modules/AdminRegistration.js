import axios from "axios";

const state = {
  requestStatus: {},
  token: ""
};

const getters = {
  createAccountResponse: state => {
    return state.requestStatus;
  },
  jwt: state => state.jwt
};

const actions = {};

const mutations = {
  saveResponseMessage: (state, message) =>
    (state.requestStatus.message = message),
  saveResponseStatus: (state, status) => (state.requestStatus.status = status)
};

export default {
  state,
  getters,
  actions,
  mutations
};
