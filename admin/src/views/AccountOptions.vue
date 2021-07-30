<template>
  <div>
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="profile-container bg-white login-container-shadow">
          <div>
            <!-- top area -->

            <div class="profile-action-container no-padding justify-content-left">
              <a
                href="javascript:void(0)"
                v-on:click="(confirmPayment('activeInvestmentTab'))"
                class="edit-tab-action-sec"
                v-bind:class="{ 'active-tab': activeInvestmentTab }"
              >Investment per date</a>
              <a
                href="javascript:void(0)"
                v-on:click="(confirmPayment('historyTab'))"
                class="edit-tab-action-sec"
                v-bind:class="{ 'active-tab': historyTab }"
              >Payout per date</a>
            </div>

            <div
              class="edit-tab-form-action animated no-padding mg-left-0 width-100"
              v-bind:class="[{ 'display-none': !activeInvestmentTab }, {'fadeIn': activeInvestmentTab }]"
            >
              <!-- actively running investments -->
              <!-- this is where data will be filled in -->

              <div class="investment-instruction">
                <div>Choose a date to see all the investment made on the date you chose</div>
                <div class="form-container mg-top-8">
                  <div class="form-label dark-grey-light">Choose a date</div>
                  <datepicker
                    input-class="form-input form-bg dark-grey"
                    @selected="investmentPerDate"
                    v-model="selectedDate"
                  ></datepicker>
                </div>
              </div>

              <div class="mg-top-32">
                <div
                  class="animated"
                  v-bind:class="[{'display-none': !investmentLoader}, {'fadeIn' : investmentLoader}]"
                >
                  <div class="loader"></div>
                </div>
                <div
                  id="investment-container"
                  class="animated"
                  v-bind:class="[{'display-none': !investmentDataState}]"
                >
                  <div v-if="investmentResponseStatus">
                    <div class="inv-card-container">
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Total Capital invested</div>
                        <div class="inv-main-data">₦{{ FormatDigit(totalCapital) }}</div>
                      </div>
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Total ROI expected</div>
                        <div class="inv-main-data">₦{{ FormatDigit(totalRoi) }}</div>
                      </div>
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Sum total of CAPITAL and ROI</div>
                        <div class="inv-main-data">₦{{ FormatDigit(totalCashOut) }}</div>
                      </div>
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Total investment</div>
                        <div class="inv-main-data">{{resultCount}}</div>
                      </div>

                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Human readable date</div>
                        <div class="inv-main-data">{{humanDate}}</div>
                      </div>
                    </div>
                    <!-- v-for for array of data representing investments made in a certain day -->
                    <div v-for="investmentData in InvestmentResponseData" :key="investmentData.id">
                      <div class="login-container-shadow bg-white card-design">
                        <div class="card-upper-layer">
                          <img
                            v-bind:src="getHttDPImage(investmentData.userid, investmentData.profilePicture)"
                            class="profile-card-img"
                          />
                          <div class="investor-card-details">
                            <router-link
                              :to="`/InvestorProfile/${investmentData.userid}`"
                            >{{ investmentData.fullname }}</router-link>
                            <div
                              class="date-info"
                            >{{ investmentData.uploadDate }} @ {{ investmentData.uploadTime }}</div>
                          </div>
                        </div>
                        <div class="investment-card-detail">
                          <div class="card-info-details">
                            <small>Capital</small>
                            <div>₦{{FormatDigit(investmentData.capital)}}</div>
                          </div>
                          <div class="card-info-details">
                            <small>ROI</small>
                            <div>₦{{FormatDigit(investmentData.roi)}}</div>
                          </div>
                        </div>
                        <a
                          :href="getHttProofImage(investmentData.userid, investmentData.proofOfPayment)"
                          class="investment-proof"
                        >
                          <img
                            v-bind:src="getHttProofImage(investmentData.userid, investmentData.proofOfPayment)"
                            alt
                          />
                        </a>
                      </div>
                    </div>
                  </div>
                  <div v-else>
                    <div class="alert-info">{{ InvestmentResponseData }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div
              class="edit-tab-form-action-two animated no-padding"
              v-bind:class="[{ 'display-block': historyTab }, {'fadeIn': historyTab}]"
            >
              <!-- all investment history -->
              <!-- This is where data will be filled in -->
              <div class="investment-instruction">
                <div>Choose a date to see all the payouts to be made on the date you chose</div>
                <div class="form-container mg-top-8">
                  <div class="form-label dark-grey-light">Choose a date</div>
                  <datepicker
                    input-class="form-input form-bg dark-grey"
                    @selected="payoutPerDate"
                    v-model="selectedDate"
                  ></datepicker>
                </div>
              </div>

              <div class="mg-top-32">
                <div
                  class="animated"
                  v-bind:class="[{'display-none': !payoutLoader}, {'fadeIn' : payoutLoader}]"
                >
                  <div class="loader"></div>
                </div>
                <div id="payout-container" v-bind:class="[{'display-none': !payoutDataState}]">
                  <div v-if="payoutResponseStatus">
                    <!-- v-if -->
                    <div class="inv-card-container">
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Total Capital invested</div>
                        <div class="inv-main-data">₦{{ FormatDigit(payoutTotalCapital) }}</div>
                      </div>
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Total ROI expected</div>
                        <div class="inv-main-data">₦{{ FormatDigit(payoutTotalRoi) }}</div>
                      </div>
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Total Cashout</div>
                        <div class="inv-main-data">₦{{ FormatDigit(payoutTotalCashOut) }}</div>
                      </div>
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Total investment</div>
                        <div class="inv-main-data">{{ payoutResultCount }}</div>
                      </div>
                      <div class="inv-card login-container-shadow">
                        <div class="inv-card-title">Human Readable date</div>
                        <div class="inv-main-data">{{ payoutHumanDate }}</div>
                      </div>
                    </div>

                    <div v-for="investmentData in payoutResponseData" :key="investmentData.id">
                      <div class="login-container-shadow bg-white card-design">
                        <div class="card-upper-layer">
                          <img
                            v-bind:src="getHttDPImage(investmentData.userid, investmentData.profilePicture)"
                            class="profile-card-img"
                          />
                          <div class="investor-card-details">
                            <router-link
                              :to="`/InvestorProfile/${investmentData.userid}`"
                            >{{ investmentData.fullname }}</router-link>
                            <div
                              class="date-info"
                            >{{ investmentData.uploadDate }} @ {{ investmentData.uploadTime }}</div>
                          </div>
                        </div>
                        <div class="investment-card-detail">
                          <div class="card-info-details">
                            <small>Capital</small>
                            <div>₦{{FormatDigit(investmentData.capital)}}</div>
                          </div>
                          <div class="card-info-details">
                            <small>ROI</small>
                            <div>₦{{FormatDigit(investmentData.roi)}}</div>
                          </div>
                        </div>
                        <a
                          :href="getHttProofImage(investmentData.userid, investmentData.proofOfPayment)"
                          class="investment-proof"
                        >
                          <img
                            v-bind:src="getHttProofImage(investmentData.userid, investmentData.proofOfPayment)"
                            alt
                          />
                        </a>
                      </div>
                    </div>
                  </div>

                  <div v-else>
                    <div class="alert-info">{{ payoutResponseData }}</div>
                  </div>
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
import Datepicker from "vuejs-datepicker";
import moment from "moment";
import Logic from "../modules/Logic";
import { type } from "os";
export default {
  extends: Logic,
  components: {
    Navigation,
    Datepicker
  },
  data() {
    return {
      activeInvestmentTab: "",
      historyTab: "",

      token: "",

      activeInvestmentPropertyMessage: "",
      activeInvestmentPropertyStatus: "",

      historyInvestmentPropertyMessage: "",
      historyInvestmentPropertyStatus: "",
      selectedDate: "",
      // this object property holds the state of data on each tab
      payoutDataState: 0,
      payoutResponseData: "",

      investmentDataState: 0,
      InvestmentResponseData: "",

      // this is the loader for each action(payout & investment)
      payoutLoader: 0,
      investmentLoader: 0,

      // this represents the response status for each action
      investmentResponseStatus: false,
      payoutResponseStatus: false,

      // this is are the properties that holds data like total capital and roi for investment
      totalCapital: "",
      totalRoi: "",
      totalCashOut: "",
      resultCount: "",
      humanDate: "",

      // this is are the properties that holds data like total capital and roi for payout
      payoutTotalCapital: "",
      payoutTotalRoi: "",
      payoutTotalCashOut: "",
      payoutResultCount: "",
      payoutHumanDate: ""
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
    investmentPerDate() {
      this.investmentLoader = 1;
      this.investmentDataState = 0;
      setTimeout(() => {
        this.customFormatter(this.selectedDate);
        this.investmentFnc();
      }, 1000);
    },
    investmentFnc() {
      this.investmentLoader = !this.investmentLoader;
      let data = JSON.stringify({
        jwt: this.token,
        dataDate: this.selectedDate,
        type: "investment"
      });

      let apiLink =
        "https://api.digitalearners.cc/action/investment/dailyInvestmentData.php";
      this.makePostRequest(data, apiLink, "investmentAccountCallBack");
    },
    payoutFnc() {
      this.payoutLoader = !this.payoutLoader;
      let data = JSON.stringify({
        jwt: this.token,
        dataDate: this.selectedDate,
        type: "payout"
      });
      let apiLink =
        "https://api.digitalearners.cc/action/investment/dailyInvestmentData.php";
      this.makePostRequest(data, apiLink, "payoutAccountCallBack");
    },
    investmentAccountCallBack(response) {
      this.investmentLoader = 0;
      this.investmentDataState = 1;
      if (response.status == "false") {
        this.investmentResponseStatus = false;
        this.InvestmentResponseData = response.message;
      } else if (response.status == "true") {
        this.investmentResponseStatus = true;
        this.InvestmentResponseData = response.records;
        this.totalCapital = response.totalCapital.toString();
        this.totalRoi = response.totalRoi.toString();
        this.totalCashOut = response.cashTotal.toString();
        this.resultCount = response.recordCount.toString();
        this.humanDate = response.humanDate.toString();
      }
    },
    payoutAccountCallBack(response) {
      this.payoutLoader = 0;
      this.payoutDataState = 1;
      if (response.status == "false") {
        this.payoutResponseStatus = false;
        this.payoutResponseData = response.message;
      } else if (response.status == "true") {
        this.payoutResponseStatus = true;
        this.payoutResponseData = response.records;
        this.payoutTotalCapital = response.totalCapital.toString();
        this.payoutTotalRoi = response.totalRoi.toString();
        this.payoutTotalCashOut = response.cashTotal.toString();
        this.payoutresultCount = response.recordCount.toString();
        this.payoutHumanDate = response.humanDate.toString();
      }
    },
    payoutPerDate() {
      setTimeout(() => {
        this.customFormatter(this.selectedDate);
        this.payoutFnc();
      }, 1000);
    },
    customFormatter(date) {
      this.selectedDate = moment(date).format("MM D YYYY");
    }
  },
  created() {
    this.activeInvestmentTab = 1;
    this.token = this.authenticateUser();
  }
};
</script>
