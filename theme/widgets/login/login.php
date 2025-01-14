<div class="login">
    <article class="login_box radius">
        <h1 class="hl">Login</h1>
        <form action="dash.php?app=dash/home" method="post">
            <label>
                <span class="field icon-envelope">E-mail:</span>
                <input type="email" placeholder="Informe seu e-mail" required/>
            </label>

            <label>
                <span class="field icon-unlock-alt">Senha:</span>
                <input type="password" placeholder="Informe sua senha:" required/>
            </label>

            <button class="radius gradient gradient-green gradient-hover icon-sign-in">Entrar</button>
        </form>

        <footer>
            
            <p>&copy; <?= date("Y"); ?> - todos os direitos reservados</p>
            
        </footer>
    </article>
</div>