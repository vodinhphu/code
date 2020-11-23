<?php
namespace RewardPoint\ApplyRwP\Observer;

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
        $point = $quote->getData('rewardpoint');
        if (!$point) {
            return $this;
        }
        //Set fee data to order
        $order = $observer->getOrder();
        $order->setData('rewardpoint', $point);
        
		return $this;
    }
}
