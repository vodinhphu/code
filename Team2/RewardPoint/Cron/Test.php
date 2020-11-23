<?php
namespace Team2\RewardPoint\Cron;

use Psr\Log\LoggerInterface;

class Test {
    protected $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

   /**
    * Write to system.log
    *
    * @return void
    */
    public function execute() {
        $this->logger->info('Cron Works');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $configPath = 'rwpoint_section/point_earn_calc/point_earnr';
        $earnRate =  $scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $tableName = "sales_order" ;
        $sql = "Select * FROM " . $tableName." Where status = 'complete' and flash_rewardpoint = 0";
        $result = $connection->fetchAll($sql);
        if(count($result)>0)
        {
            foreach ($result as $row) {
                if($row['customer_id'] != Null){
                    $reward_point = $row['base_subtotal']*$earnRate;
                    $customer_id = $row['customer_id'];
                    $order_id = $row['entity_id'];
                    $tableName2 = "reward_point";
                    $tableName3 = "reward_point_history";
                    //insert table reward_point_history
                    $sql1 = "INSERT INTO ".$tableName3." (customer_id,action,point,type,order_id,author) values ($customer_id,'Order Completed',$reward_point,'shopping cart',$order_id,'Admin')";
                    $connection->query($sql1);
                    //end
                    $sql2 = "Select * FROM reward_point Where customer_id=".$customer_id;
                    $result2 = $connection->fetchAll($sql2);
                    if (count($result2)>0) {
                        foreach ($result2 as $row2) {
                        $pointOld = $row2["point"];
                        }
                        $newPoint = $pointOld + $reward_point;
                        $sql3 = "Update " . $tableName2 . " Set point = ".$newPoint." where customer_id = ".$customer_id;
                        $connection->query($sql3);
                        $sql33 = "Update " . $tableName . " Set flash_rewardpoint = 1 where entity_id = ".$order_id;
                        $connection->query($sql33);
                    } else {
                        $sql4 = "INSERT INTO ".$tableName2." (customer_id,point,type) values ($customer_id,$reward_point,'shopping cart')";
                        $connection->query($sql4);
                        $sql44 = "Update " . $tableName . " Set flash_rewardpoint = 1 where entity_id = ".$order_id;
                        $connection->query($sql44);
                    }
                }
            }
        }
    }
}
