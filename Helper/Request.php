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

use Magento\Framework\App\Helper\Context;
use Magento\Backend\App\ConfigInterface;
use Ced\Dropship\Helper\Config;

/**
 * Class Request, for api integration
 */
class Request extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Ced\Dropship\Helper\Config
     */
    protected $helper;

    /**
     * @var body
     */
    protected $body;

    /**
     * Data Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Ced\Dropship\Helper\Config $helper,
        \Magento\Backend\App\ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($context);
        $this->curl = $curl;
        $this->helper = $helper;
    }

    /**
     * Set parameters body
     *
     * @param array $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get parameters body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get API Base URL
     *
     * @return string
     */
    private function getApiUrl()
    {
        return "https://api.dropshipzone.com.au/";
    }

    /**
     * Get Auth Token
     *
     * @param string $user
     * @param string $pass
     * @return array
     */
    public function getAuthToken($user, $pass)
    {
        $url = $this->getApiUrl().'auth';
        $headers = ["Content-Type" => "application/json"];
        $this->curl->setHeaders($headers);
        $params = [
            'email' => $user,
            'password' => $pass,
        ];
        $this->curl->post($url, json_encode($params));
        $res = $this->curl->getBody();
        $res = json_decode($res, true);
        if (isset($res['code'])) {
            return $res['code'];
        } else {
            return $res['token'];
        }
    }

    /**
     * Get request API
     *
     * @param string $url
     * @param string $method
     * @return array
     */
    public function sendRequest($url, $method)
    {
        try {
            $user = $this->helper->getCustomerEmail();
            $pass = $this->helper->getCustomerPassword();
            $token = $this->getAuthToken($user, $pass);
            if ($token == "Authentication Failed") {
                return ['code' => $token];
            }
            $url = $this->getApiUrl().$url;
            $headers = ["Content-Type" => "application/json", "Authorization" => "jwt ".$token];
            $this->curl->setHeaders($headers);
            if ($method == 'get') {
                $this->curl->get($url);
            } else {
                $this->curl->post($url, $this->getBody());
            }
            return $this->curl->getBody();
        } catch (Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/request.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
