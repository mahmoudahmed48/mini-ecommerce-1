import './App.css';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { Toaster } from 'react-hot-toast';
import Home from './pages/Home';
import Products from './pages/Products';


function App() {
  return (
<>


    <Router>
      <AuthProvider>
        <Toaster position='top-center' reverseOrder={false} toastOptions={{ duration: 3000, style: {fontFamily:'inherit'} }} />

        <Routes>

          {/* Public Routes */}
          <Route path='/' element={<Home />}/>
          <Route path='/products' element={<Products />}/>
          {/* Public Routes */}
          {/* Protected Routes (require login) */}

          {/* Protected Routes (require login) */}


        </Routes>

      </AuthProvider>
    </Router>
</>


  );
}

export default App;
