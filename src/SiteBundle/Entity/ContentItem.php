<?php

namespace Acme\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zicht\Bundle\PageBundle\Entity\ContentItem as BaseContentItem;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 */
class ContentItem extends BaseContentItem
{
}