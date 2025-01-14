<?php $this->layout("_theme"); ?>
<?php $this->start("styles"); ?>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<?php $this->stop() ?>
<?php
// var_dump($category);
// $title = isset($category) ? $category->name : $product->name;
// $urlForm = !empty($category) ? "galerias/categorias/atualizar/{$category->id}" : "galerias/produtos/atualizar/{$product->id}";
// $urlDelete = $category ? "galerias/remover/": "galerias/produtos/";


?>
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-plus-circle">Editar Galeria - <?= $title ?></h2>
    </header>

    <div class="dash_content_app_box">
        <form action="<?= url("$urlForm"); ?>" class="dropzone" id="my-awesome-dropzone"></form>


        <div class="galleryItems">
            <?php if ($images) : ?>
                <?php foreach ($images as $image) : ?>
                    <div class="galleryItem">
                        <img src="<?= url($image->name); ?>" class=''>
                        <div class="actions">
                            <button class="icon-trash-o btn btn-red" onclick="deletar('<?= url("$urlDelete/$image->id"); ?>')">Deletar</button>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="al-right">
            <a href="<?= $urlNewProduct ?>" class="btn btn-green icon-check-square-o">Novo produto</a>
        </div>
    </div>
</section>
<?php $this->start("scripts"); ?>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.autoDiscover = false;
    let myDropzone = new Dropzone("#my-awesome-dropzone", {
        url: "<?= url("$urlForm"); ?>",
        // uploadMultiple: true,
    });

    myDropzone.on("complete", function(file) {
        // myDropzone.processQueue();
        //   window.location.reload();
    });

    //     var existingImages = <?php //echo $images; 
                                ?>;
    // existingImages.forEach(function(image) {
    //     var mockFile = { name: image.name, id: image.id };
    //     myDropzone.options.addedfile.call(myDropzone, mockFile);
    //     myDropzone.emit("thumbnail", mockFile, image.url);
    // });
</script>
<script>
    function atualizarBanco() {
        var confirmado = confirm('Atenção! Você tem certeza que quer realizar essa tarefa?');
        if (confirmado) {
            $.ajax({
                url: "<?= url("$urlForm") ?>",
                method: "POST",
                // data: </?= $category->id ?>,
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
</script>
<?php $this->stop() ?>