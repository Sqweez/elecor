import axios from 'axios';

export async function getOrders() {
    const response = await axios.get('/api/orders');
    return response.data;
}

export async function changeOrderStatus(order) {
    await axios.patch(`/api/orders/${order.id}`, order);
}
