import axios from 'axios';

export async function getStats({dateStart, dateEnd}) {
    const queryParams = `start_date=${dateStart}&end_date=${dateEnd}`;
    const {data} = await axios.get(`/api/stats?${queryParams}`);
    return data;
}
