<?php

namespace EthanYehuda\CronjobManager\Observer;

use EthanYehuda\CronjobManager\Helper\Config;
use EthanYehuda\CronjobManager\Model\CleanRunningJobs;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CleanRunningJobsObserver implements ObserverInterface
{
    /** @var Config */
    private $configHelper;

    /** @var CleanRunningJobs */
    private $cleanRunningJobs;

    public function __construct(
        CleanRunningJobs $cleanRunningJobs,
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
        $this->cleanRunningJobs = $cleanRunningJobs;
    }

    /**
     * If this feature is active, Find all jobs in status "running" (according to db),
     * and check if the process is alive. If not, set status to error, with the message
     * "Process went away"
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->configHelper->getConfig('clean_running_schedule')) {
            return;
        }

        $this->cleanRunningJobs->execute();
    }
}
