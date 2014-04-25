var client_inertialScrollY = 0;

function clients_scrollTo(id)
{
	var elm;
	var mastercarte;
	var pos = 0;
	
	elm = document.querySelector("#" + id);
	mastercarte = document.getElementById("mastercarte");
	if (elm)
	{
		pos = elm.offsetTop;
		if (mastercarte)
			pos += mastercarte.offsetTop;
		window.scroll(0, pos);
		return false;
	}
	return true;
}

function clients_updatePageBlanche()
{
	var main;
	var footer;
	var pageBlanche;
	var overflow;
	var fichePlat;
	var restore = false;
	var paddingTop = 0;

	main = document.getElementById('main');
	footer = document.querySelector('footer');
	pageBlanche = document.getElementById('pageBlanche');
	fichePlat = document.querySelector('.fichePlat');
	if (main && footer)
	{
		paddingTop = parseInt(window.getComputedStyle(main, null).getPropertyValue("padding-top"));
		if (isNaN(paddingTop))
			paddingTop = 0;
		if (main.scrollHeight == paddingTop)
		{
			overflow = main.style.overflowY;
			main.style.overflowY = "scroll";
			restore = true;
		}
		if (pageBlanche)
			pageBlanche.style.height = (footer.offsetTop - 40) + 'px';
		main.style.height = 0;
		if (fichePlat)
			main.style.height = Math.max(footer.offsetTop, Math.max(fichePlat.offsetHeight + 100, main.scrollHeight + 100)) + 'px';
		else
			main.style.height = Math.max(footer.offsetTop, main.scrollHeight + 100) + 'px';
		if (restore)
			main.style.overflowY = overflow;
	}
}

function clients_initPageBlanche()
{
	window.onresize = clients_updatePageBlanche;
	clients_updatePageBlanche();
}


function clients_detectIpad()
{
	var menuonline;
	var y;

	menuonline = document.getElementById("menuonline");
	if (navigator.userAgent.match(/iPad/i) != null)
	{
		clients_classListAdd(menuonline, "ipad");
		addEventListener('touchstart', inertialScrollYStart, false);
		addEventListener('touchmove', inertialScrollYScroll, false);
		addEventListener('touchstop', inertialScrollYStop, false);
		window.scroll(0, 0);
		function inertialScrollYStart(event)
		{
			var evt;

			if (event.touches && event.touches[0])
				evt = event.touches[0];
			else
				evt = event;
			client_inertialScrollY = 0;
			y = evt.pageY;
		}
		function inertialScrollYScroll(event)
		{
			var evt;
			
			if (event.touches && event.touches[0])
				evt = event.touches[0];
			else
				evt = event;
			client_inertialScrollY = evt.pageY - y;
			if (typeof carteClient_onScroll == 'function')
				carteClient_onScroll();
		}
		function inertialScrollYStop(event)
		{
			client_inertialScrollY = 0;
			y = 0;
		}
	}
}
