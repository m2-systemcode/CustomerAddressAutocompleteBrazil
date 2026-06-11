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

namespace SystemCode\CustomerAddressAutocompleteBrazil\Model\Data;

use Magento\Framework\DataObject;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\Data\AddressSearchResultInterface;

class AddressSearchResult extends DataObject implements AddressSearchResultInterface
{
    /**
     * Check whether entity has error.
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return (bool) $this->getData(self::ERROR);
    }

    /**
     * Set error.
     *
     * @param bool $error
     * @return AddressSearchResultInterface
     */
    public function setError(bool $error): AddressSearchResultInterface
    {
        return $this->setData(self::ERROR, $error);
    }

    /**
     * Retrieve zip code.
     *
     * @return ?string
     */
    public function getZipCode(): ?string
    {
        $value = $this->getData(self::ZIP_CODE);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Set zip code.
     *
     * @param ?string $zipCode
     * @return AddressSearchResultInterface
     */
    public function setZipCode(?string $zipCode): AddressSearchResultInterface
    {
        return $this->setData(self::ZIP_CODE, $zipCode);
    }

    /**
     * Retrieve street.
     *
     * @return ?string
     */
    public function getStreet(): ?string
    {
        $value = $this->getData(self::STREET);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Set street.
     *
     * @param ?string $street
     * @return AddressSearchResultInterface
     */
    public function setStreet(?string $street): AddressSearchResultInterface
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * Retrieve neighborhood.
     *
     * @return ?string
     */
    public function getNeighborhood(): ?string
    {
        $value = $this->getData(self::NEIGHBORHOOD);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Set neighborhood.
     *
     * @param ?string $neighborhood
     * @return AddressSearchResultInterface
     */
    public function setNeighborhood(?string $neighborhood): AddressSearchResultInterface
    {
        return $this->setData(self::NEIGHBORHOOD, $neighborhood);
    }

    /**
     * Retrieve additional info.
     *
     * @return ?string
     */
    public function getAdditionalInfo(): ?string
    {
        $value = $this->getData(self::ADDITIONAL_INFO);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Set additional info.
     *
     * @param ?string $additionalInfo
     * @return AddressSearchResultInterface
     */
    public function setAdditionalInfo(?string $additionalInfo): AddressSearchResultInterface
    {
        return $this->setData(self::ADDITIONAL_INFO, $additionalInfo);
    }

    /**
     * Retrieve city.
     *
     * @return ?string
     */
    public function getCity(): ?string
    {
        $value = $this->getData(self::CITY);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Set city.
     *
     * @param ?string $city
     * @return AddressSearchResultInterface
     */
    public function setCity(?string $city): AddressSearchResultInterface
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Retrieve region.
     *
     * @return ?string
     */
    public function getRegion(): ?string
    {
        $value = $this->getData(self::REGION);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Set region.
     *
     * @param ?string $region
     * @return AddressSearchResultInterface
     */
    public function setRegion(?string $region): AddressSearchResultInterface
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * Retrieve region id.
     *
     * @return ?string
     */
    public function getRegionId(): ?string
    {
        $value = $this->getData(self::REGION_ID);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Set region id.
     *
     * @param ?string $regionId
     * @return AddressSearchResultInterface
     */
    public function setRegionId(?string $regionId): AddressSearchResultInterface
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * Check whether valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return (bool) $this->getData(self::IS_VALID);
    }

    /**
     * Set is valid.
     *
     * @param bool $isValid
     * @return AddressSearchResultInterface
     */
    public function setIsValid(bool $isValid): AddressSearchResultInterface
    {
        return $this->setData(self::IS_VALID, $isValid);
    }
}
