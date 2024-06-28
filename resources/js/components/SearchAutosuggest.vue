<!-- resources/js/components/SearchAutosuggest.vue -->
<template>
    <div>
        <input type="text" placeholder="Search" v-model="query">
        
        <ul v-if="results.length > 0 && query">
            <li v-for="result in results.slice(0, 10)" :key="result.id">
                <a :href="result.url">
                    <div v-text="result.title"></div>
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            query: '',
            results: []
        };
    },
    watch: {
        query(after, before) {
            this.fetchSuggestions();
        }
    },
    methods: {
        fetchSuggestions() {
            axios.get('/search', { params: { query: this.query } })
                .then(response => {
                    this.results = response.data;
                })
                .catch(error => {
                    console.error('Error fetching search suggestions:', error);
                });
        }
    }
}
</script>

<style scoped>
/* Add your scoped styles here */
</style>
