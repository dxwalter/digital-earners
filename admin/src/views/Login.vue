<template>
  <div>
    <div class="welcome-header dark-grey">Welcome to Digital Earners</div>
    <div class="instruction dark-grey-light">Sign in to continue</div>
    <div class="login-container login-container-shadow bg-white">
      <form @submit="loginUser">
        <div class="form-container">
          <div class="form-label dark-grey-light">Username</div>
          <input type="text" class="form-input form-bg dark-grey" v-model="username" />
        </div>

        <div class="form-container">
          <div class="form-label dark-grey-light">Password</div>
          <input type="password" class="form-input form-bg dark-grey" v-model="password" />
        </div>

        <div class="form-container">
          <button type="submit" class="btn-element orange-bg orange-btn" :disabled="!isDisabled">Sign in</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { mapActions } from "vuex";
import Logic from "../modules/Logic";
export default {
  name: "Login",
  extends: Logic,
  data() {
    return {
      username: "",
      password: ""
    };
  },
  methods: {
    ...mapActions(["setJWTCookey"]),
    loginUser(e) {
      e.preventDefault();

      let username = this.username;
      let password = this.password;

      if (username.length == 0 || password.length == 0) {
        this.$toast.info({
          title: "Error",
          message: "Enter your username and password."
        });
        return;
      }

      let data = JSON.stringify({
        username: username,
        password: password
      });

      this.authenticateUser(data);
    },
    authenticateUser(data) {
      this.isDisabled = !this.isDisabled;
      let apiLink = "https://api.digitalearners.cc/action/admin/login.php";
      this.makePostRequest(data, apiLink, "getLoginCallBack");
    },
    getLoginCallBack(response) {
      let status = response.status;
      let message = response.message;

      this.isDisabled = !this.isDisabled;
      if (status == "true") {
        //
        let jwt = response.jwt;
        this.setJWTCookey(jwt);

        this.$toast.success({
          title: "Success",
          message: message
        });
        // redirect to login page
        setTimeout(() => {
          this.$router.push({ name: "Dashboard" });
        }, 3000);
        return;
      } else {
        this.$toast.info({
          title: "Error",
          message: message
        });
        return;
      }
    }
  }
};
</script>