<?php

namespace Source\Admin;

use Source\Models\User as ModelsUser;
use League\Plates\Engine;
use Source\Session\Login;
use stdClass;

#[\AllowDynamicProperties]

class User{
    private $view;
    private $role;

    public function __construct(){
        $this->view = new Engine(__DIR__."/../../theme");
        $this->role = 'admin';
    }

    public function login(): void
    {
        // print_r([
        //     $_POST["csrf_token"],
        //     $_SESSION["csrf_token"]
        // ]);
        Login::requireLogout();
        if ($this->isLoginBlocked()) {
          
            // exibe mensagem de erro informando que o usuário está bloqueado
            echo 'Usuário bloqueado por 3 minutos.';
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new stdClass();
            $user->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $user->password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
           

            
            
            //if (isset($_POST["csrf_token"]) && $_POST["csrf_token"] === $_SESSION["csrf_token"]) {
                // processar submissão do formulário

                //verifica se o usuario informado existe
                $checkUser = $this->getUserByEmail($user->email);

                if(!is_null($checkUser)){
                    //compara a senha informada com a do banco
                    if (password_verify($user->password, $checkUser->password)) {
                        $user->name = $checkUser->name;
                        Login::login($user);
                        exit; // sai do script pois o login foi feito com sucesso
                    }
                }

                // aumenta o número de tentativas de login e redireciona para a página de login com mensagem de erro
                $_SESSION['login_attempts']++;
                header('Location: '.url('login?error=1'));
                exit;
           /* } else {
               
                // token CSRF inválido - tratar erro
                die('Token CSRF inválido');
            }*/
        }

        // gera um novo token CSRF para cada sessão
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        echo $this->view->render("login",[
            'csrf_token' => $_SESSION['csrf_token'],
            'block' => $this->isLoginBlocked()
        ]);
    }

    public function logout(): void
    {
        Login::logout();
    }

    public static function getUserByEmail($email)
    {
        // use prepared statements para proteger contra injeção de SQL
        $user = (new ModelsUser())->find("email=:email", "email=$email")->fetch();
        return $user;
    }

    private function isLoginBlocked(): bool
    {
        if(!isset($_SESSION['login_attempts'])){
            $_SESSION['login_attempts'] = 1;
        }
        //$_SESSION['login_attempts'] = isset($_SESSION['login_attempts']) ? $_SESSION['login_attempts'] + 1 : 1;

        if ($_SESSION['login_attempts'] >= 5 && !isset($_SESSION['login_block_expires'])) {
            // bloqueia o usuário por 3 minutos
            $_SESSION['login_block_expires'] = time() + 180;
            return true;
        }

        if(isset($_SESSION['login_block_expires']) && $_SESSION['login_block_expires'] > time()){
            return true;
        }

        // if (isset($_SESSION['login_block_expires']) && $_SESSION['login_block_expires'] > time()) {
        //     unset($_SESSION['login_attempts']);
        //     unset($_SESSION['login_block_expires']);
        //     return true;
        // }

       

       
        return false;
    }

    public function createToken()
    {
        
        $token = bin2hex(random_bytes(32));
        $_SESSION["csrf_token"] = $token;

        return $token;
    }
}