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

namespace SystemCode\CustomerAddressAutocompleteBrazil\Api\Data;

/**
 * Provide configured behavior.
 *
 * @api
 */
interface AddressSearchResultInterface
{
    public const string ERROR = 'error';
    public const string ZIP_CODE = 'zip_code';
    public const string STREET = 'street';
    public const string NEIGHBORHOOD = 'neighborhood';
    public const string ADDITIONAL_INFO = 'additional_info';
    public const string CITY = 'city';
    public const string REGION = 'region';
    public const string REGION_ID = 'region_id';
    public const string IS_VALID = 'is_valid';

    /**
     * Check whether entity has error.
     *
     * @return bool
     */
    public function hasError(): bool;

    /**
     * Set error.
     *
     * @param bool $error
     * @return $this
     */
    public function setError(bool $error): self;

    /**
     * Retrieve zip code.
     *
     * @return string|null
     */
    public function getZipCode(): ?string;

    /**
     * Set zip code.
     *
     * @param string|null $zipCode
     * @return $this
     */
    public function setZipCode(?string $zipCode): self;

    /**
     * Retrieve street.
     *
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * Set street.
     *
     * @param string|null $street
     * @return $this
     */
    public function setStreet(?string $street): self;

    /**
     * Retrieve neighborhood.
     *
     * @return string|null
     */
    public function getNeighborhood(): ?string;

    /**
     * Set neighborhood.
     *
     * @param string|null $neighborhood
     * @return $this
     */
    public function setNeighborhood(?string $neighborhood): self;

    /**
     * Retrieve additional info.
     *
     * @return string|null
     */
    public function getAdditionalInfo(): ?string;

    /**
     * Set additional info.
     *
     * @param string|null $additionalInfo
     * @return $this
     */
    public function setAdditionalInfo(?string $additionalInfo): self;

    /**
     * Retrieve city.
     *
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * Set city.
     *
     * @param string|null $city
     * @return $this
     */
    public function setCity(?string $city): self;

    /**
     * Retrieve region.
     *
     * @return string|null
     */
    public function getRegion(): ?string;

    /**
     * Set region.
     *
     * @param string|null $region
     * @return $this
     */
    public function setRegion(?string $region): self;

    /**
     * Retrieve region id.
     *
     * @return string|null
     */
    public function getRegionId(): ?string;

    /**
     * Set region id.
     *
     * @param string|null $regionId
     * @return $this
     */
    public function setRegionId(?string $regionId): self;

    /**
     * Check whether valid.
     *
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Set is valid.
     *
     * @param bool $isValid
     * @return $this
     */
    public function setIsValid(bool $isValid): self;
}
