<?php

use Zicht\SymfonyUtil\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $ret = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new Zicht\Bundle\PageBundle\ZichtPageBundle(),
            new Zicht\Bundle\UrlBundle\ZichtUrlBundle(),

            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Zicht\Bundle\MenuBundle\ZichtMenuBundle(),

            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Zicht\Bundle\AdminBundle\ZichtAdminBundle(),

            new Acme\SiteBundle\AcmeSiteBundle()
        ];

        if ($this->getEnvironment() === 'development') {
            $ret[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }
    
        return $ret;
    }
}
