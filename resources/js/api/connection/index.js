import axios from 'axios';
import {fromHex} from "vuetify/lib/components/VColorPicker/util";
import {reactiveProp} from "vue-chartjs/es/mixins";

export async function getDuplicate(account) {
    const response = await axios.get(`/api/connections/duplicates?personal_account=${account}`);
    return response.data;
}

export async function createConnection(service) {
    //@TODO после того как сделаю авторизацию на сайте убрать заглушку
    service.user_id = 1;
    const response = await axios.post(`/api/connections`, service);
    console.log(response);
    return response.data;
}

export async function disconnectService(service) {
    const response = await axios.patch(`/api/connections/disconnect/${service}`);
    return response.data;
}

export async function connectService(service) {
    const response = await axios.patch(`/api/connections/connect/${service}`);
    return response.data;
}

export async function addBalance(service, balance) {
    //@TODO после того как сделаю авторизацию на сайте убрать заглушку
    balance.user_id = 1;
    balance.balance_change = balance.balance;
    delete balance.balance;
    const response = await axios.post(`/api/connections/balance/${service}`, balance);
    return response.data;
}

export async function makeSale(sale) {
    //@TODO после того как сделаю авторизацию на сайте убрать заглушку
    sale.user_id = 1;
    const response = await axios.post(`/api/connections/sale`, sale);
    return response.data;
}

export async function getHistory(connection) {
    const response = await axios.get(`/api/connections/history/${connection}`);
    return response.data;
}

export async function findAccount(account) {
    const formData = new FormData;
    formData.append('account', account);
    const response = await axios.post(`/api/connections/find`, formData);
    return response.data;
}

export async function addBalances(balances) {
    const formData = new FormData;
    formData.append('balances', JSON.stringify(balances));
    const response = await axios.post('/api/connections/balances', formData);
    return response.data;
}

export async function editConnection(connection) {
    const response = await axios.patch(`/api/connections/${connection.id}`, connection);
    return response.data;
}

export async function deleteConnection(connection) {
    await axios.delete(`/api/connections/${connection}`);
}

export default {};
