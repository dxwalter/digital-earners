<template>
  <div class="dashboard-card">
    <a href="#" class="content-carrier content-carrier-card-three login-container-shadow">
      <h3 class="card-hearder">Total active investment</h3>
      <div class="major-detail">₦{{totalCapital}}</div>
      <div class="minor-detail">This is the sum amount of capitals actively running.</div>
    </a>
    <a href="#" class="content-carrier content-carrier-card-two login-container-shadow">
      <h3 class="card-hearder">Total investment ROI</h3>
      <div class="major-detail">₦{{ totalRoi }}</div>
      <div
        class="minor-detail"
      >This is the sum amount of ROI to be paid to actively running investments.</div>
    </a>
    <a href="#" class="content-carrier content-carrier-card-one login-container-shadow">
      <h3 class="card-hearder">All investors</h3>
      <div class="major-detail">{{ allInvestor }}</div>
      <div class="minor-detail">This is the total number of registered investors</div>
    </a>
    <a href="#" class="content-carrier content-carrier-card-three login-container-shadow">
      <h3 class="card-hearder">New investments</h3>
      <div class="major-detail">{{ newInvestment }}</div>
      <div class="minor-detail">This is the total number of investments yet to be confirmed</div>
    </a>
  </div>
</template>

<script>
import Logic from "../../../modules/Logic";
export default {
  name: "DashboardCard",
  extends: Logic,
  data() {
    return {
      totalCapital: "",
      totalRoi: "",
      allInvestor: "",
      newInvestment: ""
    };
  },
  methods: {
    getCardCallback(response) {
      this.totalCapital = this.FormatDigit(response.runningCapital.capital);
      this.totalRoi = this.FormatDigit(response.totalRoi.totalRoi);
      this.allInvestor = this.FormatDigit(response.totalInvestors.allInvestors);
      this.newInvestment = this.FormatDigit(
        response.unconfirmedINV.unconfirmedInvestments
      );
    }
  },
  created() {
    let data = JSON.stringify({
      jwt: this.authenticateUser()
    });
    let apiLink =
      "https://api.digitalearners.cc/action/investment/adminCustomerData.php";
    this.makePostRequest(data, apiLink, "DashboardCardCallback");
  }
};
</script>

<style>
</style>
                