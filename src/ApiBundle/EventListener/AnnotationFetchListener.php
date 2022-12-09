<?php

namespace ApiBundle\EventListener;

use Common\Annotation\AppAnnotationInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class AnnotationFetchListener
 * @package ApiBundle\EventListener
 */
class AnnotationFetchListener
{

    /**
     * @var Reader
     */
    private $reader;

    /**
     * AnnotationFetchListener constructor.
     *
     * @param Reader $reader A Reader instance.
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param FilterControllerEvent $event A FilterControllerEvent instance.
     *
     * @return void
     */
    public function handle(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        $className = ClassUtils::getClass($controller[0]);
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($controller[1]);

        $classAnnotation = $this
            ->getAnnotations($this->reader->getClassAnnotations($class));
        $methodAnnotation = $this
            ->getAnnotations($this->reader->getMethodAnnotations($method));

        $annotations = array_merge($classAnnotation, $methodAnnotation);
        $request = $event->getRequest();
        foreach ($annotations as $key => $annotation) {
            $request->attributes->set($key, $annotation);
        }
    }

    /**
     * Get annotations from array of fetched annotations.
     *
     * @param array $annotations Array of fetched annotations.
     *
     * @return array
     */
    protected function getAnnotations(array $annotations)
    {
        $cwAnnotation = [];
        foreach ($annotations as $annotation) {
            if ($annotation instanceof AppAnnotationInterface) {
                $key = '_'. strtolower(\app\c\getShortName($annotation));
                $cwAnnotation[$key] = $annotation;
            }
        }

        return $cwAnnotation;
    }
}
