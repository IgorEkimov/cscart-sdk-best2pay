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

namespace Tygh\Addons\Tinkoff\Payments;

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\RusTaxes\Receipt\Item;
use Tygh\Addons\RusTaxes\ReceiptFactory;
use Tygh\Addons\Tinkoff\Client\EACQApiClient;
use Tygh\Addons\Tinkoff\Enum\QRDataTypes;
use Tygh\Enum\FiscalData1212ObjectNames;
use Tygh\Enum\FiscalData1212Objects;
use Tygh\Enum\YesNo;
use Tygh\Http;
use Tygh\Tygh;

/**
 * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 */
class EACQClient extends EACQApiClient
{
    /** @var string $terminal_key */
    protected $terminal_key;

    /** @var string $password */
    protected $password;

    /**
     * @var ReceiptFactory
     */
    private $receipt_factory;

    /**
     * Constructor of EACQClient class.
     *
     * @param string         $terminal_key    Terminal key.
     * @param string         $password        Terminal password.
     * @param ReceiptFactory $receipt_factory Receipt factory.
     */
    public function __construct($terminal_key, $password, ReceiptFactory $receipt_factory)
    {
        $this->terminal_key = $terminal_key;
        $this->password = $password;
        $this->receipt_factory = $receipt_factory;
    }

    /**
     * Method for initialize payment at Tinkoff API.
     *
     * @param array<string, float|int|string|array<string, string>>                           $order_info       Order information.
     * @param array{send_prepayment_receipt: string, pay_type: string, is_recurrent?: string} $processor_params Payment processor parameters.
     *
     * @return array<string, string>|string
     */
    public function init(array $order_info, array $processor_params)
    {
        $method = 'Init';

        $amount = fn_format_price_by_currency($order_info['total'], CART_PRIMARY_CURRENCY, 'RUB') * 100;
        $data = [
            'TerminalKey' => $this->terminal_key,
            'Amount'      => (string) $amount,
            'OrderId'     => $order_info['order_id'],
        ];
        if (YesNo::toBool($processor_params['send_prepayment_receipt'])) {
            $data['Receipt'] = $this->generateReceipt($order_info, $processor_params);
        }

        /** @var \Tygh\Storefront\Repository $repository */
        $repository = Tygh::$app['storefront.repository'];
        $storefront = $repository->findById((int) $order_info['storefront_id']);
        if ($storefront) {
            $protocol = fn_get_storefront_protocol() . '://';
            $storefront_url = $protocol . $storefront->url;
            $data['NotificationURL'] = $storefront_url . '/index.php?dispatch=tinkoff.get_notification';
            $data['SuccessURL']      = $storefront_url . '/index.php?dispatch=tinkoff.success&OrderId=${OrderId}&PaymentId=${PaymentId}';
            $data['FailURL']         = $storefront_url . '/index.php?dispatch=tinkoff.fail&OrderId=${OrderId}&Message=${Message}';
        }

        $optional_data = [
            'IP'              => $order_info['ip_address'],
            'Language'        => CART_LANGUAGE,
            'Recurrent'       => $processor_params['is_recurrent'] ?? YesNo::NO,
            'PayType'         => $processor_params['pay_type'],
        ];
        if (YesNo::toBool($optional_data['Recurrent'])) {
            /** @psalm-suppress InvalidArrayOffset */
            unset($optional_data['Recurrent'], $optional_data['CustomerKey']); //TODO For recurrent payments this has to be changed.
        }
        $data = array_merge($data, $optional_data);
        /** @psalm-suppress InvalidArgument */
        $data['Token'] = $this->generateRequestToken($data, $this->password);
        return $this->execute($method, Http::POST, $data);
    }

    /**
     * Creates QR-code for payment.
     *
     * @param string $payment_id Payment identifier into Tinkoff API.
     * @param string $type       QR type.
     *
     * @return string|string[]
     */
    public function getQr($payment_id, $type = QRDataTypes::PAYLOAD)
    {
        $method = 'GetQr';
        $data = [
            'TerminalKey' => $this->terminal_key,
            'PaymentId'   => $payment_id,
            'DataType'    => $type,
        ];
        $data['Token'] = $this->generateRequestToken($data, $this->password);
        return $this->execute($method, Http::POST, $data);
    }

    /**
     * Method for confirming payment. For 2step payments.
     *
     * @param array<string, int|string|array<string, string>> $order_info Order information.
     *
     * @return string|string[]
     */
    public function confirm(array $order_info)
    {
        $method = 'Confirm';
        $query_params = [
            'TerminalKey' => $this->terminal_key,
            'PaymentId'   => $order_info['payment_info']['payment_id'],
        ];

        $query_params['Token'] = $this->generateRequestToken($query_params, $this->password);
        return $this->execute($method, Http::POST, $query_params);
    }

