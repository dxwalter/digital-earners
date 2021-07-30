<template>
  <div>
    <div class="welcome-header dark-grey">Welcome to Digital Earners</div>
    <div class="instruction dark-grey-light">Sign up to continue</div>
    <div class="login-container login-container-shadow bg-white">
      <form @submit="createAccount">
        <div class="form-container">
          <div class="form-label dark-grey-light">Fullname</div>
          <input type="text" class="form-input form-bg dark-grey" v-model="fullname" />
        </div>

        <div class="form-container">
          <div class="form-label dark-grey-light">Username</div>
          <input type="text" class="form-input form-bg dark-grey" v-model="username" />
        </div>

        <div class="form-container">
          <div class="form-label dark-grey-light">Password</div>
          <input type="password" class="form-input form-bg dark-grey" v-model="password" />
        </div>

        <div class="form-container">
          <button type="submit" class="btn-element orange-bg orange-btn">Sign up</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { setTimeout } from "timers";
import Logic from "@/modules/Logic";

export default {
  name: "CreateController",
  extends: Logic,
  data() {
    return {
      fullname: "",
      username: "",
      password: ""
    };
  },
  methods: {
    createAccount(e) {
      e.preventDefault();
      let fullname = this.fullname;
      let username = this.username;
      let password = this.password;

      if (fullname.length < 3) {
        this.$toast.info({
          title: "Error",
          message: "Enter your valid fullname"
        });
        return;
      }

      if (username.length < 4) {
        this.$toast.info({
          title: "Error",
          message: "Your username must be greater than 4 characters"
        });
        return;
      }

      if (password.length < 6) {
        this.$toast.info({
          title: "Error",
          message: "Your password must be greater than 5 characters"
        });
        return;
      }

      let data = JSON.stringify({
        username: username,
        fullname: fullname,
        password: password
      });

      // this is where i called the create account action and
      // passed in data as a parameter

      this.createAdminRequest(data);
    },
    createAdminRequest(data) {
      let apiLink =
        "https://api.digitalearners.cc/action/admin/createAdmin.php";

      this.makePostRequest(data, apiLink, "createAdminResponseCallBack");
    },
    createAdminResponseCallBack(response) {
      let responseData = response;
      if (responseData.status == "false") {
        this.$toast.info({
          title: "Error",
          message: responseData.message
        });
        return;
      } else if (responseData.status == "true") {
        this.$toast.success({
          title: "Success",
          message: responseData.message
        });
        // redirect to login page
        setTimeout(() => {
          this.$router.push({ name: "Login" });
        }, 3000);
        return;
      }
    }
  }
};
</script>