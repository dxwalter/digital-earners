<template>
  <div>
    <div class="history-table-container">
      <div class="history-table-header blue-color">
        <div>New investments</div>
      </div>

      <div class="history-listing body-color" v-if="status">
        <router-link
          to="/ConfirmInvestments"
          class="history-card bg-white"
          v-for="investment in newInvestment"
          :key="investment.id"
        >
          <div
            class="history-card-date dark-grey-light"
          >{{ investment.uploadDate }} @ {{ investment.uploadTime }}</div>

          <div class="history-data-container">
            <div class="history-label dark-grey-light">Invested</div>
            <div class="history-card-data dark-grey">₦{{ FormatDigit(investment.capital) }}</div>
          </div>

          <div class="history-data-container">
            <div class="history-label dark-grey-light">ROI</div>
            <div class="history-card-data dark-grey">₦{{ FormatDigit(investment.roi) }}</div>
          </div>

          <div class="history-bottom">
            <div class="investment-status-label confirmed-green">View more details</div>
          </div>
        </router-link>
      </div>

      <div class="history-listing body-color" v-else>
        <div class="alert-info">{{message}}</div>
      </div>

      <div v-show="status == true">
        <div class="history-table-header blue-color">
          <router-link
            to="/ConfirmInvestments"
            class="btn-element orange-bg orange-btn btn-width-auto"
          >View all investments</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Logic from "../../../modules/Logic";
export default {
  name: "CustomersToPay",
  extends: Logic,
  data() {
    return {
      status: "",
      message: "",
      newInvestment: ""
    };
  },
  methods: {
    dashboardCustomersToPay: function(response) {
      if (response.status == "false") {
        this.status = 0;
        this.message = response.message;
      } else {
        this.status = 1;
        this.newInvestment = response.records;
      }
    }
  },
  created() {
    let data = JSON.stringify({
      jwt: this.authenticateUser(),
      count: 3
    });

    let apiLink =
      "https://api.digitalearners.cc/action/investment/listOfUnconfirmedInvestment.php";

    this.makePostRequest(data, apiLink, "DBcustomerToPay");
  }
};
</script>

<style>
</style>
