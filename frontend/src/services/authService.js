import api from "./api";
import toast from "react-hot-toast";

const authService = {

    register: async (userData) => 
    {
        try 
        {
            const response = await api.post('/register', userData)
            if (response.data.token)
            {
                localStorage.setItem('token')
                localStorage.setItem('user', JSON.stringify(response.data.user))
                toast.success('Created Account Successfully')
            }
            return response.data
        }
        catch (error)
        {
            throw error
        }
    }, 

    login : async (credintials) => 
    {
        try 
        {
            const response = await api.post('/login', credintials)
            if (response.data.token)
            {
                localStorage.setItem('token', response.data.token)
                localStorage.setItem('user', JSON.stringify(response.data.user))
                toast.success('Signed Up!')
            }

            return response.data
        }
        catch (error)
        {
            throw error
        }
    },

    logout : async () => 
    {
        try 
        {
            await api.post('/logout')
        }
        finally
        {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            toast.success('Logged Out')
        }
    },

    getCurrentUser :  () => 
    {
        const userStr = localStorage.getItem('user')
        if (userStr)
        {
            return JSON.parse(userStr)
        }

        return null
    },

    isAuthenticated : () => 
    {
        return !!localStorage.getItem('token')
    },

    isAdmin : () => 
    {
        const user = authService.getCurrentUser()
        return user && user.role === 'admin'
    }

}

export default authService;
