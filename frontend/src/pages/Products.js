import React, { useEffect, useState } from 'react'
import api from '../services/api'
import Layout from '../components/layout/Layout'
import ProductCard from '../components/products/ProductCard'

const Products = () => {

    const [products, setProducts] = useState([])
    const [loading, setLoading] = useState(true)
    const [filters, setFilters] = useState({
        category: '',
        search: '',
        sort: 'newest'
    })

    useEffect(() => {
        featuredProducts()
    }, [filters])
 
    const featuredProducts = async () => {
        setLoading(true)
        try 
        {
            const response = await api.get('/products', {params: filters})
            setProducts(response.data.data)
        }
        catch (error)
        {
            console.log('Error Fetching Products:', error)
        }
        finally
        {
            setLoading(false)
        }
    }


  return (
    <Layout>
        <div className='py-8 container-custom'>

            <h1 className='mb-6 text-2xl font-bold'>All Products</h1>
            {
                loading ? (
                    <div className='flex justify-center py-12'>
                        <div className='w-12 h-12 border-b-2 rounded-full animate-spin border-primary-600'></div>
                    </div>
                ) : (
                    <div className='grid grid-cols-1 gap-6 md:grid-cols-3 lg:grid-cols-4'>
                        {
                            products.map(product => (
                                <ProductCard key={product.id} product={product} />
                            ))
                        }
                    </div>
                )
            }

        </div>
    </Layout>
  )
}

export default Products
