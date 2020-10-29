<?php
namespace Team2DetailPage\CustomHTMLBlock\Block\Widget;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class customHTMLBlock extends Template implements BlockInterface {
    protected $_template = "widget/customHTMLBlock.phtml";
}