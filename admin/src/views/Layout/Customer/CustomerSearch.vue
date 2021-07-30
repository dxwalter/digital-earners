<template>
  <div>
    <div class="investors-search-container">
      <h1 class="page-essence dark-grey">List of investors</h1>
      <input
        type="text"
        class="form-input dark-grey bg-white investors-search"
        placeholder="Search..."
        v-model="searchString"
        v-on:keyup="getSearch"
      />
    </div>

    <div
      class="search-result-container animated"
      v-bind:class="{
      fadeIn: searchContainer, 
      'display-none': !searchContainer
      }"
    >
      <div class="search-indicator">
        <small>
          <b>Search Result</b>
        </small>
        <button class="close-search" type="button" v-on:click="closeSearchResult"></button>
      </div>

      <div class="bg-white card-design border-grey mg-top-8">
        <!-- continue working on search UI -->

        <div class="loader" v-if="searchLoader"></div>

        <div v-if="resultCount">
          <div v-for="record in searchResult" :key="record.id">
            <router-link :to="`/InvestorProfile/${record.userId}`" class="search-container">
              <div class="search-img-holder">
                <img v-bind:src="getHttDPImage(record.userId, record.profilePicture)" alt />
              </div>
              <div class="search-name">{{ record.fullname }}</div>
            </router-link>
          </div>
        </div>

        <div v-else>
          <div class="alert-info">{{ searchResult }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { setTimeout } from "timers";
import Logic from "@/modules/Logic";
export default {
  name: "CustomerSearch",
  extends: Logic,
  data() {
    return {
      searchLoader: 0,
      searchString: "",
      searchContainer: "",
      token: "",
      resultCount: "",
      searchResult: ""
    };
  },
  methods: {
    getSearch() {
      if (this.searchString.trim() == "" && this.searchString.length < 1) {
        this.searchContainer = 0;
        this.searchLoader = 0;
      } else {
        this.searchContainer = 1;
        this.searchLoader = 1;

        let data = JSON.stringify({
          searchString: this.searchString,
          jwt: this.token
        });

        let apiLink =
          "https://api.digitalearners.cc/action/customer/customerSearch.php";

        setTimeout(
          this.makePostRequest(data, apiLink, "customerSearchResultCallBack"),
          2000
        );
      }
    },
    customerSearchResultCallBack(response) {
      if (response.status == "true") {
        if (response.recordCount > 0) {
          this.resultCount = response.recordCount;
          this.searchResult = response.records;
        } else {
          this.resultCount = 0;
          this.searchResult = response.message;
        }

        this.searchLoader = 0;
      } else {
        this.$toast.info({
          title: "Error",
          message: response.message
        });
        this.searchLoader = 0;
        this.searchContainer = 0;
      }
    },
    closeSearchResult () {
      this.searchResult = "";
      this.searchContainer = 0;
    }
  },
  created() {
    this.token = this.authenticateUser();
  }
};
</script>

<style>
</style>
