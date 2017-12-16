	var temporizador = setInterval("verificar()", 10000);
	var verificado = 0;

	$(function(){
		verificar();
	});

	function verificar(){
		if(verificado === 0) {
			$.ajax({
				type: 'POST',
		        url:  '/Area-Restrita/Pedido/BuscarDocumentos',
		        data: 'codigo-pedido='+$('#codigo-pedido').val(),
		        dataType: 'json',
		        beforeSend: function(){
					$('#exibeDocumentos').html("<div class=\"alert alert-info\" role=\"alert\"><img alt=\"carregando...\" src=\"/Public/img/loader.gif\" />"
						+ "<span class=\"textoHeader\">Aguarde... Verificando documentos do pedido</span></div>");
		        }
			}).done(function(dado){
				if(Array.isArray(dado)){
					if (dado.length > 0) {
						$('#exibeDocumentos').html("");
						addNaDiv(dado);
						verificado = 1;
					}
				};
			}).fail(function(){
				$('#exibeDocumentos').html("<div class=\"alert alert-danger\" role=\"alert\"><span class=\"textoHeader\">Não Foi Possível verificar os documentos do pedido.</span>"
					+ "<br /><a role=\"button\" class=\"btn btn-danger btn-lg\" href=\"javascript:verificar()\">Tentar Novamente</a></div>");
			});
		} else {
			$.ajax({
				type: 'POST',
				url:  '/Area-Restrita/Pedido/BuscarMaisDocumentos',
				data: 'codigo-pedido='+$('#codigo-pedido').val(),
				dataType: 'json',
				beforeSend: function(){
					if($('#conteudo div.verificando-pedido').length > 0)
						$('#conteudo div.verificando-pedido').fadeIn();
					else {
						var div_wait = "<div class=\"verificando-pedido text-center\"><img alt=\"carregando...\" src=\"/Public/img/loader.gif\" />Verificando se há mais arquivos...</div>";
						$(div_wait).insertAfter('#exibeDocumentos');
					}
				}
			}).done(function(dado){
				if(Array.isArray(dado)){
					if (dado.length > 0) {
						addNaDiv(dado);
					}
				};
			}).always(function(){
				$('#conteudo div.verificando-pedido').fadeOut();
			});
		}
	}

	function addNaDiv(dado) {
		for (i = dado.length-1; i >= 0; i--) {
			if (dado[i].tipo == "01") {
				$('#exibeDocumentos').append("<div class=\"alert alert-success\" role=\"alert\">"
			    	+"Arquivo PDF da Nota Fiscal: <a role=\"button\" target=\"blank\" data-arquivo=\""+dado[i].arquivo+"\" class=\"btn btn-info btn-lg\" href=\"/Public/arquivos/pdfs/"+dado[i].arquivo+"\">Visualizar Documento<a></div>");
			} else{
				$('#exibeDocumentos').append("<div class=\"alert alert-success\" role=\"alert\">"
			    	+"Arquivo do Boleto Nº "+(dado[i].tipo-1)+": <a role=\"button\" data-arquivo=\""+dado[i].arquivo+"\" class=\"btn btn-info btn-lg\" target=\"blank\" href=\"/Public/arquivos/pdfs/"+dado[i].arquivo+"\">Visualizar Documento<a></div>");
			};
		};
	}
