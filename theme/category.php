<?php $this->layout("_theme"); ?>
<?php $this->start("styles"); ?>
<?php $this->stop() ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-plus-circle"><?= $title ?></h2>
    </header>

    <div class="dash_content_app_box">
        <form class="app_form" action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
            <label class="label">
                <span class="legend">*Título:</span>
                <input type="text" name="name" placeholder="O nome da categoria" required value='<?= $category->name; ?>' />
            </label>
            <?php if (URL_CATEGORIA) : ?>
                <label id="urlContainer" class="label">
                    <span class="legend">URL:</span>
                    <input id="inputUrl" type="text" name="url" placeholder="Url da categoria" value="<?= $category->url; ?>" />
                </label>
            <?php endif; ?>

            <?php if (SUBTITLE_CATEGORIA) : ?>
                <label class="label">
                    <span class="legend">Sub titulo:</span>
                    <input type="text" name="sub_name" placeholder="Sub titulo do card" value='<?= $category->sub_name; ?>' />
                </label>
            <?php endif; ?>

            <label class="label">
                <span class="legend">Descrição:</span>
                <textarea name="description" placeholder="Sobre esta categoria"><?= $category->description; ?></textarea>
            </label>

            <?php if (CARD_DESC_CATEGORIA) : ?>
                <label class="label">
                    <span class="legend">Descrição do Card:</span>
                    <textarea name="card_description" placeholder="Descrição do card"><?= $category->card_description; ?></textarea>
                </label>
            <?php endif; ?>

            <label class="label">
                <span class="legend">Capa:</span>
                <input type="file" name="cover" placeholder="Uma imagem de capa" />
            </label>
            <?php if ($category->cover) : ?>
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
                            <td>
                                <img src="<?= url("$category->cover"); ?>" alt="" width="150">
                            </td>
                            <td>
                                <a class="icon-trash-o btn btn-red" href="#" onclick="atualizarBanco()">Deletar</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>

            <label class="label_g2">
                <div class="label">
                    <span class="legend">Categoria Principal:</span>
                    <select name="father_id">
                        <option value="0">Sem Categoria</option>
                        <?php foreach ($treeSortCategories as $categoryFather) : ?>
                            <option value="<?= $categoryFather->id; ?>" <?= $category->father_id == $categoryFather->id ? "selected" : "" ?>><?= $categoryFather->indentation; ?><?= $categoryFather->name; ?></option>

                        <?php endforeach; ?>
                    </select>
                </div>

                <label class="label">
                    <span class="legend">Ordem:</span>
                    <input type="number" name="priority" placeholder="Insira a posição da categoria" value="<?= $category->priority; ?>" />
                </label>
            </label>

            <?php if (PDF_CATEGORIA) : ?>

                <label class="label">
                    <span class="legend">PDF:</span>
                    <input type="file" name="pdf" placeholder="PDF da categoria" />
                </label>
                <?php if ($category->pdf) : ?>
                    <table class='table'>
                        <thead>
                            <tr>
                                <td>
                                    PDF
                                </td>
                                <td>
                                    Ação
                                </td>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <embed src="<?= url("../$category->pdf"); ?>" alt="" width="150">
                                </td>
                                <td>
                                    <a class="icon-trash-o btn btn-red" href="#" onclick="atualizarBanco()">Deletar</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>

            <?php endif; ?>

            <div class="al-right">
                <input type="hidden" name="type" value="<?= $type ?>">
                <!-- <button class="btn btn-green icon-check-square-o">Salvar</button> -->
            </div>
            <div class="fix-save">
            <button class="btn btn-green icon-check-square-o">Salvar</button>
            </div>
        </form>
    </div>

    <!-- <div>
        <ul>
            </?php foreach ($treeSortCategories as $categoryFather) : ?>
                <li></?= $categoryFather->indentation; ?></?= $categoryFather->name; ?> - </?= $categoryFather->id; ?></li>
            </?php endforeach; ?>
        </ul>
    </div> -->
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
    <?php if ($type === "edit") : ?>

        function atualizarBanco() {
            var confirmado = confirm('Atenção! Você tem certeza que quer realizar essa tarefa?');
            if (confirmado) {
                $.ajax({
                    url: "<?= url("categorias/imagem/{$category->id}") ?>",
                    method: "POST",
                    // data: <?= $category->id ?>,
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
    <?php endif; ?>
</script>
<script>
    // Verificação de dupliciade de URL start
    const inputUrl = document.getElementById('inputUrl');
    let urlList = <?= $arrayListEncod ?>;

    if (inputUrl) {
        function checkUrlDuplicity() {
            for (let key in urlList) {
                const avisoExiste = document.getElementById('avisoUrl');
                if (avisoExiste) {
                    avisoExiste.remove();
                }
                if (urlList.hasOwnProperty(key)) {

                    if (urlList[key].trim() == inputUrl.value.trim()) {
                        console.log('valores iguais');
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
<?php $this->stop() ?>