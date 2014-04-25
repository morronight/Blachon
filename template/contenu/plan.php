<article id="accueilContenu">
	<h1 style="margin: 15px 0;">Coordonnées</h1>
		<section itemprop="address" itemscope="" itemtype="http://data-vocabulary.org/Address" style="color:#767676"><span >Cave Sebastien Blachon</span><br><span itemprop="street-address">16 chemin de margiriat</span><br><span itemprop="postal-code">07300</span> <span itemprop="locality">Saint Jean de Muzols</span></section>
		<section style="color:#767676">Téléphone <span itemprop="tel">+33 6 51 30 63 18</span></section>
		<?php 
	$key = Configuration::$Google['GoogleMap']['Maps'];
	$mapZoom = Configuration::$Google['GoogleMap']['MapZoom'];
	$mapPos = Configuration::$Google['GoogleMap']['MapPos'];
	if(isset($key)){ ?>
	<section id="googleMap">
	<h1 style="margin: 15px 0;">Accès</h1>
    <script type="text/javascript">
      function initialize() 
	  {
		var latLng = new google.maps.LatLng(45.084388,4.812996);
        var mapOptions = {
          center: latLng,
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map-canvas"),
            mapOptions);
			
		var marker = new google.maps.Marker({
		// Coordonnées du cinéma
		position: latLng,
		map: map,
		title: "Cave Blachon",
		icon : "Images/indicateur.png"
      });
	  
	  var contentMarker = 'Caveau ouvert sur rendez vous toute l\'année'
 
	var infoWindow = new google.maps.InfoWindow({
		content  : contentMarker,
		position : latLng
	});
	 google.maps.event.addListener(marker, 'click', function() {
    infoWindow.open(map,marker);
});
	
	  }
 google.maps.event.addDomListener(window, 'load', initialize);

    </script>
	<div id="map-canvas"/>
	</section>
	<?php } ?>
</article>