<?php

namespace ApiBundle\EventListener;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Response\ViewInterface;
use AppBundle\HttpFoundation\AppResponse;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class ApiSubscriber
 * @package ApiBundle\EventListener
 */
class ApiSubscriber implements EventSubscriberInterface
{

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ApiSubscriber constructor.
     *
     * @param NormalizerInterface $normalizer A NormalizerInterface instance.
     * @param LoggerInterface     $logger     A LoggerInterface instance.
     */
    public function __construct(
        NormalizerInterface $normalizer,
        LoggerInterface $logger
    ) {
        $this->normalizer = $normalizer;
        $this->logger = $logger;
    }

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
        return [
            KernelEvents::REQUEST => [ 'onRequest', 100 ],
            KernelEvents::VIEW => 'onView',
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    /**
     * Process application/json request.
     * Fetch json data, parse and store them as request parameters.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance.
     *
     * @return void
     *
     * @throws HttpException Then receive invalid json.
     */
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getUri();

        $isApiMethod = (strpos($uri, '/api') !== false)
            || (strpos($uri, '/security/') !== false);
        $content = trim($request->getContent());

        if ($isApiMethod && (strlen($content) > 0)) {
            // Make transformation only for api methods.
            $content = json_decode($content, true);
            if (isset($content['_format'])) {
                unset($content['_format']);
            }

            // Check json parse error.
            $code = json_last_error();
            if ($code !== JSON_ERROR_NONE) {
                $event->setResponse(AppResponse::badRequest(
                    'Invalid json ('. $code .'): '. json_last_error_msg()
                ));

                return;
            }

            $request->request->replace($content);
        }
    }

    /**
     * @param GetResponseForControllerResultEvent $event A
     *                                                   GetResponseForControllerResultEvent
     *                                                   instance.
     *
     * @return void
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        // Works only if current response returned by one of api controllers.
        $uri = $event->getRequest()->getUri();

        $isApiEndpoint = (strpos($uri, '/api') !== false) || (strpos($uri, '/security') !== false);
        if ($isApiEndpoint) {
            $result = $event->getControllerResult();

            if ($result instanceof ViewInterface) {
                $event->setResponse($result->serialize($this->normalizer));
            } else {
                $result = $this->normalize($result);
                $event->setResponse(AppResponse::create($result));
            }
        }
    }

    /**
     * @param GetResponseForExceptionEvent $event A
     *                                            GetResponseForExceptionEvent
     *                                            instance.
     *
     * @return void
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        $uri = $request->getUri();

        if (strpos($uri, '/api') !== false) {
            if ($exception instanceof HttpException) {
                //
                // Handle throwException which have status code.
                // We just get error message and status code, form it in proper
                // structure and send to client.
                //
                // But for 'no route found' message we create 405 response instead
                // of 404.
                //
                if ($exception->getPrevious()
                    && ($exception->getPrevious() instanceof ResourceNotFoundException)) {
                    $response = AppResponse::create('Unknown method.', 405);
                } else {
                    $response = AppResponse::create()
                        ->setStatusCode($exception->getStatusCode())
                        ->setData($exception->getMessage());
                }

                $event->setResponse($response);
            } else {
                //
                // If throwException occurred in one of api methods, we log it and send
                // some message to client.
                //
                $response = AppResponse::create(null, 500);

                $message = $exception->getMessage() . ' in '
                    . $exception->getFile() . ' at ' . $exception->getLine()
                    . ' occurred while processing '
                    . $request->attributes->get('_controller') . ':'
                    . $request->attributes->get('_action');

                $response->setData($message);

                // To log message we also add serialized request.
                $this->logger->error($message, [
                    'trace' => $exception->getTrace(),
                ]);

                $event->setResponse($response);
            }
        }
    }

    /**
     * Normalize response data.
     *
     * @param mixed $data Response data.
     *
     * @return array
     */
    private function normalize($data)
    {
        $groups = [];

        switch (true) {
            case is_array($data):
                return array_map([ $this, 'normalize' ], $data);

            case $data instanceof AbstractPagination:
                $entity = $data->current();
                if ($entity instanceof  NormalizableEntityInterface) {
                    $groups = $entity->defaultGroups();
                }

                return $this->normalizer->normalize($data, null, $groups);

            case is_object($data):
                if ($data instanceof NormalizableEntityInterface) {
                    $groups = $data->defaultGroups();
                }

                return $this->normalizer->normalize($data, null, $groups);
        }

        return $data;
    }
}
