<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;


class User extends DataLayer{

    public function __construct()
    {
        parent::__construct("users", ["name", "email", "password"]);

        //created_at e updated_at
    }

}