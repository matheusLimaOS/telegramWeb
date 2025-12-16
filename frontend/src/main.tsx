import { CssBaseline, ThemeProvider, Toolbar } from '@mui/material'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import { createBrowserRouter, RouterProvider } from 'react-router-dom'
import { ToastContainer } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import Header from './components/Header/index.tsx'
import './i18n'
import './index.css'
import Login from './pages/login/index.tsx'
import NewUser from './pages/newUser/index.tsx'
import theme from './theme'

const queryClient = new QueryClient()
const router = createBrowserRouter([
  {
    path: '/newUser',
    element: <NewUser />,
    children: [{ path: 'newUser' }],
  },
  {
    path: '/',
    element: <Login />,
    children: [{ path: 'login' }],
  },
])

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <QueryClientProvider client={queryClient}>
      <ThemeProvider theme={theme}>
        <Header />
        <Toolbar />
        <CssBaseline />
        <ToastContainer
          position="top-right"
          autoClose={3000}
          hideProgressBar={false}
          newestOnTop
          closeOnClick
          pauseOnHover
          draggable
        />
        <RouterProvider router={router} />
      </ThemeProvider>
    </QueryClientProvider>
  </StrictMode>,
)
