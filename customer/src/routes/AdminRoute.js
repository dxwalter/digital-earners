import Vue from "vue";
import Router from "vue-router";
// Vue.use(Router);

// When not signed in routes
import Login from "@/views/customer/Login.vue";
import ForgotPassword from "@/views/customer/RecoverPassword.vue";

// When signed in routes
import Dashboard from "@/views/customer/Dashboard.vue";
import Notification from "@/views/customer/Notification";
import History from "@/views/customer/History";
import Profile from "@/views/customer/Profile";
import Investment from "@/views/customer/Investment";
import UploadProof from "@/views/customer/UploadProof";
import EditProfile from "@/views/customer/EditProfile";
import ErrorPage from "@/views/customer/ErrorPage";

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
      path: "/ForgotPassword",
      name: "ForgotPassword",
      component: ForgotPassword
    },
    {
      path: "/Dashboard",
      name: "Dashboard",
      component: Dashboard
    },
    {
      path: "/Notification",
      name: "Notification",
      component: Notification
    },
    {
      path: "/History",
      name: "History",
      component: History
    },
    {
      path: "/Profile",
      name: "Profile",
      component: Profile
    },
    {
      path: "/Investment/:id",
      name: "Investment",
      component: Investment
    },
    {
      path: "/UploadProof",
      name: "UploadProof",
      component: UploadProof
    },
    {
      path: "/EditProfile",
      name: "EditProfile",
      component: EditProfile
    },
    {
      // will match everything
      path: "*",
      component: ErrorPage
    }
  ]
});
