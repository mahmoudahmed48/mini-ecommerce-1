import React, { useEffect, useState } from 'react'
import api from '../services/api'
import Layout from '../components/layout/Layout'
import { Link } from 'react-router-dom'
import { FiChevronRight } from 'react-icons/fi';
import ProductCard from '../components/products/ProductCard';


const Home = () => {

    const [featuredProducts, setFeaturedProducts] = useState([])
    const [categories, setCategories] = useState([])
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        fetchData()
    }, [])

    const fetchData = async () => {
        try 
        {
            const [productsRes, categoriesRes] = await Promise.all([
                api.get('/products'),
                api.get('/categories')
            ])

            setFeaturedProducts(productsRes.data.data)
            setCategories(categoriesRes.data.data)
        }
        catch (error)
        {
            console.log('Error Fetching The Data:', error )
        }
        finally
        {
            setLoading(false)
        }
    }

    if (loading) 
    {
        return (
            <Layout>
                <div className='flex items-center justify-center h-64' >
                    <div className='w-12 h-12 border-b-2 rounded-full animate-spin border-primary-600'></div>
                </div>
            </Layout>
        )
    }


  return (
    <Layout>
        {/* HERO SECTION */}
        <section className='py-16 text-white bg-gradient-to-r from-primary-500 to-primary-700'>

            <div className='container-custom'>
                <div className='max-w-2xl'>
                    <h1 className='mb-4 text-4xl font-bold'>Shop With The Best Prices</h1>
                    <p className='mb-8 text-xl'>
                        Discover Our Latest Featured Products With High Quality And Good Prices
                    </p>
                    <Link to='/products' className='inline-flex items-center px-6 py-3 font-semibold transition bg-white rounded-lg text-primary-600 hover:bg-gray-100 '>
                        Shopping Now 
                        <FiChevronRight className='mr-2' />
                    </Link>
                </div>
            </div>

        </section>
        {/* HERO SECTION */}
        {/* CATEGORIES SECTION */}
        <section className='py-12'>
            <div className='container-custom'>
                <h2 className='mb-8 text-2xl font-bold '>Categories</h2>
                <div className='grid grid-cols-2 gap-4 md:grid-cols-4'>
                    {
                        categories.map(category => (
                            <Link to={`/categories/${category.id}`} className='p-6 text-center transition bg-white rounded-lg shadow-md hover:shadow-lg'>
                                <h3 className='font-semibold text-gray-800'>{category.name}</h3>
                                <p className='mt-2 text-sm text-gray-500'>
                                    {categories.product_count || 0} Products
                                </p>
                            </Link>
                        ))
                    }
                </div>
            </div>
        </section>
        {/* CATEGORIES SECTION */}
        {/* FEATURED SECTION */}
        <section className='py-12 bg-gray-100'>
            <div className='container-custom'>
                <h2 className='mb-8 text-2xl font-bold'>Featured Products</h2>
                <div className='grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4'>
                    {
                        featuredProducts.map(product => (
                            <ProductCard key={product.id} product={product} />
                        ))
                    }
                </div>
            </div>
        </section>
        {/* FEATURED SECTION */}

        {/* FEATURES */}
        <section className='py-12'>
            <div className='container-custom'>
                <div className='grid grid-cols-1 gap-6 md:grid-cols-3'>

                    <div className='p-6 text-center'>
                        <div className='mb-4 text-4xl'>🚚</div>
                        <div className='mb-2 font-bold'>Fast Deleviry</div>
                        <p className='text-gray-600'>3 Days Shipping</p>
                    </div>

                    <div className='p-6 text-center'>
                        <div className='mb-4 text-4xl'>🔒</div>
                        <div className='mb-2 font-bold'>Secure Payments</div>
                        <p className='text-gray-600'>Secure Payment Methods</p>
                    </div>

                    <div className='p-6 text-center'>
                        <div className='mb-4 text-4xl'>⭐</div>
                        <div className='mb-2 font-bold'>Original Products</div>
                        <p className='text-gray-600'>Provide High Quality Products</p>
                    </div>

                </div>
            </div>
        </section>
        {/* FEATURES */}

    </Layout>
  )
}

export default Home
