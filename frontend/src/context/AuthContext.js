import authService from '../services/authService'
import { createContext, useContext, useState, useEffect } from "react";

const AuthContext = createContext()

export const useAuth = () => {
    const context = useContext(AuthContext)

    if (!context)
    {
        throw new Error('useAuth Must Be Used within AuthProvider') 
    }

    return context
}

export const AuthProvider = ({children}) => {

    const [user, setUser] = useState(null)
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        const currentUser = authService.getCurrentUser()
        if (currentUser)
        {
            setUser(currentUser)
        }
        setLoading(false)
    }, [])

    const login = async (credintials) => {
        const response = await authService.login(credintials)
        setUser(response.user)
        return response
    }

    const register = async (userData) => {
        const response = await authService.register(userData)
        setUser(response.user)
        return response
    }

    const logout = async () => {
        await authService.logout()
        setUser(null)
    }

    const value = {user, loading, login, register, logout, isAuthenticated: authService.isAuthenticated, isAdmin : authService.isAdmin}

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    )
}