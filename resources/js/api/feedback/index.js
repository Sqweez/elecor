import axios from 'axios';

export async function getFeedback() {
    const response = await axios.get(`/api/feedback`);
    return response.data;
}

export async function changeFeedbackStatus(feedback) {
    //@TODO
    //Убрать пользователя
    feedback.user_id = 1;
    await axios.patch(`/api/feedback/${feedback.id}`, feedback);
}

export async function getCount() {
    const {data} = await axios.get('/api/feedbacks');
    return data;
}
