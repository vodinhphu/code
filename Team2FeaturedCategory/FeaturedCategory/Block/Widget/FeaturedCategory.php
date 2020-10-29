<?php
namespace Team2FeaturedCategory\FeaturedCategory\Block\Widget;

class FeaturedCategory extends \Magento\Framework\View\Element\Template
{
    protected $_categoryFactory;
    protected $helperData;
    protected $_template = 'widget/FeaturedCategory.phtml';
    /**
     * @var \Magento\Review\Model\RatingFactory
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Team2FeaturedCategory\FeaturedCategory\Helper\Data $helperData,
        array $data = []
    )
    {
        $this->_categoryFactory = $categoryFactory;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    public function getCategoryProductCollection()
    {
        $categoryId = $this->helperData->getGeneralConfig('category_id');
        $category = $this->_categoryFactory->create()->load($categoryId);

        $collection = $category->getProductCollection()
            ->addAttributeToSelect('*')
            ->setPageSize($this->helperData->getGeneralConfig('limit_product'));
        //$collection->getSelect()->orderRand();
        return $collection;
    }

    public function getNameDisplay()
    {
        return $this->helperData->getGeneralConfig('display_name');
    }
}
