import GETTERS from "../getters";
import MUTATIONS from "../mutations";
import ACTIONS from "../actions";
import {changeStatus, createStock, deleteStock, editStock, getStocks} from "../../api/stocks";

const stockModule = {
    state: {
        stocks: null,
    },
    getters: {
        [GETTERS.STOCKS] (state) {
            return state.stocks;
        },
        stock: state => id => state.stocks.find(s => s.id === id)
    },
    mutations: {
        [MUTATIONS.SET_STOCKS] (state, payload) {
            state.stocks = payload;
        },
        [MUTATIONS.EDIT_STOCK] (state, payload) {
            state.stocks = state.stocks.map(s => {
                if (s.id === payload.id) {
                    console.log(payload);
                    s = payload;
                }
                return s;
            })
        },
        [MUTATIONS.CHANGE_STOCK_STATUS] (state, payload) {
            state.stocks = state.stocks.map(s => {
                if (s.id === payload.id) {
                    s.is_visible = !s.is_visible;
                }
                return s;
            })
        },
        [MUTATIONS.ADD_STOCK] (state, payload) {
            state.stocks.push(payload);
        },
        [MUTATIONS.DELETE_STOCK] (state, payload) {
            state.stocks = state.stocks.filter(s => s.id !== payload);
        }
    },
    actions: {
        async [ACTIONS.GET_STOCKS] ({commit}){
            const stocks = await getStocks();
            commit(MUTATIONS.SET_STOCKS, stocks);
        },
        async [ACTIONS.CREATE_STOCK] ({commit}, payload) {
            const stock = await createStock(payload);
            console.log(stock);
            await commit(MUTATIONS.ADD_STOCK, stock);
        },
        async [ACTIONS.DELETE_STOCK] ({commit}, payload) {
            await deleteStock(payload);
            await commit(MUTATIONS.DELETE_STOCK, payload);
        },
        async [ACTIONS.EDIT_STOCK] ({commit}, payload) {
            const response = await editStock(payload);
            await commit(MUTATIONS.EDIT_STOCK, response);
        },
        async [ACTIONS.CHANGE_STOCK_STATUS] ({commit}, payload) {
            await changeStatus(payload);
            await commit(MUTATIONS.CHANGE_STOCK_STATUS, payload);
        }
    }
};

export default stockModule;
