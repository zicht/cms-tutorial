<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Acme\SiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zicht\Bundle\FrameworkExtraBundle\Fixture\Builder;
use Zicht\Bundle\MenuBundle\Entity\MenuItem;

class SiteFixtures implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function load(ObjectManager $manager)
    {
        $em = $this->container->get('doctrine')->getManager();

        $pages = [];
        Builder::create('Acme\\SiteBundle\\Entity')
            ->always(function ($object) use ($em, &$pages) {
                $pages[$object->getTitle()]= $object;
                $object->setLanguage('en');
                $em->persist($object);
            })
            ->ArticlePage('Home')
                ->setContent('<p>Welcome :)</p>')
            ->end()
            ->ArticlePage('Products')
                ->setContent('<p>We also have cool products</p>')
            ->end()
            ->ArticlePage('Product A')
                ->setContent('<p>A for "Awesome"</p>')
            ->end()
            ->ArticlePage('Product B')
                ->setContent('<p>B for "Better"</p>')
            ->end()
            ->ArticlePage('Product C')
                ->setContent('<p>C for "Cool"</p>')
            ->end()
            ->ArticlePage('Contact')
                ->setContent('<p>Contact us whenever you wish</p>')
            ->end();

        $em->flush();

        $urlProvider = $this->container->get('zicht_page.page_url_provider');

        Builder::create('Zicht\Bundle\MenuBundle\Entity')
            ->always(function(MenuItem $object) use ($em, $urlProvider, $pages) {
                if (isset($pages[$object->getTitle()])) {
                    $object->setPath($urlProvider->url($pages[$object->getTitle()]));
                }
                $em->persist($object);
            })
            ->MenuItem('main', '', 'main')
                ->setLanguage('en')
                    ->MenuItem('Home', null, 'home')->end()
                    ->MenuItem('Products')
                    ->MenuItem('Product A')->end()
                    ->MenuItem('Product B')->end()
                    ->MenuItem('Product C')->end()
                ->end()
                ->MenuItem('Contact', null)->end()
            ->end();

        $em->flush();
    }
}
