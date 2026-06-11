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

use SystemCode\CustomerAddressAutocompleteBrazil\Api\ConfigInterface;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\Data\AddressSearchResultInterface;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\Data\AddressSearchResultInterfaceFactory;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\ZipCodeSearchInterface;

class ZipCodeSearch implements ZipCodeSearchInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param ViaCepClient $viaCepClient
     * @param RegionResolver $regionResolver
     * @param AddressSearchResultInterfaceFactory $resultFactory
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly ViaCepClient $viaCepClient,
        private readonly RegionResolver $regionResolver,
        private readonly AddressSearchResultInterfaceFactory $resultFactory
    ) {
    }

    /**
     * Handle search.
     *
     * @param string $zipCode
     * @return AddressSearchResultInterface
     */
    public function search(string $zipCode): AddressSearchResultInterface
    {
        $result = $this->resultFactory->create();
        $normalizedZipCode = preg_replace('/\D/', '', $zipCode) ?? '';

        if (!$this->config->isEnabled()) {
            return $result
                ->setError(true)
                ->setZipCode($normalizedZipCode)
                ->setIsValid(false);
        }

        if (strlen($normalizedZipCode) !== 8) {
            return $result
                ->setError(true)
                ->setZipCode($normalizedZipCode)
                ->setIsValid(false);
        }

        $response = $this->viaCepClient->fetch($normalizedZipCode);

        if ($response === null) {
            return $result
                ->setError(true)
                ->setZipCode($normalizedZipCode)
                ->setIsValid(false);
        }

        $street = $this->normalizeValue($response['logradouro'] ?? null);
        $neighborhood = $this->normalizeValue($response['bairro'] ?? null);
        $city = $this->normalizeValue($response['localidade'] ?? null);
        $region = $this->normalizeValue($response['uf'] ?? null);
        $additionalInfo = $this->normalizeValue($response['complemento'] ?? null);
        $isValid = $this->isValidResult($street, $city, $region);

        return $result
            ->setError(!$isValid)
            ->setZipCode($normalizedZipCode)
            ->setStreet($street)
            ->setNeighborhood($neighborhood)
            ->setAdditionalInfo($additionalInfo)
            ->setCity($city)
            ->setRegion($region)
            ->setRegionId($this->regionResolver->getRegionId($region))
            ->setIsValid($isValid);
    }

    /**
     * Check whether valid result.
     *
     * @param ?string $street
     * @param ?string $city
     * @param ?string $region
     * @return bool
     */
    private function isValidResult(?string $street, ?string $city, ?string $region): bool
    {
        if ($city === null || $region === null) {
            return false;
        }

        if ($street !== null) {
            return true;
        }

        return $this->config->isAcceptGeneralZipCode();
    }

    /**
     * Handle normalize value.
     *
     * @param mixed $value
     * @return ?string
     */
    private function normalizeValue(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
