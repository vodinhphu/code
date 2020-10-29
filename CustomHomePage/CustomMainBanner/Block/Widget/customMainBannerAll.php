<?php
namespace CustomHomePage\CustomMainBanner\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;


use Magento\Backend\Block\Widget\Button;
use \Magento\Framework\Data\Form\Element\AbstractElement as Element;
use \Magento\Backend\Block\Template\Context as TemplateContext;
use \Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;

/**
 * Class ImageChooser
 * @package Dlambauer\WidgetImageUpload\Block\Adminhtml\Widget
 */
class customMainBannerAll extends Template implements BlockInterface
{
    /**
     * @var Factory
     */
    protected $_template = "widget/customMainBannerAll.phtml";
    protected $_elementFactory;

    /**
     * @param TemplateContext $context
     * @param FormElementFactory $elementFactory
     * @param array $data
     */
    public function __construct(TemplateContext $context, FormElementFactory $elementFactory, $data = [])
    {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Element $element
     * @return Element
     */
    public function prepareElementHtml(Element $element)
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl('cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']);

        /** @var \Magento\Backend\Block\Widget\Button $chooser */
        $chooser = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                        ->setType('button')
                        ->setClass('btn-chooser')
                        ->setLabel($config['button']['open'])
                        ->setOnClick('MediabrowserUtility.openDialog(\'' . $sourceUrl . '\', 0, 0, "MediaBrowser", {})')
                        ->setDisabled($element->getReadonly());

        /** @var \Magento\Framework\Data\Form\Element\Text $input */
        $input = $this->_elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("widget-option input-text admin__control-text");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $element->setData('after_element_html', $input->getElementHtml()
            . $chooser->toHtml() . "<script>require(['mage/adminhtml/browser']);</script>");

        return $element;
    }
}