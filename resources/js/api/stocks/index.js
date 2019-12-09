import axios from 'axios';

export async function getStocks() {
    const response = await axios.get('/api/stocks');
    return response.data;
}

export async function createStock(stock) {
    const response = await axios.post('/api/stocks', stock);
    return response.data;
}

export async function deleteStock(id) {
    return await axios.delete(`/api/stocks/${id}`)
}

export async function editStock(stock) {
    const response = await axios.patch(`/api/stocks/${stock.id}`, stock);
    return response.data;
}

export async function changeStatus(stock) {
    return await axios.patch(`/api/stocks/${stock.id}`, stock);
}
