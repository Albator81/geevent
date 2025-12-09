const zoneRecherche = document.getElementById("search");
const resultatsRecharche = document.getElementById("results");

zoneRecherche.addEventListener("keyup", () => {
    const query = zoneRecherche.value.trim();
    resultatsRecharche.innerHTML = "";

    if (query.length < 2) {
        resultatsRecharche.classList.remove("show");
        return;
    }

    fetch(`/events/search?search=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) console.error("Erreur :", response.status);
            return response.json();
        })
        .then(data => {
            if (data.length === 0) {
                resultatsRecharche.classList.remove("show");
                return;
            }

            data.forEach(element => {
                resultatsRecharche.innerHTML += `
                    <a class="dropdown-item" href="/events/${element["id"]}">
                        ${element["title"]}
                    </a>
                `;
            });

            resultatsRecharche.classList.add("show");
        })
        .catch(error => {
            console.error("Erreur fetch :", error);
        });
});
