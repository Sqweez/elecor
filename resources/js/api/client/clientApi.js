import axios from 'axios';

export async function getAllClients() {
    const response = await axios.get('api/clients');
    return response.data;
}

export async function createUser(user) {
    if (!user.birth_date) {
        delete user.birth_date;
    }
    const {data} = await axios.post('api/clients', user);
    return data;
}

export async function getClientTypes() {
    const response = await axios.get('/api/clients/types');
    return response.data;
}

export async function getClientById(id) {
    const response = await axios.get(`/api/clients/${id}`);
    return response.data;
}

export async function editClient(user) {
    const response = await axios.patch(`/api/clients/${user.id}`, user);
    return response.data;
}

export async function deleteClient(id) {
    const response = await axios.delete(`/api/clients/${id}`);
    return response.data;
}

export async function parseClients(file) {
    const formData = new FormData;
    formData.append('file', file);
    const response = await axios.post('/api/parse_clients', formData);
    return response.data;
}

export async function parseBalance(file) {
    const formData = new FormData;
    formData.append('file', file);
    const response = await axios.post('/api/parse_balance', formData);
    return response.data;
}

export async function createParsedClients(clients) {
    const formData = new FormData;
    formData.append('clients', JSON.stringify(clients));
    const response = await axios.post('/api/clients/create_clients', formData);
    return response.data;
}

export async function sendPushToClient(push) {
    const response = await axios.post(`/api/clients/push`, push);
    return response.data;
}

export async function getDebts() {
    const response = await axios.get('/api/clients/debt');
    const {data} = response;
    return data;
}

export async function getQRCode(id) {
    return await axios.get(`/api/v2/referral/${id}/qr`);
}

export async function getGenders() {
    return await axios.get('/api/clients/genders');
}

export async function getLanguages() {
    return await axios.get('/api/clients/languages');
}


