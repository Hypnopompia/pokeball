@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Dashboard</div>

				<div class="panel-body">
					<p>
						Location: {{ $pokeball->latitude }} / {{ $pokeball->longitude }}<br/>
						Updated: {{ $pokeball->updated_at->setTimezone('MST')->toDateTimeString() }}
					</p>
					<div id="map" height="600"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var map;
	function initMap() {
		map = new google.maps.Map(document.getElementById('map'), {
			center: {lat: {{ $pokeball->latitude }}, lng: {{ $pokeball->longitude }}},
			zoom: 19,
			mapTypeId: google.maps.MapTypeId.HYBRID
		});


		var pokeballLocation = new google.maps.LatLng( {{ $pokeball->latitude }}, {{ $pokeball->longitude }} );

		marker = new google.maps.Marker({
			position: pokeballLocation,
			draggable: false,
			icon: '/images/pokeball.png'
		});

		marker.setMap(map);
	}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBl4um9KiaO9JjsamW61w_pEzqj9K5q-6I&callback=initMap" async defer></script>
@endsection
