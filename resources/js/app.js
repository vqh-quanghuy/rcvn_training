// import 'material-design-icons-iconfont/dist/material-design-icons.css'
import {createApp} from 'vue'
import { createVuetify } from 'vuetify'

import App from './App.vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const app = createApp(App)
const vuetify = createVuetify({
    components,
    directives,
})

app.use(VueAxios, axios)
app.use(vuetify)
app.mount("#app")