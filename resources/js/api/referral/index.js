import axios from 'axios';

export async function getReferralOperationTypes() {
    return await axios.get('/api/v2/referral/operations/types');
}

export async function createReferralOperation(payload) {
    return await axios.post('/api/v2/referral/bonuses', payload);
}

export async function getReferralSettings() {
    return await axios.get(`/api/v2/referral/settings`);
}

export async function updateReferralSettings(payload) {
    return await axios.patch(`/api/v2/referral/settings`, payload);
}
