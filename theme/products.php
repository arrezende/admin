<?php $this->layout("_theme"); ?>
<?php require __DIR__ . "/widgets/produtos/sidebar.php"; ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-pencil-square-o">Produtos</h2>
        <form method="POST" action='<?= url('busca/produtos'); ?>' class="app_search_form ajax_off">
            <input type="text" name="s" list="browsers" placeholder="Pesquisar Produtos:">
            <button class="icon-search icon-notext"></button>
        </form>
        <!-- <a class="icon-plus-circle btn btn-green" href="produtos/adicionar">Novo Produto</a> -->
    </header>

    <div class="dash_content_app_box">
        <section class="app_blog_home">
            <?php if (!is_null($products)) : ?>
                <?php foreach ($products as $product) : ?>
                    <article class=' <?= $product->status; ?>'>
                        <div class="cover embed radius" style='background-image:url(<?= url("{$product->cover}"); ?>); background-size:cover'></div>
                        <h3 class="title">
                            <a target="_blank" href="<?= url($product->url); ?>"><?= $product->name; ?></a>
                        </h3>
                        <div class="info">
                            <div class="info_aligns">
                                <p class="icon-clock-o"><?= (new DateTime($product->updated_at))->format("d-m-Y"); ?></p>
                                <p class="icon-exchange before-rotate">Ordem: <?= $product->priority; ?></p>
                            </div>
                            <div class="info_aligns v2">
                                <p class="icon-folder"><?= $product->category_name; ?></p>
                            </div>
                            <div class="info_aligns">
                                <p class="icon-flag"><?= $product->tag_name; ?></p>
                                <p class="icon-bookmark"><?= ($product->status == "draft") ? "Rascunho" : ($product->status == "trash" ? "Lixo" : "Publicado"); ?></p>
                            </div>
                        </div>

                        <div class="actions">
                            <a class="icon-pencil btn btn-blue" href="<?= url('produtos/editar/' . $product->id) ?>" title="">Editar</a>
                            <?php if ($product->status != 'trash') : ?>
                                <button class="icon-trash-o btn btn-red" onclick="deletar('<?= url("produtos/remover/$product->id"); ?>')">Deletar</button>
                            <?php else : ?>
                                <button class="icon-check btn btn-green" onclick="ativar('<?= url("produtos/ativar/$product->id"); ?>')">Ativar</button>
                            <?php endif; ?>

                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <h3>Nenhum produto cadastrado</h3>
            <?php endif; ?>

        </section>
        <?= $pagination; ?>
    </div>
</section>