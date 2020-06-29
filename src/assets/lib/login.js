//
// CUSTOM JAVASCRIPT
// ----------------------------------------------------------------------------

"use strict";

$(document).ready(function(){

    var path = $("body").data("url");

    // Accessibility - WAI-ARAI Roles
    $("nav").attr("role","navigation");
    $("nav ul li a").attr("role","menuitem");
    $("#header").attr("role","banner");
    $("#footer").attr("role","contentinfo");
    $("section").attr("role","region");
    $(".content").attr("role","main");
    $(".sidebar").attr("role","complementary");
    $(".alert").attr("role","alert");
    $("a.btn").attr("role","button");
    $("details, figure").attr("role", "group");
    $(".tabs").attr("role","tablist");
    $(".search-form").attr("role","search");

    $('[data-toggle="tooltip"]').tooltip();

    var SPMaskBehavior = function (val) {return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';},
    spOptions = {onKeyPress: function(val, e, field, options) {field.mask(SPMaskBehavior.apply({}, arguments), options);}};

     /* Masks */
    // $('.telefone').mask(SPMaskBehavior, spOptions);

    /* Função para logar por ajax */
    $("#login").submit(function(e)
    {
        e.preventDefault()
        var em = $("input[name=user_email]").val();
        var sen = $("input[name=user_password]").val();
        var cont = $(this).data("controller");
        console.log('lorem ipsum');
        if(em === "")
        {
            swal("Erro", "E-mail não pode ser em branco", "error");
        }
        else if(sen === "")
        {
            swal("Erro", "Senha não pode ser em branca", "error");
        }
        else
        {
            sen = CryptoJS.SHA512(sen);
            sen = sen.toString();

            $("#loading2").attr("style", "display: table");


            $.ajax
            ({
                url: path+cont+'/login/',
                type: "post",
                data: "user_email="+em+"&user_password="+sen,
                success: function(dados)
                {
                    console.log(dados);
                    $("#loading2").hide();
                    if(dados === '1')
                    {
                        window.location = path+cont;
                    }
                    else
                    {
                        swal("Erro", "Dados inválidos", "error");
                    }
                }
            });
        }

        return false;
    });



    $("#cadastro").submit(function()
    {
        var em   = $("input[name=user_email]").val();
        var sen  = $("input[name=user_password]").val();
        var id   = $("input[name=id]").val();
        var cont = $(this).data("controller");


        if(sen === "")
        {
            swal("Erro", "Senha não pode ser em branca", "error");
        }
        else
        {
            sen = CryptoJS.SHA512(sen);
            sen = sen.toString();

            $("#loading2").attr("style", "display: table");


            $.ajax
            ({
                url: path+cont+'/cadastro/',
                type: "post",
                data: "user_email="+em+"&user_password="+sen+'&id='+id,
                success: function(dados)
                {
                    console.log(dados);
                    $("#loading2").hide();
                    if(dados === '1')
                    {
                        window.location = path+cont;
                    }
                    else
                    {
                        swal("Erro", "Erro ao se cadastrar.", "error");
                    }
                }
            });
        }

        return false;
    });
});
