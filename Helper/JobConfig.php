<?php

namespace EthanYehuda\CronjobManager\Helper;

use EthanYehuda\CronjobManager\Model\ManagerFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\ValidatorException;

class JobConfig extends AbstractHelper
{
    /**
     * @var WriterInterface
     */
    private $configWriter;
    
    /**
     * @var \EthanYehuda\CronjobManager\Model\Manager
     */
    protected $manager;
    
    protected $jobs = null;
    
    public function __construct(
        Context $context,
        WriterInterface $configWriter,
        ManagerFactory $managerFactory
    ) {
        parent::__construct($context);
        $this->configWriter = $configWriter;
        $this->manager = $managerFactory->create();
    }

    /**
     * @param string $jobCode
     * @return array|bool
     */
    public function getJobData($jobCode)
    {
        if(is_null($this->jobs)) {
            $this->jobs = $this->manager->getCronJobs();
        }
        
        foreach($this->jobs as $groupName => $group) {
            if (isset($group[$jobCode])) {
                $group[$jobCode]['group'] = $groupName;
                return $this->sanitizeJobConfig($group[$jobCode]);
            }
        }
        
        return false;
    }

    /**
     * @param string $path
     * @param string $frequency
     */
    public function saveJobFrequencyConfig($path, $frequency)
    {
        $this->configWriter->save($path, $frequency);
    }

    /**
     * @param string $path
     */
    public function restoreSystemDefault($path)
    {
        $this->configWriter->delete($path);
    }

    /**
     * @param $jobCode
     * @param null $group
     * @return string
     * @throws ValidatorException
     */
    public function constructFrequencyPath($jobCode, $group = null)
    {
        $validGroupId = $this->manager->getGroupId($jobCode);
        if (!$validGroupId) {
            throw new ValidatorException(__('Job Code: %1 does not exist in the system', $jobCode));
        }
        if ($group) {
            if ($group != $validGroupId) {
                throw new ValidatorException(__('Invalid Group ID: %1 for %2', $group, $jobCode));
            }
        } else {
            $group = $validGroupId;
        }
        return "crontab/$group/jobs/$jobCode/schedule/cron_expr";
    }

    /**
     * Cleans a job config, providing it with defaults to prevent method failure
     *
     * @param array $job
     * @return array
     */
    public function sanitizeJobConfig(array $job)
    {
        $fields = [ 'name', 'group', 'schedule', 'instance', 'method' ];
        foreach ($fields as $field) {
            $job[$field] = $job[$field] ?? '';
        }
        return $job;
    }
}
