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
class Upsdeprecated implements IService
{
    /**
     * API version
     */
    const VERSION = '2201';
    /**
     * Production service URL
     */
    const URL_PRODUCTION  = 'https://onlinetools.ups.com:443/ups.app/xml/Rate';
    /**
     * Development service URL
     */
    const URL_DEVELOPMENT = 'https://wwwcie.ups.com:443/ups.app/xml/Rate';
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
     * @var bool $_allow_multithreading Availability multithreading in this module
     */
    private $_allow_multithreading = true;
    /**
     * @var string Service URL
     */
    private $service_url;
    /**
     * @var array<string, string> Shipping settings
     */
    private $settings;
    /**
     * @var array<string, array<string, string>> Package info
     */
    private $package;
    /**
     * @var array<string, string|int> Stored shipping information
     */
    private $_shipping_info = [];

    /**
     * @inheritdoc
     */
    public function prepareData($shipping_info)
    {
        $this->_shipping_info = $shipping_info;

        $this->settings = $shipping_info['service_params'];

        $this->package = $shipping_info['package_info'];
        $this->package['origination'] = $this->prepareAddress($this->package['origination']);
        $this->package['location'] = $this->prepareAddress($this->package['location']);

        $this->service_url = isset($this->settings['test_mode']) && $this->settings['test_mode'] === YesNo::YES
            ? self::URL_DEVELOPMENT
            : self::URL_PRODUCTION;
    }

