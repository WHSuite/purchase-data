<?php
namespace Whsuite\PurchaseData;
/**
 * PurchaseData Utility
 *
 * The PurchaseData utility provides an easy way of working with custom product
 * purchase data. This is used mainly by addons that need to store custom data
 * for each purchase.
 */
class PurchaseData
{
    public $purchaseData = array();
    public $purchase = null;

    /**
     * Init
     *
     * Loads up the purchase data in the same way you would normally do so via
     * a relationship method in the product purchases model. Then stores it
     * within the purchase data utility by slug name.
     *
     * @param object $purchase   The product purchase database object.
     * @return null
     */
    public function init($purchase)
    {
        $this->purchase = $purchase;

        $purchaseData = $purchase->ProductPurchaseData()->get();

        $purchaseDataArray = array();

        if (! empty($purchaseData)) {
            foreach ($purchaseData as $item) {
                $purchaseDataArray[$item->slug] = $item;
            }
        }

        $this->purchaseData = $purchaseDataArray;

        return;
    }

    /**
     * Get
     *
     * Used to retrieve a single slug record from the purchase data array.
     *
     * @param string $slug   The slug of the record you wish to retrieve
     * @return array|false   Returns the array of data for the slug or false if not found
     */
    public function get($slug)
    {
        if (! empty($this->purchaseData) && in_array($slug, $this->purchaseData)) {
            return $this->purchaseData[$slug];
        }

        return false;
    }

    public function add($slug, $value)
    {
        if (! empty($slug)) {

            // set carbon object ready
            $Carbon = \Carbon\Carbon::now(
                \App::get('configs')->get('settings.localization.timezone')
            );
            $date = $Carbon->toDateTimeString();

            $data = array(
                'product_purchase_id' => $this->purchase->id,
                'slug' => $slug,
                'value' => $value,
                'created_at' => $date,
                'updated_at' => $date
            );

            return $this->purchase->ProductPurchaseData()->insert($data);
        }

        return false;

    }
}
