<?php

namespace App\Constante;

final class Verif
{
    public const boutiqueOptique='(optique)';
    public const subBoutiqueLunette='(lunettes-de-soleil)|(cadres-optiques)';
    public const subSubBoutiqueLunette ='(lunettes-femme)|(lunettes-homme)|(cadres-optiques)|(lunettes-unisex)';
    public const boutiqueHorlogerie='(horlogerie)';
    public const subBoutiqueHorlogerie='(montres-pour-hommes)|(montres-pour-femmes)|(montres-pour-enfants)|(smartwatches)';
    public const boutiqueParfumerie='(parfumerie)';
    public const boutiqueSubParfumerie='(parfums)|(maquillage)|(appareils)';
    public const subSubBoutiqueParfumerie ='(parfums-pour-hommes)|(parfums-pour-femmes)|(parfums-pour-enfants)|(parfums)
                                    |(levres)|(ongles)|(palettes-coffret)|(pinceaux-accessoires)|(visage)|(yeux)|(maquillage)
                                    |(appareils-pour-hommes)|(appareils-pour-femmes)|(appareils)
                                     ';
    public const boutiqueOccasion='(occasion)';
    public const boutiqueAccessoires='(accessoires)';
    public const boutiqueSubAccessoires='(sac-a-main)|(bijoux)|(articles-pour-cadeaux)|(portefeuilles)|(sac-pour-homme)';
}
