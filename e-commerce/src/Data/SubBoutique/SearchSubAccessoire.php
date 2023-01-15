<?php

namespace App\Data\SubBoutique;

use App\Entity\Marque;

class SearchSubAccessoire
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
