<template>
    <div>
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search" v-model="query" @input="fetchResults">
            <div class="input-group-append" v-if="showResults && results.length > 0">
                <ul class="dropdown-menu">
                    <li v-for="(result, index) in results" :key="index">
                        <a :href="result.url" class="dropdown-item" @click.prevent="handleResultClick(result)">
                            {{ result.title }} ({{ result.type }})
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            query: '',
            results: [],
            showResults: false
        };
    },
    methods: {
        fetchResults() {
            if (this.query.length > 2) {
                axios.get('/search', { params: { query: this.query } })
                    .then(response => {
                        this.results = response.data;
                        this.showResults = true;
                    })
                    .catch(error => {
                        console.error('Error fetching search results:', error);
                    });
            } else {
                this.results = [];
                this.showResults = false;
            }
        },
        handleResultClick(result) {
            // Optional: Add any additional handling before navigation
            window.location.href = result.url;
        }
    }
};
</script>

<style scoped>
.dropdown-menu {
    position: absolute;
    width: 100%;
    z-index: 1000;
}
</style>
