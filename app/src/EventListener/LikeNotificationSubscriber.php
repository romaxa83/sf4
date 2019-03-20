<?php

namespace App\EventListener;

use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;

class LikeNotificationSubscriber implements EventSubscriber
{
    //возвращает массив событий
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush
        ];
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        /** @var $em EntityManagerInterface*/
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        /** @var $collectionUpdate PersistentCollection*/
        foreach ($uow->getScheduledCollectionUpdates() as $collectionUpdate){
            if(!$collectionUpdate->getOwner() instanceof MicroPost){
                continue;
            }

            if('likedBy' !== $collectionUpdate->getMapping()['fieldName']){
                continue;
            }

            $insertDiff = $collectionUpdate->getInsertDiff();

            if(!count($insertDiff)){
                return;
            }

            /** @var MicroPost $microPost */
            $microPost = $collectionUpdate->getOwner();

            $notification = new LikeNotification();
            $notification->setUser($microPost->getUser());
            $notification->setMicroPost($microPost);
            $notification->setLikedBy(reset($insertDiff));

            $em->persist($notification);

            $uow->computeChangeSet(
                $em->getClassMetadata(LikeNotification::class),$notification);
        }
    }
}