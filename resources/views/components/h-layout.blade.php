<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
	<!-- JQuery -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
	<style>
.center{margin: auto;  
width: 50%;
  padding: 0 10px;
  text-align:center;
justify-content:center;}
a:link    { color: blue; }
a:visited { color: blue; }
</style>
    </head>
<body>
<div class="center">
<a href={{url('/')}}>Home</a>
<a href={{url('/logout')}}>Logout</a>
{{$slot}}
</div>
</body>
</html>

