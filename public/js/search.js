// resources/js/search.js

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const searchInputMobile = document.getElementById('searchInputMobile');
    const searchResultsMobile = document.getElementById('searchResultsMobile');

    let timeoutId = null;

    // Desktop search input listener
    searchInput.addEventListener('input', function () {
        clearTimeout(timeoutId);
        const query = this.value.trim();

        if (query.length > 2) {
            timeoutId = setTimeout(function () {
                fetchSearchResults(query, searchResults);
            }, 300); 
        } else {
            clearResults(searchResults);
        }
    });

    // Mobile search input listener
    searchInputMobile.addEventListener('input', function () {
        clearTimeout(timeoutId);
        const query = this.value.trim();

        if (query.length > 2) {
            timeoutId = setTimeout(function () {
                fetchSearchResults(query, searchResultsMobile);
            }, 300); 
        } else {
            clearResults(searchResultsMobile);
        }
    });

    function fetchSearchResults(query, resultsElement) {
        fetch(`/search?query=${query}`)
            .then(response => response.json())
            .then(results => {
                displayResults(results, resultsElement);
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
            });
    }

    function displayResults(results, resultsElement) {
        resultsElement.innerHTML = '';
        results.forEach(result => {
            const listItem = document.createElement('li');
            const link = document.createElement('a');
            link.href = result.url;
            link.textContent = `${result.title} (${result.type})`;
            listItem.appendChild(link);
            resultsElement.appendChild(listItem);
        });
        resultsElement.style.display = 'block'; // Show the results dropdown
    }

    function clearResults(resultsElement) {
        resultsElement.innerHTML = '';
        resultsElement.style.display = 'none'; // Hide the results dropdown
    }
});
