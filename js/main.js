document.addEventListener('DOMContentLoaded', function() {
  var initialSearchOption = document.querySelector('input[name="searchOption"]:checked').value;
  updateDistanceSettingsVisibility(initialSearchOption);
});

document.getElementById('searchButton').addEventListener('click', function() {
  var query = document.getElementById('search').value;
  var searchOption = document.querySelector('input[name="searchOption"]:checked').value;

  if (query) {
    getUserLocation(query, searchOption);
  }
});

document.querySelectorAll('input[name="searchOption"]').forEach(function(input) {
  input.addEventListener('change', function() {
    var searchOption = this.value;
    updateDistanceSettingsVisibility(searchOption);
  });
});

function getUserLocation(query, searchOption) {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      console.log('Latitude: ' + position.coords.latitude + ', Longitude: ' + position.coords.longitude);
      searchPlaces(query, position.coords.latitude, position.coords.longitude, searchOption);
    }, function() {
      alert('Geolocation service failed.');
    });
  } else {
    alert('Geolocation is not supported by this browser.');
  }
}

function searchPlaces(query, lat, lng, searchOption) {
  var radiusKm = parseFloat(document.getElementById('distance-input').value) || 5; // Valor por defecto si no se ingresa distancia
  var radiusMeters = radiusKm * 1000; // Convertir kilómetros a metros
  var service = new google.maps.places.PlacesService(document.createElement('div'));
  var location = new google.maps.LatLng(lat, lng);

  var request = {
    location: location,
    radius: radiusMeters,
    keyword: query,
    type: ['restaurant', 'cafe', 'movie_theater', 'amusement_park'] // Define tipos si es necesario
  };

  console.log('Request:', request);

  service.nearbySearch(request, function(results, status) {
    if (status === google.maps.places.PlacesServiceStatus.OK) {
      var filteredResults;

      // Siempre se filtra por distancia, y solo se ordena por calificación si está seleccionada la opción 'rating'
      if (searchOption === 'rating') {
        filteredResults = results
          .filter(place => place.rating) // Filtra los que tienen calificación
          .sort((a, b) => b.rating - a.rating) // Ordena por calificación de mayor a menor
          .slice(0, 3); // Selecciona los 3 mejores resultados
      } else if (searchOption === 'distance') {
        filteredResults = results
          .sort((a, b) => {
            var distanceA = calculateDistance(lat, lng, a.geometry.location.lat(), a.geometry.location.lng());
            var distanceB = calculateDistance(lat, lng, b.geometry.location.lat(), b.geometry.location.lng());
            return distanceA - distanceB; // Ordena por distancia de menor a mayor
          })
          .slice(0, 3); // Selecciona los 3 mejores resultados
      }

      displayResults(filteredResults, lat, lng);
    } else {
      document.getElementById('results').innerText = 'No results found';
    }
  });
}

function displayResults(places, userLat, userLng) {
  var resultsDiv = document.getElementById('results');
  resultsDiv.innerHTML = '';

  places.forEach(function(place) {
    var placeDiv = document.createElement('div');
    placeDiv.classList.add('place');

    var name = document.createElement('h2');
    name.textContent = place.name;
    placeDiv.appendChild(name);

    if (place.rating) {
      var rating = document.createElement('p');
      rating.textContent = 'Rating: ' + place.rating;
      placeDiv.appendChild(rating);
    }

    var distance = calculateDistance(userLat, userLng, place.geometry.location.lat(), place.geometry.location.lng());
    var distanceText = document.createElement('p');
    distanceText.textContent = 'Distance: ' + distance.toFixed(2) + ' km';
    placeDiv.appendChild(distanceText);

    if (place.vicinity) {
      var address = document.createElement('p');
      address.textContent = 'Address: ' + place.vicinity;
      placeDiv.appendChild(address);
    }

    resultsDiv.appendChild(placeDiv);
  });
}

function calculateDistance(lat1, lng1, lat2, lng2) {
  var R = 6371; // Radius of the Earth in km
  var dLat = (lat2 - lat1) * Math.PI / 180;
  var dLng = (lng2 - lng1) * Math.PI / 180;
  var a = 
    0.5 - Math.cos(dLat) / 2 + 
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
    (1 - Math.cos(dLng)) / 2;

  return R * 2 * Math.asin(Math.sqrt(a));
}
