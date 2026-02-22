import React, { useEffect, useState } from 'react'
import { useAuth } from '../../context/AuthContext'
import { FiShoppingCart, FiUser, FiMenu, FiX } from 'react-icons/fi';
import api from '../../services/api'
import toast from 'react-hot-toast'
import {Link, useNavigate} from 'react-router-dom'

const Navbar = () => {

    const {user, logout, isAuthenticated, isAdmin} = useAuth()
    const navigate = useNavigate()
    const [isOpen, setIsOpen] = useState(false)
    const [cartCount, setCartCount] = useState(0)
    const [categories, setCategories] = useState([])

    useEffect(() => {
        fetchCategories()
        if (isAuthenticated())
        {
            fetchCartCount()
        }
    }, [isAuthenticated])

    const fetchCategories = async () => {
        try 
        {
            const response = await api.get('/categories')
            setCategories(response.data.data)
        }
        catch(error)
        {
            console.log('Error Fetching Categories:', error)
        }
    }

    const fetchCartCount = async () => {
        try
        {
            const response = await api.get('/cart/count')
            setCartCount(response.data.data)
        }
        catch(error)
        {
            console.log('Error Fetching Cart Count:', error)
        }
    }

    const handleLogout = async () => {
        await logout()
        navigate('/')
        toast.success('Logged Out Successfully!')
    }

  return (
    <nav className='sticky top-0 z-50 bg-white shadow-lg'>

        <div className='container-custom'>

            <div className='flex items-center justify-between h-16'>

                {/* LOGO */}
                <Link to='/' className='flex items-center space-x-2'>
                    <span className='text-2xl font-bold text-primary-600'>Shop</span>
                    <span className='text-2xl font-light text-gray-600'>.com</span>
                </Link>
                {/* LOGO */}
                {/* DESKTOP MENU */}
                <div className='items-center hidden space-x-8 md:flex '>
                    
                    <Link to='/' className='text-gray-700 hover:text-primary-600'>Home</Link>
                    {/* CATEGORIES DROPDOWN */}
                    <div className='relative group'>

                        <button className='text-gray-700 hover:text-primary-600'>Products</button>

                        <div className='absolute right-0 hidden w-48 mt-2 bg-white rounded-lg shadow-lg group-hover:block'>
                        {
                            categories.map(category => (
                                <Link key={category.id} to={`/categories/${category.id}`}
                                 className='block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600'>
                                    {category.name}
                                </Link>
                            ))
                        }
                        </div>

                    </div>
                    {/* CATEGORIES DROPDOWN */}

                    {
                        isAuthenticated() && (
                            <>
                                <Link to='/cart' className='relative'>

                                    <FiShoppingCart className='text-2xl text-gray-700 hover:text-primary-600' />

                                    {
                                        cartCount > 0 && (
                                            <span className='absolute flex items-center justify-center w-5 h-5 text-xs text-white rounded-full -top-2 -right-2 bg-primary-600'>
                                                {cartCount}
                                            </span>
                                        )
                                    }

                                </Link>
                                {/* USER MENU */}
                                <div className='relative group'>
                                    <button className='flex items-center space-x-2 text-gray-700 hover:text-primary-600'>
                                        <FiUser className='text-xl' />
                                        <span>{user?.name}</span>
                                    </button>

                                    <div className='absolute left-0 hidden w-48 mt-2 bg-white rounded-lg shadow-lg group-hover:block'>
                                        <Link to='/profile' className='block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600'>
                                            Profile
                                        </Link>

                                        <Link to='/orders' className='block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600'>
                                            Orders
                                        </Link>
                                        {
                                            isAdmin() && (
                                            <Link to='/admin/dashboard' className='block px-4 py-2 text-gray-700 hover:bg-primary-50 hover:text-primary-600'>
                                                Dashboard
                                            </Link>
                                            )
                                        }
                                        <button onClick={handleLogout} className='block w-full px-4 py-2 text-right text-red-600 hover:bg-red-50'>
                                            Logout
                                        </button>
                                    </div>
                                </div>
                                {/* USER MENU */}
                            </>
                        )
                    }
                    {
                        !isAuthenticated() && (
                            <>
                                <Link to='login' className='text-gray-700 hover:text-primary-600'>
                                    Login
                                </Link>

                                <Link to='/register' className='btn-primary'>
                                    Register
                                </Link>
                            </>
                        )
                    }

                </div>
                {/* DESKTOP MENU */}

                {/* MOBILE MENU */}
                <button onClick={() => setIsOpen(!isOpen)} className='text-gray-700 md:hidden'>
                    {isOpen ? <FiX size={24} /> : <FiMenu size={24} />}
                </button>

                {/* MOBILE MENU */}

            </div>

            
            {
                    isOpen && (
                        <div className='py-4 border-t md:hidden'>

                            <Link to='/' className='block py-2 text-gray-700 hover:text-primary-600' onClick={() => setIsOpen(false)} >
                                Home
                            </Link>

                            {
                                categories.map(category => (
                                    <Link key={category.id} to={`/categories/${category.id}`} onClick={() => setIsOpen(false)}
                                    className='block py-2 pr-4 text-gray-700 hover:text-primary-600'>
                                    {category.name}
                                    </Link>
                                ))
                            }
                            {
                                isAuthenticated() ? (
                                    <>
                                        <Link to='/cart' className='block py-2 text-gray-700 hover:text-primary-600' onClick={() => setIsOpen(false)}>
                                            Cart ({cartCount})
                                        </Link>
                                        <Link to='/profile' className='block py-2 text-gray-700 hover:text-primary-600' onClick={() => setIsOpen(false)}>
                                            Profile
                                        </Link>
                                        <Link to='/orders' className='block py-2 text-gray-700 hover:text-primary-600' onClick={() => setIsOpen(false)}>
                                            Orders
                                        </Link>
                                        {
                                            isAdmin () && (
                                            <Link to='/admin/dashboard' className='block py-2 text-gray-700 hover:text-primary-600' onClick={() => setIsOpen(false)}>
                                                Dashboard
                                            </Link>
                                            )
                                        }
                                        <button onClick={() => { handleLogout(); setIsOpen(false);}} className='block w-full py-2 text-right text-red-600'>
                                            Logout
                                        </button>
                                    </>
                                ) : (
                                    <>
                                        <Link to='/login' className='block py-2 text-gray-700 hover:text-primary-600 ' onClick={() => setIsOpen(false)}>
                                            Login
                                        </Link>

                                        <Link to='/register' className='block py-2 font-bold text-primary-600'>
                                            Register
                                        </Link>
                                    </>
                                )
                            }

                        </div>
                    )
                }

        </div>

    </nav>
  )
}

export default Navbar
 