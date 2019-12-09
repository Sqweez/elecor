import axios from 'axios';

export async function getContacts() {
    const response = await axios.get('/api/mobile/contacts');
    return response.data;
}

export async function updateContacts(contacts) {
    const response = await axios.patch(`/api/mobile/contacts/${contacts.id}`, contacts)
}

export async function getMobileServices() {
    const response = await axios.get(`/api/mobile/services`);
    return response.data;
}

export async function createMobileService(service) {
    const response = await axios.post(`/api/mobile/services`, service);
    return response.data;
}

export async function deleteMobileService(id) {
    await axios.delete(`/api/mobile/services/${id}`)
}

export async function editMobileService(service) {
    await axios.patch(`/api/mobile/services/${service.id}`, service);
}
