<template>
  <div>
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="edit-tab-form-action login-container-shadow bg-white add-padding width-manager">
          <div class="upload-proof-header">
            Backdate
            <span style="color: blue">{{fullname}}'s</span> account
          </div>

          <form @submit="createInvestment">
            <div class="form-container">
              <div class="form-label dark-grey-light">Capital invested</div>
              <input type="number" class="form-input form-bg dark-grey" v-model="capital" />
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">Choose a date the investement was made</div>
              <datepicker input-class="form-input form-bg dark-grey" v-model="dateInvested"></datepicker>
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">Has the returns of this investment been paid?</div>
              <select class="form-input form-bg dark-grey" v-model="paymentStatus">
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>

            <div class="form-container">
              <button type="submit" class="btn-element-two white-color blue-bg">Create Investment</button>
            </div>
          </form>
          <div class="edit-btn-container" id="errorDiv"></div>
          <div class="edit-btn-container">
            <router-link :to="`/InvestorProfile/${userId}`" class="bckProfileLink">Back to profile</router-link>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>
<script>
import Navigation from "@/views/Layout/Navigation";
import Datepicker from "vuejs-datepicker";
import moment from "moment";
import Logic from "../modules/Logic";
import { type } from "os";
export default {
  name: "Login",
  extends: Logic,
  components: {
    Navigation,
    Datepicker
  },
  data() {
    return {
      fullname: "",
      userId: "",
      dateInvested: "",
      capital: "",
      paymentStatus: ""
    };
  },
  methods: {
    getCustomerData() {
      let data = JSON.stringify({
        customer: this.userId,
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/customer/getCustomerData.php";

      this.makePostRequest(data, apiLink, "customerDetailsCallback");
    },
    customerDetailsCallback(response) {
      if (response.fullname == "") {
        this.fullname = "No name yet";
      } else {
        this.fullname = response.fullname;
      }
    },
    createInvestment(e) {
      e.preventDefault();

      if (this.capital.length < 5) {
        this.$toast.info({
          title: "Error",
          message: `Enter the capital invested by ${this.fullname}`
        });
        return;
      }

      if (this.dateInvested.length < 1) {
        this.$toast.info({
          title: "Error",
          message: `Choose the date the investment was made`
        });
        return;
      } else {
        this.customFormatter(this.dateInvested);
      }

      if (this.paymentStatus.length < 1) {
        this.$toast.info({
          title: "Error",
          message: `Confirm the clearance status of this investment`
        });
        return;
      }

      document.getElementById("errorDiv").innerHTML = "";

      let data = JSON.stringify({
        customer: this.userId,
        jwt: this.token,
        capital: this.capital,
        dateInvested: this.dateInvested,
        paymentStatus: this.paymentStatus
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/backDateInvestment.php";

      this.makePostRequest(data, apiLink, "backDateInvestmentCallback");
    },
    customFormatter(date) {
      this.dateInvested = moment(date).format("MM D YYYY");
    },
    backDateInvestmentCallback(response) {
      let pageMessage;

      if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
        pageMessage = `<div class="alert-info animated fadeIn">${response.message}</div>`;
      } else if (response.status == "true") {
        this.$toast.success({
          title: "Success",
          message: response.message
        });
        pageMessage = `<div class="alert-success animated fadeIn">The investment of ₦${this.FormatDigit(
          this.capital
        )} was successful</div>`;
      }

      document.getElementById("errorDiv").innerHTML = pageMessage;
    }
  },
  created() {
    this.activeInvestmentTab = 1;
    // get userId
    this.userId = this.$route.params.userId;

    this.token = this.authenticateUser();

    this.getCustomerData();
  }
};
</script>
