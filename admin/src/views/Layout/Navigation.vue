<template>
  <div>
    <div class="right-top-nav login-container-shadow bg-white">
      <!-- top nav -->
      <div class="right-side-nav">
        <a href="#" class="notif-area-2" v-on:click="navToggleControl">
          <img src="@/assets/img/nav-toggle.svg" />
          <div class="notif-label-2" v-if="totalLabel">{{ totalLabel }}</div>
        </a>
      </div>

      <div class="top-side-control">
        <router-link to="/CreateInvestor" class="btn-md orange-btn orange-bg">Create Investor</router-link>
      </div>
    </div>

    <div class="structure-container">
      <aside
        class="side-nav-aside bg-white login-container-shadow animated"
        v-bind:class="{ 'display-block': navToggle, 'fadeIn':navToggle}"
      >
        <div class="nav-container">
          <!-- Side bar -->
          <div class="blue-bg side-profile-top-div j-flex-end">
            <a href="#" v-on:click="navToggleControl">
              <img src="@/assets/img/close-times.svg" alt />
            </a>
          </div>

          <router-link to="/Dashboard" class="nav-link">
            <img src="@/assets/img/dashboard.svg" alt />
            <span>Dashboard</span>
          </router-link>

          <router-link to="/PayInvestors" class="nav-link">
            <img src="@/assets/img/orange-bell.svg" alt />
            <span>Pay Investors</span>
            <div class="notif-label-nav" v-if="investorsToPay">{{ investorsToPay }}</div>
          </router-link>

          <router-link to="/ConfirmInvestments" class="nav-link">
            <img src="@/assets/img/orange-bell.svg" alt />
            <span>New Investments</span>
            <div class="notif-label-nav" v-if="newInvestments">{{ newInvestments }}</div>
          </router-link>

          <router-link to="/Investors" class="nav-link">
            <img src="@/assets/img/investors.svg" alt />
            <span>Investors</span>
          </router-link>

          <router-link to="/AccountOptions" class="nav-link">
            <img src="@/assets/img/accounting.svg" alt />
            <span>Accounting</span>
          </router-link>

          <a href="#" class="nav-link" v-on:click="logOutUser()">
            <img src="@/assets/img/logout.svg" alt />
            <span>Logout</span>
          </a>
        </div>
      </aside>

      <!-- <router-view></router-view> -->
    </div>
  </div>
</template>
<script>
import Logic from "../../modules/Logic";

export default {
  name: "Navigation",
  extends: Logic,
  data() {
    return {
      token: "",
      investorsToPay: "",
      newInvestments: "",
      totalLabel: "",
      navToggle: 0,
      randVal: ""
    };
  },
  methods: {
    getNotificationCallBack(response) {
      this.investorsToPay = parseInt(response.investorsToPay);
      this.newInvestments = parseInt(response.newInvestments);

      this.totalLabel =
        parseInt(response.newInvestments) + parseInt(response.investorsToPay);
    },
    navToggleControl() {
      if (this.navToggle == 0) {
        this.navToggle = 1;
      } else if (this.navToggle == 1) {
        this.navToggle = 0;
      }
    }
  },
  created: function() {
    this.navToggle = 0;
    let screenWidth = screen.width;

    let changingVar = Math.random() * (10 - 5) + 5;

    this.randVal = screenWidth = changingVar;

    if (screenWidth <= 767) {
      this.navToggle = 0;
    } else {
      this.navToggle = 1;
    }

    let data = JSON.stringify({
      jwt: this.authenticateUser()
    });

    let apiLink =
      "https://api.digitalearners.cc/action/admin/adminNotificationCount.php";
    this.makePostRequest(data, apiLink, "notificationCallback");
  }
};
</script>   