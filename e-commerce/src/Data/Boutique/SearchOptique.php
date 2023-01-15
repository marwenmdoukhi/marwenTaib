<?php

namespace  App\Data\Boutique;

 use App\Entity\Cadre;
 use App\Entity\Category;
 use App\Entity\Forme;
 use App\Entity\Marque;
 use App\Entity\Style;

 class SearchOptique
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
      * @var Style[]
      */
     public $style = [];

     /**
      * @var Forme[]
      */
     public $formes = [];

     /**
      * @var Cadre[]
      */
     public $cadres = [];

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

     /**
      * @var string
      */
     public $sex;
 }
