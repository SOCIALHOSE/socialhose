<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 */
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            new Gesdinet\JWTRefreshTokenBundle\GesdinetJWTRefreshTokenBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new Miracode\StripeBundle\MiracodeStripeBundle(),

            new AdminBundle\AdminBundle(),
            new IndexBundle\IndexBundle(),
            new CacheBundle\CacheBundle(),
            new UserBundle\UserBundle(),
            new ApiBundle\ApiBundle(),
            new AuthenticationBundle\AuthenticationBundle(),
            new AppBundle\AppBundle(),
            new QueueBundle\QueueBundle(),
            new PaymentBundle\PaymentBundle(),
        ];

        if (in_array($this->getEnvironment(), [ 'dev', 'stage', 'test' ], true)) {
            $bundles[] = new Nelmio\ApiDocBundle\NelmioApiDocBundle();
            $bundles[] = new ApiDocBundle\ApiDocBundle();
            $bundles[] =
                new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        if (in_array($this->getEnvironment(), [ 'dev', 'test' ], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] =
                new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
