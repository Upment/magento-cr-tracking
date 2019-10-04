<?php
class Upment_Tracking_Block_Event extends Mage_Core_Block_Template
{

  /**
   * Render information about specified orders and their items
   *
   * @return string
   */
  public function getOrderInfo()
  {
      $orderIds = $this->getOrderIds();
      $orderId = $orderIds[0];
      $order = Mage::getModel('sales/order')->load($orderId);
      $total = $order->getGrandTotal();
      $currency = $order->getOrderCurrencyCode();
      $result = array();
      $result['id'] = $order->getIncrementId();
      $result['value'] = $total;
      $result['currency'] = $currency;
      return $result;
  }

  /**
   * Get current page type
   *
   * @return string
   */
  public function getPageType()
  {
      $thispage = 'other';
      $page = Mage::getSingleton('cms/page');
      if ($page->getId()) {
        if ($page->getIdentifier() == Mage::getStoreConfig('web/default/cms_home_page')) {
          $thispage='homepage';
        }
      }
      $product = Mage::registry('current_product');
      $category = Mage::registry('current_category');
      if ($product && $product->getId()) {
        $thispage='product';
      } elseif ($category && $category->getId()) {
        $thispage='category';
      }

      $fullActionName = Mage::app()->getFrontController()->getAction()->getFullActionName();

      if ($fullActionName == 'checkout_cart_index') {
        $thispage='cart';
      }

      if (0 === strpos($fullActionName, 'catalogsearch_')) {
        $thispage='searchresults';
      }
      if ( ('checkout_onepage_index' == $fullActionName) || ('aw_onestepcheckout_index_index' == $fullActionName) || ('checkout_prime_index' == $fullActionName) || ('checkout_klarna_index' == $fullActionName) ) {
        $thispage='checkout';
      }
      if ( ('checkout_onepage_success' == $fullActionName) || ('checkout_prime_success' == $fullActionName) || ('checkout_klarna_success' == $fullActionName) ) {
        $thispage='success';
      }
      return $thispage;
  }

  /**
   * Get GTag code
   *
   * @return string
   */
   public function getGtagCode()
   {
      $gtag = "gtag('event', ";
      $thispage = $this->getPageType();

      if ($thispage != 'success') {
        $gtag.="'page_view', {\n";
        $gtag.="    ecomm_pagetype: '" . $thispage . "'";
        if ($thispage == 'product') {
          $product = Mage::registry('current_product');
          $gtag.=",\n";
          $gtag.="    ecomm_prodid: '" . $product->getSku() . "',\n";
          $gtag.="    ecomm_totalvalue: " . $product->getFinalPrice();
        }
        if ($thispage == 'cart') {
          $cart = Mage::getModel('checkout/cart')->getQuote();
          if ($cart->getItemsCount() > 0) {
            $gtag.=",\n";
            $gtag.="    ecomm_prodid: [";
            $isFirst = true;
            foreach ($cart->getAllVisibleItems() as $item) {
              if (!$isFirst) {
                $gtag.=", ";
              }
              $isFirst = false;
              $gtag.="'" . $item->getProduct()->getSku() . "'";
            }
            $gtag.="],\n";
            $gtag.="    ecomm_totalvalue: " . $cart->getGrandTotal();
          }
        }
      } elseif ($thispage == 'success') {
        $_conversionId = $this->helper('tracking')->getConversionId();
        $_orderInfo = $this->getOrderInfo();
        $gtag.="'purchase', {\n";
        $gtag.="    'send_to': '" . $_conversionId . "',\n";
        $gtag.="    'value': " . $_orderInfo['value'] . ",\n";
        $gtag.="    'currency': '" . $_orderInfo['currency'] . "',\n";
        $gtag.="    'transaction_id': '" . $_orderInfo['id'] . "'";
      }
      $gtag.="\n});\n";
      return $gtag;
    }

    /**
     * Get Bing code
     *
     * @return string
     */
     public function getBingCode()
     {

       $thispage = $this->getPageType();
       if ($thispage != 'success') {
         return '';
       } else {
         $_orderInfo = $this->getOrderInfo();
         $bing = "<script>\n";
         $bing.= "    window.uetq = window.uetq || [];\n";
         $bing.= "    window.uetq.push('event', '', {revenue_value: " . $_orderInfo['value'] . ", 'currency': '" . $_orderInfo['currency'] . "'});\n";
         $bing.="\n</script>\n";
         return $bing;
       }

     }


}
