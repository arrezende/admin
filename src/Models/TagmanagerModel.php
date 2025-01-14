<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class TagmanagerModel extends DataLayer
{

    public function __construct()
    {
        parent::__construct("tagsmanager", ["head_value"]);

        //url, description, cover, father_id, pdf, gallery_id, created_at e updated_at
    }

}
