<template>
  <div>
    <div class="right-top-nav login-container-shadow bg-white">
      <!-- top nav -->
      <div class="right-side-nav">
        <a href="#" v-on:click="navToggleControl">
          <img src="@/assets/img/nav-toggle.svg" />
        </a>

        <router-link to="/Notification" class="notif-area">
          <img src="@/assets/img/bell.svg" alt />
          <div class="notif-label" v-if="unreadNotificationCount">{{unreadNotificationCount}}</div>
        </router-link>
      </div>

      <div class="top-side-control">
        <router-link to="/UploadProof" class="btn-md orange-btn orange-bg">Upload proof</router-link>
        <router-link to="/Profile" class="nav-profile login-container-shadow">
          <img v-bind:src="getHttDPImage(userId, NavprofilePicture)" alt />
        </router-link>
      </div>
    </div>

    <div class="structure-container">
      <aside
        class="side-nav-aside bg-white login-container-shadow animated"
        v-bind:class="{ 'display-block': navToggle, 'fadeIn':navToggle}"
      >
        <div class="nav-container">
          <!-- Side bar -->
          <div class="blue-bg side-profile-top-div">
            <div class="side-nav-profile-container"></div>

            <a href="#" v-on:click="navToggleControl">
              <img src="@/assets/img/close-times.svg" alt />
            </a>
          </div>

          <router-link to="/Dashboard" class="nav-link">
            <img src="@/assets/img/dashboard.svg" alt />
            <span>Dashboard</span>
          </router-link>

          <router-link to="/Notification" class="nav-link">
            <img src="@/assets/img/orange-bell.svg" alt />
            <span>Notification</span>
            <div
              class="notif-label-nav"
              v-if="unreadNotificationCount"
            >{{ unreadNotificationCount }}</div>
          </router-link>

          <router-link to="/History" class="nav-link">
            <img src="@/assets/img/account.svg" alt />
            <span>History</span>
          </router-link>

          <router-link to="/Profile" class="nav-link">
            <img src="@/assets/img/profile.svg" alt />
            <span>Profile</span>
          </router-link>

          <a href="#" class="nav-link" v-on:click="logOutUser()">
            <img src="@/assets/img/logout.svg" alt />
            <span>Logout</span>
          </a>
        </div>
      </aside>
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import Logic from "@/modules/Logic";
export default {
  name: "Navigation",
  extends: Logic,
  data() {
    return {
      navToggle: 0,
      randVal: "",
      fullname: "",
      NavprofilePicture: "",
      unreadNotificationCount: ""
    };
  },
  methods: {
    navToggleControl() {
      if (this.navToggle == 0) {
        this.navToggle = 1;
      } else if (this.navToggle == 1) {
        this.navToggle = 0;
      }
    },
    getUserData() {
      if (this.userId) {
        let data = JSON.stringify({
          customer: this.userId,
          jwt: this.authenticateUser()
        });

        let apiLink =
          "https://api.digitalearners.cc/action/customer/getCustomerData.php";
        this.makePostRequest(data, apiLink, "getUserDataResponse");
      } else {
        setTimeout(() => {
          this.$router.push({ name: "Login" });
        }, 3000);
        return;
      }
    },
    getUserDataResponse(response) {
      this.NavprofilePicture = response.profile_picture;
    },
    notificationCount() {
      let data = JSON.stringify({
        notificationId: 1,
        type: "count",
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/notification/notificationManager.php";
      this.makePostRequest(data, apiLink, "notificationCountCallback");
    },
    notificationCountCallback(response) {
      this.unreadNotificationCount = parseInt(response.unreadNotificationCount);
    }
  },
  created() {
    this.navToggle = 0;
    let screenWidth = screen.width;
    let changingVar = Math.random() * (10 - 5) + 5;

    this.randVal = screenWidth = changingVar;

    if (screenWidth <= 767) {
      this.navToggle = 0;
    } else {
      this.navToggle = 1;
    }
    this.authenticateUser();
    this.getUserData();
    this.notificationCount();

    // console.log(this.$parent.$parent);
  }
};
</script>   