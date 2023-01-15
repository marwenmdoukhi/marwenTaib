<?php

namespace  App\Service\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @param SessionInterface $session
     * @param ProductRepository $productRepository
     * @param RequestStack $request
     */
    public function __construct(SessionInterface $session, ProductRepository $productRepository, RequestStack   $request)
    {
        $this->session=$session;
        $this->productRepository=$productRepository;
        $this->request = $request;
    }

    /**
     * @param int $id
     */
    public function add(int $id)
    {
        $panier= $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id]=1;
        }
        $this->session->set('panier', $panier);
    }

    /**
     * @param int $id
     */
    public function remove(int  $id)
    {
        $panier=$this->session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    /**
     * @param int $id
     */
    public function decrease(int $id)
    {
        $panier= $this->session->get('panier', []);
        if ($panier[$id] >1) {
            $panier[$id]--;
        } else {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    /**
     * @return array
     */
    public function getfullCarte(): array
    {
        $painer=  $this->session->get('panier', []);
        $tab=[];
        foreach ($painer as $id => $qte) {
            $tab[]=[
                'product'=>$this->productRepository->find($id),
                'qte'=>$qte
            ];
        }
        return  $tab;
    }

    /**
     * @return float
     */
    public function gettotal(): float
    {
        $total=0;
        foreach ($this->getfullCarte() as $item) {
            $total+= $item['product']->getNewPrice() * $item ['qte'];
        }
        return $total;
    }

    public function addmuliple($id)
    {
        $session = $this->request->getCurrentRequest()->getSession();
        if (!$session->has('panier')) {
            $session->set('panier', array());
        }
        $panier = $session->get('panier');
        if (array_key_exists($id, $panier)) {
            if ($this->request->getCurrentRequest()->get('qte') != null) {
                $panier[$id] = $this->request->getCurrentRequest()->query->get('qte');
            }
        } else {
            if ($this->request->getCurrentRequest()->query->get('qte') != null) {
                $panier[$id] = $this->request->getCurrentRequest()->query->get('qte');
            } else {
                $panier[$id] = 1;
            }
        }
        $session->set('panier', $panier);
    }
}
