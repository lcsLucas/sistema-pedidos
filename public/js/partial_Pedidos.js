	$(function(){
		var prod = $('#selProduto');
		var vUni = $('#txtValUni');
		var qtde = $('#txtQtde');
		var subT = $('#txtSubTot');
                var monet = $('#txtMonetario').val();

		if ($('#selCliente').prop('selectedIndex') > 0) {
                    $( "#btnConfCliente" ).trigger( "click" );
                };
                
                if(monet === "1") {//valor monetario
                    $('#parteProduto').on('select2:select', function (evt) {
                            qtde.val('');
                            subT.val('');
                            if (prod.prop('selectedIndex') > 0) {
                                    vUni.val(prod.select2().find(":selected").data("preco"));
                                    qtde.focus();
                            } else{
                                    vUni.val('');
                            }
                    });

                    qtde.keyup(function () {  //validações cada vez que o usuario digita algo no campo Qtde
                        conteudo = apenasNumeros($(this).val());
                        $(this).val(conteudo);
                        var calc = 0.00;
                        var uni = vUni.val();
                        uni = uni.replace(".","").replace(",",".");
                        calc = conteudo * uni;
                        subT.val(number_format(calc, 2, ',', '.'));
                    });
                    qtde.blur(function () {
                        if (isNaN($(this).val())) {
                            $(this).val("");
                            subT.val("");
                        }
                    });
                } else {//sem valor monetario
                    $('#parteProduto').on('select2:select', function (evt) {
                        qtde.val('');
                        if (prod.prop('selectedIndex') > 0) {
                                qtde.focus();
                        }
                    });

                    qtde.keyup(function () {  //validações cada vez que o usuario digita algo no campo Qtde
                        conteudo = apenasNumeros($(this).val());
                        $(this).val(conteudo);
                    });
                    qtde.blur(function () {
                        if (isNaN($(this).val())) {
                            $(this).val("");
                        }
                    });
                }
                
	    $('#tabela-pedidos').on('click', 'a', function () {
	    	if($(this).hasClass('link-excluir'))
	    	{
	    		$.ajax({
                	type: 'POST',
                	url:  $(this).attr('href'),
                	dataType: 'json',
                	beforeSend: function(){
	                    $('#tabela-pedidos a').css("pointer-events","none");
	                    $('#addProduto, #btnEnviarPed').prop("disabled",true);
                	}
	    		}).done(function(data){
	    			if (data["Tipo"] === 1) {
	                    $('#tabela-pedidos a').css("pointer-events","visible");
	                    $('#addProduto, #btnEnviarPed').prop("disabled",false);
	    				$('#tabela-pedidos tbody')
	    				.html("<tr><td colspan=\"6\">Nenhum Produto Adicionado</td></tr>");
	    				$('#valorTotal').text("0,00");
	    			}
	    		}).fail(function(){
	                if($('#ModalErro').length < 1){
	                    $('body').append(criaModalErro);
	                }
	                $( "#btTrigger" ).trigger( "click" );
            	});
	    	}
	    	else{
	    		$.ajax({
                	type: 'POST',
                	url:  $(this).attr('href'),
                	data: 'codProduto='+$(this).data('id'),
                	dataType: 'json',
                	beforeSend: function(){
	                    $('#tabela-pedidos a').css("pointer-events","none");
	                    $('#addProduto, #btnEnviarPed').prop("disabled",true);
	                    $('#tabela-pedidos tbody').html("<tr><td colspan=\"5\"><img src=\"/Public/img/loader.gif\" /></td></tr>");
                	}
	    		}).done(function(dado){
	    			$('#tabela-pedidos tbody').html("");
					if(dado.length > 0){
	                    for (i = 0; i < dado.length; i++) {
	                        addTableRow(i+1, dado[i][0], dado[i][1], dado[i][2], dado[i][3], dado[i][4]);
	                    }
	                    let ultimo = dado.length-1;
	                    $('#valorTotal').text(dado[ultimo][5]);
	                } else{
						$('#tabela-pedidos tbody')
	    					.html("<tr><td colspan=\"6\">Nenhum Produto Adicionado</td></tr>");
	    				$('#valorTotal').text("0,00");
	                }
                    $('#tabela-pedidos a').css("pointer-events","visible");
                    $('#addProduto, #btnEnviarPed').prop("disabled",false);
	    		}).fail(function(){
	                if($('#ModalErro').length < 1){
	                    $('body').append(criaModalErro);
	                }
	                $( "#btTrigger" ).trigger( "click" );
            	});
	    	}
	    	return false;
	    });

	    $('.reset').click(function(){
	    	var redirect = $(this).attr('href');
			bootbox.confirm({
				closeButton: false,
				size: 'large',
				message: "Deseja Realmente Descartar Esse Pedido?",
				buttons: {
					confirm: {
						label: 'Sim',
						className: 'btn-success btn-lg'
					},
					cancel: {
						label: 'Não',
						className: 'btn-danger btn-lg'
					}
				},
				callback: function (result) {
					if(result) {
						$.ajax({
							type: 'POST',
							url:  redirect,
						}).done(function(){
							location.reload();
						});
					}
				}
			});
			return false;
	    });

	    //mostra quantos caracteres restam para escrever no campo txtObs, sendo o total 255
	    $('#txtObs').keyup(function () {
	        textCounter($(this), $('#remLen'), 255);
	    });
	    $('#txtObs').keydown(function () {
	        textCounter($(this), $('#remLen'), 255);
	    });

	    //mostra a div de txtObs que esta oculta, ou esconde ela
	    $('#mostrar').click(function (event) { // Esconde ou mostra o campo de Observação
	        var display = $('#camadaefeitos');
	        if (display.css('display') === 'none')
	            this.text = "Remover Observação";
	        else {
	            this.text = "Adicionar Observação";
	            $('#txtObs').val("");
	        }
	        display.toggle("blind");
	        event.preventDefault();
	    });

	    var mostrarMais = $("#mostrarMais");

	    mostrarMais.click(function(){
	    	$.ajax({
	        	type: 'POST',
	        	url:  '/Area-Restrita/Pedido/MostrarMais',
	        	data: 'pagina='+$(this).data("pagina"),
	        	dataType: 'json',
	        	beforeSend: function(){

	        	}
	    	}).done(function(dado){
	    		for (i = 0; i < dado.length; i++){
	    			var date = new Date(dado[i]['ped_datahora']);

	    			var newRow = $('<tr>');
    				var cols = "";
    				cols += '<td class="text-center">' +
    				("0" + date.getDate()).slice(-2) + '/' + ("0" + date.getMonth()).slice(-2) + '/' +  date.getFullYear() + " " +
    				("0" + date.getHours()).slice(-2) + ':' + ("0" + date.getMinutes()).slice(-2) + ':' + ("0" + date.getSeconds()).slice(-2)
    				+ '</td>';
    				cols += '<td>' + dado[i]['cli_nome'] + '</td>';
    				cols += '<td class="text-center">' + number_format(dado[i]['Total'], 2, ',', '.') + '</td>';
    				if (dado[i]['ped_status'] == "0")
    					cols += '<td class="text-center"><img title=\"Pedido não criado corretamente.\" src=\"/Public/img/img_warning.png\" /><br />Pendente</td>';
    				else if(dado[i]['ped_status'] == "1")
    					cols += '<td class="text-center"><img title=\"Pedido Enviado\" src=\"/Public/img/img_check.png\" /><br />Enviado</td>';
    				cols += '<td class="text-center"><a class="btn-lg" data-id="'+dado[i]['ped_codigo']+'" href="">Detalhes</a></td>';
    				newRow.append(cols);

    				if(dado.length > 0)
    					$("#tabela-vis tbody").append(newRow);
	    		}
	    		mostrarMais.data("pagina",mostrarMais.data("pagina")+1);
	    	}).fail(function(){
	            if($('#ModalErro').length < 1){
	                $('body').append(criaModalErro);
	            }
	            $( "#btTrigger" ).trigger( "click" );
	    	});
	    	return false;
	    });

		$('#tabela-vis').on("click", "a", function(){
			$('#codigo-pedido').val($(this).data("id"));
			$("#formVis").submit();

			return false;
		});
	});