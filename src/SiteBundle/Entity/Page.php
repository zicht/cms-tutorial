<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */
namespace Acme\SiteBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zicht\Bundle\PageBundle\Entity\Page as BasePage;
use Zicht\Bundle\PageBundle\Model\ContentItemInterface;
/**
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type")
 */
abstract class Page extends BasePage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $title = '';
    private $contentItems;
    public function __construct()
    {
        $this->contentItems = new ArrayCollection();
    }
    public function getContentItems($region = null)
    {
        return $this->contentItems;
    }
    public function addContentItem(ContentItemInterface $contentItem)
    {
        return $this->contentItems->add($contentItem);
    }
    public function removeContentItem(ContentItemInterface $contentItem)
    {
        $this->contentItems->removeElement($contentItem);
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getId()
    {
        return $this->id;
    }
}
