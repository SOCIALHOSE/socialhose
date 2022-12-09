<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\ConfigParametersSectionType;
use AppBundle\AppBundleServices;
use AppBundle\Configuration\ConfigurationMutableInterface;
use AppBundle\Configuration\ConfigurationParameterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class ConfigurationOptionsController
 * @package AdminBundle\Controller
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("configuration")
 */
class ConfigurationOptionsController extends Controller
{

    /**
     * @Route("/", name="admin_configuration_index")
     * @Template
     *
     * @param Request $request A Request instance.
     *
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var ConfigurationMutableInterface $configuration */
        $configuration = $this->get(AppBundleServices::CONFIGURATION);

        $oldParams = $configuration->getParameters();
        /** @var ConfigurationParameterInterface[] $newParams */
        $newParams = array_map(function ($object) {
            return clone $object;
        }, $oldParams);

        $form = $this->createForm(ConfigParametersSectionType::class, $newParams);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updated = [];
            foreach ($newParams as $param) {
                $name = $param->getName();

                if ($oldParams[$name]->getValue() !== $param->getValue()) {
                    $updated[$name] = $param->getValue();
                }
            }

            $configuration->setParameters($updated);
            $configuration->sync();

            // We should re-render form.
            return $this->redirect($request->getRequestUri());
        }

        return [ 'form' => $form->createView() ];
    }
}
