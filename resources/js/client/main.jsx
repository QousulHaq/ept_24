import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'

import { Provider } from 'react-redux'
import { PersistGate } from 'redux-persist/integration/react'

import store, { persistor } from './slices/store.js'
import App from './App.jsx'
import SnackbarProvider from './context/SnackbarProvider.jsx'

import './index.css'
import '../../../public/css/pre/editor.css'

createRoot(document.getElementById('app')).render(
  <StrictMode>
    <Provider store={store}>
      <PersistGate loading={null} persistor={persistor}>
        <SnackbarProvider />
        <App />
      </PersistGate>
    </Provider>
  </StrictMode>,
)
