import axios from "axios";

const baseUrl = "/api/poker";

export default {
    async getPlayers() {
        const response = await axios.get(`${baseUrl}`);
        return response.data;
    },
    async create(data) {
        const response = await axios.post(`${baseUrl}`, data);
        return response.data.pokers;
    },
    async update(data) {
        const response = await axios.post(`${baseUrl}/${data.id}`, {
            ...data,
            _method: "patch",
        });
        return response.data;
    },
    async delete(data) {
        const response = await axios.delete(`${baseUrl}/${data}`);
        return response.data;
    },
    async blacklist(data) {
        const response = await axios.post(`${baseUrl}/blacklist`, data);
        return response.data;
    },
    async timer(data) {
        const response = await axios.post(`${baseUrl}/timer`, data);
        return response.data;
    },
    async isTime(data) {
        const response = await axios.post(`${baseUrl}/isTime`, data);
        return response.data;
    },
    async createPlayers(data) {
        const response = await axios.post(`${baseUrl}/createPlayers`, {
            date: data.value,
        });
        return response.data.pokers;
    },
    async resetAllTime(data) {
        const response = await axios.post(`${baseUrl}/resetAllTime`);
        return response.data.pokers;
    },
    async stopAllTimer(data) {
        const response = await axios.post(`${baseUrl}/stopAllTimer`);
        return response.data.pokers;
    },
};
