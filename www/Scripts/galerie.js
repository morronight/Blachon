function galerie_selectPhoto(id)
{
	var zoomGalerie;
	var img;
	
	img = document.getElementById('image_' + parseInt(id));
	zoomGalerie = document.getElementById('zoomGalerie');
	if (zoomGalerie && img)
	{
		if (zoomGalerie.parentNode != document.body)
		{
			zoomGalerie.parentNode.removeChild(zoomGalerie);
			document.body.appendChild(zoomGalerie);
		}
		zoomGalerie.innerHTML = '<h2>' + img.alt + '</h2><img src="/Images/I' + parseInt(id) + '" alt="' + img.alt + '" title="' + img.alt + '"/>';
		zoomGalerie.style.display = 'block';
		zoomGalerie.style.height = document.body.scrollHeight + 'px';
		zoomGalerie.style.paddingTop = document.body.scrollTop + 'px';
	}
}

function galerie_fermerZoom()
{
	var zoomGalerie;
	
	zoomGalerie = document.getElementById('zoomGalerie');
	if (zoomGalerie)
		zoomGalerie.style.display = 'none';
}