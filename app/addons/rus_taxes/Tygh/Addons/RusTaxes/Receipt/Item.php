<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\RusTaxes\Receipt;


use Tygh\Enum\FiscalData1212Objects;

/**
 * Model of receipt item.
 *
 * @package Tygh\Addons\RusTaxes\Receipt
 */
class Item
{
    const TYPE_PRODUCT = 'product';

    const TYPE_SHIPPING = 'shipping';

    const TYPE_SURCHARGE = 'surcharge';

    const TYPE_GIFT_CERTIFICATE = 'gift_certificate';

    /** @var int|string Item identifier */
    protected $id;

    /** @var string Item type */
    protected $type;

    /** @var string Item name */
    protected $name;

    /** @var string Item code */
    protected $code;

    /** @var float Item price */
    protected $price;

    /** @var float Item quantity */
    protected $quantity;

    /** @var float Item tax sum */
    protected $tax_sum;

    /** @var string Item tax type */
    protected $tax_type;

    /** @var float Item total discount */
    protected $total_discount;

    /** @var int Item payment subject tag 1212 */
    protected $fiscal_data_1212;

    /** @var list<array{marking_code_format: string, marking_code: string}> Item marking codes data */
    protected $marking_codes_data;

    /**
     * Receipt item constructor
     *
     * @param int|string                                                     $id                 Item identifier
     * @param string                                                         $type               Item type
     * @param string                                                         $name               Item name
     * @param string                                                         $code               Item code
     * @param float                                                          $price              Item price
     * @param float                                                          $quantity           Item quantity
     * @param string                                                         $tax_type           Item tax type
     * @param float                                                          $tax_sum            Item tax sum
     * @param float                                                          $total_discount     Item total discount
     * @param int                                                            $fiscal_data_1212   Item payment subject tag 1212
     * @param list<array{marking_code_format: string, marking_code: string}> $marking_codes_data Item marking data
     */
    public function __construct(
        $id,
        $type,
        $name,
        $code,
        $price,
        $quantity,
        $tax_type,
        $tax_sum,
        $total_discount = 0.0,
        $fiscal_data_1212 = FiscalData1212Objects::PAYMENT,
        array $marking_codes_data = []
    ) {
        $this->setId($id);
        $this->setType($type);
        $this->setName($name);
        $this->setCode($code);
        $this->setPrice($price);
        $this->setTaxSum($tax_sum);
        $this->setTaxType($tax_type);
        $this->setQuantity($quantity);
        $this->setTotalDiscount($total_discount);
        $this->setFiscalData1212($fiscal_data_1212);
        $this->setMarkingCodesData($marking_codes_data);
    }

    /**
     * Gets receipt item total
     *
     * @return float
     */
    public function getTotal()
    {
        return Receipt::roundPrice($this->price * $this->quantity - $this->total_discount);
    }

    /**
     * Gets receipt item identifier
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets receipt item identifier
     *
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets receipt item type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets receipt item type.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = (string) $type;
    }

    /**
     * Gets receipt item price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets receipt item price.
     *
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = (float) $price;
    }

    /**
     * Gets receipt item quantity.
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets receipt item quantity.
     *
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (float) $quantity;
    }

    /**
     * Gets receipt item tax type (none, vat0, vat10, vat18, etc).
     *
     * @return string
     */
    public function getTaxType()
    {
        return $this->tax_type;
    }

    /**
     * Sets receipt item tax type.
     *
     * @param string $tax_type
     */
    public function setTaxType($tax_type)
    {
        $this->tax_type = trim($tax_type);
    }

    /**
     * Gets receipt item tax sum.
     *
     * @return float
     */
    public function getTaxSum()
    {
        return $this->tax_sum;
    }

    /**
     * Sets receipt item tax sum.
     *
     * @param float $tax_sum
     */
    public function setTaxSum($tax_sum)
    {
        $this->tax_sum = (float) $tax_sum;
    }

    /**
     * Gets receipt item total discount.
     *
     * @return float
     */
    public function getTotalDiscount()
    {
        return $this->total_discount;
    }

    /**
     * Sets receipt item total discount.
     *
     * @param float $total_discount
     */
    public function setTotalDiscount($total_discount)
    {
        $this->total_discount = (float) $total_discount;
    }

    /**
     * Gets receipt item name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets receipt item name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = trim(strip_tags($name));
    }

    /**
     * Gets receipt item code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets receipt item code.
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = trim($code);
    }

    /**
     * Gets receipt item payment subject tag 1212.
     *
     * @return int
     */
    public function getFiscalData1212()
    {
        return $this->fiscal_data_1212;
    }

    /**
     * Sets receipt item payment subject.
     *
     * @param int $fiscal_data_1212 Item payment subject tag 1212
     *
     * @return void
     */
    public function setFiscalData1212($fiscal_data_1212)
    {
        $this->fiscal_data_1212 = $fiscal_data_1212;
    }

    /**
     * Gets receipt item marking codes data.
     *
     * @return list<array{marking_code_format: string, marking_code: string}>
     */
    public function getMarkingCodesData()
    {
        return $this->marking_codes_data;
    }

    /**
     * Sets receipt item marking codes data.
     *
     * @param list<array{marking_code_format: string, marking_code: string}> $marking_codes_data Item marking codes data
     *
     * @return void
     */
    public function setMarkingCodesData(array $marking_codes_data)
    {
        $this->marking_codes_data = $marking_codes_data;
    }

    /**
     * Converts receipt item to array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'code' => $this->code,
            'price' => $this->price,
            'tax_sum' => $this->tax_sum,
            'tax_type' => $this->tax_type,
            'quantity' => $this->quantity,
            'total_discount' => $this->total_discount,
            'fiscal_data_1212' => $this->fiscal_data_1212,
            'marking_codes_data' => $this->marking_codes_data,
        ];
    }
}
