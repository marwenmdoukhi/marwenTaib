<?php

namespace  App\Data\Boutique;

 use App\Entity\FormeDuCadran;
 use App\Entity\Marque;
 use App\Entity\MatierBracelet;
 use App\Entity\Style;
 use App\Entity\TypeDuMouvement;

 class SearcHorlogerie
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
      * @var FormeDuCadran[]
      */
     public $formehorlogries = [];

     /**
      * @var MatierBracelet[]
      */
     public $matierBracelet = [];

     /**
      * @var TypeDuMouvement[]
      */
     public $typedeMouvmemnet = [];

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
