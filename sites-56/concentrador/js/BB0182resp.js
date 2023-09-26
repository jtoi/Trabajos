/*! Developed by Julio Loayza at Ilios.org for BBVA. October 2013. */

/* ···  ··· */



/* ···  ··· */

$(document).ready(function() {

	//
	// Tooltips
	//
	
	if ($('.tooltip').length) {
	
		$('a.tooltip').on('click', function(e) {
			
			e.preventDefault();
			
		});
	
		$('a.tooltip').tooltip({
			position: {
				my: 'center top+5',
				at: 'center bottom'
			},
			tooltipClass: 'tip'
		});
		
		$('dt.tooltip').tooltip({
			position: {
				my: 'center top+5',
				at: 'center bottom'
			},
			tooltipClass: 'tip'
		});
		
		$('dd.tooltip').tooltip({
			position: {
				my: 'center top+5',
				at: 'center bottom'
			},
			tooltipClass: 'tip'
		});
		
		$('input.tooltip').tooltip({
			position: {
				my: 'left top+1',
				at: 'left bottom'
			},
			tooltipClass: 'form-help'
		});
		
	}



	//
	// Placeholder
	//
	
	if (!Modernizr.input.placeholder) {
			
		$('[placeholder][type="text"]').each(function() {
			var input = $(this);
			input.val() == input.attr('placeholder');
		});
		
		$('[placeholder][type="text"]').focus(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
				input.val('');
				input.removeClass('placeholder');
			}
		}).blur(function() {
			var input = $(this);
			if (input.val() == '' || input.val() == input.attr('placeholder')) {
				input.addClass('placeholder');
				input.val(input.attr('placeholder'));
			}
		}).blur();
		
		$('[placeholder][type="text"]').parents('form').submit(function() {
			$(this).find('[placeholder]').each(function() {
				var input = $(this);
				if (input.val() == input.attr('placeholder')) {
					input.val('');
				}
			})
		});
		
	}



	//
	// Print
	//

	$('.print a').on('click', function(e) {
		
		e.preventDefault();

		window.print();
		
	});
	
	
	
	//
	// Other payment options
	//
	
	if ($('#formModalidad').length) {
	
		var $payment_options = $('#formModalidad');

		//
		// Disable form submit
		//
		
		$payment_options.find('input[type="button"]').attr('disabled','disabled');
	
		//
		// Transform accesible version to styled version
		//
	
		var $new_options_list = $('<ul/>');
		var $purchase_info = $('.purchase-info');
		var $payment_form = $('#formTarjeta');
		
		$('#formModalidad .options li').each(function() {
		
			$anchor = $(this).find('a');
			$image = $(this).find('img');
			
			$('<li/>')
			.append(
				$('<input/>')
				.attr({
					'type':'radio',
					'name': 'Ds_Merchant_PayMethod',
					'value': $anchor.attr('href'),
					'id': $image.data('id')
				})
			)
			.append(
				$('<label/>')
				.attr({
					'title': $image.attr('title'),
					'for': $image.data('id')
				})
				.append($image)
			)
			.appendTo($new_options_list);
			
		});
		
		$('#formModalidad .options').empty().append($new_options_list);
		
		$payment_options.on('click', 'input[type="radio"]', function() {
					
			$payment_options.removeClass('disabled');
			$payment_options.find('input[type="button"]').removeAttr('disabled');
			$payment_form.addClass('disabled');
			
		});
		
		//
		// Blur other payment methods when payment with card is being used
		//

		$('#formTarjeta').on('focus', 'input[type="text"]', function() {
			
			$payment_options.addClass('disabled');
			$payment_form.removeClass('disabled');
			
		});
		
		//
		// Match purchase info height to right column height for aesthetic reasons
		//
		
		$purchase_info.height(
		
			$('.main.section').height() - (($purchase_info.outerHeight() - $purchase_info.height()) / 2)
		
		);

		//
		// Fix for IE 8 bug
		//
		
		if ($('html').hasClass('lt-ie9')) {
		
			$('label').on('click', 'img', function() {
				
				$('#' + $(this).parents('label').attr('for')).click();
				$form = $(this).parents('form');
				
				$form.removeClass('disabled');
				$form.find('input[type="button"]').removeAttr('disabled');

				$payment_form.addClass('disabled');
				
			});
			
		}

	}
	
});

