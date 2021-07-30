import Vue from "vue";
import Router from "vue-router";
import Login from "@/views/Login.vue";
import CreateBoss from "@/views/CreateController.vue";

// import Dashboard from "@/views/Dashboard.vue";
import Dashboard from "@/views/Dashboard";
import PayInvestors from "@/views/PayInvestors";
import ConfirmInvestments from "@/views/ConfirmInvestments";
import Investors from "@/views/Investors";
import InvestorProfile from "@/views/InvestorProfile";
import CreateInvestor from "@/views/CreateInvestor";
import ErrorPage from "@/views/ErrorPage";

import Navigation from "@/views/Layout/Navigation";
import BackdateAccount from "@/views/BackdateAccount";
import InvestmentDetails from "@/views/investmentDetails";

import AccountOptions from "@/views/AccountOptions";
import Offline from "@/views/Offline";

Vue.use(Router);

export default new Router({
  mode: "history",
  base: process.env.BASE_URL,
  routes: [
    {
      path: "/",
      alias: "/Login",
      name: "Login",
      component: Login
    },
    {
      path: "/CreateBoss",
      name: "CreateBoss",
      component: CreateBoss
    },
    {
      path: "/Dashboard",
      name: "Dashboard",
      component: Dashboard
    },
    {
      path: "/PayInvestors",
      name: "PayInvestors",
      component: PayInvestors
    },
    {
      path: "/ConfirmInvestments",
      name: "ConfirmInvestments",
      component: ConfirmInvestments
    },
    {
      path: "/Investors",
      name: "Investors",
      component: Investors
    },
    {
      path: "/AccountOptions",
      name: "AccountOptions",
      component: AccountOptions
    },
    {
      path: "/InvestorProfile/:userId",
      name: "InvestorProfile",
      component: InvestorProfile
    },
    {
      path: "/CreateInvestor",
      name: "CreateInvestor",
      component: CreateInvestor
    },
    {
      path: "/Navigation",
      name: "Navigation",
      component: Navigation
    },
    {
      path: "/BackdateAccount/:userId",
      name: "BackdateAccount",
      component: BackdateAccount
    },
    {
      path: "/investmentDetails/:id/:userId",
      name: "/investmentDetails",
      component: InvestmentDetails
    },
    {
      path: "/Offline",
      name: "/Offline",
      component: Offline
    },
    {
      // will match everything
      path: "*",
      component: ErrorPage
    }
  ]
});
