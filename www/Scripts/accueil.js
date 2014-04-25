function accueil_scrollGalerie()
{
	var galerie;
	var photos;
	var i;
	
	galerie = document.getElementById("galerieAccueil");
	if (galerie)
	{
		photos = galerie.getElementsByTagName("span");
		if (photos)
		{
			for (i = 0; i < photos.length; i++)
			{
				if (photos[i].style.display != "none")
				{
					photos[i].style.display = "none";
					if  (i < (photos.length - 1))
						photos[i + 1].style.display = "block";
					else
						photos[0].style.display = "block";
					window.setTimeout("accueil_scrollGalerie()", 4000);
					break;
				}
			}
		}
	}
}

window.setTimeout("accueil_scrollGalerie()", 4000);

function accueil_goTo(url)
{
	var xhr = new XMLHttpRequest();
	var main;
	var reg = /\?/;
	
	main = document.getElementById('main');
	if (xhr && main)
	{
		xhr.addEventListener
		(
			'readystatechange'
			, function()
			{
				if (xhr.readyState == 4)
				{
					switch (xhr.status)
					{
						case 0:
							return false;
							break;
						case 200:
							main.innerHTML = xhr.responseText;
							clients_updatePageBlanche();
							return false;
							break;
						case 500:
							alert("Erreur lors du chargement de la page.");
							break;
						default:
							alert("Erreur lors de l'envoi (" + xhr.status + ")");
							break;
					}
				}		
			}
			, true
		);
		if (reg.exec(url) == null)
			url += "?methode=ajax"
		else
			url += "&methode=ajax"
		xhr.open("GET", url, true);
		xhr.send();
	}
	else
		window.location = url;
	return false;
}


function clients_updatePageBlanche()
{
	var div
	var centre;
	var menus;
	var footer;
	var pageBlanche;
	var overflow;
	var fichePlat;
	var restore = false;
	var paddingTop = 0;

	centre = document.getElementById('centre');
	menus = document.getElementById('css3menu1');
	footer = document.querySelector('footer');
	div = document.createElement('div');
	document.body.appendChild(div);
	div.style.position = 'fixed';
	div.style.bottom = 0;
	pageBlanche = document.getElementById('pageBlanche');
	if (pageBlanche && footer)
			pageBlanche.style.height = div.offsetTop - footer.offsetHeight - footer.offsetTop + 'px';
}

function clients_initPageBlanche()
{
	window.onresize = clients_updatePageBlanche;
	clients_updatePageBlanche();
}