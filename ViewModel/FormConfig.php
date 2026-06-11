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

namespace SystemCode\CustomerAddressAutocompleteBrazil\ViewModel;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\ConfigInterface;

class FormConfig implements ArgumentInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param Json $json
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly Json $json
    ) {
    }

    /**
     * Retrieve json config.
     *
     * @return string
     */
    public function getJsonConfig(): string
    {
        return $this->json->serialize([
            'enabled' => $this->config->isEnabled(),
            'lockFoundFields' => $this->config->isLockFoundFields(),
            'postcodeBeforeAddress' => $this->config->isPostcodeBeforeAddress(),
            'lockFieldsUntilLookup' => $this->config->isLockFieldsUntilLookup(),
            'streetLineMapping' => $this->config->getStreetLineMapping(),
        ]);
    }
}
