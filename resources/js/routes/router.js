import Vue from 'vue';
import VueRouter from "vue-router";
import routes from "./routes";
import store from "../store";
import showToast from "../utils/Toast";

Vue.use(VueRouter);

const Router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
    scrollBehavior(to, from, savePos) {
        return {x: 0, y: 0};
    }
});

Router.beforeEach(async (to, from, next) => {
    if (!store.getters.hasToken && to.name !== 'Login') {
        next('/login');
        return;
    }

    if (localStorage.getItem('token') && !store.getters.isLoggedIn) {

        const response = await store.dispatch('login', {token: localStorage.getItem('token')});

        if (response.error) {
            localStorage.removeItem('token');
            showToast(response.error, '', 'error');
            next('/login');
        }
    }

    const user = store.getters.user;

    if (user && to.meta.denied && to.meta.denied.includes(user.role_id)) {
        next('/clients');
        return;
    }

    if (to.name === 'Login' && store.getters.user) {
        next('/clients');
        return;
    }

    next();
});

export default Router;
