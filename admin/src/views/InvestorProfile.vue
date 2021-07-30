<template>
  <div>
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="profile-container bg-white login-container-shadow">
          <div>
            <!-- top area -->

            <div class="investor-profile-top mg-bottom-16">
              <div class="investor-profile">
                <img v-bind:src="getHttDPImage(userId, profilePicture)" alt />
              </div>

              <div>
                <div class="investor-profile-name dark-grey">{{fullname }}</div>
                <div class="investor-minor-details">{{ email }}</div>
                <div class="investor-minor-details mg-bottom-16">
                  <a :href="`tel:${phoneNumber}`">{{ phoneNumber }}</a>
                </div>

                <div class="investor-minor-details mg-bottom-16 investment-date">
                  <div>Last seen</div>
                  {{ lastSeen }}
                </div>

                <div class="investor-minor-details btn">
                  <!-- When activeStatus == 1, it means the user
                        is still actively using the service.
                        When activeStatus == 0, it means the user has been
                        suspended
                  -->
                  <button
                    class="block-user-btn block-user"
                    type="button"
                    v-if="activeStatus == 1"
                    v-on:click="changeUserStatus(0)"
                  >Block User</button>
                  <button
                    class="block-user-btn allow-user"
                    type="button"
                    v-if="activeStatus == 0"
                    v-on:click="changeUserStatus(1)"
                  >Allow User</button>
                  <router-link
                    :to="`/BackdateAccount/${userId}`"
                    class="block-user-btn backdate-acc"
                  >Backdate Account</router-link>
                </div>
              </div>
            </div>

            <div class="profile-action-container">
              <a
                href="javascript:void(0)"
                v-on:click="(confirmPayment('activeInvestmentTab'))"
                class="edit-tab-action"
                v-bind:class="{ 'active-tab': activeInvestmentTab }"
              >Active investment</a>
              <a
                href="javascript:void(0)"
                v-on:click="(confirmPayment('historyTab'))"
                class="edit-tab-action"
                v-bind:class="{ 'active-tab': historyTab }"
              >History</a>
            </div>

            <div
              class="edit-tab-form-action animated no-padding"
              v-bind:class="[{ 'display-none': !activeInvestmentTab }, {'fadeIn': activeInvestmentTab }]"
            >
              <!-- actively running investments -->
              <div v-if="activeInvestmentPropertyStatus == 0">
                <div class="alert-info">{{ activeInvestmentPropertyMessage }}</div>
              </div>

              <div v-else>
                <div v-for="investment in activeInvestmentPropertyMessage" :key="investment.id">
                  <router-link
                    :to="`/investmentDetails/${investment.id}/${userId}`"
                    class="main-history-list-item"
                  >
                    <div
                      class="investment-status running"
                      v-if="investment.roiReceived == '0'"
                    >Running</div>
                    <div
                      class="investment-status running"
                      v-else-if="investment.roiReceived == '1'"
                    >Completed</div>

                    <div class="investment-details running bg-white">
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

            <div
              class="edit-tab-form-action-two animated no-padding"
              v-bind:class="[{ 'display-block': historyTab }, {'fadeIn': historyTab}]"
            >
              <!-- all investment history -->
              <div v-if="historyInvestmentPropertyStatus == 0">
                <div class="alert-info">{{ historyInvestmentPropertyMessage }}</div>
              </div>

              <div v-else>
                <div v-for="investment in historyInvestmentPropertyMessage" :key="investment.id">
                  <router-link
                    :to="`/investmentDetails/${investment.id}/${userId}`"
                    class="main-history-list-item"
                  >
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
      activeInvestmentTab: "",
      historyTab: "",
      userId: "",
      activeStatus: "",
      email: "",
      fullname: "",
      lastSeen: "",
      phoneNumber: "",
      profilePicture: "",
      token: "",
      // tab properties
      activeInvestmentPropertyMessage: "",
      activeInvestmentPropertyStatus: "",

      historyInvestmentPropertyMessage: "",
      historyInvestmentPropertyStatus: ""
    };
  },
  methods: {
    confirmPayment(tab) {
      if (tab == "activeInvestmentTab") {
        this.activeInvestmentTab = 1;
        this.historyTab = 0;
      } else if (tab == "historyTab") {
        this.activeInvestmentTab = 0;
        this.historyTab = 1;
      }
    },
    customerDetailsCallback(response) {
      if (response.status == true) {
        this.lastSeen = response.last_seen;
        this.email = response.email;
        this.activeStatus = parseInt(response.active_status);
        this.phoneNumber = response.phone_number;
        this.profilePicture = response.profile_picture;
        if (response.fullname == "") {
          this.fullname = "No name yet";
        } else {
          this.fullname = response.fullname;
        }

        if (response.last_seen == "") {
          this.lastSeen = "This customer has never signed in";
        } else {
          this.lastSeen = response.last_seen;
        }
      } else if (response.status == "false") {
        // show error message and redirect to
        // the page the page that has the list of investors
        // after 3 seconds
        this.$toast.info({
          title: "Error",
          message: response.message
        });

        // redirect to login page
        setTimeout(() => {
          this.$router.push({ name: "Investors" });
        }, 3000);
      }
    },
    getCustomerData() {
      let data = JSON.stringify({
        customer: this.userId,
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/customer/getCustomerData.php";

      this.makePostRequest(data, apiLink, "customerDetailsCallback");
    },
    changeUserStatus(action) {
      let userToken = this.token;
      let apiLink;

      if (action == 1) {
        // activate user
        apiLink =
          "https://api.digitalearners.cc/action/customer/allowCustomer.php";
      } else if (action == 0) {
        // suspend user
        apiLink =
          "https://api.digitalearners.cc/action/customer/blockCustomer.php";
      }

      let data = JSON.stringify({
        jwt: userToken,
        customerId: this.userId
      });

      this.makePostRequest(data, apiLink, "changeCustomerAccessStatus");
    },
    changeCustomerAccessStatus(response) {
      if (response.status == "true") {
        this.$toast.success({
          title: "Success",
          message: response.message
        });

        if (this.activeStatus == 1) {
          this.activeStatus = 0;
        } else if (this.activeStatus == 0) {
          this.activeStatus = 1;
        }
      } else if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
      }
    },
    getActiveInvestment() {
      let data = JSON.stringify({
        type: "categorised",
        categoryType: "running",
        count: 21,
        investor: this.userId,
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/historyListing.php";

      this.makePostRequest(data, apiLink, "getActiveInvestmentCallback");
    },
    getInvestmentHistory() {
      let data = JSON.stringify({
        type: "uncategorised",
        categoryType: "running",
        count: 100,
        investor: this.userId,
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/historyListing.php";

      this.makePostRequest(data, apiLink, "getInvestmentHistoryCallback");
    },
    getActiveInvestmentCallback(response) {
      if (response.status == "false") {
        this.activeInvestmentPropertyMessage = response.message;
        this.activeInvestmentPropertyStatus = 0;
      } else {
        this.activeInvestmentPropertyStatus = 1;
        this.activeInvestmentPropertyMessage = response.records;
      }
    },
    getInvestmentHistoryCallback(response) {
      if (response.status == "false") {
        this.historyInvestmentPropertyMessage = response.message;
        this.historyInvestmentPropertyStatus = 0;
      } else {
        this.historyInvestmentPropertyStatus = 1;
        this.historyInvestmentPropertyMessage = response.records;
      }
    }
  },
  created() {
    this.activeInvestmentTab = 1;
    // get userId
    this.userId = this.$route.params.userId;

    this.token = this.authenticateUser();

    this.getCustomerData();

    this.getActiveInvestment();

    this.getInvestmentHistory();
  }
};
</script>
