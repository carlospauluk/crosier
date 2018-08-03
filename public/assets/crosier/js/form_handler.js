$(document).ready(function() {
	maskDateTimes();
	maskMoneys();
	maskDecs();
	maskCPF_CNPJ();
	maskTelefone9digitos();
	maskCEP();
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
		
		$(this).maskMoney({
			prefix : '',
			thousands : '.',
			decimal : ',',
			affixesStay : true,
			precision : 2
		});
		$(this).maskMoney('mask');

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

function maskCEP() {
	$('.cep').mask('00000-000', {
		clearIfNotMatch : true,
		selectOnFocus : true
	});
}

function maskTelefone9digitos() {
	
	// http://igorescobar.github.io/jQuery-Mask-Plugin/docs.html
	var SPMaskBehavior = function (val) {
	  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	},
	spOptions = {
	  onKeyPress: function(val, e, field, options) {
	      field.mask(SPMaskBehavior.apply({}, arguments), options);
	    }
	};

	$('.telefone').mask(SPMaskBehavior, spOptions);
	
}
