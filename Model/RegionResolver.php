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

use Magento\Directory\Model\RegionFactory;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\ZipCodeSearchInterface;

class RegionResolver
{
    /**
     * Initialize dependencies.
     *
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        private readonly RegionFactory $regionFactory
    ) {
    }

    /**
     * Retrieve region id.
     *
     * @param ?string $regionCode
     * @return ?string
     */
    public function getRegionId(?string $regionCode): ?string
    {
        if ($regionCode === null || $regionCode === '') {
            return null;
        }

        $regionId = $this->regionFactory->create()
            ->loadByCode($regionCode, ZipCodeSearchInterface::COUNTRY_ID)
            ->getRegionId();

        return $regionId ? (string) $regionId : null;
    }
}
