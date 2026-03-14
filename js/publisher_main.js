document.addEventListener('DOMContentLoaded', function() {
  fetchUserPlaces();
});

function fetchUserPlaces() {
  fetch('../php/get_places.php')
      .then(response => response.json())
      .then(data => {
          const placesContainer = document.getElementById('userPlaces');
          placesContainer.innerHTML = '';
          data.places.forEach(place => {
              const placeDiv = document.createElement('div');
              placeDiv.classList.add('place-item');
              placeDiv.innerHTML = `
                  <h3>${place.name}</h3>
                  <p>${place.address}</p>
                  <p>${place.description}</p>
                  <p>Distance: ${place.distance} km</p>
                  <button onclick="deletePlace(${place.id})">Delete</button>
              `;
              placesContainer.appendChild(placeDiv);
          });
      })
      .catch(error => console.error('Error fetching user places:', error));
}

function deletePlace(placeId) {
  fetch('../php/delete_place.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: placeId }),
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          alert('Place deleted successfully.');
          window.location.reload();
      } else {
          alert('Error deleting place.');
      }
  })
  .catch(error => console.error('Error deleting place:', error));
}
