<template>
  <div>
    <Navigation :key="componentKey"></Navigation>

    <aside class="main-content-aside">
      <div class="main-content-body">
        <!-- content body -->

        <DashboardCard></DashboardCard>

        <CustomersToPay></CustomersToPay>
      </div>
    </aside>
  </div>
</template>

<script>
import Navigation from "@/views/Layout/Navigation";
import DashboardCard from "@/views/Layout/Dashboard/DashboardCard";
import CustomersToPay from "@/views/Layout/Dashboard/CustomersToPay";
import Logic from "@/modules/Logic";

export default {
  extends: Logic,
  components: {
    Navigation,
    DashboardCard,
    CustomersToPay
  },
  data () {
    return {
      componentKey: 0,
    }
  },
  methods: {
    forceRerender() {
      this.componentKey += 1;  
    }
  },
  created() {
    this.authenticateUser();
    this.forceRerender();
  }
};
</script>
