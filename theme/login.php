<?php
if(session_status() !== PHP_SESSION_ACTIVE){
    //Inicia a sessão
    session_start();
    $token = bin2hex(random_bytes(32));
        $_SESSION["csrf_token"] = $token;
}

?><!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="<?=url("/theme/assets/css/boot.css");?>" />
    <link rel="stylesheet" href="<?=url("/theme/assets/css/styles.css");?>" />
    <link rel="stylesheet" href="<?=url("/theme/assets/css/login.css");?>" />


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
    
    <?php if($block == true):?>

        <div class="alert block">Ooopss...Você realizou muitas tentantivas de acesso, tente novamente em 5 minutos</div>
        <?php endif;?>

    <div class="login <?= ($block == true) ? 'block' : '' ;?>"  style='background-image: url(<?=url("/theme/assets/images/bg-login.webp")?>)'>
        <article class="login_box radius">
            <img class="login-img" src="<?=url("/theme/assets/images/logo.png");?>"/>
            <form action="<?=url("login");?>" method="post">
            <input type='hidden' name='csrf_token' value='<?=$token?>'>
                <label>
                    <span class="field icon-envelope">E-mail:</span>
                    <input type="email" placeholder="Informe seu e-mail" name='email' required>
                </label>

                <label>
                    <span class="field icon-unlock-alt">Senha:</span>
                    <input type="password" placeholder="Informe sua senha:" name='password' required>
                </label>

                <!-- <input type="submit" value="Entrar" class="radius gradient gradient-arrezende gradient-hover icon-sign-in"> -->


                <button class="radius gradient gradient-arrezende gradient-hover icon-sign-in" type="submit">Entrar</button>
                
                
                <?php if(isset($_GET['error'])):?>
                    <div class="alert">Ooopss... usuario ou senha não existem</div>

                <?php endif;?>
            </form>

            <footer>
                <p>Desenvolvido por www.<b>arrezende</b>.com.br</p>
                <p>© <?=date("Y");?> - todos os direitos reservados</p>
                
            </footer>
        </article>
    </div>

    
</body>

</html>