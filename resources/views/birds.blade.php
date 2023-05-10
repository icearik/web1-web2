<x-layout>
<script>
function getCalls() {
    $.ajax({
        url: 'https://y8229boeqg.execute-api.us-east-1.amazonaws.com/prod/calls',
        method: 'GET',
        headers: {
            "X-AUTH-FINAL-TOKEN": "{{ session('authKey') }}"
        },
        success: function (response) {
            $('#callCount').html('Calls Made: ' + response.calls);
        },
        error: function (xhr, status, error) {
            alert(xhr.responseJSON.message);
        }
    });
}
$(document).ready(function () {
    $('form').submit(function (event) {
        event.preventDefault();
        let regionName = $('#regionName').val();
        $.ajax({
            url: 'https://y8229boeqg.execute-api.us-east-1.amazonaws.com/prod/api?region=' + regionName,
            method: 'GET',
            headers: {
                "X-AUTH-FINAL-TOKEN": "{{ session('authKey') }}"
            },
            success: function (response) {
                let sightings = response.slice(0, 10);
                let html = '<table><tr><th>Species Name</th><th>Sighting</th></tr>';
                for (let i = 0; i < sightings.length; i++) {
                    let sighting = sightings[i];
                    html += '<tr><td>' + sighting.comName + '</td><td>' + sighting.locName + '</td></tr>'
                }
                html += '</table>';
                $('#sightings').html(html);
                getCalls();
            },
            error: function (xhr, status, error) {
                alert(xhr.responseJSON.message);
            }
        });

    });
});
</script>
<h1>eBird API</h1>
	<form id="birdSightingsForm">
	<label for="regionName">Region Name:</label>
	<input type="text" id="regionName" name="regionName"><br><br>
	<input type="submit" value="Submit">
	</form>
<div id="sightings"></div>
<br><br><br><br><br>
<div id="callCount"></div>
</x-layout>
