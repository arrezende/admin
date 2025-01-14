<?php $this->layout("_theme"); ?>
<?php $this->start("styles"); ?>
<?php $this->stop() ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-plus-circle"><?= $title ?></h2>
    </header>

    <div class="dash_content_app_box">
        <div>
            <p><span>Status: </span><?= $tags->active == 1 ? 'Ativado' : 'Desativado'; ?></p>
        </div>
        <form class="app_form" action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
            <label class="label">
                <span class="legend">* Head:</span>
                <textarea style="min-height: 300px" name="head_value" placeholder="Tags do Head - mantenha a tag <script>" required><?= $tags->head_value ?? ''; ?></textarea>
            </label>

            <label class="label">
                <span class="legend">Body:</span>
                <textarea style="min-height: 150px" name="body_value" placeholder="Tags do Body - mantenha a tag <script>"><?= $tags->body_value ?? ''; ?></textarea>
            </label>

            <!-- <div class="al-right">
                <input type="hidden" name="type" value="save"> -->
            <!-- <button class="btn btn-green icon-check-square-o">Salvar</button> -->
            <!-- </div> -->
            <div class="fix-save">
                <button class="btn btn-green icon-check-square-o" onclick="javascript:void(0);checkAction()">Salvar</button>
            </div>
        </form>

        <div class="al-right">
            <button class="btn btn-yellow icon-check-square-o mr-3" onclick="javascript:void(0);">On / off</button>
            <button class="btn btn-red icon-check-square-o" onclick="javascript:void(0);removeTag()">Deletar</button>
        </div>
    </div>

    <!-- <div>
        <ul>
            </?php foreach ($treeSortCategories as $categoryFather) : ?>
                <li></?= $categoryFather->indentation; ?></?= $categoryFather->name; ?> - </?= $categoryFather->id; ?></li>
            </?php endforeach; ?>
        </ul>
    </div> -->
</section>

<?php $this->start("scripts"); ?>
<script>
    function checkAction() {
        // VERIFICAÇÃO ANTES DE CRIAR / EDITAR
        var confirmado = confirm('Atenção! Vocé tem certeza que quer realizar essa tarefa?');
        if (confirmado) {
            document.getElementById('form').submit();
        }
    }
</script>
<script>
    function removeTag() {
        var confirmado = confirm('Atenção! Você tem certeza REMOVER essa tag?');
        if (confirmado) {
            $.ajax({
                url: "<?= url("tagsmanager/delete/{$tags->id}") ?>",
                method: "POST",
                // data: </?= $tags->id ?>,
                success: function(response) {
                    console.log(response);
                    console.log("Tagmanager removida com sucesso!");
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.log("Erro ao remover os dados: " + error);
                }
            });
        }
    }
</script>
<?php $this->stop() ?>