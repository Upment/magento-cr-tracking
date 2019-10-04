<?php
class Upment_Tracking_Model_Observer
{
    /**
     * Create Google Tag block for success page view
     *
     * @deprecated after 1.3.2.3 Use setGtagOnOrderSuccessPageView() method instead
     * @param Varien_Event_Observer $observer
     */
    public function order_success_page_view($observer)
    {
        $this->setGtagOnOrderSuccessPageView($observer);
    }

    /**
     * Add order information into GTag block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setGtagOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('tracking_event');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
