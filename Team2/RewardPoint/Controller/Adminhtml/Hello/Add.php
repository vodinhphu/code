<?php
namespace Team2\RewardPoint\Controller\Adminhtml\Hello;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Team2\RewardPoint\Model\DataExampleFactory;
use Team2\RewardPoint\Model\DataPointFactory;

class Add extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    /**
     * @var DataExampleFactory
     */
    protected $dataExample;
    protected $dataPoint;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        PageFactory $resultPageFactory,
        DataExampleFactory $dataExample,
        DataPointFactory $dataPoint
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dataExample = $dataExample;
        $this->dataPoint = $dataPoint;
        $this->_coreRegistry = $registry;
    }

    public function execute()
    {
//        echo $_POST['action'] . "+" . $_POST['amount'] . "+" . $_POST['comment'];
        if (($_POST['amount'] != '') && ($_POST['comment'] != '') && (is_numeric($_POST['amount']) == 1 )) {


        //Add to Reward Point History
        $cusId = $_POST['cusId'];
        $amount = $_POST['amount'];
        $type = $_POST['action'];
        $point = $_POST['point'];
        $comment = $_POST['comment'];

        $model = $this->dataExample->create();
        //validate
        if ($type == 'Add' && $amount >= 0)
        {
            $model->addData([
                "customer_id" => $cusId,
                "action" => 'Admin Point Change',
                "point" => $amount,
                "type" => 1,
                "order_id" => 1,
                "author" => 'Admin',
                "sort_order" => 1,
                "comment" => $comment
            ]);
        }
        if ($type == 'Deduct')
        {
            if ($amount >= 0 && $amount <= $point)
            {
                $rw = $amount * -1;
                $model->addData([
                    "customer_id" => $cusId,
                    "action" => 'Admin Point Change',
                    "point" => $rw,
                    "type" => 1,
                    "order_id" => 1,
                    "author" => 'Admin',
                    "sort_order" => 1,
                    "comment" => $comment
                ]);
            }
            else {
                $this->messageManager->addWarning( __('Amount in valid.') );
            }
        }
        $model->save();

        //Update to Reward Point
        $cusId = $_POST['cusId'];
        $amount = $_POST['amount'];
        $type = $_POST['action'];
        $point = $_POST['point'];
        $post = $this->dataPoint->create();

        //Phu them
        $total = 0;
        //
        if ($type == 'Add' && $amount >= 0)
        {
            $total = $amount + $point;
        }
        if ($type == 'Deduct')
        {
            if ($amount >= 0 && $amount <= $point)
            {
                $total = $point - $amount;
            }
            else {
                $this->messageManager->addWarning( __('Amount in valid.') );
            }
        }

        if ($point == 0)
        {
            //TUAN 
            // $post->addData([
            //     "customer_id" => $cusId,
            //     "action" => 'Admin Point Change',
            //     "point" => $total,
            //     "type" => 1
            // ]);
            //PHU INSERT DB

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $tableName2 = "reward_point" ;
                $sql4 = "INSERT INTO ".$tableName2." (customer_id,point,type) values ($cusId,$total,'shopping cart')";
                    $connection->query($sql4);
        }
        else {
            $postUpdate = $post->load($cusId,'customer_id');
            $postUpdate->setPoint($total);
            $postUpdate->save();
            // $postData = $postUpdate->save();
        }
        


        $saveData = $model->save();
        //old
        // if ($saveData && $postData) {
        //     $this->messageManager->addSuccess(__('Update Successfully !'));
        // }
        //new
         if ($saveData) {
            $this->messageManager->addSuccess(__('Update Successfully !'));
        }
        } else {
            $this->messageManager->addWarning(__('Amount in valid.'));
        }
    }
}
