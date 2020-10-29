import {
    createUser,
    deleteClient,
    editClient,
    getAllClients,
    getClientById,
    getClientTypes, getDebts
} from "../../api/client/clientApi";
import ACTIONS from "../actions";
import MUTATIONS from "../mutations";
import GETTERS from "../getters";
import {
    addBalance,
    connectService,
    createConnection, deleteConnection,
    disconnectService,
    editConnection,
    makeSale
} from "../../api/connection";

const clientsModule = {
    state: {
        subjects: [],
        clients: null,
        client: null,
        debts: [],
        languages: [
            {
                id: 'ru',
                lang: 'Русский'
            },
            {
                id: 'kz',
                lang: 'Казахский'
            }
        ],
        genders: [
            {
                id: 'M',
                gender: 'Мужской'
            },
            {
                id: 'F',
                gender: 'Женский'
            }
        ],
        current_page: 1,
    },
    getters: {
        [GETTERS.CLIENTS](state) {
            if (typeof state.clients === 'string') {
                return null;
            }
            return state.clients;
        },
        [GETTERS.CLIENTS_COUNT](state) {
            if (!state.clients || typeof state.clients === 'string') {
                return 0;
            }
            return state.clients.length;
        },
        [GETTERS.CLIENT_BY_ID]: state => id => {
            return state.client;
        },
        [GETTERS.CLIENT_TYPES]: state => {
            return state.subjects;
        },
        [GETTERS.CLIENT]: state => {
            return state.client;
        },
        [GETTERS.CLIENT_DUPLICATE]: state => _client => {
            return state.clients.filter(c => _client.name === c.name);
        },
        [GETTERS.CLIENT_DUPLICATES]: state => _clients => {
            let duplicates = [];
            _clients.forEach((_c) => {
                const _duplicates = state.clients.filter(c => c.name === _c.name);
                if (_duplicates.length) {
                    duplicates.push(
                        {
                            importName: _c.name,
                            duplicates: _duplicates
                        }
                    )
                }
            });
            return duplicates;
        },
        debts: state => state.debts,
        LANGUAGES: state => state.languages,
        GENDERS: state => state.genders,
        CURRENT_PAGE: state => state.current_page,
    },
    mutations: {
        [MUTATIONS.SET_CLIENTS](state, payload) {
            state.clients = payload.map(c => {
                c._personalAccounts = c.personal_accounts.join(' ');
                c._addresses = c.addresses.join(' ');
                c._trademarks = c.trademarks.join(' ');
                return c;
            });
        },
        [MUTATIONS.SET_CLIENT_TYPES](state, payload) {
            state.subjects = payload;
        },
        [MUTATIONS.SET_CLIENT](state, payload) {
            state.client = payload;
        },
        [MUTATIONS.ADD_CLIENT](state, payload) {
            state.clients.push(payload);
        },
        [MUTATIONS.DELETE_USER](state, payload) {
            if (state.clients) {
                state.clients = state.clients.filter(c => c.id != payload)
            }
        },
        [MUTATIONS.EDIT_USER](state, payload) {
            state.client = payload;
        },
        [MUTATIONS.CLEAR_CLIENT](state) {
            state.client = null;
        },
        [MUTATIONS.ADD_CONNECTION] (state, payload) {
            state.client.connections.push(payload);
        },
        [MUTATIONS.CONNECT] (state, payload) {
            state.client.connections = state.client.connections.map(s => {
                if (s.id === payload) {
                    s.is_active = 1;
                }
                return s;
            });
        },
        [MUTATIONS.DISCONNECT] (state, payload) {
            state.client.connections = state.client.connections.map(s => {
                if (s.id === payload) {
                    s.is_active = 0;
                }
                return s;
            });
        },
        [MUTATIONS.CHANGE_BALANCE] (state, payload) {
            state.client.connections = state.client.connections.map(c => {
               if (c.id === payload.id) {
                   c.balance = +c.balance + +payload.balance;
               }
               return c;
            });
        },
        [MUTATIONS.EDIT_CONNECTION] (state, payload) {
            state.client.connections = state.client.connections.map(c => {
                if (payload.id === c.id) {
                    c.trademark = payload.trademark;
                    c.address = payload.address;
                    c.price = payload.price;
                    c.personal_account = payload.personal_account;
                    c.company = payload.company;
                    c.company_id = payload.company_id;
                }
                return c;
            })
        },
        [MUTATIONS.DELETE_CONNECTION] (state, payload) {
            state.client.connections = state.client.connections.filter(c => {
                return c.id !== payload;
            })
        },
        setDebts (state, payload) {
            state.debts = payload;
        },
        setCurrentPage(state, payload) {
            state.current_page = payload;
        }
    },
    actions: {
        async [ACTIONS.GET_CLIENTS]({commit}) {
            const clients = await getAllClients();
            await commit(MUTATIONS.SET_CLIENTS, clients);
        },
        async [ACTIONS.CREATE_CLIENT]({commit, dispatch}, client) {
            const response = await createUser(client);
            await commit(MUTATIONS.ADD_CLIENT, response);
            return response;
        },
        async [ACTIONS.GET_CLIENT_TYPES]({commit}) {
            const client_types = await getClientTypes();
            await commit(MUTATIONS.SET_CLIENT_TYPES, client_types);
        },
        async [ACTIONS.GET_CLIENT]({commit}, payload) {
            const client = await getClientById(payload);
            await commit(MUTATIONS.SET_CLIENT, client);
        },
        async [ACTIONS.CLEAR_CLIENT]({commit}) {
            await commit(MUTATIONS.CLEAR_CLIENT);
        },
        async [ACTIONS.EDIT_CLIENT]({commit}, payload) {
            const {client, newPhoto} = payload;
            if (newPhoto) {
                client.photo = newPhoto;
            }
            const result = await editClient(client);
            await commit(MUTATIONS.EDIT_USER, result);
            return result;
        },
        async [ACTIONS.DELETE_CLIENT]({commit}, payload) {
            await deleteClient(payload);
            await commit(MUTATIONS.DELETE_USER, payload)
        },
        async [ACTIONS.ADD_CONNECTION]({commit}, payload) {
            const connection = await createConnection(payload);
            await commit(MUTATIONS.ADD_CONNECTION, connection);
        },
        async [ACTIONS.CONNECT]({commit}, payload) {
            await connectService(payload);
            await commit(MUTATIONS.CONNECT, payload);
        },
        async [ACTIONS.DISCONNECT]({commit}, payload) {
            await disconnectService(payload);
            await commit(MUTATIONS.DISCONNECT, payload);
        },
        async [ACTIONS.ADD_BALANCE]({commit}, payload) {
            await addBalance(payload.id, {balance: payload.balance});
            await commit(MUTATIONS.CHANGE_BALANCE, payload);
        },
        async [ACTIONS.SALE] ({commit}, payload) {
            await makeSale(payload);
            await commit(MUTATIONS.CHANGE_BALANCE, {id: payload.connection_id, balance: payload.price * -1})
        },
        async [ACTIONS.EDIT_CONNECTION] ({commit}, payload) {
            const connection = await editConnection(payload);
            await commit(MUTATIONS.EDIT_CONNECTION, connection);
        },
        async [ACTIONS.DELETE_CONNECTION] ({commit}, payload) {
            await deleteConnection(payload);
            await commit(MUTATIONS.DELETE_CONNECTION, payload);
        },
        async getDebts({commit}) {
            const response = await getDebts();
            commit('setDebts', response.debts);
        }
    }
};

export default clientsModule;
