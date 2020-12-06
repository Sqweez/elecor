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
import mailingModule from "./modules/mailing";
import referralModule from "./modules/referral";
import frontEndModule from "./modules/frontend";
Vue.use(Vuex);

const store = new Store({
    state: {},
    mutations: {},
    actions: {
        [ACTIONS.INIT]({dispatch}) {
            dispatch('getFields');
            dispatch(ACTIONS.GET_CLIENT_TYPES);
            dispatch('getGenders');
            dispatch('getLanguages');
            dispatch('getUsers');
            dispatch(ACTIONS.GET_SERVICES);
            //dispatch(ACTIONS.GET_CLIENTS);
            dispatch('GET_COMPANIES');
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
        userModule: userModule,
        mailingModule: mailingModule,
        referralModule: referralModule,
        frontEndModule: frontEndModule
    }
});

export default store;
