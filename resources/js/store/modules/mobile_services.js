import GETTERS from "../getters";
import MUTATIONS from "../mutations";
import ACTIONS from "../actions";
import {createMobileService, deleteMobileService, editMobileService, getMobileServices} from "../../api/mobile";

const mobileServicesModule = {
    state: {
        service: null,
        services: [],
    },
    getters: {
        getMobileServices(state) {
            return state.services;
        },
        getMobileService: state => id => state.services.find(s => s.id === id),
        getMainMobileServices(state) {
            return state.services.filter(s => !s.main_id);
        }
    },
    mutations: {
        [MUTATIONS.SET_MOBILE_SERVICES] (state, payload) {
            state.services = payload;
        },
        [MUTATIONS.CREATE_MOBILE_SERVICE] (state, payload) {
            state.services.push(payload);
        },
        [MUTATIONS.DELETE_MOBILE_SERVICE] (state, payload) {
            state.services = state.services.filter(s => s.id !== payload);
        },
        [MUTATIONS.EDIT_MOBILE_SERVICE] (state, payload) {
            state.services = state.services.map(s => {
                if (s.id === payload.id) {
                    s = payload;
                }
                return s;
            })
        }
    },
    actions: {
        async [ACTIONS.GET_MOBILE_SERVICES] ({commit}) {
            const services = await getMobileServices();
            commit(MUTATIONS.SET_MOBILE_SERVICES, services);
        },
        async [ACTIONS.CREATE_MOBILE_SERVICE] ({commit}, payload) {
            const newService = await createMobileService(payload);
            commit(MUTATIONS.CREATE_MOBILE_SERVICE, newService);
        },
        async [ACTIONS.DELETE_MOBILE_SERVICE] ({commit}, payload) {
            await deleteMobileService(payload);
            commit(MUTATIONS.DELETE_MOBILE_SERVICE, payload);
        },
        async [ACTIONS.EDIT_MOBILE_SERVICE] ({commit}, payload) {
            await editMobileService(payload);
            commit(MUTATIONS.EDIT_MOBILE_SERVICE, payload);
        }
    }

};

export default mobileServicesModule;
