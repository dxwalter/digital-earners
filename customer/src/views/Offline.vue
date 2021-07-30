<template>
  <div id="app-layout">
    <div class="main-container">
      <div class="offline-icon-box">
        <svg xmlns="http://www.w3.org/2000/svg" width="69" height="42" viewBox="0 0 69 42">
          <g fill="#FF7F7F" fill-rule="evenodd">
            <path
              d="M33.956 32.989a7.538 7.538 0 0 0-2.922-.597 7.54 7.54 0 0 0-5.366 2.248 1.959 1.959 0 0 0 .023 2.77l3.97 3.92c.76.748 1.982.751 2.746 0l3.988-3.901a1.958 1.958 0 0 0 .023-2.77 7.513 7.513 0 0 0-2.462-1.67z"
            />
            <path
              d="M38.596 24.901a16.83 16.83 0 0 0-7.562-1.799c-2.688 0-5.305.65-7.653 1.85a16.822 16.822 0 0 0-5.982 5.117 1.962 1.962 0 0 0 3.177 2.301 12.909 12.909 0 0 1 4.584-3.935 12.896 12.896 0 0 1 5.874-1.405c2.049 0 4.03.48 5.798 1.37a12.91 12.91 0 0 1 4.564 3.833 1.959 1.959 0 0 0 3.146-2.331 16.81 16.81 0 0 0-5.946-5z"
            />
            <path
              d="M42.794 14.707A27.818 27.818 0 0 0 31.035 12.1c-4.12 0-8.145.92-11.806 2.637A27.824 27.824 0 0 0 9.65 22.13a1.956 1.956 0 0 0 3.006 2.5 23.896 23.896 0 0 1 8.228-6.35 23.911 23.911 0 0 1 10.15-2.253c3.534 0 6.976.781 10.102 2.238a23.889 23.889 0 0 1 8.208 6.293 1.96 1.96 0 0 0 3.007-2.517 27.842 27.842 0 0 0-9.557-7.335z"
            />
            <path
              d="M31.034 4.403a35.525 35.525 0 0 1 14.907 3.274c.652.302 1.293.628 1.926.969a11.57 11.57 0 0 1 .789-3.997c-.358-.178-.712-.364-1.075-.532A39.447 39.447 0 0 0 31.034.477 39.447 39.447 0 0 0 14.642 4.05 39.447 39.447 0 0 0 1.21 14.105a1.958 1.958 0 0 0 2.962 2.562A35.514 35.514 0 0 1 16.268 7.61a35.517 35.517 0 0 1 14.766-3.206z"
            />
            <path
              fill-rule="nonzero"
              d="M59.507.107c-4.83 0-8.76 3.93-8.76 8.76s3.93 8.759 8.76 8.759 8.759-3.93 8.759-8.76-3.93-8.759-8.76-8.759zm4.4 11.492a1.082 1.082 0 0 1-1.53 1.53l-2.83-2.829-2.828 2.83a1.08 1.08 0 0 1-1.53 0 1.082 1.082 0 0 1-.001-1.53l2.828-2.83-2.828-2.829a1.082 1.082 0 0 1 1.53-1.53l2.829 2.83 2.829-2.83a1.082 1.082 0 0 1 1.53 1.53l-2.83 2.83 2.831 2.829z"
            />
          </g>
        </svg>
      </div>
      <h3>No Internet connection.</h3>
      <p class="feedback-text">Make sure Wi-Fi or cellular data is turned on, then try again.</p>
      <button class="retry-button" @click="rollBackPage">Retry</button>
    </div>
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
  methods: {
    rollBackPage() {
      this.rollBackToOldPage();
    }
  }
};
</script>
<style scoped>
* {
  margin: 0;
  padding: 0;
  text-align: center;
}

body {
  text-align: center;
  background-color: #fff;
  font-family: "Helvetica Neue", -apple-system, BlinkMacSystemFont, "Segoe UI",
    "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans",
    sans-serif;
  font-size: 16px;
}

.navbar {
  padding: 15px 0;
  background-color: #f5f5f5;
}

.main-container {
  background-color: #fff;
  padding: 20px;
  border-radius: 3px;
}

.offline-icon-box {
  background-color: #ffe1e1;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto 20px;
}

.feedback-text {
  max-width: 300px;
  font-size: 1rem;
  font-weight: 300;
  line-height: 1.5;
  margin: 0 auto;
}

.retry-button {
  border: 0;
  border-radius: 3px;
  height: 40px;
  background-color: #33b27b;
  color: #fff;
  width: 200px;
  font-size: 15px;
  font-weight: 600;
  margin: 30px 0;
  cursor: pointer;
  margin: 30px auto;
}

@media (min-width: 500px) {
  body {
    background-color: #f5f5f5;
  }

  .main-container {
    padding: 50px;
    display: flex;
    flex-direction: column;
    border-radius: 3px;
    margin-top: 50px;
  }
}
</style>