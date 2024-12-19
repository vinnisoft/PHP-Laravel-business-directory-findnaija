google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
    var input = document.getElementById('address');
    var address = new google.maps.places.Autocomplete(input);
    address.addListener('place_changed', function () {
        var place = address.getPlace();
        $('#latitude').val(place.geometry['location'].lat());
        $('#longitude').val(place.geometry['location'].lng());
        var latlng = new google.maps.LatLng(place.geometry['location'].lat(), place.geometry['location'].lng());
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'latLng': latlng }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var continent = '';
                var country = '';
                var state = '';
                var city = '';
                for (var i = 0; i < results[0].address_components.length; i++) {
                    var component = results[0].address_components[i];

                    if (component.types.includes('country')) {
                        country = component.long_name;
                        getContinentFromCountryCode(component.short_name, function (continent) {
                            if (continent) {
                                console.log(continent);
                                $('#continent').val(continent);
                            }
                        });
                    } else if (component.types.includes('administrative_area_level_1')) {
                        state = component.long_name;
                    } else if (component.types.includes('locality')) {
                        city = component.long_name;
                    }
                }
                $('#country').val(country);
                $('#state').val(state);
                $('#city').val(city);
            }
        });
    });
}

function getContinentFromCountryCode(countryCode, callback) {
    const apiUrl = `https://restcountries.com/v3.1/alpha/${countryCode}`;
    fetch(apiUrl).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (Array.isArray(data) && data.length > 0) {
            const continent = data[0].region;
            if (continent !== undefined) {
                console.log(`Continent for ${countryCode}: ${continent}`);
                callback(continent);
            } else {
                console.log(`Continent is undefined for ${countryCode}`);
            }
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });
}