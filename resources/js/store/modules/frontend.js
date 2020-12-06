const frontEndModule = {
    state: {
        loadingOverlay: false,
    },
    getters: {
        IS_LOADING: s => s.loadingOverlay,
    },
    mutations: {
        enableLoading(state) {
            state.loadingOverlay = true;
        },
        disableLoading(state) {
            state.loadingOverlay = false;
        },
        toggleLoading(state) {
            state.loadingOverlay = !state.loadingOverlay;
        },
    },
    actions: {}
};

export default frontEndModule;
