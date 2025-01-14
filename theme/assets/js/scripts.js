// JQUERY INIT

$(function () {
    var ajaxResponseBaseTime = 3;

    // MOBILE MENU

    $(".mobile_menu").click(function (e) {
        e.preventDefault();

        var menu = $(".dash_sidebar");
        menu.animate({ right: 0 }, 200, function (e) {
            $("body").css("overflow", "hidden");
        });

        menu.one("mouseleave", function () {
            $(this).animate({ right: '-260' }, 200, function (e) {
                $("body").css("overflow", "auto");
            });
        });
    });

    //NOTIFICATION CENTER

    $(".notification_center_open").click(function (e) {
        e.preventDefault();

        var center = $(".notification_center");
        center.css("display", "block").animate({ right: 0 }, 200, function (e) {
            $("body").css("overflow", "hidden");
        });

        center.one("mouseleave", function () {
            $(this).animate({ right: '-320' }, 200, function (e) {
                $("body").css("overflow", "auto");
                $(this).css("display", "none");
            });
        });
    });



    //FORMS

    $("form:not('.ajax_off')").submit(function (e) {
        e.preventDefault();

        var form = $(this);
        var load = $(".ajax_load");
        var needConfirm = form.attr("data-confirm") ? form.attr("data-confirm") : false
        var confirm = false;

        if (needConfirm) {
            // *** para inserir confirmação caso necessario insira data-confirm="true" na tag form ***
            confirm = window.confirm("Atencão! Tem certeza que quer editar esse item?");
        } else {
            confirm = true
        }

        if (confirm) {

            if (typeof tinyMCE !== 'undefined') {
                tinyMCE.triggerSave();
            }

            form.ajaxSubmit({
                url: form.attr("action"),
                type: "POST",
                dataType: "json",
                beforeSend: function () {
                    load.fadeIn(200).css("display", "flex");
                },
                uploadProgress: function (event, position, total, completed) {
                    var loaded = completed;
                    var load_title = $(".ajax_load_box_title");
                    load_title.text("Enviando (" + loaded + "%)");

                    form.find("input[type='file']").val(null);
                    if (completed >= 100) {
                        load_title.text("Aguarde, carregando...");
                    }
                },
                success: function (response) {

                    // console.log(response)
                    console.log(response.message)
                    //redirect
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        load.fadeOut(200);
                    }

                    //reload
                    if (response.reload) {
                        setTimeout(function () {
                            window.location.reload();
                        }, 500)
                    } else {
                        load.fadeOut(200);
                    }

                    // Clear Inputs
                    if (response.clearInputs) {
                        if (form[0].querySelector("[name='url']")) {
                            form[0].querySelector("[name='url']").value = '';
                        }
                    }

                    //message
                    if (response.message) {
                        if (response.success == false) {
                            ajaxMessage(`<div class='message error icon-warning'>${response.message}...</div>`, 5);
                        } else {
                            ajaxMessage(`<div class='message success icon-success'>${response.message}...</div>`, 5);
                        }
                    }

                    //ID
                    if (response.galleryButton) {
                        if (response.success == false) {
                            ajaxMessage(`<div class='message success icon-success'>Erro...</div>`, 5);
                        } else {
                            createBtnGallery(response.galleryButton);
                        }
                    }

                    //image by fsphp mce upload
                    if (response.mce_image) {
                        $('.mce_upload').fadeOut(200);
                        tinyMCE.activeEditor.insertContent(response.mce_image);
                    }
                },
                complete: function () {
                    if (form.data("reset") === true) {
                        form.trigger("reset");
                    }
                },
                error: function (data) {
                    console.log(e);
                    console.log(this);
                    var message = "<div class='message error icon-warning'>Desculpe mas não foi possível processar sua requisição...</div>";
                    ajaxMessage(message, 5);
                    load.fadeOut();
                    // var responseText=JSON.parse(data.responseText);
                    // console.log(responseText.messages);
                    // alert("Error(s) while building the ZIP file:\n"+responseText.messages);

                }
            });
        }
    });

    // AJAX RESPONSE

    function ajaxMessage(message, time) {
        var ajaxMessage = $(message);
        ajaxMessage.append("<div class='message_time'></div>");
        ajaxMessage.find(".message_time").animate({ "width": "100%" }, time * 1000, function () {
            $(this).parents(".message").fadeOut(200);
        });

        $(".ajax_response").append(ajaxMessage);
    }

    // AJAX CRIAR BOTÃO PARA GALERIA

    function createBtnGallery(productId) {
        var saveBtn = document.getElementById('saveBtn'),
            galleryBtn = document.createElement('a');
        galleryBtn.textContent = "Galeria";
        galleryBtn.setAttribute("id", "goGalleryBtn");
        galleryBtn.setAttribute("class", "btn btn-green icon-check-square-o mr-3");
        galleryBtn.setAttribute("href", `${productId}`);

        var fatherElExists = saveBtn.parentNode;

        fatherElExists.insertBefore(galleryBtn, saveBtn)
        inputChangeObserver();
    }

    // Observador dos inputs de produtos
    // Seleciona os elementos que serão observados
    function inputChangeObserver() {
        const targetsInput = document.querySelectorAll('#prodForm input');
        const targetsSelect = document.querySelectorAll('select');

        targetsInput.forEach((e) => {
            e.addEventListener('keydown', () => {
                const goGalleryBtn = document.getElementById('goGalleryBtn');
                if (goGalleryBtn) {
                    goGalleryBtn.remove();
                }
            });
        })
        targetsTextarea.forEach((e) => {
            e.addEventListener('change', () => {
                const goGalleryBtn = document.getElementById('goGalleryBtn');
                if (goGalleryBtn) {
                    goGalleryBtn.remove();
                }
            });
        })
    }

    // AJAX RESPONSE MONITOR

    $(".ajax_response .message").each(function (e, m) {
        ajaxMessage(m, ajaxResponseBaseTime += 1);
    });

    // AJAX MESSAGE CLOSE ON CLICK

    $(".ajax_response").on("click", ".message", function (e) {
        $(this).effect("bounce").fadeOut(1);
    });

    // MAKS

    $(".mask-date").mask('00/00/0000');
    $(".mask-datetime").mask('00/00/0000 00:00');
    $(".mask-month").mask('00/0000', { reverse: true });
    $(".mask-doc").mask('000.000.000-00', { reverse: true });
    $(".mask-card").mask('0000  0000  0000  0000', { reverse: true });
    $(".mask-money").mask('000.000.000.000.000,00', { reverse: true, placeholder: "0,00" });
});

