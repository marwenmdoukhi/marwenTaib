<?php

namespace App\Repository;

use App\Data\Boutique\Searchaccessoire;
use App\Data\Boutique\SearchOccasion;
use App\Data\Boutique\SearchOptique;
use App\Data\Boutique\SearcHorlogerie;
use App\Data\Boutique\SearchParfumerie;
use App\Data\Boutique\SearchSolde;
use App\Data\SubBoutique\SearchSubAccessoire;
use App\Data\SubBoutique\SearchSubHorlogerie;
use App\Data\SubBoutique\SearchSubOptique;
use App\Data\SubBoutique\SearchSubParfumerie;
use App\Data\SubSubBoutique\SearchSubSubOptique;
use App\Data\SubSubBoutique\SearchSubSubParfumerie;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $pagination;

    /**
     * @param ManagerRegistry $registry
     * @param PaginatorInterface $pagination
     */
    public function __construct(ManagerRegistry $registry, PaginatorInterface $pagination)
    {
        $this->pagination = $pagination;
        parent::__construct($registry, Product::class);
    }

    /**
     * @return int|mixed|string
     */
    public function nouveauxProduitClientHome()
    {
        return $this->createQueryBuilder('p')
            ->addOrderBy('p.id', 'desc')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function nouveauxProduitClientHomeMobile()
    {
        return $this->createQueryBuilder('p')
            ->addOrderBy('p.id', 'desc')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function xmlSolde()
    {
        return $this->createQueryBuilder('p')
            ->where('p.promo =true')
            ->addOrderBy('p.id', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function pormoProduitClientHome()
    {
        return $this->createQueryBuilder('p')
            ->where('p.promo = true')
            ->addOrderBy('p.newprice', 'desc')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return int|mixed|string
     */
    public function pormoProduitClientHomeMobile()
    {
        return $this->createQueryBuilder('p')
            ->where('p.promo = true')
            ->addOrderBy('p.newprice', 'desc')
            ->setMaxResults(8)
            ->getQuery()
            ->getResult();
    }

    public function totalpromo()
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT  count(p)
                FROM App\Entity\Product p 
                where 
                p.promo=true
            ');
        return  $query->getSingleScalarResult();
    }



    public function meilleurproduitHome()
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT  p
                FROM App\Entity\Product p ,App\Entity\CommandeArticle c 
                JOIN c.Product a
                JOIN  c.Commande ca
                where 
                ca.terminer=1
                GROUP BY a
                ORDER BY SUM(c.Product) DESC 
            ')->setMaxResults(12);
        return  $query->getResult() ;

    }


    public function meilleurproduitHomeMobile()
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT  p
                FROM App\Entity\Product p ,App\Entity\CommandeArticle c 
                JOIN c.Product a
                JOIN  c.Commande ca
                where 
                ca.terminer=1
                GROUP BY a
                ORDER BY SUM(c.Product) DESC 
            ')->setMaxResults(8)
        ;
        return  $query->getResult()  ;
    }


    /**
     * @param $slug
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findbyslug($slug,$id)
    {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->andWhere('p.id = :id')
            ->setParameters([
                'slug' => $slug,
                'id' => $id,
            ])
            ->getQuery()
            ->getSingleResult()
            ;
    }

    /**
     * @param $slug
     * @return int|mixed|string
     */
    public function productAsec($slug)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'ca')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.newprice', 'desc')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()

            ;
    }

    /**
     * @param $slug
     * @return int|mixed|string
     */
    public function productDesc($slug)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'ca')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.newprice', 'asc')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()

            ;
    }

    /**
     * @param $slug
     * @return int|mixed|string
     */
    public function productInCategory($slug)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'ca')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.newprice', 'desc')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return int|mixed|string
     */
    public function produit()
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT p
             FROM App\Entity\Product p 
             "
        );
        return  $query->getResult()  ;
    }

    /**
     * @return int|mixed|string
     */
    public function produithome()
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT p
                FROM App\Entity\Product p 
                where 
                p.produithome=true
                "
        );
        return  $query->getResult()  ;
    }

    /**
     * @param $array
     * @return int|mixed|string
     */
    public function getArray($array)
    {
        $qb = $this->createQueryBuilder('u')
            ->Select('u')
            ->Where('u.id IN (:array)')
            ->setParameter('array', $array);
        return $qb->getQuery()->getResult();
    }


    /**
     * @param SearchOptique $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueOptique(SearchOptique $search, $slug): PaginationInterface
    {
        $query = $this->getSearchBoutiqueOptiqueQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            //$query->setCacheable(true)->setCacheRegion('entity_that_rarely_changes')->getResult(),
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @param SearchOptique $search
     * @param $slug
     * @return int[]
     */
    public function findMinMaxBoutiqueOptique(SearchOptique $search, $slug): array
    {
        $results = $this->getSearchBoutiqueOptiqueQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchOptique $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchBoutiqueOptiqueQuery(SearchOptique $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->innerJoin('p.categories', 'ca')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('sb.id IN (:subcategories)')
                ->setParameter('subcategories', $search->subcategories);
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }

        if (!empty($search->style)) {
            $query = $query
                ->andWhere('p.style IN (:style)')
                ->setParameter('style', $search->style);
        }

        if (!empty($search->formes)) {
            $query = $query
                ->andWhere('p.forme IN (:forme)')
                ->setParameter('forme', $search->formes);
        }
        if (!empty($search->cadres)) {
            $query = $query
                ->andWhere('p.cadres IN (:cadre)')
                ->setParameter('cadre', $search->cadres);
        }
        if (!empty($search->sex)) {
            $query = $query
                ->andWhere('p.sex IN (:sex)')
                ->setParameter('sex', $search->sex);
        }
        return $query;
    }

    /**
     * @param SearchSubOptique $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findBySubBoutiqueOptique(SearchSubOptique $search, $slug): PaginationInterface
    {
        $query = $this->getSearchSubBoutiqueOptiqueQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @return integer[]
     */
    public function findMinMaxfindBySubBoutiqueOptique(SearchSubOptique $search, $slug): array
    {
        $results = $this->getSearchSubBoutiqueOptiqueQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchSubOptique $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchSubBoutiqueOptiqueQuery(SearchSubOptique $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->innerJoin('p.subSubCategories', 'ssb')
            ->where('sb.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('ssb.id IN (:subSubCategories)')
                ->setParameter('subSubCategories', $search->subcategories);
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }

        if (!empty($search->style)) {
            $query = $query
                ->andWhere('p.style IN (:style)')
                ->setParameter('style', $search->style);
        }
        if (!empty($search->formes)) {
            $query = $query
                ->andWhere('p.forme IN (:forme)')
                ->setParameter('forme', $search->formes);
        }
        if (!empty($search->cadres)) {
            $query = $query
                ->andWhere('p.cadres IN (:cadre)')
                ->setParameter('cadre', $search->cadres);
        }
        if (!empty($search->sex)) {
            $query = $query
                ->andWhere('p.sex IN (:sex)')
                ->setParameter('sex', $search->sex);
        }
        return $query;
    }

    /**
     * @param SearchSubSubOptique $search
     * @param $slug
     * @param $name
     * @return PaginationInterface
     */
    public function findBySubSubBoutiqueOptique(SearchSubSubOptique $search, $slug, $name): PaginationInterface
    {
        $query = $this->getSearchSubSubBoutiqueOptiqueQuery($search, $slug, $name)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @return integer[]
     */
    public function findMinMaxfindBySubSubBoutiqueOptique(SearchSubSubOptique $search, $slug, $name): array
    {
        $results = $this->getSearchSubSubBoutiqueOptiqueQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchSubSubOptique $search
     * @param $slug
     * @param $name
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchSubSubBoutiqueOptiqueQuery(SearchSubSubOptique $search, $slug, $name, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->innerJoin('p.subSubCategories', 'ssb')
            ->where('ssb.slug= :slug')
            ->andWhere('sb.slug =:name')
            ->setParameters(['slug'=>$slug , 'name'=>$name])
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('ssb.id IN (:subcategories)')
                ->setParameter('subcategories', $search->subcategories);
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }

        if (!empty($search->style)) {
            $query = $query
                ->andWhere('p.style IN (:style)')
                ->setParameter('style', $search->style);
        }
        if (!empty($search->formes)) {
            $query = $query
                ->andWhere('p.forme IN (:forme)')
                ->setParameter('forme', $search->formes);
        }
        if (!empty($search->cadres)) {
            $query = $query
                ->andWhere('p.cadres IN (:cadre)')
                ->setParameter('cadre', $search->cadres);
        }
        if (!empty($search->sex)) {
            $query = $query
                ->andWhere('p.sex IN (:sex)')
                ->setParameter('sex', $search->sex);
        }
        return $query;
    }


    /**
     * @param SearchOptique $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueHorlogerie(SearcHorlogerie $search, $slug): PaginationInterface
    {
        $query = $this->getSearchBoutiqueHorlogerieQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @param SearcHorlogerie $search
     * @param $slug
     * @return int[]
     */
    public function findMinMaxBoutiqueHorlogrie(SearcHorlogerie $search, $slug): array
    {
        $results = $this->getSearchBoutiqueHorlogerieQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearcHorlogerie $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchBoutiqueHorlogerieQuery(SearcHorlogerie $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->innerJoin('p.categories', 'ca')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('sb.id IN (:subcategories)')
                ->setParameter('subcategories', $search->subcategories);
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }

        if (!empty($search->style)) {
            $query = $query
                ->andWhere('p.style IN (:style)')
                ->setParameter('style', $search->style);
        }
        if (!empty($search->formehorlogries)) {
            $query = $query
                ->andWhere('p.formeDuCadran IN (:formehorlogries)')
                ->setParameter('formehorlogries', $search->formehorlogries);
        }

        if (!empty($search->typedeMouvmemnet)) {
            $query = $query
                ->andWhere('p.typeDuMouvement IN (:typedeMouvmemnet)')
                ->setParameter('typedeMouvmemnet', $search->typedeMouvmemnet);
        }
        if (!empty($search->matierBracelet)) {
            $query = $query
                ->andWhere('p.matierBracelet IN (:matierBracelet)')
                ->setParameter('matierBracelet', $search->matierBracelet);
        }
        return $query;
    }

    /**
     * @param SearchSubOptique $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueSubHorlogerie(SearchSubHorlogerie $search, $slug): PaginationInterface
    {
        $query = $this->getSearchSubBoutiqueHorlogrieQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @return integer[]
     */
    public function findMinMaxfindBySubBoutiqueHorlogerie(SearchSubHorlogerie $search, $slug): array
    {
        $results = $this->getSearchSubBoutiqueHorlogrieQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchSubHorlogerie $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchSubBoutiqueHorlogrieQuery(SearchSubHorlogerie $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->where('sb.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }

        if (!empty($search->style)) {
            $query = $query
                ->andWhere('p.style IN (:style)')
                ->setParameter('style', $search->style);
        }
        if (!empty($search->formehorlogries)) {
            $query = $query
                ->andWhere('p.formeDuCadran IN (:formehorlogries)')
                ->setParameter('formehorlogries', $search->formehorlogries);
        }

        if (!empty($search->typedeMouvmemnet)) {
            $query = $query
                ->andWhere('p.typeDuMouvement IN (:typedeMouvmemnet)')
                ->setParameter('typedeMouvmemnet', $search->typedeMouvmemnet);
        }
        if (!empty($search->matierBracelet)) {
            $query = $query
                ->andWhere('p.matierBracelet IN (:matierBracelet)')
                ->setParameter('matierBracelet', $search->matierBracelet);
        }
        return $query;
    }

    /**
     * @param SearchOccasion $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueOccasion(SearchOccasion $search, $slug): PaginationInterface
    {
        $query = $this->getSearchBoutiqueOccasionQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @param SearchOccasion $search
     * @param $slug
     * @return int[]
     */
    public function findMinMaxBoutiqueOccasion(SearchOccasion $search, $slug): array
    {
        $results = $this->getSearchBoutiqueOccasionQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchOccasion $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchBoutiqueOccasionQuery(SearchOccasion $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.categories', 'ca')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('p.subCategory IN (:subcategories)')
                ->setParameter('subcategories', $search->subcategories);
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }

        return $query;
    }


    /**
     * @param SearchParfumerie $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueParfumerie(SearchParfumerie $search, $slug): PaginationInterface
    {
        $query = $this->getSearchBoutiqueParfumerieQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @param SearchParfumerie $search
     * @param $slug
     * @return int[]
     */
    public function findMinMaxBoutiqueParfumerie(SearchParfumerie $search, $slug): array
    {
        $results = $this->getSearchBoutiqueParfumerieQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchParfumerie $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchBoutiqueParfumerieQuery(SearchParfumerie $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.categories', 'ca')
            ->innerJoin('p.subCategory', 'sb')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('sb.id IN (:subcategories)')
                ->setParameter('subcategories', $search->subcategories);
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }
        if (!empty($search->volume)) {
            $query = $query
                ->andWhere('p.volume IN (:volume)')
                ->setParameter('volume', $search->volume);
        }
        if (!empty($search->type)) {
            $query = $query
                ->andWhere('p.typeDeMaquillage IN (:type)')
                ->setParameter('type', $search->type);
        }
        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }
        return $query;
    }


    /**
     * @param SearchSubParfumerie $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueSubParfumerie(SearchSubParfumerie $search, $slug): PaginationInterface
    {
        $query = $this->getSearchSubBoutiqueParfumerieQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @return integer[]
     */
    public function findMinMaxfindBySubBoutiqueParfumerie(SearchSubParfumerie $search, $slug): array
    {
        $results = $this->getSearchSubBoutiqueParfumerieQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchSubParfumerie $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchSubBoutiqueParfumerieQuery(SearchSubParfumerie $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->innerJoin('p.subSubCategories', 'ssb')
            ->where('sb.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('ssb.id IN (:subSubCategories)')
                ->setParameter('subSubCategories', $search->subcategories);
        }

        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }
        if (!empty($search->volume)) {
            $query = $query
                ->andWhere('p.volume IN (:volume)')
                ->setParameter('volume', $search->volume);
        }
        if (!empty($search->type)) {
            $query = $query
                ->andWhere('p.typeDeMaquillage IN (:type)')
                ->setParameter('type', $search->type);
        }
        if (!empty($search->sex)) {
            $query = $query
            ->andWhere('p.sex IN (:sex)')
            ->setParameter('sex', $search->sex);
        }

        return $query;
    }


    /**
     * @param SearchSubSubParfumerie $search
     * @param $slug
     * @param $name
     * @return PaginationInterface
     */
    public function findBySubSubBoutiqueParfumerie(SearchSubSubParfumerie $search, $slug, $name): PaginationInterface
    {
        $query = $this->getSearchSubSubBoutiqueParfumerieQuery($search, $slug, $name)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @return integer[]
     */
    public function findMinMaxfindBySubSubBoutiqueParfumerie(SearchSubSubParfumerie $search, $slug, $name): array
    {
        $results = $this->getSearchSubSubBoutiqueParfumerieQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchSubSubParfumerie $search
     * @param $slug
     * @param $name
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchSubSubBoutiqueParfumerieQuery(SearchSubSubParfumerie $search, $slug, $name, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->innerJoin('p.subSubCategories', 'ssb')
            ->where('ssb.slug= :slug')
            ->andWhere('sb.slug =:name')
            ->setParameters(['slug'=>$slug , 'name'=>$name])
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if (!empty($search->volume)) {
            $query = $query
                ->andWhere('p.volume IN (:volume)')
                ->setParameter('volume', $search->volume);
        }
        if (!empty($search->type)) {
            $query = $query
                ->andWhere('p.typeDeMaquillage IN (:type)')
                ->setParameter('type', $search->type);
        }
        if (!empty($search->sex)) {
            $query = $query
                ->andWhere('p.sex IN (:sex)')
                ->setParameter('sex', $search->sex);
        }

        return $query;
    }

    /**
     * @param Searchaccessoire $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueAccessoire(Searchaccessoire $search, $slug): PaginationInterface
    {
        $query = $this->getSearchBoutiqueAccessoireQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @param Searchaccessoire $search
     * @param $slug
     * @return int[]
     */
    public function findMinMaxBoutiqueAccessoire(Searchaccessoire $search, $slug): array
    {
        $results = $this->getSearchBoutiqueAccessoireQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param Searchaccessoire $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchBoutiqueAccessoireQuery(Searchaccessoire $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.categories', 'ca')
            ->innerJoin('p.subCategory', 'sb')
            ->where('ca.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }
        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->subcategories)) {
            $query = $query
                ->andWhere('sb.id IN (:subcategories)')
                ->setParameter('subcategories', $search->subcategories);
        }

        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }
        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }
        return $query;
    }




    /**
     * @param SearchSubParfumerie $search
     * @param $slug
     * @return PaginationInterface
     */
    public function findSearchBoutiqueSubAccessoires(SearchSubAccessoire $search, $slug): PaginationInterface
    {
        $query = $this->getSearchSubBoutiqueAccessoiresQuery($search, $slug)->getQuery();
        return $this->pagination->paginate(
            $query->execute(),
            $search->page,
            15
        );
    }

    /**
     * @return integer[]
     */
    public function findMinMaxfindBySubBoutiqueAccessoires(SearchSubAccessoire $search, $slug): array
    {
        $results = $this->getSearchSubBoutiqueAccessoiresQuery($search, $slug, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()->execute();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    /**
     * @param SearchSubParfumerie $search
     * @param $slug
     * @param false $ignorePrice
     * @return QueryBuilder
     */
    private function getSearchSubBoutiqueAccessoiresQuery(SearchSubAccessoire $search, $slug, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->innerJoin('p.subCategory', 'sb')
            ->where('sb.slug= :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('p.id', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }

        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        return $query;
    }

    /**
     * @param SearchSolde $search
     * @return PaginationInterface
     */
    public function findSearchSolde(SearchSolde $search): PaginationInterface
    {
        $query = $this->getSearchQuery($search)->getQuery();
        return $this->pagination->paginate(
            $query,
            $search->page,
            15
        );
    }

    /**
     * @return integer[]
     */
    public function findMinMaxSolde(SearchSolde $search): array
    {
        $results = $this->getSearchQuery($search, true)
            ->select('MIN(p.newprice) as min', 'MAX(p.newprice) as max')
            ->getQuery()
            ->getScalarResult();
        return [(int)$results[0]['min'], (int)$results[0]['max']];
    }

    private function getSearchQuery(SearchSolde $search, $ignorePrice = false): QueryBuilder
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->where('p.promo =true')
            ->join('p.categories', 'c')
            ->addOrderBy('p.newprice', 'asc')
        ;

        if (!empty($search->ref)) {
            $query = $query
                ->andWhere('p.refrence LIKE :ref')
                ->setParameter('ref', "%{$search->ref}%");
        }

        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.newprice <= :max')
                ->setParameter('max', $search->max);
        }
        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }
        if (!empty($search->marque)) {
            $query = $query
                ->andWhere('p.marque IN (:marque)')
                ->setParameter('marque', $search->marque);
        }

        return $query;
    }
}
