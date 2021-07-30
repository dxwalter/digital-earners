<template>
  <div id="app-layout">
    <Navigation></Navigation>

    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="edit-tab-form-action login-container-shadow bg-white add-padding width-manager">
          <div class="upload-proof-header">Upload proof of payment</div>

          <form>
            <div class="form-container">
              <div class="form-label dark-grey-light">Amount invested</div>
              <input
                type="number"
                class="form-input form-bg dark-grey"
                v-model="capital"
                v-on:keyup="showRoi"
              />
              <small v-if="roi" class="animated" v-bind:class="{'fadeIn':roi, 'fadeOut' : !roi}">
                Your expected
                <b>ROI</b> is
                <b>₦{{ FormatDigit(roi.toString()) }}</b>
              </small>
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">Select picture of proof</div>
              <input
                type="file"
                class="form-input form-bg dark-grey"
                @change="imagePreview"
                accept="image"
                ref="imageFile"
              />
            </div>

            <div class="form-container" v-if="previewImage">
              <div class="form-label dark-grey-light">upload preview</div>
              <div class="uploaded-proof login-container-shadow upload-proof-manager">
                <img :src="previewImage" alt />
              </div>
            </div>

            <div
              class="form-container animated"
              v-bind:class="{'display-block fadeIn': imageUploadProgress, 'display-none': !imageUploadProgress}"
            >
              <div class="uploadProgressContainer">
                <div
                  class="w3-container w3-blue"
                  v-bind:style="`width:${imageUploadProgress}%`"
                >{{imageUploadProgress}}%</div>
              </div>
              <div id="responseDiv"></div>
            </div>

            <div class="form-container">
              <button
                type="button"
                class="btn-element-two white-color blue-bg"
                v-on:click="uploadProof"
                :disabled="!isDisabled"
              >Upload proof</button>
            </div>
          </form>
        </div>
      </div>
    </aside>
  </div>
</template>

<script>
import Navigation from "./Layout/CustomerPageLayout.vue";
import Logic from "@/modules/Logic";
import { type } from "os";
export default {
  extends: Logic,
  components: {
    Navigation
  },
  data() {
    return {
      previewImage: null,
      capital: "",
      roi: "",
      imageFile: null,
      isDisabled: true
    };
  },
  methods: {
    imagePreview(e) {
      const file = e.target.files[0];
      this.previewImage = URL.createObjectURL(file);
      const uploadFile = e.target.files[0];
      this.imageFile = uploadFile;
    },
    showRoi() {
      if (this.capital >= 10000) {
        let roiCalc = this.capital * 0.3;

        if (Number.isInteger(this.capital / 10000) == false) {
          this.$toast.info({
            title: "Error",
            message: "Your capital must be in round figure"
          });
          return;
        } else {
          this.roi = roiCalc;
        }
      } else {
        this.roi = 0;
      }
    },
    uploadProof(e) {
      e.preventDefault();

      // check if the capital is a string or number
      // check if an image with an image type of
      // either jpg/jpeg/png was uploaded

      if (this.capital.length < 5) {
        this.$toast.info({
          title: "Error",
          message: "The capital you are investing must not be less than ₦10,000"
        });
        return;
      }

      let regex = /^\d+$/;
      if (regex.test(this.capital) == false) {
        this.$toast.info({
          title: "Error",
          message: "Your capital must be in figures"
        });
        return;
      }

      if (this.imageFile == null) {
        this.$toast.info({
          title: "Error",
          message: "Select your proof of payment"
        });
        return;
      }

      // check image type
      let fileType = this.imageFile.type
        .replace("image/", "")
        .toLowerCase()
        .toString();

      if (fileType == "jpeg" || fileType == "png" || fileType == "jpg") {
      } else {
        this.$toast.info({
          title: "Error",
          message: "Your proof of statement must be a jpeg/png/jpg file format"
        });
        return;
      }

      // disable button
      this.isDisabled = !this.isDisabled;

      let formData = new FormData();

      let investmentData = JSON.stringify({
        capital: this.capital,
        jwt: this.token
      });

      formData.append("image", this.imageFile);
      formData.append("investmentData", investmentData);

      let apiLink =
        "https://api.digitalearners.cc/action/investment/createInvestment.php";
      this.uploadFile(formData, apiLink, "uploadProofOfPaymentCallback");
    },
    uploadProofOfPaymentCallback(response) {
      if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
      } else {
        this.$toast.success({
          title: "Success",
          message: response.message
        });
        let pageMessage = `<div class="alert-success animated fadeIn">Your investment of  ₦${this.FormatDigit(
          this.capital
        )} has been created successfully</div>`;
        let divId = `responseDiv`;
        document.getElementById(divId).innerHTML = "";
        document.getElementById(divId).innerHTML = pageMessage;
      }

      this.isDisabled = !this.isDisabled;
    }
  },
  created() {
    this.authenticateUser();
  }
};
</script>

