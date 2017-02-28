<?php

namespace Acme\SiteBundle\Entity\ContentItem;

use Acme\SiteBundle\Entity\ContentItem as BaseContentItem;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Text extends BaseContentItem
{
    /**
     * @ORM\Column(type="string")
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
}
