// Script pour la carte
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('maCarte').setView([46.2052, 5.2275], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
        maxZoom: 20,
        minZoom: 1,
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var customIcon = L.icon({
        iconUrl: '../images/Dajun-icon.png',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });
    // Pour afficher les POI sur la carte
    fetch('/api/pois')
        .then(response => response.json())
        .then(data => {
            data.forEach(poi => {
                var marker = L.marker([poi.lat, poi.long], {icon: customIcon})
                    .bindPopup(`<b>${poi.name}</b><br>${poi.description}<br><img src="${poi.picture}" alt="Photo de ${poi.name}" style="width:100%;">`);
                marker.addTo(map);
            });
        })
        .catch(error => console.error('Error loading the POI data: ', error));

    map.on('click', function(e) {
        var coord = e.latlng;
        var lat = coord.lat;
        var lng = coord.lng;

        // Appel API pour récupérer les images & les afficher dans le formulaire
        fetch('/api/images')
            .then(response => response.json())
            .then(images => {
                var options = images.map(image => `<option value="../images/${image}">${image}</option>`).join('');

                var popupContent = `<form id="createPoiForm">
                Nom: <input type="text" id="name" required><br>
                Description: <textarea id="description" required></textarea><br>
                Teaser: <input type="text" id="teaser"><br>
                Image: <select id="picture">${options}</select><br>
                <input type="button" value="Enregistrer POI" onclick="submitPoi(${lat}, ${lng})">
            </form>`;

                var popup = L.popup()
                    .setLatLng(coord)
                    .setContent(popupContent)
                    .openOn(map);
            })
            .catch(error => console.error('Error loading the images: ', error));
    });

    window.submitPoi = function(lat, lng) {
        var name = document.getElementById('name').value;
        var description = document.getElementById('description').value;
        var teaser = document.getElementById('teaser').value;
        var picture = document.getElementById('picture').value;

        if (!name || !description) {
            alert('Veuillez remplir tous les champs requis.');
            return;
        }

        fetch('/api/pois/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name, description: description, teaser: teaser, lat: lat, long: lng, picture: picture })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    L.marker([lat, lng], { icon: customIcon, title: name })
                        .bindPopup(`<strong>${name}</strong><br>${description}<br><img src="${picture}" alt="Photo de ${name}" style="width:100%;">`)
                        .addTo(map);
                    map.closePopup();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la soumission du POI.');
            });
    };
});