    /**
     * Processes the response from Shipping service with rate information
     *
     * @param string $response Response from Shipping service server
     *
     * @return array<string, array<string, string>> Shipping cost and errors
     */
    public function processRates(string $response): array
    {
        $xml = @simplexml_load_string($response);
        $return = [];

        if (!empty($xml)) {
            foreach ($xml->RatedShipment as $shipment) {
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

                if (!empty($shipment->ScheduledDeliveryTime)) {
                    $return[$service_code]['delivery_time'] = (string) $shipment->ScheduledDeliveryTime;
                } elseif (!empty($shipment->GuaranteedDaysToDelivery)) {
                    $return[$service_code]['delivery_time'] = __('n_days', [$shipment->GuaranteedDaysToDelivery]);
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
        $return = [
            'cost' => false,
            'error' => false,
            'delivery_time' => false,
        ];

        $code = $this->_shipping_info['service_code'];
        $rates = $this->processRates($response);

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
        // Parse XML message returned by the UPS post server.
        $xml = @simplexml_load_string($response);

        if (!empty($xml)) {
            $status_code = (string) $xml->Response->ResponseStatusCode;

            if ($status_code !== '1') {
                $return = (string) $xml->Response->Error->ErrorDescription;
                if (!empty($xml->Response->Error->ErrorDigest)) {
                    $return .= ' (' . (string) $xml->Response->Error->ErrorDigest . ').';
                }

                return $return;
            }
        }

        return '';
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
            'method' => 'post',
            'url' => $this->service_url,
            'data' => $this->getAccessRequest() . $this->getRatingRequest(),
            'headers' => [
                'Content-type: text/xml'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSimpleRates(): array
    {
        $data = $this->getRequestData();
        return Http::post($data['url'], $data['data'], ['headers' => 'Content-type: text/xml']);
    }

    /**
     * Fill required address fields
     *
     * @param array<array-key, string> $address Address data
     *
     * @return array<array-key, string> Filled address data
     */
    public function prepareAddress(array $address): array
    {
        $default_fields = [
            'zipcode' => '',
            'state' => '',
            'country' => '',
        ];

        return array_merge($default_fields, $address);
    }

    /**
     * Returns shipping service information
     *
     * @return array<array-key, string> information
     */
    public static function getInfo(): array
    {
        return [
            'name' => __('carrier_upsdeprecated'),
            'tracking_url' => 'http://wwwapps.ups.com/etracking/tracking.cgi?tracknum=%s'
        ];
    }

    /**
     * Provides content of Access Request.
     *
     * @return string Access Request
     */
    private function getAccessRequest(): string
    {
        return '<?xml version="1.0"?>'
            . fn_array_to_xml([
                'AccessRequest@xml:lang=en-US' => [
                    'AccessLicenseNumber' => empty($this->settings['access_key']) ? '' : $this->settings['access_key'],
                    'UserId' => empty($this->settings['username']) ? '' : $this->settings['username'],
                    'Password' => empty($this->settings['password']) ? '' : $this->settings['password'],
                ]
            ]);
    }

    /**
     * Provides content of Rating Service Selection Request.
     *
     * @return string Rating Service Selection Request
     */
    private function getRatingRequest(): string
    {
        $pickup_type = empty($this->settings['pickup_type']) ? '' : $this->settings['pickup_type'];

        $rating_request = [
            'Request' => [
                'SubVersion' => self::VERSION,
                'TransactionReference' => [
                    'CustomerContext' => 'Rate Request',
                    'XpciVersion' => '1.0',
                ],
                'RequestAction' => 'Rate',
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
                        'ResidentialAddressIndicator' => '',
                    ],
                ],
                'ShipFrom' => [
                    'Address' => [
                        'StateProvinceCode' => $this->package['origination']['state'],
                        'PostalCode' => $this->package['origination']['zipcode'],
                        'CountryCode' => $this->package['origination']['country'],
                        'ResidentialAddressIndicator' => '',
                    ]
                ]
            ]
        ];

        if ((int) $this->_shipping_info['service_code'] === self::WORLDWIDE_EXPRESS_FREIGHT) {
            $rating_request['Shipment']['NumOfPieces'] = empty($this->package['I']) ? 1 : $this->package['I'];
        }

        if (isset($this->settings['negotiated_rates']) && $this->settings['negotiated_rates'] === YesNo::YES) {
            $rating_request['Shipment']['Shipper']['ShipperNumber'] = $this->settings['shipper_number'];
            $rating_request['Shipment']['RateInformation'] = [
                'NegotiatedRatesIndicator' => ''
            ];
        }

        if (!empty($this->package['location']['address'])) {
            $rating_request['Shipment']['ShipTo']['Address']['AddressLine1'] = $this->package['location']['address'];
            if (!empty($this->package['location']['address_2'])) {
                $rating_request['Shipment']['ShipTo']['Address']['AddressLine2'] = $this->package['location']['address_2'];
            }
        }

        $rating_request['Shipment'] += $this->preparePackages();
        $rating_request['Shipment'] += $this->prepareShipmentOptions();

        return '<?xml version="1.0"?>'
            . fn_array_to_xml([
                'RatingServiceSelectionRequest@xml:lang=en-US' => $rating_request
            ]);
    }

    /**
     * Prepares shipment service options based on the shipping method settings and delivery type (local/international).
     *
     * @return array<string, array<string, array<string, int>>>
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
                        'DCISType' => (int) $this->settings['dcist_type'] - 1
                    ],
                ],
            ];
        }

        return $shipment_options;
    }

    /**
     * Prepares package service options based on the shipping method settings and delivery type (local/international).
     *
     * @return array<string, array<string, array<string, int>>> Package service options
     */
    private function preparePackageOptions(): array
    {
        $package_options = [];

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
                'DCISType' => (int) $this->settings['dcist_type']
            ];
        }

        return $package_options ? ['PackageServiceOptions' => $package_options] : [];
    }

    /**
     * Gets meausement units for the specified country.
     *
     * @param string $country Country code
     *
     * @return array<array-key, string> Weight unit and length unit
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
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     *
     * @return array<string, mixed> Packages
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
                    ],
                    'Length' => empty($package['shipping_params']['box_length']) ? $default_length : $package['shipping_params']['box_length'],
                    'Width' => empty($package['shipping_params']['box_width']) ? $default_width  : $package['shipping_params']['box_width'],
                    'Height' => empty($package['shipping_params']['box_height']) ? $default_height : $package['shipping_params']['box_height'],
                ],
                'PackageWeight' => [
                    'UnitOfMeasurement' => [
                        'Code' => $weight_unit,
                    ],
                    'Weight' => empty($package['weight']) ? $default_weight : $this->prepareWeight($package['weight'], $weight_unit),
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
}
