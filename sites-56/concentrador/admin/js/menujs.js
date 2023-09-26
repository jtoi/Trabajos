function redirect(url)
{
	xajax_cargaTiempo('regionCentral');
	xajax_evaluaUrl(url);
	return false;
}
function showId(id)
{
	var obj = document.getElementById(id);
	obj.style.display = 'block';
	return false;
}
function hideId(id)
{
	var obj = document.getElementById(id);
	obj.style.display = 'none';
	return false;
}
