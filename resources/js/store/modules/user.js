import axios from 'axios';

const userModule = {
    state: {
        token: localStorage.getItem('token') || '',
        user: null,
        users: [],
        roles: [],
        fields: [],
    },
    getters: {
        isLoggedIn: state => !!state.user,
        hasToken: state => !!state.token,
        user: state => state.user,
        users: state => state.users,
        user_by_id: state => id => state.users.filter(s => s.id === id)[0],
        get_roles: state => state.roles,
        get_fields: state => state.fields,
        active_fields: state => state.fields.filter(s => s.is_active == 1),
    },
    mutations: {
        setUser(state, payload) {
            state.user = payload;
            state.token = payload.token;
        },
        logout(state) {
            state.user = null;
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
        },
        setFields(state, payload) {
            state.fields = payload;
        },
        createField(state, payload) {
            state.fields.push(payload);
        },
        changeField(state, payload) {
            state.fields = state.fields.map(f => {
                if (f.id === payload.id) {
                    f.is_active = payload.is_active;
                }
                return f;
            })
        },
        deleteField(state, payload) {
            state.fields = state.fields.filter(s => s.id !== payload);
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
        async getFields({commit}) {
            const response = await axios.get('/api/fields');
            commit('setFields', response.data);
        },
        async createField({commit}, payload) {
            payload.is_active = 1;
            const response = await axios.post(`/api/fields`, payload);
            commit('createField', response.data);
        },
        async changeField({commit}, payload) {
            await axios.patch(`/api/fields/${payload.id}`, payload);
            await commit('changeField', payload);
        },
        async deleteField({commit}, payload) {
            await axios.delete(`/api/fields/${payload}`);
            await commit('deleteField', payload);
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
