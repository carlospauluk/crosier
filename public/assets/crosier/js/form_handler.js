$(document).ready(function() {
	maskDateTimes();
	maskMoneys();
	maskDecs();
	maskCPF_CNPJ();
});

function maskDateTimes() {
	$('.crsr-date').mask('00/00/0000', {
		clearIfNotMatch : true,
		selectOnFocus : true
	});
	$('.crsr-datetime').mask('00/00/0000 00:00:00', {
		clearIfNotMatch : true,
		selectOnFocus : true
	});
}

function maskMoneys() {
	$(".crsr-money").each(function() {
		var value;
		value = $(this).html();

		$(this).val(value);
		$(this).maskMoney({
			prefix : '',
			thousands : '.',
			decimal : ',',
			affixesStay : true,
			precision : 2
		});
		$(this).maskMoney('mask');

		// if ($(this).is('td')) {
		$(this).html($(this).val());
		// } else {
		// $(this).val(formatted);
		// }
	});
}

function maskDecs() {

	$(".crsr-dec2").maskMoney({
		prefix : '',
		thousands : '.',
		decimal : ',',
		affixesStay : true,
		precision : 2
	});
	$(".crsr-dec3").maskMoney({
		prefix : '',
		thousands : '.',
		decimal : ',',
		affixesStay : true,
		precision : 3
	});
	$(".crsr-dec4").maskMoney({
		prefix : '',
		thousands : '.',
		decimal : ',',
		affixesStay : true,
		precision : 4
	});

}

function maskCPF_CNPJ() {
	$('.cpf').mask('000.000.000-00', {
		clearIfNotMatch : true,
		selectOnFocus : true
	});
	$('.cnpj').mask('00.000.000/0000-00', {
		clearIfNotMatch : true,
		selectOnFocus : true
	});
}
