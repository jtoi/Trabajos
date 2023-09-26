function especial () {
	var tiempo = 500;
    $('#MenuIntel').slideUp(tiempo);
    $('#imgMenUp').css('display', 'none');
	setTimeout( "$('#imgMenDown').css('display', 'block');", tiempo+50);
};

//$("#imgMenDown").hover(function(){
//$('#imgMenDown').css('display', 'none');
//$('#MenuIntel').slideDown(500);
////$('#imgMenUp').css('display', 'block');
//});
//
//$("#cubreMenu").mouseout(function(){
//$('#MenuIntel').slideUp(700);
//$('#imgMenUp').css('display', 'none');
//setTimeout( "$('#imgMenDown').css('display', 'block')", 700);
//});

