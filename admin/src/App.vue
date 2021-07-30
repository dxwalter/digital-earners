<template>
  <div id="app">
    <transition
      name="router-animation"
      enter-active-class="animated fadeIn fast"
      leave-active-class="animated fadeOut faster"
    >
      <keep-alive
        :include="['CreateInvestor', 'CreateContoller', 'Login']"
        :exclude="['Navigation', 'Dashboard', 'PayInvestors', 'ConfirmInvestments', 'Investors', 'InvestorProfile']"
        :max="0"
      >
        <router-view></router-view>
      </keep-alive>
    </transition>
  </div>
</template>

<script>
// Every component is imported into the app.vue
export default {
  name: "app"
};
</script>


<style>
@import "./assets/style/animate.css";
@import "./assets/style/app.css";
</style>