<?php
/**
 * NOTICE OF LICENSE
 *
 * @category  SystemCode
 * @package   Systemcode_CustomerAddressAutocompleteBrazil
 * @author    Eduardo Diogo Dias <contato@systemcode.com.br>
 * @copyright System Code LTDA - ME
 * @license   http://opensource.org/licenses/osl-3.0.php
 */
declare(strict_types=1);

namespace SystemCode\CustomerAddressAutocompleteBrazil\Api;

interface ConfigInterface
{
    public const string XML_PATH_ENABLED = 'customeraddressautocompletebrazil/general/enabled';
    public const string XML_PATH_ACCEPT_GENERAL_ZIPCODE =
        'customeraddressautocompletebrazil/general/accept_general_zipcode';
    public const string XML_PATH_LOCK_FOUND_FIELDS =
        'customeraddressautocompletebrazil/general/lock_found_fields';
    public const string XML_PATH_POSTCODE_BEFORE_ADDRESS =
        'customeraddressautocompletebrazil/general/postcode_before_address';
    public const string XML_PATH_LOCK_FIELDS_UNTIL_LOOKUP =
        'customeraddressautocompletebrazil/general/lock_fields_until_lookup';
    public const string XML_PATH_STREET_MAPPING_LINE_1 = 'customeraddressautocompletebrazil/street_mapping/line_1';
    public const string XML_PATH_STREET_MAPPING_LINE_2 = 'customeraddressautocompletebrazil/street_mapping/line_2';
    public const string XML_PATH_STREET_MAPPING_LINE_3 = 'customeraddressautocompletebrazil/street_mapping/line_3';
    public const string XML_PATH_STREET_MAPPING_LINE_4 = 'customeraddressautocompletebrazil/street_mapping/line_4';
    public const string STREET_FIELD_NONE = 'none';
    public const string STREET_FIELD_STREET = 'street';
    public const string STREET_FIELD_NEIGHBORHOOD = 'neighborhood';
    public const string STREET_FIELD_ADDITIONAL_INFO = 'additional_info';
    public const int STREET_LINE_COUNT = 4;
    public const string SEARCH_URL_PATH = 'rest/V1/systemcode-brazil-zipcode/search/';
    public const string POSTCODE_COMPONENT = 'SystemCode_CustomerAddressAutocompleteBrazil/js/checkout/postcode';
    public const string POSTCODE_TEMPLATE = 'SystemCode_CustomerAddressAutocompleteBrazil/checkout/postcode';

    /**
     * Check whether enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Check whether accept general zip code.
     *
     * @return bool
     */
    public function isAcceptGeneralZipCode(): bool;

    /**
     * Check whether lock found fields.
     *
     * @return bool
     */
    public function isLockFoundFields(): bool;

    /**
     * Check whether postcode before address.
     *
     * @return bool
     */
    public function isPostcodeBeforeAddress(): bool;

    /**
     * Check whether lock fields until lookup.
     *
     * @return bool
     */
    public function isLockFieldsUntilLookup(): bool;

    /**
     * Retrieve search url path.
     *
     * @return string
     */
    public function getSearchUrlPath(): string;

    /**
     * Retrieve street line mapping.
     *
     * @return string[]
     */
    public function getStreetLineMapping(): array;
}
