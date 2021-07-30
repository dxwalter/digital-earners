import Vue from "vue";
import Router from "vue-router";

// When not signed in routes
import Login from "./views/Login.vue";
import ForgotPassword from "./views/RecoverPassword.vue";

// When signed in routes
import Dashboard from "./views/Dashboard.vue";
import Notification from "@/views/Notification";
import History from "@/views/History";
import Profile from "@/views/Profile";
import Investment from "@/views/Investment";
import UploadProof from "@/views/UploadProof";
import EditProfile from "@/views/EditProfile";
import ErrorPage from "@/views/ErrorPage";
import NewPassword from "./views/NewPassword.vue";
import Offline from "./views/Offline.vue";

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
      path: "/NewPassword/:userId",
      name: "NewPassword",
      component: NewPassword
    },
    {
      path: "/Offline",
      name: "Offline",
      component: Offline
    },
    {
      // will match everything
      path: "*",
      component: ErrorPage
    }
  ]
});
