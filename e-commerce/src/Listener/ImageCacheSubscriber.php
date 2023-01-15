<?php

namespace App\Listener;

use App\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    /**
     * @var CacheManager
     */
    private $cache_manager;

    /**
     * @var UploaderHelper
     */
    private $helper;

    public function __construct(CacheManager $cache_manager, UploaderHelper $helper)
    {
        $this->cache_manager = $cache_manager;
        $this->helper = $helper;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Product) {
            return;
        }
        $this->cache_manager->remove($this->helper->asset($entity, 'imageFile'));
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!($entity instanceof Product)) {
            return;
        }
        if ($entity->getImageFile() instanceof UploadedFile) {
            $this->cache_manager->remove($this->helper->asset($entity, 'imageFile'));
        }
    }
}
