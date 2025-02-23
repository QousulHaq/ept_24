import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import './index.css'
import '../../../public/css/pre/editor.css'
import { RouterProvider } from 'react-router-dom'
import router from './routers'
import { Provider } from 'react-redux'
import { PersistGate } from 'redux-persist/integration/react'
import store, { persistor } from './slices/store.js'

createRoot(document.getElementById('app')).render(
  <StrictMode>
    <Provider store={store}>
      <PersistGate loading={null} persistor={persistor}>
        <RouterProvider router={router} >
          <App />
        </RouterProvider>
      </PersistGate>
    </Provider>
  </StrictMode>,
)
