	$(document).ready(function() {
  		$('#txtPrecoNovo').maskMoney({showSymbol: false, decimal: ",", thousands: ".", defaultZero: false});

		var links = document.querySelectorAll("a[data-alterar]");

		for(var i = 0; i < links.length; i++){
		    links[i].onclick = Alterar;
		}

		$('#selProduto').change(function(){
			$('.mostrar').fadeOut("fast");
			var option = $(this).prop('selectedIndex');
			if(option > 0) {
				$('#txtPrecoAtual').val($('#selProduto option:selected').attr("data-preco"));
				$.ajax({
					type: 'POST',
	                url:  '/Area-Restrita/Cliente/RecProduto',
	                data: 'recProd='+$(this).val()+"&cli="+$('#txtCodigo').val()+"&token="+$('#txtToken').val(),
	                dataType: 'json',
	                beforeSend: function(){
	                	$('#selProduto').prop('disabled', true);
	                }
				}).done(function(data){
	                if (data["Tipo"] === 1 && data["Mensagem"].length > 0){
	                	$('#txtPrecoDef').val(data["Mensagem"]);
	                	$('.mostrar').fadeIn("fast");
	                }
	            }).fail(function(){
	                if($('#ModalErro').length < 1){
	                    $('body').append(criaModalErro);
	                }
	                $( "#btTrigger" ).trigger( "click" );
	            });//ajax
	            $('#selProduto').prop('disabled', false);
			} else {
				$('#txtPrecoAtual').val("");
				$('#txtPrecoDef').val("");
			}
		});

		$('#btnRemover').click(function(){
			bootbox.confirm({
				closeButton: false,
				size: 'large',
				message: "Deseja Restaurar o Preço Desse Produto?",
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
				            url:  '/Area-Restrita/Cliente/RemProduto',
				            data: 'remProd='+$('#selProduto').val()+"&cli="+$('#txtCodigo').val()+"&token="+$('#txtToken').val(),
				            dataType: 'json'
						}).done(function(data){
			                bootbox.alert({
			                	closeButton: false,
			                	size: 'large',
			                    message: data["Mensagem"],
			                    callback: function () {
			                        if (data["Tipo"] === 1)
			                            location.reload();
			                    }
			                });
			            }).fail(function(){
			                if($('#ModalErro').length < 1){
			                    $('body').append(criaModalErro);
			                }
			                $( "#btTrigger" ).trigger( "click" );
			            });//ajax;
					}
				}
			});
		});
	});

	function Alterar(){
		Limpar();
		$("#myModalLabel").text($("#myModalLabel").text()+this.getAttribute('data-nome'));
	    $('#txtCodigo').val(this.getAttribute('data-id'));
	}

	function Limpar() {
	    $("#txtCodigo").val("");
	    $('#txtPrecoAtual').val("");
	    $('#txtPrecoNovo').val("");
	    $('#selProduto').val("").change();
	    $("#myModalLabel").text("Mudar Preço de Produto para o ");
	}
