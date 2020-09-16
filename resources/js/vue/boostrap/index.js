import Vue from 'vue'
import DataTable from 'laravel-vue-datatable';
import VJsoneditor from 'v-jsoneditor'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VJsoneditor)
Vue.use(DataTable);

const files = require.context('../', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

export default new Vue({
    el: '#app',
    async created() {
        const {data: {data}} = await axios.get('/api/token')
        window.axios.defaults.headers.common['Authorization'] = data
    }
});
