<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
		<link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
		<title>@yield('title')</title>
	</head>
	<body>
		<main class="container-fluid h-100">
			@yield('content')
		</main>

		<script src="{{ asset('js/app.js') }}"></script>
	</body>
</html>
