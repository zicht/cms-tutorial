<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Acme\SiteBundle\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zicht\Bundle\FrameworkExtraBundle\Fixture\Builder;

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

        Builder::create('Acme\\SiteBundle\\Entity')
            ->always(function ($object) use ($em) {
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

        Builder::create('Zicht\Bundle\MenuBundle\Entity')
            ->always(function($object) use ($em) {
                $em->persist($object);
            })
            ->MenuItem('main', '', 'main')
                ->setLanguage('en')

                ->MenuItem('Home', '/en/page/1', 'home')->end()
                ->MenuItem('Products', '/en/page/2', '')
                    ->MenuItem('Product A', '/en/page/3', '')->end()
                    ->MenuItem('Product B', '/en/page/4', '')->end()
                    ->MenuItem('Product C', '/en/page/5', '')->end()
                ->end()
                ->MenuItem('Products', '/en/page/6', '')->end()

            ->end();

        $em->flush();
    }
}