<?php

namespace RewardPoint\ApplyRwP\Controller\Cart;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class rewardPost extends \Magento\Checkout\Controller\Cart implements HttpPostActionInterface
{
    protected $scopeConfig;
    const XML_PATH_REWARD_POINT = 'rwpoint_section/';
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        // \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        // \Magento\Framework\View\Result\PageFactory $pageFactory,
        // \MagentoVendor\CRUD\Model\PostFactory $postFactory,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) 
    {
         // $this->couponFactory = $couponFactory;
         $this->quoteRepository = $quoteRepository;
         // $this->_pageFactory = $pageFactory;
         // $this->_postFactory = $postFactory;
         $this->scopeConfig = $scopeConfig;
        return parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
       
    }
    public function updateQuoteData($quoteId, $point, $rewardpointReal)
    {
        $quote = $this->quoteRepository->get($quoteId); // Get quote by id
        $quote->setData('rewardpoint', $point); // Fill data
        $quote->setData('rewardpointDB', $rewardpointReal);
        $this->quoteRepository->save($quote); // Save quote
    }
    public function execute()
    {
        // $pointCurrent = 1900;  // Take from database
        //point obj
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        
        $customer_id = $cart->getQuote()->getData('customer_id');
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('reward_point');
        $sql2 = "Select * FROM " . $tableName." Where customer_id=".$customer_id;
        $result = $connection->fetchAll($sql2);
        foreach($result as $row){
            $rwpoint = $row["point"];
        }
        $pointCurrent = $rwpoint;
        //end point
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        // get earning rate order 
        $pointSpendRate = $this->scopeConfig->getValue(self::XML_PATH_REWARD_POINT.'point_earn_calc/point_spendr', $storeScope);

        // get minium rate order 
        $pointMinimumRequire = $this->scopeConfig->getValue(self::XML_PATH_REWARD_POINT.'order_limit/min_point', $storeScope);

        // Get limit point per order 
        $pointLimitperOrder = $this->scopeConfig->getValue(self::XML_PATH_REWARD_POINT.'order_limit/redem_order', $storeScope);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        // Get sub total of cart 
        $orderTotal = $cart->getQuote()->getSubtotal();

        // set spend rate to Quote in cart 
        $cart->getQuote()->setData('earnpoint',$pointSpendRate);  // Lay nay luu xuong DB, qua ben kia check point > subtotal

        $quoteId = $cart->getQuote()->getId();
        $rewardPoint = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('reward_point'));
      
        
        try {
            $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
            if($cart->getQuote()->getData('rewardpoint') > 0){
                $cart->getQuote()->setData('rewardpoint',null);
                $this->messageManager->addWarningMessage(
                    __(
                        'You cancel the reward point!!!'
                    ));
                    $this->updateQuoteData($quoteId, 0 , 0);
                    return $this->_goBack();
            }
            elseif ($rewardPoint < 0){
                $this->messageManager->addWarningMessage(
                    __(
                        'Your redemption points are not valid. Please try again.'
                        // $escaper->escapeHtml($rewardPoint)
                    ));
                    return $this->_goBack();
            }
            elseif ($rewardPoint > $pointCurrent){
                $this->messageManager->addWarningMessage(
                    __(
                        'Your redemption points are higher than your current points. Please try again.'
                        // $escaper->escapeHtml($pointLimitperOrder)
                    ));
                    return $this->_goBack();
            }
            elseif ($rewardPoint > $pointLimitperOrder){
                $this->messageManager->addWarningMessage(
                    __(
                        'Number of redemption reward points cannot exceed "%1" for this order. You used "%1" point(s)',
                        $escaper->escapeHtml($pointLimitperOrder)
                    ));
                    return $this->_goBack();
            }
            elseif ($rewardPoint/$pointSpendRate > $orderTotal){
                $this->messageManager->addWarningMessage(
                    __(
                        'Reward point higher than total order. '
                    ));
                    return $this->_goBack();
            }
            else{
                // $this->_checkoutSession->getQuote()->setCouponCode($rewardPoint)->save();
                $point = $rewardPoint * $pointSpendRate;
                $this->updateQuoteData($quoteId, $point, $rewardPoint);
                // Get real reward point
                  $rewardpointDB = $cart->getQuote()->getData('rewardpointDB');
                $this->messageManager->addSuccessMessage(
                    __(
                        'You used "%1" points.',
                        $escaper->escapeHtml($rewardpointDB)
                    )
                );

                  $this->updateQuoteData($quoteId, $point, $rewardpointDB);
                // echo $cart->getQuote()->getId();

                
                // $response = $this->resultFactory
                // ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
                // ->setData([
                //     'rewardpoint'  =>  $point,
                // ]);
                return $this->_goBack();

        
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot apply the reward.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        // echo "cc";
        return $this->_goBack();
    }

}