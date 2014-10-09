<?php

class Elgentos_AutoConnect_Model_Observer extends Mage_Core_Model_Abstract
{

    protected  function getConfig($key)
    {
        return Mage::getStoreConfig('autoconnect/general/' . $key, Mage::app()->getStore());
    }

    public function catalogProductSaveBefore($observer)
    {
        if ($this->getConfig('disable_ext')) return;
        if (!$this->getConfig('autodisconnect')) return;

        $connect = $this->getConfig('connect');
        if($connect==false) $connect = 'up_sell';
        $connects = explode(',',$connect);

        foreach($connects as $connect) {
            $_product = $observer->getProduct();
            $linkApi = Mage::getModel('Mage_Catalog_Model_Product_Link_Api');
            $localProducts = $linkApi->items($connect, $_product->getId());
            foreach($localProducts as $localProduct) {
                $alreadyConnected = false;
                $foreignItems = $linkApi->items($connect, $localProduct['product_id']);
                foreach($foreignItems as $foreignItem) {
                    if($foreignItem['product_id']==$_product->getId()) {
                        $alreadyConnected = true;
                    }
                }
                if($alreadyConnected) {
                    if (!$linkApi->remove($connect, $localProduct['product_id'], $_product->getId())) {
                        Mage::log('Removing '.$connect.' assignment '.$localProduct['product_id'].' to '.$_product->getId().' failed.');
                    }
                }
            }
        }
    }

    public function catalogProductSaveAfter($observer)
    {
        if ($this->getConfig('disable_ext')) return;

        $connect = $this->getConfig('connect');
        if($connect==false) $connect = 'up_sell';
        $connects = explode(',',$connect);

        foreach($connects as $connect) {
            $_product = $observer->getProduct();
            $linkApi = Mage::getModel('Mage_Catalog_Model_Product_Link_Api');
            $localProducts = $linkApi->items($connect, $_product->getId());
            foreach($localProducts as $localProduct) {
                $alreadyConnected = false;
                $foreignItems = $linkApi->items($connect, $localProduct['product_id']);
                foreach($foreignItems as $foreignItem) {
                    if($foreignItem['product_id']==$_product->getId()) {
                        $alreadyConnected = true;
                    }
                }
                if(!$alreadyConnected) {
                    if (!$linkApi->assign($connect, $localProduct['product_id'], $_product->getId(), array('position' => 0))) {
                        Mage::log('Assigning '.$connect.' '.$localProduct['product_id'].' to '.$_product->getId().' failed.');
                    }
                }
            }
        }
    }
}