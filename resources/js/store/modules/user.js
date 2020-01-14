import axios from 'axios';

const userModule = {
    state: {
        token: localStorage.getItem('token') || '',
        user: null,
        users: [],
        roles: [],
    },
    getters: {
        isLoggedIn: state => !!state.user,
        hasToken: state => !!state.token,
        user: state => state.user,
        users: state => state.users,
        user_by_id: state => id => state.users.filter(s => s.id === id)[0],
        get_roles: state => state.roles,
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
        },
        setUsers(state, payload) {
            state.users = payload;
        },
        setRoles(state, payload) {
            state.roles = payload;
        },
        addUser(state, payload) {
            state.users.push(payload);
        },
        editUser(state, payload) {
            state.users = state.users.map(s => {
               if (s.id === payload.id) {
                   s = payload;
               }
               return s;
            });
        },
        deleteUser(state, payload) {
            state.users = state.users.filter(u => u.id !== payload)
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
        async getUsers({commit}) {
            const {data} = await axios.get('/api/users');
            commit("setUsers", data);
        },
        async createUser({commit}, payload) {
            const {data} = await axios.post('/api/users', payload);
            commit('addUser', data);
        },
        async editUser({commit}, payload) {
            const {data} = await axios.patch('/api/users/' + payload.id, payload);
            commit('editUser', data);
        },
        async deleteUser({commit}, payload) {
            await axios.delete(`/api/users/${payload}`);
            commit("deleteUser", payload);
        },
        async getRoles({commit}) {
            const {data} = await axios.get('/api/roles');
            commit('setRoles', data);
        },
        async logout({commit}) {
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
            commit('logout');
        }
    },
};

export default userModule;
