<?php
/**
 * @author Gerard van Helden <gerard@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Acme\SiteBundle\Admin\Page;

use Acme\SiteBundle\Admin\PageAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class ArticlePageAdmin extends PageAdmin
{
    public function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper->tab('admin.tab.general')->add('content');
    }
}