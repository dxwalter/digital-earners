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
      userId: "",
      token: "",
      firstTimeAccess: "",
      imageUploadProgress: "",
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
      document.cookie = "userId= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
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
          if (callBackMethod == "getUserDataResponse") {
            this.getUserDataResponse(response.data);
          }

          if (callBackMethod == "getLoginCallBack") {
            this.getLoginCallBack(response.data);
          }

          if (callBackMethod == "customerDetailsCallback") {
            this.customerDetailsCallback(response.data);
          }

          if (callBackMethod == "getDashboardInvestmentDataCallback") {
            this.getDashboardInvestmentDataCallback(response.data);
          }

          if (callBackMethod == "getBDinvestmentHistory") {
            this.getBDinvestmentHistory(response.data);
          }

          if (callBackMethod == "allInvestmentHistoryCallBack") {
            this.allInvestmentHistoryCallBack(response.data);
          }

          if (callBackMethod == "investmentDetails") {
            this.investmentDetails(response.data);
          }

          if (callBackMethod == "notificationListing") {
            this.notificationListing(response.data);
          }

          if (callBackMethod == "notificationCountCallback") {
            this.notificationCountCallback(response.data);
          }

          if (callBackMethod == "changePasswordCallback") {
            this.changePasswordCallback(response.data);
          }

          if (callBackMethod == "recoverPasswordCallBack") {
            this.recoverPasswordCallBack(response.data);
          }

          if (callBackMethod == "newPasswordNoJwt") {
            this.newPasswordNoJwt(response.data);
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
      this.userId = VueCookies.get("userId");
      if (auth === null) {
        this.$router.push({ name: "Login" });
        return;
      }
      this.token = auth;
      return auth;
    },
    rollBackToOldPage() {
      let lastLink = VueCookies.get("lastUrl");
      document.cookie = "lastUrl= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
      window.location.href = lastLink;
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
    },
    setJWTCookey(jwt) {
      VueCookies.set("userToken", jwt);
    },
    setUserIdCookey(id) {
      VueCookies.set("userId", id);
    },
    setUrlCookey(url) {
      VueCookies.set("lastUrl", url);
    },
    uploadFile(formData, apiLink, callBackMethod) {
      axios.defaults.headers = {
        "Content-Type": "application/x-www-form-urlencoded",
        "Access-Control-Request-Method": "GET,PUT,POST,DELETE,PATCH,OPTIONS",
        "Access-Control-Request-Headers": "origin, x-requested-with"
      };
      NProgress.start();
      axios
        .post(apiLink, formData, {
          onUploadProgress: uploadEvent => {
            this.imageUploadProgress = Math.round(
              (uploadEvent.loaded / uploadEvent.total) * 100
            );
          }
        })
        .then(response => {
          if (callBackMethod == "uploadProofOfPaymentCallback") {
            this.uploadProofOfPaymentCallback(response.data);
          }
          if (callBackMethod == "updateBasicProfileCallBack") {
            this.updateBasicProfileCallBack(response.data);
          }
          this.imageUploadProgress = "";
          NProgress.done();
        })
        .catch(err => {
          this.$toast.info({
            title: "Error",
            message: "A network error occured. Refresh page and try again"
          });
          this.isDisabled = !this.isDisabled;
          this.imageUploadProgress = "";
          NProgress.done();
          return;
        });
    }
  }
};
