<x-layout>
<h2>Welcome, {{cas()->user()}}</h2>
<p><a href={{url('/profile')}}>/profile</a></p>
<p><a href={{url('/numbers')}}>/numbers</a></p>
<p><a href={{url('/authors')}}>/authors</a></p>
<p><a href={{url('/api')}}>/api</a></p>
<script>
    $(document).ready(function() {
        $.ajax({
            type: 'GET',
            url: "{{url('/auth')}}",
            success: function() {
                // do nothing on success
            },
            error: function() {
                alert('Unexpected error, couldn\'t assign a token');
            }
        });
    });
</script>
</x-layout>
