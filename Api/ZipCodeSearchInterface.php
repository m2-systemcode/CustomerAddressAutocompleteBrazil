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

use SystemCode\CustomerAddressAutocompleteBrazil\Api\Data\AddressSearchResultInterface;

/**
 * Provide configured behavior.
 *
 * @api
 */
interface ZipCodeSearchInterface
{
    public const string BASE_URL = 'https://viacep.com.br/ws/';
    public const int TIMEOUT = 5;
    public const string COUNTRY_ID = 'BR';

    /**
     * Search address data by Brazilian zip code.
     *
     * @param string $zipCode
     * @return \SystemCode\CustomerAddressAutocompleteBrazil\Api\Data\AddressSearchResultInterface
     */
    public function search(string $zipCode): AddressSearchResultInterface;
}
