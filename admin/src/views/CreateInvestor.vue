<template>
  <div>
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="edit-tab-form-action login-container-shadow bg-white add-padding width-manager">
          <div class="upload-proof-header">Create a new investor</div>

          <form @submit="createInvestor">
            <div class="form-container">
              <div class="form-label dark-grey-light">Email address</div>
              <input type="email" class="form-input form-bg dark-grey" v-model="email" />
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">Phone number</div>
              <input type="number" class="form-input form-bg dark-grey" v-model="phoneNumber" />
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">
                Password
                <br />
                <small>
                  <i>Six or more characters</i>
                </small>
              </div>
              <input type="password" class="form-input form-bg dark-grey" v-model="password" />
            </div>

            <div class="form-container">
              <button
                type="submit"
                class="btn-element-two white-color blue-bg"
                :disabled="!isDisabled"
              >Create investor</button>
            </div>
          </form>
        </div>
      </div>
    </aside>
  </div>
</template>
<script>
import Navigation from "@/views/Layout/Navigation";
import Logic from "../modules/Logic";
import { type } from "os";
export default {
  name: "Login",
  extends: Logic,
  components: {
    Navigation
  },
  data() {
    return {
      email: "",
      phoneNumber: "",
      password: "",
      token: "",
      isDisabled: true
    };
  },
  methods: {
    createInvestor(e) {
      e.preventDefault();
      let email = this.email;
      let phoneNumber = this.phoneNumber;
      let password = this.password;

      if (email.length < 1) {
        this.$toast.info({
          title: "Error",
          message: "Enter the email address of the customer"
        });
        return;
      }

      if (phoneNumber.length < 11) {
        this.$toast.info({
          title: "Error",
          message: "Enter the phone number of the customer"
        });
        return;
      }

      if (password.length < 6) {
        this.$toast.info({
          title: "Error",
          message:
            "The password for the customer must be greater than six characters"
        });
        return;
      }

      let data = JSON.stringify({
        email: email,
        phone: phoneNumber,
        password: password,
        jwt: this.token
      });
      this.isDisabled = !this.isDisabled;
      let apiLink =
        "https://api.digitalearners.cc/action/customer/createAccount.php";
      this.makePostRequest(data, apiLink, "createNewInvestor");
    },
    createNewInvestor(response) {
      this.isDisabled = !this.isDisabled;
      if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
        return;
      } else if (response.status == "true") {
        this.$toast.success({
          title: "Success",
          message: response.message
        });
        return;
      }
    }
  },
  created() {
    let jwToken = this.authenticateUser();
    this.token = jwToken;
  }
};
</script>
