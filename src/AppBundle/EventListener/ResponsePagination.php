<?php

namespace AppBundle\EventListener;

use AppBundle\Response\SearchResponseInterface;
use Knp\Component\Pager\Event\ItemsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ResponsePagination
 * Need for knp paginator.
 *
 * @package SearchBundle\EventListener
 */
class ResponsePagination implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and
     *  respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority),
     *  array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [ 'knp_pager.items' => [ 'handle', 0 ] ];
    }

    /**
     * @param ItemsEvent $event A ItemsEvent instance.
     *
     * @return void
     */
    public function handle(ItemsEvent $event)
    {
        $response = $event->target;
        if (! $response instanceof SearchResponseInterface) {
            return;
        }
        $event->stopPropagation();

        $event->items = $response->getDocuments();
        $event->count = $response->getTotalCount();
    }
}
