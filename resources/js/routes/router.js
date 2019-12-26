import Vue from 'vue';
import VueRouter from "vue-router";
import routes from "./routes";
import store from "../store";

Vue.use(VueRouter);

const Router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
    scrollBehavior(to, from, savePos) {
        return {x: 0, y: 0};
    }
});

Router.beforeEach((to, from, next) => {


    if (!store.getters.hasToken && to.name !== 'Login') {
        next('/login');
        return;
    }
    next();
});

export default Router;
