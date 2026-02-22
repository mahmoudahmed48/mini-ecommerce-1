import React from 'react'
import { useAuth } from '../../context/AuthContext'
import toast from 'react-hot-toast'
import api from '../../services/api'
import { Link } from 'react-router-dom'
import { FiShoppingCart, FiEye } from 'react-icons/fi';

const ProductCard = ({product}) => {

    const {isAuthenticated} = useAuth()

    const addToCart = async (e) => {

        e.preventDefault()
        if (!isAuthenticated())
        {
            toast.error('Please Login First')
            return
        }

        try 
        {
            await api.post('/cart/add', {
                product_id: product.id,
                quantity: 1
            })
            toast.success('Added To Cart')
        }
        catch(error)
        {
            toast.error('Error Occured Please Try Again!')
        }


    }

  return (
    <div className='overflow-hidden transition bg-white rounded-lg shadow-md hover:shadow-lg group'>

        <Link to={`/products/${product.id}`}>

            <div className='relative h-48 overflow-hidden'>
                <img src={product.image || 'https://via.placeholder.com/300'} alt={product.name} 
                className='object-cover w-full transition duration-300 group-hover:scale-110'/>
                {
                    product.stock === 0 && (
                        <div className='absolute inset-0 flex items-center justify-center bg-black bg-opacity-50'>
                            <span className='font-bold text-white'>Not Available</span>
                        </div>
                    )
                }
            </div>

            <div className='p-4'>
                <h3 className='mb-2 font-semibold text-gray-800'>{product.name}</h3>
                <div className='flex items-center justify-center' >

                    <span className='font-bold text-primary-600'>
                        ${product.price}
                    </span>

                    <div className='flex space-x-2'>

                        <button onClick={addToCart} disabled={product.stock === 0} 
                        className={`p-2 rounded-full ${product.stock === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-primary-100 text-primary-600 hover:bg-primary-200'}`} >
                            <FiShoppingCart />
                        </button>

                        <Link to={`/products/${product.id}`} className='p-2 text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200'>
                            <FiEye />
                        </Link>

                    </div>

                </div>
            </div>

        </Link>

    </div>
  )
}

export default ProductCard
