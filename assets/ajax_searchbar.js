const zoneRecherche = document.getElementById("search");
const resultatsRecharche = document.getElementById("results");

zoneRecherche.onkeydown = () => {
    resultatsRecharche.innerHTML = "";

    fetch(`/events/search?search=${encodeURIComponent(zoneRecherche.value)}`)
        .then((response) => {
            if (!response.ok) {
                console.error("erreur, c'est pas ok : ", response.status);
            }
            return response.json(); 
        })
        .then((data) => {
            //console.log(data);
            data.forEach(element => {
                resultatsRecharche.innerHTML += `<a class="search-result-item" href="/events/${element["id"]}">${element["title"]}</a><br>`;
            });
        })
        .catch((error) => {
            console.error("Oh non, une erreure : ", error);
        });
};