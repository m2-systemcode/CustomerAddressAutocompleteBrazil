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

namespace SystemCode\CustomerAddressAutocompleteBrazil\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Check whether enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check whether accept general zip code.
     *
     * @return bool
     */
    public function isAcceptGeneralZipCode(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ACCEPT_GENERAL_ZIPCODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether lock found fields.
     *
     * @return bool
     */
    public function isLockFoundFields(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_LOCK_FOUND_FIELDS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether postcode before address.
     *
     * @return bool
     */
    public function isPostcodeBeforeAddress(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_POSTCODE_BEFORE_ADDRESS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether lock fields until lookup.
     *
     * @return bool
     */
    public function isLockFieldsUntilLookup(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_LOCK_FIELDS_UNTIL_LOOKUP,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve search url path.
     *
     * @return string
     */
    public function getSearchUrlPath(): string
    {
        return self::SEARCH_URL_PATH;
    }

    /**
     * Retrieve street line mapping.
     *
     * @return string[]
     */
    public function getStreetLineMapping(): array
    {
        $paths = [
            self::XML_PATH_STREET_MAPPING_LINE_1,
            self::XML_PATH_STREET_MAPPING_LINE_2,
            self::XML_PATH_STREET_MAPPING_LINE_3,
            self::XML_PATH_STREET_MAPPING_LINE_4,
        ];
        $defaults = [
            self::STREET_FIELD_STREET,
            self::STREET_FIELD_NONE,
            self::STREET_FIELD_NEIGHBORHOOD,
            self::STREET_FIELD_ADDITIONAL_INFO,
        ];
        $mapping = [];

        foreach ($paths as $index => $path) {
            $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
            $mapping[] = $this->normalizeStreetLineField(
                is_string($value) ? $value : null,
                $defaults[$index]
            );
        }

        return $mapping;
    }

    /**
     * Handle normalize street line field.
     *
     * @param ?string $value
     * @param string $default
     * @return string
     */
    private function normalizeStreetLineField(?string $value, string $default): string
    {
        $allowed = [
            self::STREET_FIELD_NONE,
            self::STREET_FIELD_STREET,
            self::STREET_FIELD_NEIGHBORHOOD,
            self::STREET_FIELD_ADDITIONAL_INFO,
        ];

        if ($value !== null && in_array($value, $allowed, true)) {
            return $value;
        }

        return $default;
    }
}
