import React from 'react'
import { Link } from 'react-router-dom'
import { FiMail, FiPhone, FiMapPin } from 'react-icons/fi';


const Footer = () => {
  return (
    <footer className='mt-12 text-white bg-gray-800'>
      <div className='py-8 container-custom'>

        <div className='grid grid-cols-1 gap-8 md:grid-cols-4'>

            <div>
                <h3 className='mb-4 text-lg font-bold'>About The Store</h3>
                <p className='text-sm text-gray-400'>
                    This Website Provides The Best Products With Best Prices.
                </p>
            </div>

            <div>
                <h3 className='mb-4 text-lg font-bold'>Quick Links</h3>
                <ul className='space-y-2 text-gray-400'>
                    <li><Link to='/' className='hover:text-primary-400'>Home</Link></li>
                    <li><Link to='/products' className='hover:text-primary-400'>Products</Link></li>
                    <li><Link to='/about' className='hover:text-primary-400'>About</Link></li>
                    <li><Link to='/contact' className='hover:text-primary-400'>Contatc</Link></li>
                </ul>
            </div>

            <div>
                <h3 className='mb-4 text-lg font-bold'>Contact Info</h3>
                <ul className='space-y-2 text-gray-400'>

                    <li className='flex items-center space-x-2'>
                        <FiMapPin className='ml-2' />
                        <span>Egypt, Alexandria</span>
                    </li>

                    <li className='flex items-center space-x-2'>
                        <FiPhone className='ml-2' />
                        <span>+20123456789</span>
                    </li>

                    <li className='flex items-center space-x-2'>
                        <FiMail className='ml-2' />
                        <span>info@store.com</span>
                    </li>

                </ul>
            </div>

            <div>
                <h3 className='mb-4 text-lg font-bold'>Newsletter</h3>
                <p className='mb-4 text-sm text-gray-400'>
                    Subscribe To Get The Latest Offers With Best Prices.
                </p>
                <form className='flex'> 
                    <input type='email' placeholder='Email' className='flex-1 px-3 py-2 text-gray-900 rounded-l-lg ' />
                    <button className='px-4 py-2 rounded-r-lg bg-primary-600 hover:bg-primary-700'>Subscribe</button>
                </form>
            </div>

        </div>

        <div className='pt-4 mt-8 text-center text-gray-400 border-t border-gray-700'>
            <p>All Rights Reserved &copy; {new Date().getFullYear()} - Store.com</p>
        </div>

      </div>
    </footer>
  )
}

export default Footer
