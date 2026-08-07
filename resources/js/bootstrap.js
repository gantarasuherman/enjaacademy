import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

/** Small helper used by the Alpine components for JSON POSTs. */
window.post = async (url, data = {}) => {
    const response = await window.axios.post(url, data);

    return response.data;
};
