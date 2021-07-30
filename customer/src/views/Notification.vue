<style>
</style>

<template>
  <div id="app-layout">
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body">
        <div class="history-table-container">
          <div class="history-table-header blue-color">
            <div>Notifications</div>
          </div>

          <div class="notif-listing">
            <!-- Notification contents will go in here -->

            <div v-if="notificationCount == 0">
              <div class="alert-info">{{ notificationList }}</div>
            </div>

            <div v-else v-for="notification in notificationList" :key="notification.id">
              <router-link
                :to="`/Investment/${notification.typeId}`"
                class="notification-item"
                v-bind:class="{'is-read': notification.readStatus == '1'}"
              >
                <div class="notif-img"></div>
                <p class="notif-msg">{{notification.message}}</p>
              </router-link>
            </div>
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
  components: {
    Navigation
  },
  data() {
    return {
      notificationCount: "",
      notificationList: ""
    };
  },
  methods: {
    getNotification() {
      let data = JSON.stringify({
        type: "listing",
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/notification/notificationManager.php";
      this.makePostRequest(data, apiLink, "notificationListing");
    },
    notificationListing(response) {
      if (response.status == "false") {
        this.notificationCount = 0;
        this.notificationList = response.message;
      } else if (response.status == "true") {
        this.notificationCount = 1;
        this.notificationList = response.records;
      }
    },
    markNotification() {
      let data = JSON.stringify({
        notificationId: 1,
        type: "mark",
        jwt: this.token
      });

      let apiLink =
        "https://api.digitalearners.cc/action/notification/notificationManager.php";
      this.makePostRequest(data, apiLink, "");
    }
  },
  created() {
    this.authenticateUser();
    this.getNotification();
    this.markNotification();
  }
};
</script>