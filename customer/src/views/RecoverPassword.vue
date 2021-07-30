<template>
  <div>
    <div class="welcome-header dark-grey">Recover password</div>
    <div class="login-container login-container-shadow bg-white">
      <form @submit.prevent="submitRequest">
        <div class="form-container">
          <div class="form-label dark-grey-light">Email Address</div>
          <input type="email" class="form-input form-bg dark-grey" v-model="emailAddress" />
        </div>

        <div class="form-container">
          <button
            type="submit"
            class="btn-element orange-bg orange-btn"
            :disabled="!isDisabled"
          >Recover password</button>
          <div class="remember-pword">
            <router-link to="/Login">I was kidding. I just remebered</router-link>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Logic from "@/modules/Logic";
export default {
  name: "ForgotPassword",
  extends: Logic,
  components: {},
  data() {
    return {
      emailAddress: "",
      isDisabled: true
    };
  },
  methods: {
    submitRequest() {
      if (this.emailAddress.length < 3) {
        this.$toast.info({
          title: "Error",
          message: "Enter a valid email address"
        });
        return;
      }

      this.isDisabled = !this.isDisabled;
      let data = JSON.stringify({
        email: this.emailAddress
      });

      this.isDisabled = !this.isDisabled;

      let apiLink =
        "https://api.digitalearners.cc/action/customer/forgotPassword.php";
      this.makePostRequest(data, apiLink, "recoverPasswordCallBack");
    },
    recoverPasswordCallBack(response) {
      if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
        return;
      } else if (response.status == "true") {
        this.$toast.success({
          title: "Sucess",
          message: response.message
        });
        return;
      }

      this.isDisabled = !this.isDisabled;
    }
  }
};
</script>

