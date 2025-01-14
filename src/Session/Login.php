<?php

namespace Source\Session;

use App\Models\User;

class Login{

    /**
     * Metodo responsavel por iniciar a sessão
     */
    private static function init(){

        //Verifica o status da sessão
        if(session_status() !== PHP_SESSION_ACTIVE){
            //Inicia a sessão
            session_start();
        }
    }

    /**
     * Métdodo Responsavel por retornar os dados do usuario Logado.
     */
    public static function getUsuarioLogado(){
        self::init();

        //Retorna dados do usuario
        return self::isLogged() ? $_SESSION['usuario'] : null;
    }

    /**
     * Método responsavel por logar o usuario
     */
    public static function login($obUsuario)
    {
        
        //Inicia a sessão
        self::init();


        $_SESSION['usuario'] = [
            'name' => $obUsuario->name,
            'email' => $obUsuario->email
        ];

        

        $url = url("dash");
        //Redireciona o usuario para a index
        header('location: '.$url);
        exit;
    }

    /**
     * Método responsavel por deslogar o usuario
     */
    public static function logout(){
        //Inicia a sessão
        self::init();

        //Remove a sessão do usuario
        unset($_SESSION['usuario']);
        $url = url("login");
         //Redireciona o usuario para a index
         header('location: '.$url);
         exit;



    }

    /**
     * Método responsável por verificar se o usiario esta logado
     */
    public static function isLogged(){
         //Inicia a sessão
         self::init();

        return isset($_SESSION['usuario']['name']);
    }


    /**
     * Método responsavel por obrigar o usuario a estar logado para acessar
     */
    public static function requireLogin(){
        if(!self::isLogged()){
            $url = url("login");
            //Redireciona o usuario para a index
            header('location: '.$url);
            exit;
        }
    }

    /**
     * Método responsavel por obrigar o usuario a estar deslogado para acessar
     */
    public static function requireLogout(){
        if(self::isLogged()){
            header("Location: /");
            exit;
        }
    }

}