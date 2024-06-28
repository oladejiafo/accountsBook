import './bootstrap';
import Vue from 'vue';
import Alpine from 'alpinejs';

import SearchAutosuggest from './components/SearchAutosuggest.vue';

Vue.component('search-autosuggest', SearchAutosuggest);

window.Alpine = Alpine;

Alpine.start();

const app = new Vue({
    el: '#app',  
});
