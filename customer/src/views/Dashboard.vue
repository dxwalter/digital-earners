<template>
  <div id="app-layout">
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body">
        <!-- content body -->

        <div class="dashboard-card">
          <router-link
            to="/History"
            class="content-carrier content-carrier-card-three login-container-shadow"
          >
            <h3 class="card-hearder">Latest Investment</h3>
            <div>
              <div class="minor-detail">₦{{ FormatDigit(latestInvestmentCapital) }}</div>
            </div>
            <div class="major-detail">₦{{ FormatDigit(latestInvestmentRoi) }}</div>

            <div class="btn-md btn-sm orange-btn orange-bg manage-btn-position">View details</div>
          </router-link>

          <router-link
            to="/History"
            class="content-carrier content-carrier-card-two login-container-shadow"
          >
            <h3 class="card-hearder">Earned ROI</h3>
            <div>
              <div class="major-detail">₦{{ FormatDigit( totalRoi ) }}</div>
            </div>

            <div class="btn-md btn-sm orange-btn orange-bg manage-btn-position">View details</div>
          </router-link>
          <router-link
            to="/History"
            class="content-carrier content-carrier-card-one login-container-shadow"
          >
            <h3 class="card-hearder">Total Investment</h3>
            <div class="major-detail">₦{{ FormatDigit( totalCapitalInvestment ) }}</div>
            <div class="btn-md btn-sm orange-btn orange-bg manage-btn-position">View details</div>
          </router-link>
        </div>

        <div class="history-table-container">
          <div class="history-table-header blue-color">
            <div>Investment history</div>
          </div>

          <div v-if="dashboardHistoryCount < 1">
            <div class="alert-infor">{{ dashboardHistory }}</div>
          </div>

          <div v-else>
            <div
              class="history-listing"
              v-for="investmentInfo in dashboardHistory"
              :key="investmentInfo.id"
            >
              <router-link :to="`/Investment/${investmentInfo.id}`" class="history-card bg-white">
                <div class="history-card-date dark-grey-light">{{ investmentInfo.uploadDate }}</div>

                <div class="history-data-container">
                  <div class="history-label dark-grey-light">Invested</div>
                  <div
                    class="history-card-data dark-grey"
                  >₦{{ FormatDigit(investmentInfo.capital) }}</div>
                </div>

                <div class="history-data-container">
                  <div class="history-label dark-grey-light">ROI</div>
                  <div class="history-card-data dark-grey">₦{{ FormatDigit(investmentInfo.roi) }}</div>
                </div>

                <div class="history-data-container">
                  <div
                    class="history-label dark-grey-light"
                  >Cashed out - {{ investmentInfo.cashOutDate }}</div>
                  <div
                    class="history-card-data-large dark-grey"
                  >₦{{ FormatDigit(investmentInfo.totalAmount.toString()) }}</div>
                  <div class="history-label dark-grey-light"></div>
                </div>

                <div class="history-bottom">
                  <div class="investment-status-label confirmed-green">Confirmed</div>

                  <img src="@/assets/img/check-circle.svg" class="cashout-check" />
                </div>
              </router-link>
            </div>
          </div>

          <div class="history-table-header blue-color">
            <router-link
              to="/History"
              class="btn-element orange-bg orange-btn btn-width-auto"
            >View history</router-link>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>

<script>
import Navigation from "./Layout/CustomerPageLayout.vue";
import Logic from "@/modules/Logic";
export default {
  name: "Dashboard",
  extends: Logic,
  components: {
    Navigation
  },
  data() {
    return {
      latestInvestmentCapital: "",
      latestInvestmentRoi: "",
      earnedRoi: "",
      totalCapitalInvestment: "",
      totalRoi: "",
      dashboardHistory: "",
      dashboardHistoryCount: ""
    };
  },
  methods: {
    getInvestmentData() {
      let data = JSON.stringify({
        jwt: this.authenticateUser(),
        investor: this.userId
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/investmentData.php";
      this.makePostRequest(data, apiLink, "getDashboardInvestmentDataCallback");
    },
    getDashboardInvestmentDataCallback(response) {
      this.latestInvestmentCapital = response.latestInvestment.capital;
      this.latestInvestmentRoi = response.latestInvestment.roi;

      this.totalCapitalInvestment = response.investedCapital.totalCapital;

      this.totalRoi = response.totalRoi.totalRoi;
    },
    getUncategorisedHistory() {
      let data = JSON.stringify({
        jwt: this.authenticateUser(),
        investor: this.userId,
        type: "uncategorised",
        count: 6,
        categoryType: "running"
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/historyListing.php";
      this.makePostRequest(data, apiLink, "getBDinvestmentHistory");
    },
    getBDinvestmentHistory(response) {
      if (response.status == "false") {
        this.dashboardHistoryCount = 0;
        this.dashboardHistory = response.message;
      } else if (response.status == "true") {
        this.dashboardHistoryCount = response.recordCount;
        this.dashboardHistory = response.records;
      }
    }
  },
  created() {
    this.authenticateUser();
    this.getInvestmentData();
    this.getUncategorisedHistory();
  }
};
</script>