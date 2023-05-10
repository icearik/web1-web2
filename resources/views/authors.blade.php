<x-layout>
    <div class="container">
        <h1>Search for Authors</h1>
        <form id="author">
            <div class="form-group">
                <label for="author-input">Author Name:</label>
                <input type="text" class="form-control" id="author-input" name="name">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <hr>
        <div id="results"></div>
    </div>
<script>
function getBooks(key) {
 	    var authorKey = key;
            var url = "https://zhj4cycyqi.execute-api.us-east-1.amazonaws.com/default/books?key=" + authorKey;
            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    "X-AUTH-FINAL-TOKEN": "{{ session('authKey') }}"
                },
                dataType: "json",
                success: function (response) {
                    var entries = response.entries;
		    var output  = '<ul>';
                    for (var i = 0; i < entries.length; i++) {
                        var entry = entries[i];
                        var title = entry.title;
                        output+= "<li><b>" + title + "</b></li>";
                    }
		    output+='</ul>';
		    $('#results').html(output);
                },
                error: function (xhr, status, error) {
                    alert("Error: " + xhr.responseText);
                }
            });
}
        $(document).ready(function () {
            $('#author').submit(function (e) {
                e.preventDefault();
                var authorName = $('#author-input').val();
                $.ajax({
                    url: "{{url('/api/author')}}",
                    method: 'GET',
                    headers: {
                        "X-AUTH-FINAL-TOKEN": "{{ session('authKey') }}"
                    },
                    data: { name: authorName },
                    success: function (response) {
                        // handle successful API call
                        var authors = JSON.parse(response);
                        var output = '<ul>';
                        for (var i = 0; i < authors.docs.length; i++) {
                            //var link = "{{url('/authors/{key}')}}".replace('{key}', authors.docs[i].key);
                            output += '<li><a href="#" onclick=\'getBooks("' + authors.docs[i].key +
						'")\'>' + authors.docs[i].name + '</a></li>';
                        }
                        output += '</ul>';
                        $('#results').html(output);
                    },
                    error: function (xhr) {
                        // handle API call error
                        $('#results').html('<p>Error: ' + xhr.statusText + '</p>');
                    }
                });
            });
        });
    </script>
</x-layout>

