import api from "./api";

const productService = {
    // Get All Products 
    getProducts : async (params = {}) => {
        const response = await api.get('/products', {params})
        return response.data
    },

    // Get One Product
    getProduct : async (id) => {
        const response = await api.get(`/products/${id}`)
        return response.data
    },

    getProductBySlug : async (slug) => {
        const response = await api.get(`/products/slug/${slug}`)
        return response.data
    },

    getProductByCategory : async (categoryId) => {
        const response = await api.get(`/products?category_id=${categoryId}`)
        return response.data
    }, 

    searchProducts : async (search) => {
        const response = await api.get(`/products?search=${search}`)
        return response.data
    }

}

export default productService