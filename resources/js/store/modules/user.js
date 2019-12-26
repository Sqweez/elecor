import axios from 'axios';

const userModule = {
    state: {
        token: localStorage.getItem('token') || '',
        user: null
    },
    getters: {
        isLoggedIn: state => !!state.user,
        hasToken: state => !!state.token,
        user: state => state.user,
    },
    mutations: {
        setUser(state, payload) {
            state.user = payload;
            state.token = payload.token;
        },
        logout(state) {
            state.user = {};
            state.token = '';
        },
        removeToken(state) {
            state.token = '';
        }
    },
    actions: {
        async login({commit}, payload) {
            localStorage.removeItem('token');
            commit('removeToken');
            const {data, error} = await axios.post('/api/auth', payload);
            if (error) {
                return null;
            }
            if (data.error) {
                return data;
            }
            axios.defaults.headers.common['Authorization'] = data.token;
            localStorage.setItem('token', data.token);
            commit('setUser', data);
            return data;
        },
        async logout({commit}) {
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
            commit('logout');
        }
    },
};

export default userModule;
