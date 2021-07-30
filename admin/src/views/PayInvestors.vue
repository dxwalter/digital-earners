<template>
  <div>
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body">
        <!-- content body -->
        <div class="history-table-container">
          <div class="history-table-header blue-color">
            <div>List of investors to be paid</div>
          </div>

          <!-- Contents go in here -->
          <div v-if="recordCount == 0">
            <div class="alert-info">{{ message }}</div>
          </div>

          <div v-else>
            <div
              class="login-container-shadow bg-white card-design"
              v-for="investment in record"
              :key="investment.investmentId"
            >
              <div class="card-upper-layer">
                <img v-bind:src="getHttDPImage(investment.userId, investment.profilePicture)" alt />
                <div class="investor-card-details">
                  <router-link
                    :to="`/InvestorProfile/${investment.userId}`"
                  >{{ investment.fullname }}</router-link>
                  <div class="date-info">{{ investment.uploadDate }} - {{ investment.cashOutDate }}</div>
                </div>
              </div>

              <div class="investment-card-detail">
                <div class="card-info-details">
                  <small>Capital</small>
                  <div>₦{{ FormatDigit(investment.capital) }}</div>
                </div>

                <div class="card-info-details">
                  <small>ROI</small>
                  <div>₦{{ FormatDigit(investment.roi) }}</div>
                </div>

                <div class="card-info-details">
                  <small>Total</small>
                  <div>₦{{ FormatDigit(investment.totalPay.toString()) }}</div>
                </div>
              </div>

              <a
                :href="getHttProofImage(investment.userId, investment.proofOfPayment)"
                class="investment-proof"
              >
                <img
                  v-bind:src="getHttProofImage(investment.userId, investment.proofOfPayment)"
                  alt
                />
              </a>

              <div :id="`investment${investment.investmentId}`">
                <button
                  class="confirmed-green white-color btn-md"
                  v-on:click="payInvestor(investment.investmentId)"
                >Made payment</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>

<script>
import Navigation from "@/views/Layout/Navigation";
import Logic from "../modules/Logic";
export default {
  name: "Login",
  extends: Logic,
  components: {
    Navigation
  },
  data() {
    return {
      recordCount: "",
      record: "",
      message: ""
    };
  },
  methods: {
    entryPoint: function() {
      let jwToken = this.authenticateUser();
      this.token = jwToken;
      let data = JSON.stringify({
        jwt: jwToken
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/listOfInvestorsToPay.php";

      this.makePostRequest(data, apiLink, "investorsToPay");
    },
    investorsToPay(response) {
      if (response.status == "false") {
        this.recordCount = 0;
        this.message = response.message;
      } else if (response.recordCount) {
        this.recordCount = response.recordCount;
        this.record = response.records;
      }
    },
    payInvestor(investmentId) {
      let data = JSON.stringify({
        investmentId: investmentId,
        jwt: this.token
      });
      let apiLink =
        "https://api.digitalearners.cc/action/investment/payInvestor.php";
      this.makePostRequest(data, apiLink, "payInvestorCallback");
    },
    payInvestorCallback(response, investmentId) {
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
        pageMessage = `<div class="alert-success animated fadeIn">${response.message}</div>`;
      }
      let divId = `investment${investmentId}`;
      document.getElementById(divId).innerHTML = "";
      document.getElementById(divId).innerHTML = pageMessage;
    }
  },
  created() {
    this.entryPoint();
  }
};
</script>
