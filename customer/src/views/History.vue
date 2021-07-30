<style>
</style>

<template>
  <div id="app-layout">
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body">
        <!-- Profile content goes in here -->

        <div class="history-table-container">
          <div class="history-table-header blue-color">
            <div>Investment History</div>
          </div>

          <div class="notif-listing">
            <!-- This is where the history listing will go -->

            <div v-if="historyCount == 0">
              <div class="alert-info">{{ historyListingMessage }}</div>
            </div>

            <div v-else-if="historyCount > 0">
              <div v-for="investment in historyListingMessage" :key="investment.id">
                <router-link :to="`/Investment/${investment.id}`" class="main-history-list-item">
                  <div
                    class="investment-status running"
                    v-if="investment.roiReceived == '0'"
                  >Running</div>
                  <div
                    class="investment-status complete"
                    v-else-if="investment.roiReceived == '1'"
                  >Completed</div>

                  <div
                    class="investment-details bg-white"
                    v-bind:class="[parseInt(investment.roiReceived) ? 'complete' : 'running']"
                  >
                    <!-- this will hold investment data-->
                    <div class="investment-date">
                      <div>Investment Date</div>
                      <div>{{ investment.uploadDate }}</div>
                    </div>

                    <div class="investment-date">
                      <div>Investment Capital</div>
                      <div>₦{{ FormatDigit(investment.capital) }}</div>
                    </div>

                    <div class="investment-date">
                      <div>ROI</div>
                      <div>₦{{ FormatDigit(investment.roi) }}</div>
                    </div>
                  </div>
                </router-link>
              </div>
            </div>
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
  extends: Logic,
  name: "History",
  components: {
    Navigation
  },
  data() {
    return {
      historyCount: "",
      historyListingMessage: ""
    };
  },
  methods: {
    historyListing() {
      let data = JSON.stringify({
        jwt: this.authenticateUser(),
        investor: this.userId,
        type: "uncategorised",
        count: "all",
        categoryType: "running"
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/historyListing.php";
      this.makePostRequest(data, apiLink, "allInvestmentHistoryCallBack");
    },
    allInvestmentHistoryCallBack(response) {
      if (response.status == "false") {
        this.historyCount = 0;
        this.historyListingMessage = response.message;
      } else if (response.status == "true") {
        this.historyCount = response.recordCount;
        this.historyListingMessage = response.records;
      }
    }
  },
  created() {
    this.authenticateUser();
    this.historyListing();
  }
};
</script>