    /**
     * Method for canceling payment.
     *
     * @param array<string, int|string|array<string, string>> $order_info       Order information.
     * @param array{send_refund_receipt: string}              $processor_params Payment processor parameters.
     *
     * @return string|string[]
     */
    public function cancel(array $order_info, array $processor_params)
    {
        $method = 'Cancel';
        $query_params = [
            'TerminalKey' => $this->terminal_key,
            'PaymentId'   => $order_info['payment_info']['payment_id'],
        ];
        if (YesNo::toBool($processor_params['send_refund_receipt'])) {
            $query_params['Receipt'] = $this->generateReceipt($order_info, $processor_params, 'full_prepayment', ['is_refund' => true]);
        }
        $query_params['Token'] = $this->generateRequestToken($query_params, $this->password);
        return $this->execute($method, Http::POST, $query_params);
    }

    /**
     * Method for getting state of payment.
     *
     * @param string $payment_id Payment identifier.
     *
     * @return string|string[]
     */
    public function getState($payment_id)
    {
        $method = 'GetState';
        $query_params = [
            'TerminalKey' => $this->terminal_key,
            'PaymentId'   => $payment_id,
        ];

        $query_params['Token'] = $this->generateRequestToken($query_params, $this->password);

        return $this->execute($method, Http::POST, $query_params);
    }

    /**
     * Method for getting notifications about payment states.
     *
     * @return string|string[]
     */
    public function resend()
    {
        $method = 'Resend';
        $query_params = [
            'TerminalKey' => $this->terminal_key,
        ];
        $query_params['Token'] = $this->generateRequestToken($query_params, $this->password);

        return $this->execute($method, Http::POST, $query_params);
    }

    /**
     * Method for sending second receipt. For 2-step payments.
     *
     * @param array<string, int|string|array<string, string>> $order_info       Order information.
     * @param array{send_full_payment_receipt: string}        $processor_params Payment processor parameters.
     *
     * @return string|string[]
     */
    public function sendClosingReceipt(array $order_info, array $processor_params)
    {
        $method = 'SendClosingReceipt';
        $query_params = [
            'TerminalKey' => $this->terminal_key,
            'PaymentId'   => $order_info['payment_info']['payment_id'],
        ];
        if (YesNo::toBool($processor_params['send_full_payment_receipt'])) {
            $query_params['Receipt'] = $this->generateReceipt($order_info, $processor_params, 'full_payment');
            $query_params['Receipt']['Payments']['AdvancePayment'] = $query_params['Receipt']['Payments']['Electronic'];
            $query_params['Receipt']['Payments']['Electronic'] = 0;
        }
        $query_params['Token'] = $this->generateRequestToken($query_params, $this->password);

        return $this->execute($method, Http::POST, $query_params);
    }

