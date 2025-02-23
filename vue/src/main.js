import { createApp } from 'vue'
import { createPinia } from 'pinia'

import '@/assets/global.css'
import '../node_modules/bootstrap/dist/css/bootstrap.min.css'
import '../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'
import '../node_modules/bootstrap-icons/font/bootstrap-icons.css'

import App from './App.vue'
import router from './router'

import Antd from 'ant-design-vue'

const app = createApp(App)

app.use(createPinia())
app.use(Antd)
app.use(router)

app.mount('#app')
