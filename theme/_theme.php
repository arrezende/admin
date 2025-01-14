<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- <link rel="stylesheet" href="<?=url("/theme/assets/css/boot.css");?>" /> -->
    <link rel="stylesheet" href="<?=url("/theme/assets/css/styles.css");?>" />
    <link rel="stylesheet" href="<?=url("/theme/assets/css/style.css");?>" />


    <title>Painel Administrativo</title>
    <?=$this->section('styles')?>
</head>

<body>

    <div class="ajax_load">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
            <p class="ajax_load_box_title">Aguarde, carregando...</p>
        </div>
    </div>

    <div class="ajax_response"></div>

    <div class="mce_upload">
        <div class="mce_upload_box">
            <form class="app_form" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="mce_uplaod" value="true" />
                <label>
                    <label class="legend">Selecione uma imagem JPG ou PNG:</label>
                    <input accept="image/*" type="file" name="image" required />
                </label>
                <button class="btn btn-blue icon-upload">Enviar Imagem</button>
            </form>
        </div>
    </div>

    <div class="dash">
        <aside class="dash_sidebar">
            <article class="dash_sidebar_user">
                <div><img class="dash_sidebar_user_thumb" src="<?=url("/theme/assets/images/logo.png");?>" /></div>
            </article>

            <ul class="dash_sidebar_nav">
                <?php
                if($this->section("sidebar")):
                    echo $this->section("sidebar");
                else:
            ?>
                <li class="dash_sidebar_nav_li "><a class="icon-home" href="<?=url("dash");?>">Dashboard</a></li>
                <li class="dash_sidebar_nav_li "><a class="icon-folder" href="<?=url("categorias");?>">Categorias</a>
                </li>
                <li class="dash_sidebar_nav_li "><a class="icon-folder" href="<?=url("produtos");?>">Produtos</a></li>
                <li class="dash_sidebar_nav_li "><a class="icon-folder" href="<?=url("tags");?>">Tags</a></li>
                <li class="dash_sidebar_nav_li "><a class="icon-camera" href="<?=url("galerias");?>">Galeria</a></li>
                <li class="dash_sidebar_nav_li "><a class="icon-tag" href="<?=url("tagsmanager");?>">Tagmanager</a></li>
                <li class="dash_sidebar_nav_li "><a class="icon-sign-out" href="<?=url("logout");?>">Sair</a></li>
                <?php
              
            endif;
            ?>
            </ul>
        </aside>
        <section class="dash_content">
            <div class="dash_userbar">
                <div class="dash_userbar_box">
                    <div class="dash_content_box">
                        <h1 class="icon-cog transition"><a href="<?=url("/");?>">Painel
                                Administrativo</a></h1>
                    </div>
                    <div class="dash_userbar_box_bar">
                        <span class="icon-menu icon-notext mobile_menu transition"></span>
                    </div>
                </div>
            </div>

            <div class="dash_content_box">
                <?=$this->section('content')?>
            </div>
        </section>
    </div>

    <script src="<?=url("/theme/assets/js/jquery.min.js");?>"></script>
    <script src="<?=url("/theme/assets/js/jquery.form.js");?>"></script>
    <script src="<?=url("/theme/assets/js/jquery-ui.js");?>"></script>
    <script src="<?=url("/theme/assets/js/jquery.mask.js");?>"></script>
    <script src="<?=url("/theme/assets/js/tinymce/tinymce.min.js");?>"></script>
    <script src="<?=url("/theme/assets/js/scripts.js");?>"></script>
    <?=$this->section('scripts')?>
</body>

</html>