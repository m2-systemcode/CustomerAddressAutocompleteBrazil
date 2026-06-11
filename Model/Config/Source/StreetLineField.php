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

namespace SystemCode\CustomerAddressAutocompleteBrazil\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\ConfigInterface;

class StreetLineField implements OptionSourceInterface
{
    /**
     * Convert to option array.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => ConfigInterface::STREET_FIELD_NONE, 'label' => __('None')],
            ['value' => ConfigInterface::STREET_FIELD_STREET, 'label' => __('Street')],
            ['value' => ConfigInterface::STREET_FIELD_NEIGHBORHOOD, 'label' => __('Neighborhood')],
            ['value' => ConfigInterface::STREET_FIELD_ADDITIONAL_INFO, 'label' => __('Additional Info')],
        ];
    }
}
