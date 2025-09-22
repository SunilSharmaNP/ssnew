// Search functionality

// Real-time search suggestions (to be implemented later)
function initializeSearch() {
    const searchInputs = document.querySelectorAll('input[name="search"]');
    
    searchInputs.forEach(input => {
        let searchTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    // Future: Implement AJAX search suggestions
                    console.log('Searching for:', query);
                }, 300);
            }
        });
    });
}

// Initialize search on page load
document.addEventListener('DOMContentLoaded', initializeSearch);