<template>
  <div>
    <div class="welcome-header dark-grey">Welcome to Digital Earners</div>
    <div class="instruction dark-grey-light">Sign in to continue</div>
    <div class="login-container login-container-shadow bg-white">
      <form @submit="loginUser">
        <div class="form-container">
          <div class="form-label dark-grey-light">Phone number</div>
          <input type="text" class="form-input form-bg dark-grey" v-model="phoneNumber" />
        </div>

        <div class="form-container">
          <div class="form-label dark-grey-light">Password</div>
          <input type="password" class="form-input form-bg dark-grey" v-model="password" />
        </div>

        <div class="form-container">
          <button
            type="submit"
            class="btn-element orange-bg orange-btn"
            :disabled="!isDisabled"
          >Sign in</button>
          <div class="remember-pword">
            <router-link to="/ForgotPassword">I can't remember my password</router-link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Logic from "@/modules/Logic";
export default {
  name: "Login",
  extends: Logic,
  components: {},
  data() {
    return {
      phoneNumber: "",
      password: "",
      isDisabled: true
    };
  },
  methods: {
    loginUser(e) {
      e.preventDefault();

      let phoneNumber = this.phoneNumber;
      let password = this.password;

      if (phoneNumber.length == 0 || password.length == 0) {
        this.$toast.info({
          title: "Error",
          message: "Enter your phone number and password."
        });
        return;
      }

      let data = JSON.stringify({
        phoneNumber: phoneNumber,
        password: password
      });

      this.authenticateUser(data);
    },
    authenticateUser(data) {
      this.isDisabled = !this.isDisabled;
      let apiLink =
        "https://api.digitalearners.cc/action/customer/login.php";
      this.makePostRequest(data, apiLink, "getLoginCallBack");
    },
    getLoginCallBack(response) {
      let status = response.status;
      let message = response.message;

      if (status == true) {
        //
        let jwt = response.jwt;
        this.setJWTCookey(jwt);
        this.setUserIdCookey(response.userData.userId);
        this.firstTimeAccess = response.userData.firstTimeAccess;

        this.$toast.success({
          title: "Success",
          message: message
        });
        this.isDisabled = !this.isDisabled;

        // redirect to login page
        if (response.userData.firstTimeAccess == "0") {
          setTimeout(() => {
            this.$router.push({ name: "EditProfile" });
          }, 3000);
          return;
        } else {
          setTimeout(() => {
            this.$router.push({ name: "Dashboard" });
          }, 3000);
          return;
        }
      } else {
        this.isDisabled = !this.isDisabled;
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