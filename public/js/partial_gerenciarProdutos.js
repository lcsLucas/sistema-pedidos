$(function(){

	$('#txtPrecoNovo').maskMoney({showSymbol: false, decimal: ",", thousands: ".", defaultZero: false});

	$('#close').click(function(){//Reseta as regras de validação do formulario
	    var validator = $( "#formProd" ).validate();
	    validator.resetForm();
	});

	var links = document.querySelectorAll("a[data-alterar]");

	for(var i = 0; i < links.length; i++){
	    links[i].onclick = Alterar;
	}
});

function Alterar(){
	Limpar();
	$("#myModalLabel").text($("#myModalLabel").text()+this.getAttribute('data-descricao'));
    $('#txtCodigo').val(this.getAttribute('data-id'));
    $('#txtPrecoAtual').val(this.getAttribute('data-preco'));
}

function Limpar() {
    $("#txtCodigo").val("");
    $('#txtPrecoAtual').val("");
    $('#txtPrecoNovo').val("");
    $("#myModalLabel").text("Alterar Preço do Produto: ");
}
