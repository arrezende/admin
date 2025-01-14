<?php $this->layout("_theme"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-plus-circle"><?= $title ?></h2>
    </header>

    <div class="dash_content_app_box">

        <form id="prodForm" class="app_form" action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
            <label class="label">
                <span class="legend">*Título:</span>
                <input type="text" name="name" placeholder="O Título do seu produto" value="<?= $product->name; ?>" required />
            </label>

            <?php if (URL_PRODUTO) : ?>
                <label id="urlContainer" class="label">
                    <span class="legend">URL:</span>
                    <input id="inputUrl" type="text" name="url" placeholder="Url do produto" value="<?= $product->url; ?>" />
                </label>
            <?php endif; ?>

            <div>
                <label class="label">
                    <span class="legend">Capa: (1920x1080px)</span>
                    <input type="file" name="cover" placeholder="Uma imagem de capa" />
                </label>
                <?php if ($product->cover) : ?>
                    <table class='table tableCover'>
                        <thead>
                            <tr>
                                <td>
                                    Imagem
                                </td>
                                <td>
                                    Ação
                                </td>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class="cover1-container">
                                    <img src="<?= url("$product->cover"); ?>" onerror="this.onerror=null;this.src='../../theme/assets/images/no-image.png';">
                                </td>
                                <td>
                                    <a class="icon-trash-o btn btn-red" href="#" onclick="atualizarBanco()">Deletar</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>

                <label class="label">
                    <span class="legend">*Conteúdo:</span>
                    <textarea class="mce" name="content"><?= $product->content; ?></textarea>
                </label>
            </div>

            <?php if (PDF_PRODUTO) : ?>

                <label class="label">
                    <span class="legend">PDF:</span>
                    <input type="file" name="pdf" placeholder="PDF do produto" />
                </label>

            <?php endif; ?>

            <div class="add-sec-container">
                <label class="label">
                    <span class="legend text-center">SEÇÃO DE CONTEÚDO ADICIONAL</span>
                </label>

                <?php
                $allContents = explode("%#%", $product->additional_content);
                $allCovers = explode("%#%", $product->additional_cover);

                if (count($allContents) > 0) :
                    foreach ($allContents as $key => $content) :
                        // if (!empty($content)) :
                ?>
                        <label id="contentImagem" class="label">
                            <span class="legend">Imagem do conteúdo adicional:</span>
                            <input type="file" name="additional_cover[<?= $key ?>]" />
                        </label>

                        <?php
                        // Verifica tiver imagem
                        if (!empty($allCovers[$key]) && $allCovers[$key] != "noimage") {
                        ?>
                            <table class='table tableCover'>
                                <thead>
                                    <tr>
                                        <td>Imagem</td>
                                        <td>Ação</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="cover2-container"><img src="<?= url("$allCovers[$key]"); ?>"></td>
                                        <!-- <td><a class="icon-trash-o btn btn-red" href="#" onclick="removerAddImg('testendo')">Deletar</a></td> -->
                                        <td><a class="icon-trash-o btn btn-red" href="javascript:void(0)" onclick="removerAddImg('<?= $allCovers[$key] ?>')">Deletar</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php
                        }
                        ?>

                        <label id="contentTexto" class="label">
                            <span class="legend">Conteúdo adicional:</span>
                            <textarea class="mce" name="additional_content[]"><?= $content; ?></textarea>
                        </label>
                <?php
                    // endif;
                    endforeach;
                endif;
                ?>


                <span id="newContentContainer"></span>
                <div class="btnAddCat"><a href="javascript:void(0)" onclick="adicionarConteudo('contentImagem','newContentContainer')">Conteúdo</a></div>
            </div>

            <div class="label_g2">
                <label class="label">
                    <span class="legend">*Categoria:</span>

                    <?php
                    $prodCountCats = explode(",", $product->category_id);
                    if (count($prodCountCats) > 0) : ?>
                        <input id="categoryId" type="hidden" name="category_id">
                        <?php foreach ($prodCountCats as $key => $catFatherId) :
                        ?>
                            <select data-select="categoryId" required>
                                <option <?= $key == 0 ? "disabled" : "" ?> selected><?= $key == 0 ? "Selecione uma Categoria" : "Remover categoria" ?></option>
                                <?php foreach ($treeSortCategories as $category) : ?>
                                    <option value="<?= $category->id; ?>" <?= $catFatherId == $category->id ? "selected" : "" ?>><?= $category->indentation; ?><?= $category->name; ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php endforeach; ?>
                    <?php endif; ?>
                    <span id="newSelectContainer"></span>
                    <div class="btnAddCat"><a href="javascript:void(0)" onclick="adicionarSelect('categoryId', 'newSelectContainer')">Add Categoria</a></div>
                </label>

                <label class="label">
                    <span class="legend">*Status:</span>
                    <select name="status" required>
                        <option value="post" <?= $product->status == "post" ? "selected" : "" ?>>Publicar</option>
                        <option value="draft" <?= $product->status == "draft" ? "selected" : "" ?>>Rascunho</option>
                        <option value="trash" <?= $product->status == "trash" ? "selected" : "" ?>>Lixo</option>
                    </select>
                </label>

            </div>

            <div class="label_g2">
                <label class="label">
                    <span class="legend">Tags:</span>
                    <?php
                    $tagsCount = explode(",", $product->tags);
                    if (count($tagsCount) > 0) : ?>
                        <input id="tagId" type="hidden" name="tags">
                        <?php foreach ($tagsCount as $key => $countItem) :
                        ?>
                            <select data-select="tagId">
                                <option selected><?= $key == 0 ? "Selecione uma tag" : "Remover tag" ?></option>
                                <?php foreach ($tags as $tag) : ?>
                                    <option value="<?= $tag->id; ?>" <?= $countItem == $tag->id ? "selected" : "" ?>><?= $tag->tag_name; ?></option>
                                <?php endforeach; ?>
                            </select>

                        <?php endforeach; ?>
                    <?php endif; ?>
                    <span id="newSelectTagContainer"></span>
                    <div class="btnAddCat"><a href="javascript:void(0)" onclick="adicionarSelect('tagId', 'newSelectTagContainer')">Add Tag</a></div>
                </label>

                <label class="label">
                    <span class="legend">Ordenação: (1 acima - 99 abaixo)</span>
                    <input type="number" name="priority" placeholder="Ordenação do produto" value="<?= $product->priority; ?>" />
                </label>
            </div>


            <div class="al-right">
                <?php if (!empty($idButton)) : ?>
                    <a id="goGalleryBtn" class="btn btn-green icon-check-square-o mr-3" href="<?= $idButton ?>">Galeria</a>
                <?php endif; ?>
                <input type="hidden" name="type" value="<?= $type ?>">
                <!-- <button id="saveBtn" class="btn btn-green icon-check-square-o getSelectedValues">Salvar</button> -->
            </div>
            <div class="fix-save">
                <button id="saveBtn" class="btn btn-green icon-check-square-o getSelectedValues">Salvar</button>
            </div>
        </form>
    </div>
</section>

<?php
// Cria um objeto da lista de array existentes compativel com JS.
$urlArrayList = [];
if (!empty($allUrls)) {
    foreach ($allUrls as $url) {
        array_push($urlArrayList, $url->url);
    }
    $arrayListEncod = json_encode($urlArrayList);
}
?>

<?php $this->start("scripts"); ?>
<script>
    var getSelectedValues = document.getElementsByClassName('getSelectedValues');
    if (getSelectedValues) {
        // Estrutura para adicionar os valores dos selects aos seus respectivos inputs para envio para o servidor
        getSelectedValues[0].addEventListener('click', function getSelectedValues() {
            var selects = document.querySelectorAll('[data-select="categoryId"]'), // categorias multiplas
                selectsTags = document.querySelectorAll('[data-select="tagId"]'); // tags multiplas

            var selectedValues = [],
                selectedTagsValues = [];

            selects.forEach((select) => {
                var selectedOption = select.options[select.selectedIndex];
                if (!isNaN(selectedOption.value)) {
                    selectedValues.push(selectedOption.value);
                }
                if (isNaN(selectedOption.value)) {
                    return false;
                };
            });

            selectsTags.forEach((select) => {
                var selectedOption = select.options[select.selectedIndex];
                if (!isNaN(selectedOption.value)) {
                    selectedTagsValues.push(selectedOption.value);
                }
                if (isNaN(selectedOption.value)) {
                    return false;
                };
            });

            var concatenatedValues = selectedValues.join(',');
            var concatenatedTagsValues = selectedTagsValues.join(',');

            document.getElementById('categoryId').value = concatenatedValues;
            document.getElementById('tagId').value = concatenatedTagsValues;

        })
    }


    function adicionarSelect(dataSetValue, newContainer) {
        var originalSelect = document.querySelector(`[data-select="${dataSetValue}"]`),
            originalOptions = originalSelect.querySelectorAll('option'),

            newSelectContainer = document.getElementById(`${newContainer}`),
            novoSelect = document.createElement("select"),
            option = document.createElement("option");

        novoSelect.setAttribute('data-select', `${dataSetValue}`);
        // novoSelect.setAttribute('required', '');

        for (let i = 0; i < originalOptions.length; i++) {
            if (i == 0) {
                var option = document.createElement("option");
                option.text = "Remover";
                option.setAttribute('selected', '')
                novoSelect.appendChild(option);
            } else {
                var option = document.createElement("option");
                option.text = originalOptions[i].innerHTML;
                option.value = originalOptions[i].value;
                novoSelect.appendChild(option);
            }

        }
        newSelectContainer.appendChild(novoSelect);
    }


    function adicionarConteudo(dataSetImg, newContainer) { // Função de adicionar novos campos de conteúdo adicional
        var originalFile = document.querySelector(`[id="${dataSetImg}"]`),
            newSelectContainer = document.getElementById(`${newContainer}`);

        var copyOfOriginalFile = originalFile.cloneNode(true),
            // para a Label, por causa do mce não tem como clonar
            novaLabel = document.createElement("label"),
            novoSpan = document.createElement("span"),
            novoTextArea = document.createElement("textarea")

        novaLabel.setAttribute('class', 'label');
        novoSpan.setAttribute('class', 'legend');
        novoSpan.innerHTML = "Conteúdo adicional:";
        novoTextArea.setAttribute('class', 'mce');
        novoTextArea.setAttribute('name', 'additional_content[]');

        novaLabel.appendChild(novoSpan);
        novaLabel.appendChild(novoTextArea);

        newSelectContainer.appendChild(copyOfOriginalFile);
        newSelectContainer.appendChild(novaLabel);

        runtinyMCE() // chama o MCE de novo para criar os campos de forma adequada
    }


    <?php if ($type === "edit") : ?>

        function atualizarBanco() {
            console.log("<?= url("produtos/imagem/{$product->id}"); ?>");
            var confirmado = confirm('Atenção! Você tem certeza que quer realizar essa tarefa?');
            if (confirmado) {
                $.ajax({
                    url: "<?= url("produtos/imagem/{$product->id}"); ?>",
                    method: "POST",
                    success: function(response) {
                        console.log(response);
                        console.log("Dados atualizados com sucesso!");
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log("Erro ao atualizar os dados: " + error);
                    }
                });
            }
        }

        function removerAddImg(imgName) {
            // console.log(imgName);
            var confirmado = confirm('Atenção! Você tem certeza que quer remover essa imagem?');
            if (confirmado) {
                $.ajax({
                    url: "<?= url("produtos/remadicional/{$product->id}"); ?>",
                    method: "POST",
                    data: {
                        img: imgName
                    },
                    success: function(response) {
                        console.log(response);
                        console.log("Imagem removida com sucesso!");
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.log("Erro ao remover imagem: " + error);
                    }
                });
            }
        }
    <?php endif; ?>

    // Verificação de dupliciade de URL start
    const inputUrl = document.getElementById('inputUrl');
    let urlList = <?= !empty($arrayListEncod) ? $arrayListEncod : "[]"; ?>;

    if (inputUrl) {
        function checkUrlDuplicity() {
            for (let key in urlList) {
                const avisoExiste = document.getElementById('avisoUrl');
                if (avisoExiste) {
                    avisoExiste.remove();
                }
                if (urlList.hasOwnProperty(key)) {
                    if (urlList[key].trim() == inputUrl.value.trim()) {
                        const urlContainer = document.getElementById('urlContainer');
                        const referencia = document.getElementById('inputUrl');
                        const aviso = document.createElement('span');
                        aviso.textContent = "Url já existente! Salve para substituir";
                        aviso.setAttribute('id', "avisoUrl")
                        aviso.setAttribute('style', "color: red")
                        urlContainer.insertBefore(aviso, referencia)
                        return false
                    }
                }
            }
        }

        inputUrl.addEventListener('change', checkUrlDuplicity);
    }
    // Verificação de dupliciade de URL end
</script>

<?php $this->stop(); ?>