<?php

namespace EthanYehuda\CronjobManager\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper
{
    const PATH_CLEAN_RUNNING = "system/cron_job_manager/clean_running_schedule";

    const SYSTEM_CONFIG_FORMAT = 'system/cron_job_manager/%s';

    protected $scopeConfig;

    public function __construct(Context $context, ScopeConfigInterface $scopeConfig)
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig(string $fragment, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null) {
        return $this->scopeConfig->getValue($this->_constructConfigPath($fragment), $scopeType, $scopeCode);
    }

    protected function _constructConfigPath(string $fragment) {
        return sprintf(self::SYSTEM_CONFIG_FORMAT, $fragment);
    }
}