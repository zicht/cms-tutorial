<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Acme\SiteBundle\Entity\Page;


use Acme\SiteBundle\Entity\ContentItem;
use Acme\SiteBundle\Entity\Page as BasePage;
use Doctrine\ORM\Mapping as ORM;
use Zicht\Bundle\PageBundle\Model\ContentItemMatrix;

/**
 * @ORM\Entity
 */
class ArticlePage extends BasePage
{
    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    public function getContentItemMatrix()
    {
        return ContentItemMatrix::create(ContentItem::class)
            ->region('right')
                ->type('Text');
    }
}
