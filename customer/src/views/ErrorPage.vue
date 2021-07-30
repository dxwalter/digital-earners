<template>
  <div id="app-layout">
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body move-to-center">
        <!-- Profile content goes in here -->

        <div class="profile-container bg-white login-container-shadow">
          <h1 class="error-page-text">
            <span>4</span>
            <span>0</span>
            <span>4</span>
          </h1>

          <div class="profile-name dark-grey">Oops! Page not found</div>

          <div class="edit-btn-container">
            <router-link
              to="/Dashboard"
              class="btn-md orange-btn white-color blue-bg"
            >Visit Dashboard</router-link>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>
<script>
import Navigation from "./Layout/CustomerPageLayout.vue";
export default {
  components: {
    Navigation
  }
};
</script>