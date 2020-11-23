<?php
namespace Team3Vendor\GiftwrapSlider\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddGiftwrapToOrderObserver implements ObserverInterface
{
    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getQuote();
        //save data
        $point = $quote->getData('rewardpoint');
        $customer_id = $quote->getData('customer_id');
        $rewardpointDB = $quote->getData('rewardpointDB');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
        if($rewardpointDB>0)
        {
            $rewardpointDB1 = $rewardpointDB*(-1);
            $tableName = $resource->getTableName('reward_point_history');
            $sql = "INSERT INTO ".$tableName." (customer_id,action,point,type,order_id,author) values ($customer_id,'Order Paid',$rewardpointDB1,'shopping cart',1,'Admin')";
            $connection->query($sql);

            $tableName2 = "reward_point" ;
            $sql2 = "Select * FROM " . $tableName2." Where customer_id=".$customer_id;
            $result = $connection->fetchAll($sql2);
            $count = count($result);
            if($count>0)
            {
                //code update
                foreach ($result as $row) {
                $pointOld = $row["point"];
                }
                $newPoint = $pointOld - $rewardpointDB;
                $sql3 = "Update " . $tableName2 . " Set point = ".$newPoint." where customer_id = ".$customer_id;
                $connection->query($sql3);
                $observer->getOrder()->setData('flash_rewardpoint', 1);

            }
        }
        //end save data
        $giftwrap = $quote->getGiftwrap();
        $giftwrapName = $quote->getGiftwrapName();
        $giftmessage = $quote->getGiftmessage();
        if (!$giftwrap) {
            return $this;
        }
        //Set fee data to order
        $order = $observer->getOrder();
        $order->setData('giftwrap', $giftwrap);
        $order->setData('giftwrap_name', $giftwrapName);
        $order->setData('giftmessage', $giftmessage);
        
		return $this;
    }
}
