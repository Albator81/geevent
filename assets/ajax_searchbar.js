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
                const htmlContent = data.map(element => {
                    
                    const eventId = element.id;
                    const title = element.title;
                    const type = element.type;
                    
                    const dateObj = new Date(element.start_datetime.date);
                    formattedDate = dateObj.toLocaleDateString('fr-FR', { 
                        weekday: 'short', 
                        day: '2-digit', 
                        month: '2-digit', 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });

                    return `
                        <a class="dropdown-item py-3 px-3 text-wrap" href="/events/${eventId}">
                            <div class="fw-medium mb-2">${title}</div>
                            <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">
                                <i class="bi bi-tag-fill"></i> ${type}
                            </span>
                            <span><i class="bi bi-clock-fill me-1"></i>${formattedDate}</span>
                            </div>
                        </a>`;
                }).join('');
                
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
