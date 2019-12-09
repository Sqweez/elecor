import axios from 'axios';

export async function getServices() {
    const result = await axios.get('/api/services');
    return result.data;
}

export async function createService(service) {
    const result = await axios.post('/api/services', service);
    return result.data;
}

export async function getTempServices(service) {
    const result = await axios.get(`/api/services/temp/${service}`);
    return result.data;
}

export async function deleteService(service) {
    await axios.delete(`/api/services/${service}`);
}

export async function editService(service) {
    await axios.put(`/api/services/${service.id}`, service);
}

export default {};
