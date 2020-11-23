<?php
namespace RewardPoint\ApplyRwP\Model\Total\Quote;

class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return $this|\Magento\Quote\Model\Quote\Address\Total\AbstractTotal
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
       
        parent::collect($quote, $shippingAssignment, $total);
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        // Get sub total of cart 

        $subTotal = $quote->getSubtotal();
        $itemCount = $quote->getData('items_count');
    
        if($itemCount == 0) {
            $point = $quote->setRewardpoint(0);
            $discount = $point;
            $quote->setRewardpointDB($point);
            $quote->setDiscount($discount);
        }
        else {
            $items = $shippingAssignment->getItems();  // Fix applied twice discount
            if (!count($items)) {
                return $this;
            }
            // Logic 
            $point = $quote->getRewardpoint();
            if($subTotal < $point) {  
                // case: subtotal < discount reward : set discount = subtotal
                // subtotal = point x Point Spending Rate => subtotal/PSR = point
                // set rewardpoint for this cart = point
                $discount = $subTotal;
                $spendRate = $quote->getEarnpoint();
                $total->addTotalAmount($this->getCode(), -$discount);
                $total->addBaseTotalAmount($this->getCode(), -$discount);
                $quote->setDiscount($discount);

                //Transform to the real point
               
                $realPointApplied = $subTotal/$spendRate;
                $quote->setData('rewardpoint',$discount);
                $quote->setData('rewardpointDB', number_format((int)$realPointApplied));
            }
            else {
                //$point = $cart->getQuote()->getData('rewardpoint');
                // $point = $cart->getQuote()->getData('rewardpoint');
                $discount = $point;
                $total->addTotalAmount($this->getCode(), -$discount);
                $total->addBaseTotalAmount($this->getCode(), -$discount);
                $quote->setDiscount($discount);
            }

        }

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return array
     */
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        // Get sub total of cart 
        $subTotal = $quote->getSubtotal();
        //$point = $cart->getQuote()->getData('rewardpoint');
        $itemCount = $quote->getData('items_count');

        if($itemCount == 0) {
            $point = $quote->setRewardpoint(0);
            $discount = $point;
            $quote->setRewardpointDB($point);
            $quote->setDiscount($discount);
        }
        //$point = $quote->getPoint($point);
        else {
            $point = $quote->getRewardpoint();
            if($subTotal < $point) {  
                // case: subtotal < discount reward : set discount = subtotal
                // subtotal = point x Point Spending Rate => subtotal/PSR = point
                // set rewardpoint for this cart = point
                $discount = $subTotal;
                $spendRate = $quote->getEarnpoint();
                $total->addTotalAmount($this->getCode(), -$discount);
                $total->addBaseTotalAmount($this->getCode(), -$discount);
                $quote->setDiscount($discount);

                //Transform to the real point
                $realPointApplied = $subTotal/$spendRate;
                $quote->setData('rewardpoint',$discount);
                $quote->setData('rewardpointDB',$realPointApplied);
            }
            else {
                $discount = $point;
            }
        }

        return [
            'code'  => 'discount',
            'title' => $this->getLabel(),
            'value' => -$discount  //You can change the reduced amount, or replace it with your own variable
        ];
    }
}