import Vue from 'vue';
import App from "./App.vue";
import vuetify from "./plugins/vuetify";
import router from "./routes/router";
import store from './store'
import CKEditor from '@ckeditor/ckeditor5-vue';
import VueMask from 'v-mask';
import Vuelidate from 'vuelidate'
import './filters/filters';

Vue.config.productionTip = false;
Vue.use(CKEditor);
Vue.use(VueMask);
Vue.use(Vuelidate);

const app = new Vue({
    el: '#app',
    vuetify,
    router,
    store,
    components: {App},
});


