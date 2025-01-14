<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class TagModel extends DataLayer
{

    public function __construct()
    {
        parent::__construct("tags", ["tag_name"]);

    }

    
    public function getAllTags() {
        return (new TagModel())->find()->fetch(true);
    }

}
