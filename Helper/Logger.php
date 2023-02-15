<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Dropship
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Dropship\Helper;

/**
 * Class Logger, get log generate
 */
class Logger extends \Ced\Integrator\Helper\Logger
{
    /**
     * Logger constructor
     *
     * @param \Ced\Integrator\Model\LogFactory $log
     * @param Config $config
     * @param string $name
     */
    public function __construct(
        \Ced\Integrator\Model\LogFactory $log,
        Config $config,
        $name = 'Dropshipzone'
    ) {
        if ($config->getDebugMode()) {
            parent::__construct($log, $name);
        }
    }
}
