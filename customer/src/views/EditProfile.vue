<template>
  <div id="app-layout">
    <Navigation></Navigation>

    <aside class="main-content-aside">
      <div class="main-content-body change-edit-layout manage-edit-profile-actions">
        <!-- Profile content goes in here -->

        <div class="edit-profile-actions login-container-shadow bg-white">
          <div class="avatar-name-container">
            <div class="edit-profile-avatar">
              <img v-bind:src="getHttDPImage(userId, EditprofilePicture)" alt />
            </div>

            <div class="user-realname-action">
              <div class="dark-grey-light">Edit profile details</div>
              <div>{{ fullname }}</div>
            </div>
          </div>

          <!-- Swipe menu -->
          <div class="tab-menu-list">
            <a
              href="javascript:void(0)"
              class="edit-tab-action"
              v-bind:class="{'active' : !tabSwitch}"
              v-on:click="switchTab"
            >
              <img src="@/assets/img/profile.svg" />
              <span class="dark-grey">Basic details</span>
            </a>

            <a
              href="javascript:void(0)"
              class="edit-tab-action"
              v-bind:class="{'active' : tabSwitch}"
              v-on:click="switchTab"
            >
              <img src="@/assets/img/security.png" />
              <span class="dark-grey">Security details</span>
            </a>
          </div>
        </div>

        <div
          class="edit-tab-form-action login-container-shadow bg-white add-padding animated fadeIn"
          v-bind:class="{'display-none' : tabSwitch}"
        >
          <form @submit="updateBasicProfile">
            <div class="form-container">
              <div class="form-label dark-grey-light">Fullname</div>
              <input type="text" class="form-input form-bg dark-grey" v-model="fullname" />
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">Email</div>
              <input type="email" class="form-input form-bg dark-grey" v-model="email" />
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">Phone number</div>
              <input type="text" class="form-input form-bg dark-grey" v-model="phoneNumber" />
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">
                Upload Picture --
                <small>
                  <i>Optional</i>
                </small>
              </div>
              <input
                type="file"
                class="form-input form-bg dark-grey"
                accept="image"
                ref="imageFile"
                @change="imagePreview"
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
            </div>

            <div class="form-container">
              <button
                type="submit"
                class="btn-element-two white-color blue-bg"
                :disabled="!isDisabled"
              >Update basic details</button>
            </div>
          </form>
        </div>

        <div
          class="edit-tab-form-action login-container-shadow bg-white add-padding animated fadeIn"
          v-bind:class="{'display-none' : !tabSwitch}"
        >
          <form>
            <div class="form-container">
              <div class="form-label dark-grey-light">
                New password
                <br />
                <small>
                  <i>Six or more characters</i>
                </small>
              </div>
              <input type="password" class="form-input form-bg dark-grey" v-model="newPassword" />
            </div>

            <div class="form-container">
              <div class="form-label dark-grey-light">Confirm new password</div>
              <input type="password" class="form-input form-bg dark-grey" v-model="confirmPassword" />
            </div>

            <div class="form-container">
              <button
                type="button"
                @click.prevent="changePasswordMethod"
                :disabled="!isDisabled"
                class="btn-element-two white-color blue-bg"
              >Update password</button>
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
export default {
  extends: Logic,
  name: "EditProfile",
  components: {
    Navigation
  },
  data() {
    return {
      userId: "",
      email: "",
      fullname: "",
      phoneNumber: "",
      EditprofilePicture: "",
      token: "",
      tabSwitch: "", // 1 == basic details, 0 == security details

      previewImage: null,

      isDisabled: true,

      // change password
      newPassword: "",
      confirmPassword: "",

      // update basic profile
      fullname: "",
      email: "",
      phoneNumber: "",
      uploadProfilePicture: null
    };
  },
  methods: {
    imagePreview(e) {
      const file = e.target.files[0];
      this.previewImage = URL.createObjectURL(file);
      const uploadFile = e.target.files[0];
      this.uploadProfilePicture = uploadFile;
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
    customerDetailsCallback(response) {
      if (response.status == true) {
        this.email = response.email;
        this.phoneNumber = response.phone_number;
        this.EditprofilePicture = response.profile_picture;
        if (response.fullname == "") {
          this.fullname = "No name yet";
        } else {
          this.fullname = response.fullname;
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
          this.$router.push({ name: "Dashboard" });
        }, 3000);
      }
    },
    switchTab() {
      this.tabSwitch = !this.tabSwitch;
    },
    changePasswordMethod() {
      if (this.newPassword.length < 6) {
        this.$toast.info({
          title: "Error",
          message:
            "Your new password is needed and it must not be less than six characters"
        });
        return;
      }

      if (this.newPassword !== this.confirmPassword) {
        this.$toast.info({
          title: "Error",
          message: "Your new password and confirm password does not match"
        });
        return;
      }

      this.isDisabled = !this.isDisabled;

      let data = JSON.stringify({
        jwt: this.token,
        passwordString: this.newPassword
      });

      let apiLink =
        "https://api.digitalearners.cc/action/customer/changePassword.php";

      this.makePostRequest(data, apiLink, "changePasswordCallback");
    },
    changePasswordCallback(response) {
      if (response.status == "false") {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
      } else {
        this.$toast.success({
          title: "Succcess",
          message: response.message
        });
      }

      this.isDisabled = !this.isDisabled;
    },
    updateBasicProfile(e) {
      e.preventDefault();

      if (this.fullname.length > 2) {
        if (this.fullname == "No name yet") {
          this.$toast.info({
            title: "Error",
            message: "Enter a valid name"
          });
          return;
        }
      } else {
        this.$toast.info({
          title: "Error",
          message: "Enter a valid name"
        });
        return;
      }

      // email address
      if (this.email.length < 3) {
        this.$toast.info({
          title: "Error",
          message: "Enter a valid email address"
        });
        return;
      }

      // phone number
      if (this.phoneNumber.length < 11) {
        this.$toast.info({
          title: "Error",
          message: "Enter a valid phone number"
        });
        return;
      }

      let formData = new FormData();
      // dp
      if (this.uploadProfilePicture != null) {
        formData.append("image", this.uploadProfilePicture);
      } else {
        formData.append("image", "");
      }

      let profileData = JSON.stringify({
        fullName: this.fullname,
        email: this.email,
        phone: this.phoneNumber,
        jwt: this.token
      });

      formData.append("data", profileData);

      this.isDisabled = !this.isDisabled;

      let apiLink =
        "https://api.digitalearners.cc/action/customer/updateBasicProfile.php";
      this.uploadFile(formData, apiLink, "updateBasicProfileCallBack");
    },
    updateBasicProfileCallBack(response) {
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
      }

      this.isDisabled = !this.isDisabled;
    }
  },
  created() {
    this.token = this.authenticateUser();
    this.getCustomerData();
  }
};
</script>



