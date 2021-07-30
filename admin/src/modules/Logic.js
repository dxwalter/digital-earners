import Vue from "vue";
import VueCookies from "vue-cookie";
import axios from "axios";
import { stat, link } from "fs";
import NProgress from "nprogress";

Vue.use(VueCookies);
Vue.use(NProgress);

export default {
  data() {
    return {
      dpUrl: "https://img.digitalearners.cc/profile_pictures/",
      proofOfPaymentLink: "https://img.digitalearners.cc/proof_of_payment/",
      httpImage: "",
      avatarImg: require("../assets/img/user-circle.svg"),
      avatarInvProof: require("../assets/img/proof-nill.png"),
      isDisabled: true
    };
  },
  methods: {
    getHttDPImage(userId, imageName) {
      let imageLink = this.dpUrl + userId + "/" + imageName;
      if (imageName == null || imageName == "") {
        return this.avatarImg;
      } else {
        return imageLink;
      }
    },
    getHttProofImage(userId, imageName) {
      let imageLink = this.proofOfPaymentLink + userId + "/" + imageName;
      if (imageName == null || imageName == "") {
        return this.avatarInvProof;
      } else {
        return imageLink;
      }
    },
    logOutUser() {
      // VueCookies.remove("userToken");
      document.cookie = "userToken= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
      this.$toast.success({
        title: "Success",
        message: "Logout was successful"
      });
      this.authenticateUser();
    },
    makePostRequest(data, apiLink, callBackMethod) {
      NProgress.start();
      axios.defaults.headers = {
        "Access-Control-Request-Method": "GET,PUT,POST,DELETE,PATCH,OPTIONS",
        "Access-Control-Request-Headers": "origin, x-requested-with"
      };
      axios
        .post(apiLink, data)
        .then(response => {
          if (callBackMethod == "notificationCallback") {
            this.getNotificationCallBack(response.data);
          }

          if (callBackMethod == "loginCallback") {
            this.getLoginCallBack(response.data);
          }

          if (callBackMethod == "DashboardCardCallback") {
            this.getCardCallback(response.data);
          }

          if (callBackMethod == "DBcustomerToPay") {
            this.dashboardCustomersToPay(response.data);
          }

          if (callBackMethod == "newInvestmentListing") {
            this.newInvestmentListing(response.data);
          }

          if (callBackMethod == "newInvestmentAction") {
            let investmentId = JSON.parse(data).investmentId;
            this.newInvestmentAction(response.data, investmentId);
          }

          if (callBackMethod == "payInvestorCallback") {
            let investmentId = JSON.parse(data).investmentId;
            this.payInvestorCallback(response.data, investmentId);
          }

          if (callBackMethod == "createNewInvestor") {
            this.createNewInvestor(response.data);
          }

          if (callBackMethod == "callInvestorsList") {
            this.callInvestorsList(response.data);
          }

          if (callBackMethod == "customerDetailsCallback") {
            this.customerDetailsCallback(response.data);
          }

          if (callBackMethod == "changeCustomerAccessStatus") {
            this.changeCustomerAccessStatus(response.data);
          }

          if (callBackMethod == "getActiveInvestmentCallback") {
            this.getActiveInvestmentCallback(response.data);
          }

          if (callBackMethod == "getInvestmentHistoryCallback") {
            this.getInvestmentHistoryCallback(response.data);
          }

          if (callBackMethod == "customerSearchResultCallBack") {
            this.customerSearchResultCallBack(response.data);
          }

          if (callBackMethod == "investmentDetails") {
            this.investmentDetails(response.data);
          }

          if (callBackMethod == "investorsToPay") {
            this.investorsToPay(response.data);
          }

          if (callBackMethod == "getLoginCallBack") {
            this.getLoginCallBack(response.data);
          }

          if (callBackMethod == "backDateInvestmentCallback") {
            this.backDateInvestmentCallback(response.data);
          }

          if (callBackMethod == "createAdminResponseCallBack") {
            this.createAdminResponseCallBack(response.data);
          }

          if (callBackMethod == "investmentAccountCallBack") {
            this.investmentAccountCallBack(response.data);
          }

          if (callBackMethod == "payoutAccountCallBack") {
            this.payoutAccountCallBack(response.data);
          }

          NProgress.done();
        })
        .catch(err => {
          this.$toast.info({
            title: "Error",
            message: "A network error occured. Refresh page and try again"
          });
          this.isDisabled = !this.isDisabled;
          NProgress.done();
          return;
        });
    },
    authenticateUser() {
      let auth = VueCookies.get("userToken");
      if (auth === null) {
        this.$router.push({ name: "Login" });
        return;
      }
      this.token = auth;
      return auth;
    },
    FormatDigit(amount) {
      let comma = ",";

      if (amount == 0) {
        return 0;
      } else if (amount == null) {
        var amount = ` 0`;
        return amount;
      }

      parseInt(amount);

      if (amount.length < 4) {
        return amount;
      }

      if (amount.length == 4) {
        let output = [amount.slice(0, 1), comma, amount.slice(1)].join("");
        return output;
      }

      if (amount.length == 5) {
        let output = [amount.slice(0, 2), comma, amount.slice(2)].join("");
        return output;
      }

      if (amount.length == 6) {
        let output = [amount.slice(0, 3), comma, amount.slice(3)].join("");
        return output;
      }

      if (amount.length == 7) {
        let output = [amount.slice(0, 1), comma, amount.slice(1)].join("");
        let secondOutput = [output.slice(0, 5), comma, output.slice(5)].join(
          ""
        );
        return secondOutput;
      }

      if (amount.length == 8) {
        let output = [amount.slice(0, 2), comma, amount.slice(2)].join("");
        let secondOutput = [output.slice(0, 6), comma, output.slice(6)].join(
          ""
        );
        return secondOutput;
      }

      if (amount.length == 9) {
        let output = [amount.slice(0, 3), comma, amount.slice(3)].join("");
        let secondOutput = [output.slice(0, 7), comma, output.slice(7)].join(
          ""
        );
        return secondOutput;
      }

      if (amount.length == 10) {
        let output = [amount.slice(0, 1), comma, amount.slice(1)].join("");
        let secondOutput = [output.slice(0, 5), comma, output.slice(5)].join(
          ""
        );
        let thirdOutput = [
          secondOutput.slice(0, 9),
          comma,
          secondOutput.slice(9)
        ].join("");
        return thirdOutput;
      }
    }
  }
};
