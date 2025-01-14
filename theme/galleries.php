<?php $this->layout("_theme"); ?>
<?php require __DIR__ . "/widgets/gallery/sidebar.php"; ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-pencil-square-o">Galerias</h2>
    </header>

    <div class="dash_content_app_box">
        <?php if ($categories) : ?>
            <section class="app_gallery_home">
                <h4 class="title">Categorias</h4>
                <?php foreach ($categories as $category) : ?>
                    <article class=' <?= $category->status; ?>'>
                        <div class="cover embed radius" style='background-image:url(<?= url("{$category->cover}"); ?>); background-size:cover'></div>
                        <h3 class="title">
                            <a target="_blank" href="<?= $category->url; ?>"><?= $category->name; ?></a>
                        </h3>

                        <div class="info">
                            <p class="icon-clock-o"><?= (new DateTime($category->updated_at))->format("d-m-Y"); ?></p>
                            <p class="icon-folder"><?= $category->category_name; ?></p>
                            <p class="icon-bookmark"><?= ($category->status == "draft") ? "Rascunho" : ($category->status == "trash" ? "Lixo" : "Publicado"); ?></p>
                        </div>

                        <div class="actions">
                            <a class="icon-pencil btn btn-blue" href="<?= url("galerias/categorias/editar/$category->id") ?>" title="">Editar</a>
                            <?php if ($category->status != 'trash') : ?>
                                <!-- <button class="icon-trash-o btn btn-red" onclick="deletar('<?= url("produtos/remover/$category->id"); ?>')">Deletar</button> -->
                            <?php else : ?>
                                <button class="icon-check btn btn-green" onclick="ativar('<?= url("produtos/ativar/$category->id"); ?>')">Ativar</button>
                            <?php endif; ?>

                        </div>
                    </article>
                <?php endforeach; ?>

                <!-- <nav class="paginator">
                <a class="paginator_item" title="Primeira página" href="">&lt;&lt;</a>
                <span class="paginator_item paginator_active">1</span>
                <a class="paginator_item" title="Página 2" href="">2</a>
                <a class="paginator_item" title="Página 3" href="">3</a>
                <a class="paginator_item" title="Página 4" href="">4</a>
                <a class="paginator_item" title="Última página" href="">&gt;&gt;</a>
            </nav> -->
            </section>
        <?php endif; ?>
        <?php if ($products) : ?>
            <section class="app_gallery_home">
                <h4 class="title">Produtos</h4>
                <?php foreach ($products as $product) : ?>
                    <article class=' <?= $product->status; ?>'>
                        <div class="cover embed radius" style='background-image:url(<?= url("{$product->cover}"); ?>); background-size:cover'></div>
                        <h3 class="title">
                            <a target="_blank" href="<?= $product->url; ?>"><?= $product->name; ?></a>
                        </h3>

                        <div class="info">
                            <p class="icon-clock-o"><?= (new DateTime($product->updated_at))->format("d-m-Y"); ?></p>
                            <p class="icon-folder"><?= $product->category_name; ?></p>
                            <p class="icon-bookmark"><?= ($product->status == "draft") ? "Rascunho" : ($product->status == "trash" ? "Lixo" : "Publicado"); ?></p>
                        </div>

                        <div class="actions">
                            <a class="icon-pencil btn btn-blue" href="<?= url("galerias/produtos/editar/$product->id") ?>" title="">Editar</a>
                            <?php if ($product->status != 'trash') : ?>
                                <!-- <button class="icon-trash-o btn btn-red" onclick="deletar('<?= url("produtos/remover/$product->id"); ?>')">Deletar</button> -->
                            <?php else : ?>
                                <button class="icon-check btn btn-green" onclick="ativar('<?= url("produtos/ativar/$product->id"); ?>')">Ativar</button>
                            <?php endif; ?>

                        </div>
                    </article>
                <?php endforeach; ?>

                <!-- <nav class="paginator">
                <a class="paginator_item" title="Primeira página" href="">&lt;&lt;</a>
                <span class="paginator_item paginator_active">1</span>
                <a class="paginator_item" title="Página 2" href="">2</a>
                <a class="paginator_item" title="Página 3" href="">3</a>
                <a class="paginator_item" title="Página 4" href="">4</a>
                <a class="paginator_item" title="Última página" href="">&gt;&gt;</a>
            </nav> -->
            </section>
        <?php endif; ?>
    </div>
</section>