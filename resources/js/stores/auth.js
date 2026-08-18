import { ref } from 'vue';
import axios from 'axios';

const user = ref(null);
const loading = ref(true);

export function useAuth() {

    const fetchUser = async () => {
        try {
            const response = await axios.get('/api/user');

            user.value = response.data.user;

            return user.value;

        } catch (error) {
            user.value = null;

            return null;
        } finally {
            loading.value = false;
        }
    };


    const logout = async () => {

        try {
            await axios.post('/api/logout');
        } finally {
            user.value = null;
        }

    };


    return {
        user,
        loading,
        fetchUser,
        logout,
    };
}