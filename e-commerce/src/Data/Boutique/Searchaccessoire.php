<?php

namespace App\Data\Boutique;

use App\Entity\Marque;

class Searchaccessoire
{
    /**
     * @var integer
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var string
     */
    public $ref = '';

    /**
     * @var array
     */
    public $subcategories = [];

    /**
     * @var Marque[]
     */
    public $marque = [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

    /**
     * @var boolean
     */
    public $promo= false;
}
