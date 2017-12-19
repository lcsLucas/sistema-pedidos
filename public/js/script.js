var monet = $('#txtMonetario').val();

window.onload = function() {    
    
    $(".select2").select2();

    //mostrar apos carregar pagina
    $('div#div-geral').fadeIn('fast');
    $('.car').fadeOut('fast');
        /*Pagina Usuario*/
    $("#formUsu").validate({
        rules:{
            txtNome:
            {
                required: true,
                maxlength: 50
            },
            txtEmail:
            {
                required: true,
                email: true,
                maxlength:50
            },
            txtSenha:
            {
                required: true,
                maxlength: 30
            },
            txtConfSenha:
            {
                required: true,
                equalTo: "#txtSenha"
            }
        },
        messages: {
            txtNome:
            {
                required: $("#txtNome").attr("title"),
                maxlength: 'Ultrapassado o limite de 50 caracteres.'
            },
            txtEmail:
            {
                required: $("#txtEmail").attr("title"),
                email: "Por Favor, informe um Email Válido",
                maxlength: 'Ultrapassado o limite de 50 caracteres.'
            },
            txtSenha:
            {
                required: $("#txtSenha").attr("title"),
                maxlength: 'Ultrapassado o limite de 30 caracteres.'
            },
            txtConfSenha:
            {
                required: $("#txtConfSenha").attr("title"),
                equalTo: "As senhas não são iguais."
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form){
            var dados = $(form).serialize();
            $.ajax({
                type: 'POST',
                url: '/Area-Restrita/Usuario/AdicionarUsuario',
                data: dados,
                dataType: 'json',
                beforeSend: function(){
                    $('#btnEnviarUsu').prop("disabled",true);
                }
            }).done(function(data){
                bootbox.alert({
                    closeButton: false,
                    size: 'large',
                    message: "<span class=\"btn-lg\">"+data["Mensagem"]+"</span>",
                    callback: function () {
                        if (data["Tipo"] === 1)
                            location.href = "/Area-Restrita/Usuario/GerenciarUsuarios";
                        else
                            $('#btnEnviarUsu').removeAttr('disabled');
                    }
                });
            }).fail(function(){
                if($('#ModalErro').length < 1){
                    $('body').append(criaModalErro);
                }
                $( "#btTrigger" ).trigger( "click" );
            });
            return false;
        }//submitHandler
    });

    $('#btnLogar').click(function(){
        $('.car').fadeIn('fast');
    });
        /*Pagina Login - Form*/
    $("#formLogin").validate({
        rules: {
            txtEmail:
            {
                required: true,
                email: true
            },
            txtSenha:
            {
                required: true
            }
        },
        messages: {
            txtEmail:
            {
                required: 'Email é Obrigatório',
                email: "Informe Um Email Válido"
            },
            txtSenha:
            {
                required: "Senha é Obrigatória"
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');

        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form){
            var dados = $(form).serialize();
            $.ajax({
                type: 'POST',
                url: '/Area-Restrita/Login',
                async: false,
                data: dados,
                dataType: 'json',
                beforeSend: function(){
                    $('#btnLogar').prop("disabled",true);
                }
            }).done(function(data){//deu certo a requisição
                if(data['Tipo'] === 1){
                    window.location = "../";
                }else{
                    var Mensagem  = data['Mensagem'];
                    var Mensagem = Mensagem.split("|");

                    if($('#divMsg').length > 0){
                        $("#divMsg").remove();
                    }
                    var div = "<div id=\"divMsg\" class=\"alert alert-danger\" role=\"alert\"><span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span><span id=\"Msg1\">"+Mensagem[0]+".</span>";
                    if(typeof(Mensagem[1]) !== "undefined"){
                        div += "<a id=\"mostrarMsg\" href=\"\">Exibir Detalhes do Erro</a><span style=\"display:none\" id=\"mostrarDetalhe\"><hr />"+Mensagem[1]+"</span></div>";
                    }
                    $('#conteudo').prepend(div);
                    $('#btnLogar').removeAttr('disabled');
                    $('.car').fadeOut('fast');
                }
            }).fail(function(){//deu erro na requisição
                if($('#ModalErro').length < 1){
                    $('body').append(criaModalErro);
                }
                $( "#btTrigger" ).trigger( "click" );
            });//ajax
            return false;
        }
    });//validate

    /*Pagina de Alterar Senha*/
    $('#formAltSenha').validate({
        rules:{
            txtSenhaAtual:{
                required: true,
                maxlength: 30
            },
            txtNovaSenha: {
                required: true,
                maxlength:30
            },
            txtConfSenha: {
                required: true,
                maxlength: 30,
                equalTo: "#txtNovaSenha"
            }
        },
        messages: {
            txtSenhaAtual: {
                required: 'Senha Atual é Obrigatória',
                maxlength: 'Ultrapassado o limite de 30 caracteres'
            },
            txtNovaSenha: {
                required: 'Nova Senha é Obrigatória',
                maxlength: 'Ultrapassado o limite de 30 caracteres'
            },
            txtConfSenha: {
                required: 'Confirmar Nova Senha é Obrigatório',
                maxlength: 'Ultrapassado o limite de 30 caracteres',
                equalTo: "Este campo está Diferente do campo \"Nova Senha\""
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form){
            var dados = $(form).serialize();
            $.ajax({
                type: 'POST',
                url:  '/Area-Restrita/Usuario/Alterar-Senha',
                data: dados,
                dataType: 'json',
                beforeSend: function(){
                    $('#btnEnviar').prop("disabled",true);
                }
            }).done(function(data){
                bootbox.alert({
                    closeButton: false,
                    size: 'large',
                    message: "<span class=\"btn-lg\">"+data["Mensagem"]+"</span>",
                    callback: function () {
                        if (data["Tipo"] === 1)
                            location.href = "/Area-Restrita/Logout";
                        else
                            $('#btnEnviar').removeAttr('disabled');
                    }
                });
            }).fail(function(){
                if($('#ModalErro').length < 1){
                    $('body').append(criaModalErro);
                }
                $( "#btTrigger" ).trigger( "click" );
            });//ajax
            return false;
        }
    });

    //Formulario Cliente
    $('#formCli').validate({
        rules: {
            txtPrecoNovo: {
                required: true
            },
            selProduto: {
                required: true
            }
        }, messages: {
            txtPrecoNovo: {
                required: "Informe Um Valor para o Novo Preço"
            },
            selProduto: {
                required: "Selecionar um Produto é Obrigatório"
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form){
            var dados = $(form).serialize();
            $.ajax({
                type: 'POST',
                url: '/Area-Restrita/Cliente/AlterarProduto',
                data: dados,
                dataType: 'json',
                beforeSend: function(){
                    $('#btnEnviar').prop("disabled",true);
                }
            }).done(function(data){
                bootbox.alert({
                    closeButton: false,
                    size: 'large',
                    message: "<span class=\"btn-lg\">"+data["Mensagem"]+"</span>",
                    callback: function () {
                        if (data["Tipo"] === 1)
                            location.reload();
                        else
                            $('#btnEnviar').removeAttr('disabled');
                    }
                });
            }).fail(function(){
                if($('#ModalErro').length < 1){
                    $('body').append(criaModalErro);
                }
                $( "#btTrigger" ).trigger( "click" );
            });
            return false;
        }
    });

    //Formulário do Produto
    $('#formProd').validate({
        rules: {
            txtPrecoNovo: {
                required: true
            }
        }, messages: {
            txtPrecoNovo: {
                required: "Informe Um Valor para o Novo Preço"
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form){
            var dados = $(form).serialize();
            $.ajax({
                type: 'POST',
                url:  '/Area-Restrita/Produto/Alterar',
                data: dados,
                dataType: 'json',
                beforeSend: function(){
                    $('#btnEnviarProd').prop("disabled",true);
                }
            }).done(function(data){
                bootbox.alert({
                    closeButton: false,
                    size: 'large',
                    message: "<span class=\"btn-lg\">"+data["Mensagem"]+"</span>",
                    callback: function () {
                        if (data["Tipo"] === 1)
                            location.reload();
                        else
                            $('#btnEnviarProd').removeAttr('disabled');
                    }
                });
            }).fail(function(){
                if($('#ModalErro').length < 1){
                    $('body').append(criaModalErro);
                }
                $( "#btTrigger" ).trigger( "click" );
            });//ajax
            return false;
        }
    });

    //Form cliente - Pedido
    $('#parteCliente > div.panel-body').on( "click",'#btnConfCliente', function() {
        if ($('#selCliente').prop('selectedIndex') > 0) {
            $.ajax({
                type: 'POST',
                url:  '/Area-Restrita/Produto/ObterPorCliente',
                data: 'cliente='+$('#selCliente').val()+"&token="+$('#txtToken').val(),
                dataType: 'json',
                beforeSend: function(){
                    $('#conteudo').fadeOut('fast');
                    $('.car').fadeIn('fast');
                }
            }).done(function(retorno){
                /**
                 * percorre o array de produtos com preço  para o
                 * cliente selecionado, e altera os option desses produtos
                 * @param  int key     indice
                 * @param  object dado
                 */
                $.each(retorno, function(key, dado){
                    var option = $('#selProduto option[value="'+dado.prod_codigo+'"]');
                    option.attr("data-preco", number_format(dado.cli_pro_preco, 2, ',', '.'));
                });

                $("#selCliente").attr("disabled", "");
                $("#btnConfCliente").fadeOut("fast");
                $('#parteProduto, #parteAdicionais, #botoesPedido, .link-trocar').fadeIn();
                $('#conteudo').fadeIn();
                $('.car').fadeOut('fast');
            }).fail(function(){
                if($('#ModalErro').length < 1){
                    $('body').append(criaModalErro);
                }
                $( "#btTrigger" ).trigger( "click" );
            });//ajax
        }
        else{
            bootbox.alert({
                closeButton: false,
                size: 'large',
                message: "<span clas=\"btn-lg\">Selecione um Cliente</span>"
            });
        }
    });

    //FormEnviar Pedido
    $('#frmEnviarPedido').validate({
        submitHandler: function(form){
            var dados = $(form).serialize();
            bootbox.confirm({
                closeButton: false,
                size: 'large',
                message: "<span class=\"btn-lg\">Confirmar o Pedido?</span>",
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
                            url: '/Area-Restrita/Pedido/EnviarPedido',
                            data: dados,
                            dataType: 'json',
                            beforeSend: function(){
                                $("#conteudo").fadeOut("fast");
                                $('.car').fadeIn('fast');
                                $('#tabela-pedidos a').css("pointer-events","visible");
                                $('#addProduto, #btnEnviarPed').prop("disabled",false);
                            }
                        }).done(function(resposta){
                            bootbox.alert({
                                closeButton: false,
                                size: 'large',
                                message: "<span class=\"btn-lg\">"+resposta["Mensagem"]+"</span>",
                                callback: function () {
                                    if(resposta["Tipo"] === 1){
                                        $("#codigo-pedido").val(resposta["Resultado"]);
                                        $("#formVis").submit();
                                    }
                                    else{
                                        $('#tabela-pedidos a').css("pointer-events","visible");
                                        $('#addProduto, #btnEnviarPed').prop("disabled",false);
                                        $("#conteudo").fadeIn("fast");
                                        $('.car').fadeOut('fast');
                                    }
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
            return false;
        }
    });

    //Form Add Produto
    $('#formaddProd').validate({
        rules: {
            selProduto: {
                required: true
            },
            txtQtde: {
                required: true,
                digits: true
            }
        },
        messages: {
            selProduto: {
                required: "Escolha Um Produto"
            },
            txtQtde: {
                required: "Informe a Quantidade",
                digits: "Apenas Números"
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            error.appendTo( element.parent());
        }, submitHandler: function(form){
            var dados = $(form).serialize();
            $.ajax({
                type: 'POST',
                url: '/Area-Restrita/Produto/AddProduto',
                data: dados,
                dataType: 'json',
                beforeSend: function(){
                    $('#tabela-pedidos a').css("pointer-events","none");
                    $('#addProduto, #btnEnviarPed').prop("disabled",true);
                    $('#tabela-pedidos tbody').html("<tr><td colspan=\"5\"><img alt=\"carregando...\" src=\"/Public/img/loader.gif\" /></td></tr>");
                }
            }).done(function(dado){
                if(Array.isArray(dado)){
                    $('#tabela-pedidos a').css("pointer-events","visible");
                    $('#addProduto, #btnEnviarPed').prop("disabled",false);
                    $('#tabela-pedidos tbody').html("");
                    
                    if(monet === "1"){//monetario
                        for (i = 0; i < dado.length; i++) {
                            addTableRow(i+1, dado[i][0], dado[i][1], dado[i][2], dado[i][3], dado[i][4]);
                        }
                        var ultimo = dado.length-1;
                        $('#valorTotal').text(dado[ultimo][5]);
                    } else {//Ñ monetario
                        for (i = 0; i < dado.length; i++) {
                            addTableRow(i+1, dado[i][0], dado[i][1], dado[i][2],"","");
                        }
                    } 
                    
                    $("#selProduto").val($("#selProduto option:first").val());
                    $('#selProduto').trigger('change.select2');
                    $('#formaddProd input[type="text"]').val("");
                    $('#formaddProd input[type="tel"]').val("");
                } else{
                    bootbox.alert({
                        closeButton: false,
                        size: 'large',
                        message: dado["Mensagem"],
                        callback: function () {
                                location.reload();
                        }
                    });
                }
            }).fail(function(){
                if($('#ModalErro').length < 1){
                    $('body').append(criaModalErro);
                }
                $( "#btTrigger" ).trigger( "click" );
            });
            return false;
        }
    });

    $("#conteudo").on('click', '#mostrarMsg', function(){
        $("#mostrarDetalhe").toggle();
        return false;
    });
    
    $('#ckMonetario').click(function(){
        $.ajax({
            type:'POST',
            url: '/Area-Restrita/Config/Monetario',
            data: 'optMonetario='+$(this).prop("checked"),
            dataType: 'json',
            beforeSend: function() {
                
            }
        }).done(function(data){
                bootbox.alert({
                    closeButton: false,
                    size: 'large',
                    message: "<span class=\"btn-lg\">"+data["Mensagem"]+"</span>",
                    callback: function () {
                        if (data["Tipo"] === 1)
                            location.href = "/Area-Restrita/Logout";
                    }
                });
        }).fail(function(){
            if($('#ModalErro').length < 1){
                $('body').append(criaModalErro);
            }
            $( "#btTrigger" ).trigger( "click" );
        });
    });   
};

function criaModalErro(){
    var estrutura = "";
    estrutura = "<button style=\"display:none\" id=\"btTrigger\" type=\"button\" data-toggle=\"modal\" data-target=\"#ModalErro\">Open Modal</button>"+
    "<div id=\"ModalErro\" class=\"modal fade\" role=\"dialog\">"+
      "<div class=\"modal-dialog\">"+
        "<div class=\"modal-content\">"+
          "<div class=\"modal-body\">"+
            "<p>Erro Ao Enviar Requisição, Atualize a Página e Tente Novamente.</p>"+
          "</div>"+
          "<div class=\"modal-footer\">"+
            "<a role=\"button\" class=\"btn btn-default btn-lg\" onclick=\"javascript:location.reload();\" data-dismiss=\"modal\">Atualizar Página</a>"+
          "</div>"+
        "</div>"+
      "</div>"+
    "</div>";

    return estrutura;
}

function apenasNumeros(string)
{
    return string.replace(/[^\d]+/g, '');
}

function number_format(numero, decimal, decimal_separador, milhar_separador) {
    numero = (numero + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+numero) ? 0 : +numero,
            prec = !isFinite(+decimal) ? 0 : Math.abs(decimal),
            sep = (typeof milhar_separador === 'undefined') ? ',' : milhar_separador,
            dec = (typeof decimal_separador === 'undefined') ? '.' : decimal_separador,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
    // Fix para IE: parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function addTableRow(seq, cod, nome, qtde, preco, subT) {
    var newRow = $('<tr>');
    var cols = "";
    /*number_format(total,2,',','.');  */
    cols += '<td>' + seq + '</td>';
    cols += '<td>' + nome + '</td>';
    cols += '<td>' + qtde + '</td>';
    if(monet === "1") {
        cols += '<td>' + preco + '</td>';
        cols += '<td>' + subT + '</td>';
    }
    cols += '<td>';
    cols += '<a data-id="'+cod+'" href="/Area-Restrita/Pedido/remProduto"><img title="Excluir Produto" class="exc" alt="Excluir Produto" src="/public/img/img_delete.png" /><br />Excluir</a>';
    cols += '</td>';

    newRow.append(cols);
    $("#tabela-pedidos tbody").append(newRow);
}

function textCounter(field, countfield, maxlimit) {//Essa funçao e para o controle de Qtde de palavras no campo de OBS
    if (field.val().length > maxlimit)
        field.val(field.val().substring(0, maxlimit));
    else
        countfield.text(maxlimit - field.val().length);
}