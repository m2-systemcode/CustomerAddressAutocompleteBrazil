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

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\ZipCodeSearchInterface;

class ViaCepClient
{
    /**
     * Initialize dependencies.
     *
     * @param Curl $curl
     * @param Json $json
     */
    public function __construct(
        private readonly Curl $curl,
        private readonly Json $json
    ) {
    }

    /**
     * Handle fetch.
     *
     * @param string $zipCode
     * @return ?array
     */
    public function fetch(string $zipCode): ?array
    {
        $zipCode = preg_replace('/\D/', '', $zipCode) ?? '';

        if (strlen($zipCode) !== 8) {
            return null;
        }

        $this->curl->setTimeout(ZipCodeSearchInterface::TIMEOUT);
        $this->curl->get(ZipCodeSearchInterface::BASE_URL . $zipCode . '/json');

        if ($this->curl->getStatus() !== 200) {
            return null;
        }

        $body = $this->json->unserialize($this->curl->getBody());

        if (!is_array($body) || ($body['erro'] ?? false) === true) {
            return null;
        }

        return $body;
    }
}
