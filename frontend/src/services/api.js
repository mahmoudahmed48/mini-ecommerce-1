import axios from "axios";
import toast from "react-hot-toast";

const api = axios.create({
    baseURL: 'http://localhost:8000/api/',
    headers: 
    {
        'Content-Type' : 'application/json',
        'Accept' : 'application/json'
    }
});


// Automatically Add Token To Request 
api.interceptors.request.use(
    (config) => {

        const token = localStorage.getItem('token');
        if (token)
        {
            config.headers.Authorization = `Bearer ${token}`
        }

        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Treatment The Response 
api.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {

        if (error.response)
        {
            if (error.response.status === 401 )
            {
                localStorage.removeItem('token')
                localStorage.removeItem('user')
                window.location.href('/login')
                toast.error('Session Expired Please Login Again!')
            }
            else if (error.response.status === 403)
            {
                toast.error('You Have No Access To This Page')
            }
            else if (error.response.status === 422)
            {
                const errors = error.response.data.errors
                Object.keys(error).forEach(key => {
                    toast.error(errors[key][0])
                });
            }
            else if (error.response.status === 500)
            {
                toast.error('Server Error Please Try Again Later!')
            }
            else if(error.request)
            {
                toast.error('Can not Connect - Make Sure To Turn on Laravel ')
            }

            return Promise.reject(error)
        }

    }
)

export default api