$(function(){
	$('#close').click(function(){//Reseta as regras de validação do formulario
	    var validator = $( "#formUsu" ).validate();
	    validator.resetForm();
	});

	$("#btnNovoAcesso").click(function () {//Limpa campos que possa está preenchido com dados anteriormente
	    Limpar();
	});

	var links = document.querySelectorAll("a[data-alterar]");

	for(var i = 0; i < links.length; i++){
	    links[i].onclick = Alterar;
	}
});

function Alterar(){
    $("#myModalLabel").text("Alterar Dados do Usuário");

    $('#txtCodigo').val(this.getAttribute('data-id'));
    $('#txtNome').val(this.getAttribute('data-nome'));
    $('#txtEmail').val(this.getAttribute('data-email'));


    if(this.getAttribute('data-status') == '1')
        $('#chkAtivo').prop("checked",true);
    else
        $('#chkAtivo').prop("checked",false);
}

function Limpar() {
    $("#txtCodigo").val("0");
    $("#txtNome").val("");
    $("#txtEmail").val("");
    $('#chkAtivo').prop("checked",false);

    $("#myModalLabel").text("Cadastrar novo usuário");
}

function Confirmar(nome, id) {
    bootbox.confirm({
        closeButton: false,
        size: 'large',
        message: "Deseja Realmente Excluir: <b>" + nome + "</b>",
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result) {
                $.ajax({
                    type: 'POST',
                    url: '/Area-Restrita/Usuario/ExcluirUsuario',
                    data: 'apagar='+id+'&token='+$("#txtToken").val(),
                    dataType: 'json'
                }).done(function(data){
                    bootbox.alert({
                        closeButton: false,
                        size: 'large',
                        message: data["Mensagem"],
                        callback: function () {
                            if (data['Tipo'] === 1)
                                location.href = "/Area-Restrita/Usuario/GerenciarUsuarios";
                        }
                    });
                }).fail(function(){
                    if($('#ModalErro').length < 1){
                        $('body').append(criaModalErro);
                    }
                    $( "#btTrigger" ).trigger( "click" );
                });
            }
        }
    });
}
