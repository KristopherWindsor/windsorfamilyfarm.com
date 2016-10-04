// Summer Web Dev Suite (C) 2008 Kristopher Windsor

// Nav

var nav_ishiding = false, nav_ison = false, nav_timer, nav_trans;

function nav_show (item, content)
	{
	// loads new content and instantly turns dropdown on (no transition)

	var d = document.getElementById('dropdown'), li = document.getElementById('li' + item);
	var l = 0, t = 0, elem = li;

	while (elem)
		{
		l += elem.offsetLeft;
		t += elem.offsetTop;
		elem = elem.offsetParent;
		}

	d.style.top = (t + li.offsetHeight + 2) + 'px';
	d.style.left = l + 'px';
	d.innerHTML = content;

	d.style.display = 'block';
	nav_on();
	}

function nav_check ()
	{
	// test nav_ishiding because nav_off() may trigger this event twice in a row, which would start two sets of nav_hide() repeat calls
	if (!nav_ison && !nav_ishiding)
		nav_hide(true);
	}

function nav_hide (start)
	{
	// fades dropdown out

	if (nav_ison)
		{
		nav_ishiding = false;
		return;
		}

	nav_ishiding = true;

	if (start)
		nav_trans = 1;
	nav_trans -= .1

	if (nav_trans < .05)
		{
		document.getElementById('dropdown').style.display = 'none';
		nav_ishiding = false;
		}
	else
		{
		document.getElementById('dropdown').style.opacity = nav_trans;
		setTimeout('nav_hide(false)', 100);
		}
	}

function nav_on ()
	{
	// reports if the mouse is over the dropdown (or list item), in which case the dropdown does not need to be closed

	if (nav_ison) return;

	nav_ison = true;
	document.getElementById('dropdown').style.opacity = '1'; // this is changed while nav is off
	}

function nav_off ()
	{
	// reports that the nav should be off, but the results have a delayed effect from the setTimeout
	// (this gives times for nav_on() to turn the nav back on, if the mouse moves to the dropdown from the list item or VV.)

	if (!nav_ison) return;

	nav_ison = false;
	setTimeout('nav_check()', 200);
	}

// XML

var xml_object, xml_url, xml_elem;

function xml_get (url, elem)
	{
	xml_object = null;

	if (window.XMLHttpRequest)
		xml_object = new XMLHttpRequest();
	else if (window.ActiveXObject)
		xml_object = new ActiveXObject("Microsoft.XMLHTTP");

	if (xml_object != null)
		{
		document.getElementById(elem).innerHTML = 'Loading...';
		xml_object.onreadystatechange = xml_change;
		xml_object.open("GET", url, true);
		xml_object.send(null);
		xml_url = url;
		xml_elem = elem;
		}
	else
		alert("Your browser does not support XMLHTTP.");
	}

function xml_change ()
	{
	if (xml_object.readyState == 4)
		{
		if (xml_object.status == 200)
			document.getElementById(xml_elem).innerHTML = xml_object.responseText;
		else
			alert("Problem retrieving XML data: " + xml_object.status);
		}
	}

function xml_get_protected (elem)
	{
	var pass = prompt('Enter the password to access this content:', '');
	if (pass == '' || pass == null) return;

	xml_get('summer-cms/protected.php?content=' + elem + '&password=' + encodeURIComponent(pass), 'content;' + elem);
	}

function xml_get_protected_nopass (elem)
	{
	xml_get('summer-cms/protected.php?content=' + elem, 'content;' + elem);
	}

// more

function changefilename (filenumber)
	{
	var file_element, filename_element, fn;

	file_element = document.getElementById('file' + filenumber);
	filename_element = document.getElementById('filename' + filenumber);

	fn = file_element.value.toLowerCase();

	filename_element.value = fn.substr(fn.lastIndexOf("\\") + 1, fn.length);
	}

function quickinsert (type)
	{
	var c = document.getElementById('content');
	var s = document.getElementById('select_' + type);
	if (s.options[s.selectedIndex].value == '') return;
	c.value += '[' + type + ']' + s.options[s.selectedIndex].value + '[/' + type + "]\n";
	s.selectedIndex = 0;
	}

function showbbcode (content)
	{
	var bbc = document.getElementById('bbcodecontainer'), bb = document.getElementById('bbcode');

	bbc.style.display = 'block';

	bb.value = content;
	bb.focus();
	bb.select();
	}

function showmail (name, domain)
	{
	if (confirm('Do you want to email ' + name + '@' + domain + '?'))
		location.href = 'mailto:' + name + '@' + domain;
	}
