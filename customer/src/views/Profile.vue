<template>
  <div id="app-layout">
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="profile-container bg-white login-container-shadow">
          <img
            v-bind:src="getHttDPImage(userId, profilePicture)"
            alt
            class="profile-lg-dp login-container-shadow"
          />

          <div class="profile-name dark-grey">{{ fullname }}</div>

          <div class="profile-detail">
            <div>
              <img src="@/assets/img/email.svg" alt />
            </div>
            <span>{{ email }}</span>
          </div>

          <div class="profile-detail">
            <div>
              <img src="@/assets/img/phone.svg" alt />
            </div>
            <span>{{ phoneNumber }}</span>
          </div>

          <div class="edit-btn-container">
            <router-link
              to="/EditProfile"
              class="btn-md orange-btn white-color blue-bg"
            >Edit profile</router-link>
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
  name: "Profile",
  components: {
    Navigation
  },
  data() {
    return {
      userId: "",
      email: "",
      fullname: "",
      phoneNumber: "",
      profilePicture: "",
      token: ""
    };
  },
  methods: {
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
        this.profilePicture = response.profile_picture;
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
    }
  },
  created() {
    this.token = this.authenticateUser();
    this.getCustomerData();
  }
};
</script>

