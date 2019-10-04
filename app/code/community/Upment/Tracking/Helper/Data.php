<?php

class Upment_Tracking_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_GTAG        = 'tracking/settings/conversion_id';
    const XML_PATH_BING        = 'tracking/settings/bing_id';
    const XML_PATH_DOMAIN      = 'tracking/settings/allowed_domain';

    /**
     * Get GTag conversion id
     *
     * @param string $store
     * @return string
     */
    public function getConversionId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GTAG, $store);
    }

    /**
     * Get GTag account id
     *
     * @param string $store
     * @return string
     */
    public function getAccountId($store = null)
    {
        $conversionId = Mage::getStoreConfig(self::XML_PATH_GTAG, $store);
        $tempArray = explode("/", $conversionId);
        $accountId = $tempArray[0];
        return $accountId;
    }

    /**
     * Get Bing tag id
     *
     * @param string $store
     * @return string
     */
    public function getBingId($store = null)
    {
      return Mage::getStoreConfig(self::XML_PATH_BING, $store);
    }

    /**
     * Get allowed domain name
     *
     * @param string $store
     * @return string
     */
    public function getAllowedDomain($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DOMAIN, $store);
    }

}
