import Vue from 'vue';
import VueRouter from "vue-router";
import routes from "./routes";
import ACTIONS from "../store/actions";

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
    // await this.$store.dispatch(ACTIONS.GET_CLIENTS);
    next();
});

export default Router;