// TINYMCE INIT

function runtinyMCE() {
    tinyMCE.remove(); // Remover o TinyMCE existente, pois temos situações de reuso

    tinyMCE.init({
        selector: "textarea.mce",
        language: 'pt_BR',
        menubar: false,
        theme: "modern",
        height: 132,
        skin: 'light',
        entity_encoding: "raw",
        theme_advanced_resizing: true,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor media"
        ],
        toolbar: "styleselect | pastetext | removeformat |  bold | italic | underline | strikethrough | bullist | numlist | alignleft | aligncenter | alignright |  link | unlink | code | fullscreen",
        style_formats: [
            { title: 'Normal', block: 'p' },
            { title: 'Titulo 2', block: 'h2' },
            { title: 'Titulo 3', block: 'h3' },
            { title: 'Titulo 4', block: 'h4' },
            { title: 'Titulo 5', block: 'h5' },
            { title: 'Código', block: 'pre', classes: 'brush: php;' }
        ],
        link_class_list: [
            { title: 'None', value: '' },
            { title: 'Blue CTA', value: 'btn btn_cta_blue' },
            { title: 'Green CTA', value: 'btn btn_cta_green' },
            { title: 'Yellow CTA', value: 'btn btn_cta_yellow' },
            { title: 'Red CTA', value: 'btn btn_cta_red' }
        ],
        // setup: function (editor) {
        //     editor.addButton('fsphpimage', {
        //         title: 'Enviar Imagem',
        //         icon: 'image',
        //         onclick: function () {
        //             $('.mce_upload').fadeIn(200, function (e) {
        //                 $("body").click(function (e) {
        //                     if ($(e.target).attr("class") === "mce_upload") {
        //                         $('.mce_upload').fadeOut(200);
        //                     }
        //                 });
        //             }).css("display", "flex");
        //         }
        //     });
        // },
        link_title: false,
        target_list: false,
        theme_advanced_blockformats: "h1,h2,h3,h4,h5,p,pre",
        media_dimensions: false,
        media_poster: false,
        media_alt_source: false,
        media_embed: false,
        extended_valid_elements: "a[href|target=_blank|rel|class]",
        imagemanager_insert_template: '<img src="{$url}" title="{$title}" alt="{$title}" />',
        image_dimensions: false,
        relative_urls: false,
        remove_script_host: false,
        paste_as_text: true
    });
} runtinyMCE()

function editar(url) {
    var confirmado = confirm('Atenção! Você tem certeza que quer editar?');

    if (confirmado) {

        $.ajax({
            url: url,
            method: 'POST',
            success: function (resultado) {
                window.location.reload();
            },
            error: function () {
                alert('Ocorreu um erro ao enviar a solicitação AJAX.');
            }
        });
    }
}

function deletar(url) {
    var confirmado = confirm('Atenção! Você tem certeza que quer realizar essa tarefa?');

    if (confirmado) {

        $.ajax({
            url: url,
            method: 'POST',
            success: function (resultado) {
                window.location.reload();
            },
            error: function () {
                alert('Ocorreu um erro ao enviar a solicitação AJAX.');
            }
        });
    }
}

function ativar(url) {
    var confirmado = confirm('Atenção! Você tem certeza que quer realizar essa tarefa?');
    if (confirmado) {
        $.ajax({
            url: url,
            method: 'POST',
            success: function (resultado) {
                window.location.reload();
                // Manipular a resposta do servidor aqui
            },
            error: function () {
                alert('Ocorreu um erro ao enviar a solicitação AJAX.');
            }
        });
    }
}


