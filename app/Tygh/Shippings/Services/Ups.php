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

namespace Tygh\Shippings\Services;

use Tygh\Enum\YesNo;
use Tygh\Shippings\IService;
use Tygh\Http;
use Tygh\Registry;

/**
 * UPS shipping service.
 * Uses Rating Package XML API 2201
 */
class Ups implements IService
{
    /**
     * API version
     */
    const VERSION = 'v1';
    /**
     * API scope
     */
    const SCOPE = 'read';
    /**
     * Production service URL
     */
    const URL_PRODUCTION  = 'https://onlinetools.ups.com/api/rating/' . self::VERSION . '/Rate';
    /**
     * Development service URL
     */
    const URL_DEVELOPMENT = 'https://wwwcie.ups.com/api/rating/' . self::VERSION . '/Rate';
    /**
     * Production URL of getting the access token
     */
    const URL_PRODUCTION_GET_TOKEN = 'https://onlinetools.ups.com/security/' . self::VERSION . '/oauth/token';
    /**
     * Development URL of getting the access token
     */
    const URL_DEVELOPMENT_GET_TOKEN = 'https://wwwcie.ups.com/security/' . self::VERSION . '/oauth/token';
    /**
     * UPS All countries: Worldwide Express Freight service code
     */
    const WORLDWIDE_EXPRESS_FREIGHT = 96;
    /**
     * Weight: pounds
     */
    const WEIGHT_POUNDS = 'LBS';
    /**
     * Weight: kilograms
     */
    const WEIGHT_KILOGRAMS = 'KGS';
    /**
     * Length: inches
     */
    const LENGTH_INCHES = 'IN';
    /**
     * Length: centimetres
     */
    const LENGTH_CENTIMETRES = 'CM';
    /**
     * @var bool Availability multithreading in this module
     */
    private $_allow_multithreading = true;
    /**
     * @var string Service URL
     */
    private $service_url;
    /**
     * @var array Shipping settings
     */
    private $settings;
    /**
     * @var array Package info
     */
    private $package;
    /**
     * @var array<array-key, string|int> Stored shipping information
     */
    private $_shipping_info = [];
    /**
     * @var string Access token
     */
    private $access_token;
    /**
     * @var int Access token expiration
     */
    private $access_token_expiration;

    /**
     * Updates the access token if necessary
     *
     * @return void
     */
    protected function getAccessToken()
    {
        if (
            !empty($this->access_token)
            && !empty($this->access_token_expiration)
            && time() < $this->access_token_expiration - 900
        ) {
            return;
        }

        $params = ['grant_type' => 'client_credentials'];

        $extra = [
            'basic_auth' => [$this->settings['client_id'], $this->settings['client_secret']],
            'headers' => [
                'x-merchant-id' => !empty($this->settings['client_id']) ? $this->settings['client_id'] : '',
                'Accept'        => 'application/json',
                'Content-type'  => 'application/x-www-form-urlencoded',
            ],
        ];

        $url = isset($this->settings['test_mode']) && YesNo::isTrue($this->settings['test_mode'])
            ? self::URL_DEVELOPMENT_GET_TOKEN
            : self::URL_PRODUCTION_GET_TOKEN;

        $response = $this->execute($url, Http::POST, $params, $extra);
        if (!isset($response['status']) || $response['status'] !== 'approved') {
            return;
        }

        $this->updateShippingServiceParams($response);
    }

    /**
     * Updates the shipping service settings
     *
     * @param array<string, string> $data Shipping data
     *
     * @return void
     */
    protected function updateShippingServiceParams(array $data)
    {
        if (empty($this->_shipping_info['shipping_id'])) {
            return;
        }

        $shipping_data = fn_get_shipping_info((int) $this->_shipping_info['shipping_id']);

        $shipping_data['service_params']['access_token'] = $this->access_token = $data['access_token'];
        $shipping_data['service_params']['access_token_expiration'] = $this->access_token_expiration = time() + (int) $data['expires_in'];

        fn_update_shipping($shipping_data, (int) $this->_shipping_info['shipping_id']);
    }

