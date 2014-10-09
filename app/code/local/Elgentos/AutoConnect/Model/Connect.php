<?php
class Elgentos_AutoConnect_Model_Connect
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return[] = array('value'=>'up_sell','label'=>'Up sell');
        $return[] = array('value'=>'cross_sell','label'=>'Cross sell');
        $return[] = array('value'=>'related','label'=>'Related products');
        return $return;
    }

}