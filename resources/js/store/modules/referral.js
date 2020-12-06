import {
    createReferralOperation,
    getReferralOperationTypes,
    getReferralSettings,
    updateReferralSettings
} from "../../api/referral";
import ACTIONS from "../actions";
import GETTERS from "../getters";
import showToast from "../../utils/Toast";
import {TOAST_TYPE} from "../../config/consts";

const referralModule = {
    state: {
        referral_operation_types: [],
        referral_settings: {},
    },
    getters: {
        REFERRAL_OPERATION_TYPES: s => s.referral_operation_types,
        REFERRAL_SETTINGS: s => s.referral_settings,
    },
    mutations: {
        setReferralOperationTypes(state, payload) {
            state.referral_operation_types = payload;
        },
        setReferralSettings(state, payload) {
            state.referral_settings = payload;
        }
    },
    actions: {
        async getReferralOperationTypes({commit}) {
            const { data } = await getReferralOperationTypes();
            commit('setReferralOperationTypes', data);
        },
        async getReferralSettings({commit}) {
            commit('enableLoading');
            const { data } = await getReferralSettings();
            commit('setReferralSettings', data);
            commit('disableLoading');
        },
        async updateReferralSettings({commit}, payload) {
            commit('enableLoading');
            const { data } = await updateReferralSettings(payload);
            commit('setReferralSettings', data);
            showToast('Настройки успешно обновлены!')
            commit('disableLoading');
        },
        async createReferralOperation({commit, dispatch, getters}, payload) {
            try {
                commit('enableLoading');
                await createReferralOperation(payload);
                await dispatch(ACTIONS.GET_CLIENT, getters[GETTERS.CLIENT].id);
            } catch (e) {
                showToast('Произошла ошибка, попробуйте повторить действие позже', 'Ошибка', TOAST_TYPE.ERROR);
            } finally {
                commit('disableLoading');
                showToast('Операция с бонусами завершена успешно!', 'Успешно');
            }

        }
    }
}

export default referralModule;
