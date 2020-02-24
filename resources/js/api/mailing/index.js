import axios from 'axios';

export async function getMailingHistory() {
    const response = await axios.get('/api/mailing_history');
    return response.data;
}

export async function deleteHistory(id) {
    await axios.delete(`/api/mailing_history/${id}`)
}

export async function getMailingTemplates() {
    const response = await axios.get('/api/mailing_templates');
    return response.data;
}

export async function createMailingTemplate(template) {
    const response = await axios.post('/api/mailing_templates', template);
    return response.data;
}

export async function deleteMailingTemplate(id) {
    await axios.delete(`/api/mailing_templates/${id}`)
}

export async function editMailingTemplate(payload) {
    const response = await axios.patch(`/api/mailing_templates/${payload.id}`, payload);
    return response.data;
}

export async function sendMailing(payload) {
    await axios.post(`/api/mailing`, payload);
}
