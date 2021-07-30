<template>
  <div>
    <div class="welcome-header dark-grey">Welcome to Digital Earners</div>
    <div class="instruction dark-grey-light">Change your password</div>
    <div class="login-container login-container-shadow bg-white">
      <form @submit.prevent="changePassword">
        <div class="form-container">
          <div class="form-label dark-grey-light">
            New password
            <br />
            <small>
              <i>Six or more characters</i>
            </small>
          </div>
          <input type="password" class="form-input form-bg dark-grey" v-model="newPassword" />
        </div>

        <div class="form-container">
          <div class="form-label dark-grey-light">Confirm password</div>
          <input type="password" class="form-input form-bg dark-grey" v-model="confirmPassword" />
        </div>

        <div class="form-container">
          <button
            type="submit"
            class="btn-element orange-bg orange-btn"
            :disabled="!isDisabled"
          >Change Password</button>
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
      newPassword: "",
      confirmPassword: "",
      userId: "",
      isDisabled: true
    };
  },
  methods: {
    changePassword() {
      if (this.newPassword.length < 6) {
        this.$toast.info({
          title: "Error",
          message:
            "Your password is required and it must be greater than 5 characters"
        });
        return;
      }

      if (this.newPassword !== this.confirmPassword) {
        this.$toast.info({
          title: "Error",
          message: "Your passwords are not identical"
        });
        return;
      }

      this.isDisabled = !this.isDisabled;

      let data = JSON.stringify({
        password: this.newPassword,
        userId: this.userId
      });

      let apiLink =
        "https://api.digitalearners.cc/action/customer/createPasswordNoJwt.php";
      this.makePostRequest(data, apiLink, "newPasswordNoJwt");
    },
    newPasswordNoJwt(response) {
      if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
        setTimeout(() => {
          this.$router.push({ name: "ForgotPassword" });
        }, 3000);
        return;
      } else {
        this.$toast.success({
          title: "Success",
          message: response.message
        });
        setTimeout(() => {
          this.$router.push({ name: "Login" });
        }, 3000);
        return;
      }

      this.isDisabled = !this.isDisbled;
    }
  },
  created() {
    this.userId = this.$route.params.userId;
  }
};
</script>