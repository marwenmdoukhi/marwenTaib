<?php

namespace  App\Data\Boutique;

 use App\Entity\Marque;
 use App\Entity\TypeDeMaquillage;
 use App\Entity\Volume;

 class SearchSolde extends \App\Data\Boutique\SearchOptique
 {
     /**
      * @var integer
      */
     public $page = 1;

     /**
      * @var string
      */
     public $ref = '';

     /**
      * @var array
      */
     public $categories = [];

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
 }
