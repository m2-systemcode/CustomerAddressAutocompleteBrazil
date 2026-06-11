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

namespace SystemCode\CustomerAddressAutocompleteBrazil\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor as Subject;
use SystemCode\CustomerAddressAutocompleteBrazil\Api\ConfigInterface;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class LayoutProcessor
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     */
    public function __construct(
        private readonly ConfigInterface $config
    ) {
    }

    /**
     * Execute after process.
     *
     * @param Subject $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(Subject $subject, array $jsLayout): array
    {
        if (!$this->config->isEnabled()) {
            return $jsLayout;
        }

        $jsLayout = $this->applyToShipping($jsLayout);

        return $this->applyToBilling($jsLayout);
    }

    /**
     * Apply to shipping.
     *
     * @param array $jsLayout
     * @return array
     */
    private function applyToShipping(array $jsLayout): array
    {
        $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];

        if (!isset($fieldset['postcode']) || !is_array($fieldset['postcode'])) {
            return $jsLayout;
        }

        $this->applyPostcodeComponent($fieldset['postcode'], $fieldset);

        return $jsLayout;
    }

    /**
     * Apply to billing.
     *
     * @param array $jsLayout
     * @return array
     */
    private function applyToBilling(array $jsLayout): array
    {
        $paymentsList = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children'] ?? null;

        if (!is_array($paymentsList)) {
            return $jsLayout;
        }

        foreach (array_keys($paymentsList) as $paymentMethodForm) {
            if (!str_ends_with($paymentMethodForm, '-form')) {
                continue;
            }

            $fieldset = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentMethodForm]
                ['children']['form-fields']['children'];

            if (!isset($fieldset['postcode']) || !is_array($fieldset['postcode'])) {
                continue;
            }

            $this->applyPostcodeComponent($fieldset['postcode'], $fieldset);
        }

        return $jsLayout;
    }

    /**
     * Apply postcode component.
     *
     * @param array $postcode
     * @param array $fieldset
     * @return void
     */
    private function applyPostcodeComponent(array &$postcode, array &$fieldset): void
    {
        $postcode['component'] = ConfigInterface::POSTCODE_COMPONENT;

        if (!isset($postcode['config']) || !is_array($postcode['config'])) {
            $postcode['config'] = [];
        }

        $postcode['config']['elementTmpl'] = ConfigInterface::POSTCODE_TEMPLATE;

        if ($this->config->isPostcodeBeforeAddress() && isset($fieldset['street'])) {
            $postcode['sortOrder'] = $this->resolvePostcodeSortOrder($fieldset);
        }
    }

    /**
     * Resolve postcode sort order.
     *
     * @param array $fieldset
     * @return int
     */
    private function resolvePostcodeSortOrder(array $fieldset): int
    {
        if (isset($fieldset['street_prefix']['sortOrder'])) {
            return (int) $fieldset['street_prefix']['sortOrder'] - 5;
        }

        return (int) ($fieldset['street']['sortOrder'] ?? 70) - 5;
    }
}
