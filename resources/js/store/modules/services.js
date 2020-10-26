import GETTERS from "../getters";
import MUTATIONS from "../mutations";
import ACTIONS from "../actions";
import {createService, deleteService, getServices, editService} from "../../api/services";
import axios from 'axios';

const ServiceModule = {
    state: {
        services: [],
        companies: [],
    },
    getters: {
        [GETTERS.SERVICES](state) {
            return state.services;
        },
        [GETTERS.TEMP_SERVICES]: state => payload => {
            console.log(payload);
            return state.services.filter(s => s.main_id === payload);
        },
        [GETTERS.NORMAL_SERVICES](state) {
            return state.services.filter(s => !s.main_id);
        },
        [GETTERS.SERVICE](state, payload) {;
            return state.services.filter(s => s.id === payload);
        },
        COMPANIES: state => state.companies,
    },
    mutations: {
        [MUTATIONS.SET_SERVICES] (state, payload) {
            state.services = payload;
        },
        [MUTATIONS.ADD_SERVICE] (state, payload) {
            state.services.push(payload);
        },
        [MUTATIONS.DELETE_SERVICE] (state, payload) {
            state.services = state.services.filter(s => s.id !== payload && s.main_id !== payload);
        },
        [MUTATIONS.EDIT_SERVICE] (state, payload) {
            state.services = state.services.map(s => {
                if (s.id == payload.id) {
                    s = payload;
                }
                return s;
            })
        },
        SET_COMPANIES(state, payload) {
            state.companies = payload;
        },
        ADD_COMPANY(state, payload) {
            state.companies.push(payload);
        },
        DELETE_COMPANY(state, payload) {
            state.companies = state.companies.filter(s => s.id != payload);
        },
        EDIT_COMPANY(state, payload) {
            state.companies = state.companies.map(s => {
                if (s.id == payload.id) {
                    s = payload;
                }
                return s;
            })
        }
    },
    actions: {
        async [ACTIONS.GET_SERVICES] ({commit}) {
            const services = await getServices();
            await commit(MUTATIONS.SET_SERVICES, services);
        },
        async [ACTIONS.CREATE_SERVICE] ({commit}, payload) {
            const service = await createService(payload);
            commit(MUTATIONS.ADD_SERVICE, service);
        },
        async [ACTIONS.DELETE_SERVICE] ({commit}, payload) {
            await deleteService(payload);
            await commit(MUTATIONS.DELETE_SERVICE, payload);
        },
        async [ACTIONS.EDIT_SERVICE] ({commit}, payload) {
            await editService(payload);
            await commit(MUTATIONS.EDIT_SERVICE, payload);
        },
        async GET_COMPANIES({commit}) {
            const response = await axios.get('/api/company');
            commit("SET_COMPANIES", response.data);
        },
        async CREATE_COMPANY({commit}, payload) {
            const response = await axios.post('/api/company', payload);
            commit('ADD_COMPANY', response.data);
        },
        async DELETE_COMPANY({commit}, payload) {
            await axios.delete(`/api/company/${payload}`);
            commit('DELETE_COMPANY', payload)
        },
        async EDIT_COMPANY({commit}, payload) {
            const response = await axios.patch(`/api/company/${payload.id}`, payload);
            commit('EDIT_COMPANY', response.data);
        }
    }
};

export default ServiceModule;
