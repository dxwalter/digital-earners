<template>
  <div>
    <Navigation></Navigation>
    <aside class="main-content-aside">
      <div class="main-content-body">
        <!-- content body -->

        <CustomerSearch></CustomerSearch>

        <div class="history-table-container mg-top-8">
          <div v-if="listingCount > 0">
            <div
              class="login-container-shadow bg-white card-design"
              v-for="investor in customerListing"
              :key="investor.id"
            >
              <div class="card-upper-layer">
                <img
                  v-bind:src="getHttDPImage(investor.userId, investor.profilePicture)"
                  alt
                  class="profile-card-img"
                />
                <div class="investor-card-details">
                  <router-link
                    v-if="investor.fullname"
                    :to="`InvestorProfile/${investor.userId}`"
                    class="display-block mg-bottom-16"
                  >{{ investor.fullname }}</router-link>

                  <router-link
                    v-else
                    :to="`InvestorProfile/${investor.userId}`"
                    class="display-block mg-bottom-16"
                  >No name yet</router-link>

                  <router-link
                    :to="`InvestorProfile/${investor.userId}`"
                    class="confirmed-green white-color btn-md"
                  >Visit profile</router-link>
                </div>
              </div>
            </div>
          </div>
          <div v-else>
            <div class="alert-info">{{message}}</div>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>

<script>
import Navigation from "@/views/Layout/Navigation";
import CustomerSearch from "@/views/Layout/Customer/CustomerSearch";
import Logic from "@/modules/Logic";
export default {
  extends: Logic,
  components: {
    Navigation,
    CustomerSearch
  },
  data() {
    return {
      customerListing: "",
      listingCount: "",
      message: ""
    };
  },
  methods: {
    callInvestorsList(response) {
      if (response.status == "true") {
        this.listingCount = response.recordCount;
        this.customerListing = response.records;
      } else if (response.status == "false") {
        this.listingCount = 0;
        this.message = response.message;
      }
    }
  },
  created() {
    let data = JSON.stringify({
      jwt: this.authenticateUser()
    });
    let apiLink =
      "https://api.digitalearners.cc/action/customer/listOfInvestors.php";
    this.makePostRequest(data, apiLink, "callInvestorsList");
  }
};
</script>
