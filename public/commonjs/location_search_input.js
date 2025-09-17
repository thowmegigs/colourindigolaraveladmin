var script = document.createElement('script');
script.onload = function () {
    // var optional_config = {
    //     /* location: [28.61, 77.23], */
    //     region: "IND",
    //     height: 300,
    // };
    // new mappls.search(document.getElementById("location_search"), optional_config, callback);

    // function callback(data) {
    //     console.log(data);
    //     if (data) {
    //         var dt = data[0];
    //         if (!dt) return false;
    //         var eloc = dt.eLoc;
    //         var place = dt.placeName + ", " + dt.placeAddress;
    //         console.log('here is search');
    //         console.log(place)
    //     }
    // }
};
/***herr accces token is iused in url =b7dbea16-c842-464d-8394-2745cb0708cd**/
script.src = 'https://apis.mappls.com/advancedmaps/api/b7dbea16-c842-464d-8394-2745cb0708cd/map_sdk_plugins?v=3.0&libraries=search&libraries=nearby';

document.body.appendChild(script);
function getCurrentLocation() {

    const successCallback = (position) => {
        let lat = position.coords.latitude;
        let long = position.coords.longitude;
        fetch(`http://apis.mapmyindia.com/advancedmaps/v1/b7dbea16-c842-464d-8394-2745cb0708cd/rev_geocode?lat=${lat}&lng=${long}`).then(async function (res) {
            let re = await res.json();
            $('#full_address').val(re['results'][0]['formatted_address']);
        });


    };

    const errorCallback = (error) => {
        console.log(error);
    };

    navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
}
const llocationModal = document.getElementById(
    "locationModal"
);
if (locationModal) {
    locationModal.addEventListener("shown.bs.modal", (event) => {
        var optional_config = {
            /* location: [28.61, 77.23], */
            region: "IND",
            height: 300,
        };
        new mappls.search(document.getElementById("location_search"), optional_config, callback);

        function callback(data) {

            if (data) {
                var dt = data[0];
                if (dt['placeName'] != 'Current Location') {
                    if (!dt) return false;
                    var eloc = dt.eLoc;
                    var place = dt.placeName + ", " + dt.placeAddress;

                    $('#full_address').val(place);
                }
                else {
                    let lat = dt.latitude;
                    let long = dt.longitude;
                    fetch(`http://apis.mapmyindia.com/advancedmaps/v1/b7dbea16-c842-464d-8394-2745cb0708cd/rev_geocode?lat=${lat}&lng=${long}`).then(async function (res) {
                        let re = await res.json();
                        $('#full_address').val(re['results'][0]['formatted_address']);
                    });
                }
            }
        }
    });
}