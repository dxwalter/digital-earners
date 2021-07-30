<template>
  <div>
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body">
        <!-- content body -->
        <div class="history-table-container">
          <div class="history-table-header dark-gray">
            <div>New investments</div>
          </div>

          <!-- Contents go in here -->
          <div v-if="status">
            <div v-for="investment in newInvestment" :key="investment.id">
              <div
                class="login-container-shadow bg-white card-design"
                :data-investmentId="investment.id"
              >
                <div class="card-upper-layer">
                  <img
                    class="profile-card-img"
                    v-bind:src="getHttDPImage(investment.userId, investment.profilePicture)"
                  />
                  <div class="investor-card-details">
                    <router-link
                      :to="`/InvestorProfile/${investment.userId}`"
                    >{{ investment.fullname }}</router-link>
                    <div class="date-info">{{ investment.uploadDate }} @ {{ investment.uploadTime }}</div>
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
                    <small>Sum Total</small>
                    <div>₦{{ FormatDigit(investment.totalAmount.toString()) }}</div>
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

                <div class="btm-btn-layout animated" :id="`investment${investment.id}`">
                  <button
                    class="confirmed-green white-color btn-md"
                    v-on:click="confirmPayment(investment.id, 'confirm', investment.userId)"
                    :value="investment.id"
                  >Confirm</button>
                  <button
                    class="cancel-inv-btn white-color btn-md"
                    v-on:click="confirmPayment(investment.id, 'cancel', investment.userId)"
                    :value="investment.id"
                  >Cancel</button>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <div class="alert-info">{{message}}</div>
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
      status: "",
      message: "",
      newInvestment: "",
      token: ""
    };
  },
  methods: {
    newInvestmentListing(response) {
      if (response.status == "false") {
        this.status = 0;
        this.message = response.message;
      } else {
        this.status = 1;
        this.newInvestment = response.records;
      }
    },
    newInvestmentAction(response, investmentId) {
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
    },
    confirmPayment: function(investmentId, actionType, investor) {
      let data = JSON.stringify({
        investor: investor,
        investmentId: investmentId,
        jwt: this.token
      });

      if (actionType == "confirm") {
        let apiLink =
          "https://api.digitalearners.cc/action/investment/confirmInvestment.php";
        this.makePostRequest(data, apiLink, "newInvestmentAction");
      } else if (actionType == "cancel") {
        let apiLink =
          "https://api.digitalearners.cc/action/investment/deleteInvestment.php";
        this.makePostRequest(data, apiLink, "newInvestmentAction");
      }
    },
    entryPoint: function() {
      let jwToken = this.authenticateUser();
      this.token = jwToken;
      let data = JSON.stringify({
        jwt: jwToken,
        count: "all"
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/listOfUnconfirmedInvestment.php";

      this.makePostRequest(data, apiLink, "newInvestmentListing");
    }
  },
  created() {
    this.entryPoint();
  }
};
</script>