    /**
     * Method for generating receipt information.
     *
     * @param array<string, int|string|array<string, string>> $order_info       Order information.
     * @param array<string, string>                           $processor_params Parameters of payment processor.
     * @param string                                          $payment_method   Payment method for current receipt.
     * @param array{is_refund?: bool}                         $extra_params     Extra parameters for current receipt.
     *
     * @return array<string, string|array<string, string>>|null
     *
     * @psalm-return array{Email: array<string, string>|int|string, FfdVersion: string, Items: array<int, array{Amount: float|string, Name: string, PaymentMethod: string, PaymentObject: string, Price: float|string, Quantity: float, Tax: string}>, Payments: array{AdvancePayment: string|int, Cash: string|int, Credit: string|int, Electronic: string|int, Provision: string|int}, Phone: array<string, string>|int|string, Taxation: mixed}|null
     *
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    protected function generateReceipt(array $order_info, array $processor_params, $payment_method = 'full_prepayment', array $extra_params = [])
    {
        $receipt = $this->receipt_factory->createReceiptFromOrder($order_info, 'RUB');

        if (!$receipt) {
            return null;
        }
        $items = [];
        $receipt_items = $receipt->getItems();
        if (empty($receipt_items)) {
            return null;
        }

        $fiscal_data_1212_map = fn_get_schema('tinkoff', 'map_fiscal_data_1212_objects');

        foreach ($receipt_items as $item) {
            $marked_item = $item->getFiscalData1212() === FiscalData1212Objects::WITH_MARKING_CODE
                || $item->getFiscalData1212() === FiscalData1212Objects::EXCISABLE_WITH_MARKING_CODE;

            $payment_object = !empty($fiscal_data_1212_map[$item->getFiscalData1212()])
                ? $fiscal_data_1212_map[$item->getFiscalData1212()]
                : FiscalData1212ObjectNames::COMMODITY;

            if ($marked_item && $payment_method !== 'full_payment' && empty($extra_params['is_refund'])) {
                $payment_object = FiscalData1212ObjectNames::COMMODITY;
            }

            if (
                $processor_params['ffd_version'] === '1.2' && $marked_item
                && ($payment_method === 'full_payment' || !empty($extra_params['is_refund']))
            ) {
                $this->getReceiptItemsForMarkedProducts($item, $items, $payment_method, $payment_object);
            } else {
                $single_item = [
                    'Name'          => $item->getName(),
                    'Quantity'      => $item->getQuantity(),
                    'Amount'        => (string) ($item->getTotal() * 100),
                    'Price'         => (string) ($item->getPrice() * 100),
                    'PaymentMethod' => $payment_method,
                    'PaymentObject' => $payment_object,
                    'Tax'           => empty($item->getTaxType()) ? 'none' : $item->getTaxType(),
                    //AgentData
                    //SupplierInfo
                ];

                if ($processor_params['ffd_version'] === '1.2') {
                    $single_item['MeasurementUnit'] = 'шт'; // FIXME: try to find other default value
                }

                $items[] = $single_item;
            }
        }

        $receipt_sum = (float) sprintf('%2f', round((float) $order_info['total'] + 0.00000000001, 2)) * 100;

        /** @var array<array-key, string> $order_info */
        return [
            'Email'         => $order_info['email'],
            'Phone'         => $this->normalizePhone($order_info['phone']),
            'Taxation'      => $processor_params['tax_system'],
            'Payments'      => $this->createPayments([
                'electronic' => (string) $receipt_sum,
            ]),
            'Items'         => $items,
            'FfdVersion'    => $processor_params['ffd_version']
        ];
    }

    /**
     * Generates receipt items for marked products.
     *
     * @param \Tygh\Addons\RusTaxes\Receipt\Item $item           Order information
     * @param array<string|int|object|bool>      $items          Support for multiple orders
     * @param string                             $payment_method Payment method
     * @param string                             $payment_object Payment object
     *
     * @return void
     */
    protected function getReceiptItemsForMarkedProducts(Item $item, array &$items, $payment_method, $payment_object)
    {
        $mark_codes = $item->getMarkingCodesData();
        if (empty($mark_codes)) {
            return;
        }

        foreach ($mark_codes as $mark_code_data) {
            $mark_code = [
                'MarkCodeType' => strtoupper($mark_code_data['marking_code_format']),
                'Value' => $mark_code_data['marking_code']
            ];

            $marked_item = [
                'Name'               => $item->getName(),
                'Price'              => (string) ($item->getPrice() * 100),
                'Quantity'           => 1,
                'Amount'             => (string) ($item->getPrice() * 100),
                'PaymentMethod'      => $payment_method,
                'PaymentObject'      => $payment_object,
                'Tax'                => empty($item->getTaxType()) ? 'none' : $item->getTaxType(),
                'MarkProcessingMode' => 0,
                'MarkCode'           => (object) $mark_code,
                'MeasurementUnit'    => 'шт'
            ];

            $items[] = $marked_item;
        }
    }

    /**
     * Creates payment object as part of receipt object.
     *
     * @param array{cash?: int|string, electronic?: int|string, prepayment?: int|string, credit?: int|string, other?: int|string} $values Distribution of funds by types.
     *
     * @return array{Cash: int|string, Electronic: int|string, AdvancePayment: int|string, Credit: int|string, Provision: int|string}
     */
    private function createPayments(array $values): array
    {
        $default_values = [
            'cash'       => 0,
            'electronic' => 0,
            'prepayment' => 0,
            'credit'     => 0,
            'other'      => 0,
        ];
        $values = array_merge($default_values, $values);
        return [
            'Cash'           => $values['cash'],
            'Electronic'     => $values['electronic'],
            'AdvancePayment' => $values['prepayment'],
            'Credit'         => $values['credit'],
            'Provision'      => $values['other']
        ];
    }

    /**
     * Normalizes the phone number
     *
     * @param string $phone Phone number
     *
     * @return string
     *
     * @psalm-suppress InvalidLiteralArgument
     */
    protected function normalizePhone($phone)
    {
        $phone_normalize = '';

        if (!empty($phone)) {
            $phone = ltrim($phone, '+');

            if ($phone[0] === '8') {
                $phone[0] = '7';
            }
            $phone_normalize = '+' . preg_replace('/\D/', '', $phone);
        }
        return $phone_normalize;
    }
}
