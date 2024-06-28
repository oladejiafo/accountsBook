// resources/js/search.js

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    let timeoutId = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeoutId);
        const query = this.value.trim();

        if (query.length > 2) {
            timeoutId = setTimeout(function () {
                fetchSearchResults(query);
            }, 300); // Adjust delay as needed
        } else {
            clearResults();
        }
    });

    function fetchSearchResults(query) {
        fetch(`/search?query=${query}`)
            .then(response => response.json())
            .then(results => {
                displayResults(results);
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
            });
    }

    function displayResults(results) {
        searchResults.innerHTML = '';
        results.forEach(result => {
            const listItem = document.createElement('li');
            const link = document.createElement('a');
            link.href = result.url;
            link.textContent = `${result.title} (${result.type})`;
            listItem.appendChild(link);
            searchResults.appendChild(listItem);
        });
        searchResults.style.display = 'block'; // Show the results dropdown
    }

    function clearResults() {
        searchResults.innerHTML = '';
        searchResults.style.display = 'none'; // Hide the results dropdown
    }
});