    /**
     * @inheritdoc
     */
    public function prepareData($shipping_info)
    {
        $this->_shipping_info = $shipping_info;
        $this->settings = $shipping_info['service_params'];

        $this->access_token = !empty($this->settings['access_token']) ? $this->settings['access_token'] : '';
        $this->access_token_expiration = !empty($this->settings['access_token_expiration']) ? $this->settings['access_token_expiration'] : '';

        $this->package = $shipping_info['package_info'];
        $this->package['origination'] = $this->prepareAddress($this->package['origination'] ?? []);
        $this->package['location'] = $this->prepareAddress($this->package['location'] ?? []);

        $this->service_url = isset($this->settings['test_mode']) && $this->settings['test_mode'] === YesNo::YES
            ? self::URL_DEVELOPMENT
            : self::URL_PRODUCTION;
    }

    /**
     * Processes the response from Shipping service with rate information
     *
     * @param string $response Response from Shipping service server
     *
     * @return array<string, array<string, string>>
     */
    public function processRates($response): array
    {
        $return = [];

        $response = json_decode($response);

        if (
            !empty($response->RateResponse->Response)
            && !empty($response->RateResponse->RatedShipment)
        ) {
            $response = $response->RateResponse;
            if (!is_array($response->RatedShipment)) {
                $response->RatedShipment = [$response->RatedShipment];
            }

            foreach ($response->RatedShipment as $shipment) {
                $total_charge = 0;
                $service_code = (string) $shipment->Service->Code;

                // Try to get negotiated rates
                if (!empty($shipment->NegotiatedRates)) {
                    $total_charge = (string) $shipment->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
                }

                if (empty($total_charge)) {
                    $total_charge = (string) $shipment->TotalCharges->MonetaryValue;
                }

                if (
                    empty($service_code)
                    || empty($total_charge)
                ) {
                    continue;
                }

                $return[$service_code] = [
                    'rate' => $total_charge
                ];

                if (!empty($shipment->ScheduledDeliveryDate)) {
                    $return[$service_code]['delivery_time'] = (string) $shipment->ScheduledDeliveryDate;
                } elseif (!empty($shipment->GuaranteedDelivery->BusinessDaysInTransit)) {
                    $return[$service_code]['delivery_time'] = __('n_days', [$shipment->GuaranteedDelivery->BusinessDaysInTransit]);
                }
            }
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function processResponse($response): array
    {
        if (empty($response)) {
            return [];
        }

        $return = [
            'cost'          => false,
            'error'         => false,
            'delivery_time' => false
        ];

        $rates = $this->processRates($response);
        $code = $this->_shipping_info['service_code'];
        if (isset($rates[$code])) {
            $return['cost'] = $rates[$code]['rate'];

            if (isset($rates[$code]['delivery_time'])) {
                $return['delivery_time'] = $rates[$code]['delivery_time'];
            }
        } else {
            $return['error'] = $this->processErrors($response);
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function processErrors($response): string
    {
        $response = json_decode($response);
        $errors = !empty($response->response->errors) ? $response->response->errors : [];
        $return = '';

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $return .= $error->message . ' (Error code: ' . $error->code . ') ';
            }
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function allowMultithreading()
    {
        return $this->_allow_multithreading;
    }

    /**
     * @inheritdoc
     */
    public function getRequestData(): array
    {
        return [
            'method' => Http::POST,
            'url' => $this->service_url,
            'data' => json_encode($this->getRatingRequest()),
            'headers' => $this->getAccessRequest(),
        ];
    }

    /**
     * Provides content of Access Request.
     *
     * @return array<array-key, string> Access Request
     */
    private function getAccessRequest(): array
    {
        $this->getAccessToken();

        return [
            'Authorization: Bearer ' . $this->access_token,
            'Content-Type: application/json',
        ];
    }

    /**
     * Process simple request to shipping service server
     *
     * @return array<string, string>
     */
    public function getSimpleRates(): array
    {
        $data = $this->getRequestData();
        return $this->execute($data['url'], Http::POST, $data['data'], $data['headers']);
    }

    /**
     * Fill required address fields
     *
     * @param array $address Address data
     *
     * @return array Filled address data
     */
    public function prepareAddress(array $address): array
    {
        $default_fields = [
            'zipcode' => '',
            'state' => '',
            'country' => ''
        ];

        return array_merge($default_fields, $address);
    }

    /**
     * Returns shipping service information
     *
     * @return array<array-key, string> Information
     */
    public static function getInfo(): array
    {
        return [
            'name' => __('carrier_ups'),
            'tracking_url' => 'http://wwwapps.ups.com/etracking/tracking.cgi?tracknum=%s'
        ];
    }

    /**
     * Provides content of Rating Service Selection Request.
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     *
     * @return array<string, array<string, mixed>> Rating Service Selection Request
     */
    private function getRatingRequest(): array
    {
        $pickup_type = empty($this->settings['pickup_type']) ? '' : $this->settings['pickup_type'];

        $rating_request = [
            'Request' => [
                'TransactionReference' => [
                    'CustomerContext' => 'Rate Request',
                ],
                'RequestOption' => 'Rate',
            ],
            'PickupType' => [
                'Code' => $pickup_type,
            ],
            'Shipment' => [
                'Service' => [
                    'Code' => $this->_shipping_info['service_code'],
                ],
                'Shipper' => [
                    'Address' => [
                        'PostalCode' => $this->package['origination']['zipcode'],
                        'CountryCode' => $this->package['origination']['country'],
                    ],
                ],
                'ShipTo' => [
                    'Address' => [
                        'StateProvinceCode' => $this->package['location']['state'],
                        'PostalCode' => $this->package['location']['zipcode'],
                        'CountryCode' => $this->package['location']['country'],
                        'ResidentialAddress' => '',
                    ],
                ],
                'ShipFrom' => [
                    'Address' => [
                        'StateProvinceCode' => $this->package['origination']['state'],
                        'PostalCode' => $this->package['origination']['zipcode'],
                        'CountryCode' => $this->package['origination']['country'],
                        'ResidentialAddress' => '',
                    ]
                ]
            ]
        ];

        if ((int) $this->_shipping_info['service_code'] === self::WORLDWIDE_EXPRESS_FREIGHT) {
            $rating_request['Shipment']['NumOfPieces'] = empty($this->package['I']) ? 1 : $this->package['I'];
        }

        if (isset($this->settings['negotiated_rates']) && $this->settings['negotiated_rates'] === YesNo::YES) {
            $rating_request['Shipment']['Shipper']['ShipperNumber'] = $this->settings['shipper_number'];
        }

        if (!empty($this->package['location']['address'])) {
            $rating_request['Shipment']['ShipTo']['Address']['AddressLine1'] = $this->package['location']['address'];
            if (!empty($this->package['location']['address_2'])) {
                $rating_request['Shipment']['ShipTo']['Address']['AddressLine2'] = $this->package['location']['address_2'];
            }
        }

        $rating_request['Shipment'] += $this->preparePackages();
        $rating_request['Shipment'] += $this->prepareShipmentOptions();

        return ['RateRequest' => $rating_request];
    }

    /**
     * Prepares shipment service options based on the shipping method settings and delivery type (local/international).
     *
     * @return array Package service options
     */
    private function prepareShipmentOptions(): array
    {
        $shipment_options = [];

        if (
            $this->package['origination']['country'] !== $this->package['location']['country']
            && !empty($this->settings['delivery_confirmation'])
            && $this->settings['delivery_confirmation'] === YesNo::YES
            && $this->settings['dcist_type'] > 1
        ) {
            $shipment_options = [
                'ShipmentServiceOptions' => [
                    'DeliveryConfirmation' => [
                        'DCISType' => $this->settings['dcist_type'] - 1
                    ],
                ],
            ];
        }

        return $shipment_options;
    }

    /**
     * Prepares package service options based on the shipping method settings and delivery type (local/international).
     *
     * @return array Package service options
     */
    private function preparePackageOptions(): array
    {
        $package_options = array();

        /*
         * Domestic confirmation:          International confirmation:
         * 1 - No Signature                -
         * 2 - Signature Required          1 - Signature Required
         * 3 - Adult Signature required    2 - Adult Signature Required
         */
        if (
            $this->package['origination']['country'] === $this->package['location']['country']
            && !empty($this->settings['delivery_confirmation'])
            && $this->settings['delivery_confirmation'] === YesNo::YES
        ) {
            $package_options['DeliveryConfirmation'] = [
                'DCISType' => $this->settings['dcist_type']
            ];
        }

        return $package_options ? ['PackageServiceOptions' => $package_options] : [];
    }

    /**
     * Gets meausement units for the specified country.
     *
     * @param string $country Country code
     *
     * @return array Weight unit and length unit
     */
    protected function getMeausementUnits($country = ''): array
    {
        if (empty($country)) {
            $country = $this->package['origination']['country'];
        }

        if (in_array($country, ['US', 'DO', 'PR'])) {
            $weight_unit = self::WEIGHT_POUNDS;
            $length_unit = self::LENGTH_INCHES;
        } else {
            $weight_unit = self::WEIGHT_KILOGRAMS;
            $length_unit = self::LENGTH_CENTIMETRES;
        }

        return [$weight_unit, $length_unit];
    }

    /**
     * Prepares information about packages in the shipment.
     *
     * @return array Packages
     */
    protected function preparePackages(): array
    {
        $package_type = empty($this->settings['package_type']) ? '' : $this->settings['package_type'];
        [$weight_unit, $length_unit] = $this->getMeausementUnits();

        $default_length = empty($this->settings['length']) ? 0 : $this->settings['length'];
        $default_width  = empty($this->settings['width'])  ? 0 : $this->settings['width'];
        $default_height = empty($this->settings['height']) ? 0 : $this->settings['height'];
        $default_weight = $this->prepareWeight($this->package['W'], $weight_unit);

        $packages = [];
        if (empty($this->package['packages'])) {
            $packages[] = [
                'shipping_params' => [
                    'box_length' => $default_length,
                    'box_width' => $default_width,
                    'box_height' => $default_height,
                ],
                'weight' => $this->package['W'],
                'cost' => $this->package['C'],
            ];
        } else {
            $packages = $this->package['packages'];
        }

        $packages_node = ['Package' => []];
        foreach ($packages as $package) {
            $package_node = [
                'PackagingType' => [
                    'Code' => $package_type,
                ],
                'Dimensions' => [
                    'UnitOfMeasurement' => [
                        'Code' => $length_unit,
                        'Description' => $length_unit,
                    ],
                    'Length' => empty($package['shipping_params']['box_length']) ? $default_length : (string) $package['shipping_params']['box_length'],
                    'Width' => empty($package['shipping_params']['box_width']) ? $default_width  : (string) $package['shipping_params']['box_width'],
                    'Height' => empty($package['shipping_params']['box_height']) ? $default_height : (string) $package['shipping_params']['box_height'],
                ],
                'PackageWeight' => [
                    'UnitOfMeasurement' => [
                        'Code' => $weight_unit,
                        'Description' => $weight_unit,
                    ],
                    'Weight' => empty($package['weight']) ? (string) $default_weight : (string) $this->prepareWeight($package['weight'], $weight_unit),
                ],
            ];

            $package_node += $this->preparePackageOptions();

            $packages_node['Package'][] = $package_node;
        }

        return $packages_node;
    }

    /**
     * Converts weight to pounds or kilograms depending on the origination country.
     *
     * @param float  $weight Weight of the package in the primary weight unit
     * @param string $unit   Weight unit: pounds (self::WEIGHT_POUNDS) or kilograms (self::WEIGHT_KILOGRAMS)
     *
     * @return float Weight in the selected unit
     */
    private function prepareWeight($weight, $unit): float
    {
        if ($unit === self::WEIGHT_POUNDS) {
            $weight = fn_convert_weight_to_imperial_units($weight);
            $weight = $weight['full_pounds'];
        } else {
            $weight = $weight / 1000 * Registry::get('settings.General.weight_symbol_grams');
        }

        return $weight;
    }

    /**
     * Makes request to API endpoint.
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     *
     * @param string                $endpoint API endpoint.
     * @param string                $method   Type of request to Tinkoff API.
     * @param array<string, string> $params   Encoded body of request.
     * @param array<string, mixed>  $headers  Request headers.
     *
     * @return array<string, string>
     */
    protected function execute($endpoint, $method, array $params, array $headers = []): array
    {
        switch ($method) {
            case Http::GET:
                $answer = Http::get($endpoint, $params, $headers);
                break;
            case Http::POST:
                $answer = Http::post($endpoint, $params, $headers);
                break;
            default:
                $answer = '';
                break;
        }
        $answer = json_decode($answer, true);
        return $answer;
    }
}
