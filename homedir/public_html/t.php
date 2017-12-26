<?php
echo 100;



/*
$mageFilename = 'app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

set_time_limit(0);
ini_set('memory_limit','1024M');
echo "hi";$html="";
        $categories = Mage::getModel('catalog/category')->getCategories(3);
        foreach($categories as $category) {
            $level = $category->getLevel();
            $childClass = $category->hasChildren() ? ' hasChild' : '';
            $html .= '<li class="level' .($level -2) .$childClass. '"><a href="' . $category->getRequestPath(). '"><span>' .$category->getName() . "</span></a>\n";
            if($childClass) $html .=  $this->getTreeCategories($category->getId());
            $html .= '</li>';
        }
        $html = '<ul class="hi '.$level.'jj level' .($level -3). '">' . $html . '</ul>';
        echo  $html;
?>