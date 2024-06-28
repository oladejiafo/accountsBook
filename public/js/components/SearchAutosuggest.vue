<template>
    <div>
        <input type="text" v-model="query" @input="fetchSuggestions">
        <ul>
            <li v-for="result in results" :key="result.id">
                <a :href="result.url">{{ result.name }}</a>
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
            results: [],
        };
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
/* Add styling for autosuggestion results if needed */
</style>
