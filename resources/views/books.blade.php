<x-layout>
<h1>Books</h1>
<a href="{{url('/authors')}}">back</a>
    <ul id="book-list"></ul>
    <script>
        $(document).ready(function() {
            var authorKey = "{{ $authorKey }}";
            var url = "https://zhj4cycyqi.execute-api.us-east-1.amazonaws.com/default/books?key=" + authorKey;
            $.ajax({
	    url: url,
		method: 'GET',
		headers: {
                    "X-AUTH-FINAL-TOKEN": "{{ session('authKey') }}"
                },
		dataType: "json",
                success: function(response) {
                    var entries = response.entries;
                    var bookList = $("#book-list");
                    for (var i = 0; i < entries.length; i++) {
                        var entry = entries[i];
                        var title = entry.title;
                        var listItem = "<li><b>" + title + "</b></li>";
                        bookList.append(listItem);
                    }
                },
                error: function(xhr, status, error) {
			alert("Error: " + xhr.responseText);
                }
            });
        });
    </script>
</x-layout>
