<template>
  <div id="app-layout">
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="investment-detail-container bg-white login-container-shadow">
          <div v-if="roiPaymentStatus == '0'">
            <div class="inprogress-image login-container-shadow">
              <img src="@/assets/img/hourglass.svg" alt />
            </div>
          </div>

          <div v-else>
            <img
              src="@/assets/img/check-circle.svg"
              alt
              class="profile-lg-dp login-container-shadow"
            />
          </div>

          <div v-if="receiptStatus == '1'">
            <div class="profile-name dark-grey">Total - ₦{{ FormatDigit(totalAmount) }}</div>
          </div>
          <div v-else>
            <div class="edit-btn-container mg-bottom-16">
              <a
                href="/ConfirmInvestments"
                class="btn-md orange-btn white-color blue-bg"
              >Confirm investment</a>
            </div>
          </div>

          <div class="investment-details-status completed" v-if="roiPaymentStatus == '1'">Completed</div>
          <div class="investment-details-status running" v-if="roiPaymentStatus == '0'">Running</div>

          <div class="history-investment-info mg-top-32">
            <div class="investment-reason dark-grey-light">Investment Capital</div>
            <div class="investment-info-date dark-grey-light">{{ investmentDate }}</div>
            <div class="investment-info-amount">₦{{ FormatDigit(capital) }}</div>
          </div>

          <div class="history-investment-info" v-if="receiptStatus == '1'">
            <div class="investment-reason dark-grey-light">Return on investment</div>
            <div class="investment-info-date dark-grey-light">{{ cashoutDate }}</div>
            <div class="investment-info-amount">₦{{ FormatDigit(roi) }}</div>
          </div>

          <a
            v-bind:href="getHttProofImage(userId, proofOfPayment)"
            class="uploaded-proof login-container-shadow"
            target="_blank"
          >
            <img v-bind:src="getHttProofImage(userId, proofOfPayment)" alt />
          </a>

          <div class="edit-btn-container">
            <router-link
              :to="`/InvestorProfile/${userId}`"
              class="btn-md orange-btn white-color blue-bg"
            >Back to profile</router-link>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>
<script>
import Navigation from "@/views/Layout/Navigation";
import Logic from "@/modules/Logic";
export default {
  extends: Logic,
  components: {
    Navigation
  },
  data() {
    return {
      userId: "",
      token: "",
      investmentId: "",

      // investmentdata
      cashoutDate: "",
      capital: "",
      investmentDate: "",
      proofOfPayment: "",
      receiptStatus: "",
      roi: "",
      roiPaymentStatus: "",
      totalAmount: ""
    };
  },
  methods: {
    getInvestmentDetails() {
      let data = JSON.stringify({
        investmentId: this.investmentId,
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/investmentDetails.php";
      this.makePostRequest(data, apiLink, "investmentDetails");
    },
    investmentDetails(response) {
      if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });

        setTimeout(() => {
          this.$router.push({ path: `/InvestorProfile/${this.userId}` });
        }, 3000);
      } else {
        this.cashoutDate = response.binaryDate;
        this.capital = response.capital;
        this.investmentDate = response.investmentDate;
        this.proofOfPayment = response.proofOfPayment;
        this.receiptStatus = response.receiptStatus;
        this.roi = response.roi;
        this.roiPaymentStatus = response.roiPaymentStatus;
        this.totalAmount = response.totalAmountToPay.toString();
      }
    }
  },
  created() {
    // get userId
    this.userId = this.$route.params.userId;
    this.token = this.authenticateUser();
    this.investmentId = this.$route.params.id;
    this.getInvestmentDetails();
  }
};
</script>