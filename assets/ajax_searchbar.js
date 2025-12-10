document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById("search");
    const resultsDiv = document.getElementById("results");
    const searchApiUrl = '/events/search';
    
    const debounce = (func, delay) => {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), delay);
        };
    };

    const toggleResults = (show, content = '') => {
        resultsDiv.innerHTML = content;
        resultsDiv.classList.toggle('show', show);
    };

    const executeSearch = async () => {
        const query = searchInput.value.trim();

        if (query.length < 3) {
            toggleResults(false);
            return;
        }

        try {
            const response = await fetch(`${searchApiUrl}?search=${encodeURIComponent(query)}`);
            
            if (!response.ok) {
                console.error("Erreur, la requête n'est pas OK : ", response.status);
                toggleResults(false);
                return;
            }

            const data = await response.json();
            
            if (data && data.length > 0) {
                const htmlContent = data.map(element => `<a class="dropdown-item" href="/events/${element.id}">${element.title}</a>`).join('');
                
                toggleResults(true, htmlContent);
            } else {
                toggleResults(true, '<span class="dropdown-item disabled">Aucun événement trouvé</span>');
            }

        } catch (error) {
            console.error("Erreur AJAX : ", error);
            toggleResults(false);
        }
    };

    searchInput.addEventListener('input', debounce(executeSearch, 300));

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            toggleResults(false);
        }
    });
});