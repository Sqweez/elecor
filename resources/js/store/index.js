import Vuex, {Store} from 'vuex';
import Vue from 'vue';
import navbarModule from "./modules/navbar";
import clientsModule from "./modules/clients";
import serviceModule from "./modules/services";
import ACTIONS from "./actions";
import orderModule from "./modules/orders";
import feedbackModule from "./modules/feedback";
import stockModule from "./modules/stocks";
import mobileServiceModule from './modules/mobile_services'
import userModule from "./modules/user";
Vue.use(Vuex);

const store = new Store({
    state: {},
    mutations: {},
    actions: {
        async [ACTIONS.INIT]({dispatch}) {
            await dispatch(ACTIONS.GET_CLIENT_TYPES);
            await dispatch('getUsers');
            await dispatch(ACTIONS.GET_SERVICES);
            await dispatch(ACTIONS.GET_CLIENTS);
        }
    },
    modules: {
        navbarModule: navbarModule,
        clientsModule: clientsModule,
        serviceModule: serviceModule,
        orderModule: orderModule,
        feedbackModule: feedbackModule,
        stocksModule: stockModule,
        mobileServiceModule: mobileServiceModule,
        userModule: userModule
    }
});

export default store;
