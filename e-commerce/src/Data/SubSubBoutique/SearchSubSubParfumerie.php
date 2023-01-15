<?php

namespace  App\Data\SubSubBoutique;

 use App\Entity\Marque;
 use App\Entity\TypeDeMaquillage;
 use App\Entity\Volume;

 class SearchSubSubParfumerie
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
      * @var Volume[]
      */
     public $volume = [];

     /**
      * @var TypeDeMaquillage[]
      */
     public $type = [];

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
