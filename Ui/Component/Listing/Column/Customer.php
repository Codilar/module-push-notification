<?php

namespace Codilar\PushNotification\Ui\Component\Listing\Column;

use Codilar\PushNotification\Logger\Logger;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException as NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * class Customer
 *
 * @description Data Manipulation for Registered Customer Row data
 * @author   Codilar Team Player <ankith@codilar.com>
 * @license  Open Source
 * @link     https://www.codilar.com
 * @copyright Copyright © 2020 Codilar Technologies Pvt. Ltd.. All rights reserved
 *
 * Data Manipulation for Registered Customer Row data
 */

class Customer extends Column
{

    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var array
     */
    private $data;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    private $country;

    /**
     * @var array
     */
    protected $filterMap = [
        'default' => 'text',
        'select' => 'select',
        'boolean' => 'select',
        'multiselect' => 'select',
        'date' => 'dateRange',
    ];

    /**
     * Thumbnail constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param \Magento\Directory\Model\CountryFactory $country
     * @param Logger $logger
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $groupRepository,
        \Magento\Directory\Model\CountryFactory $country,
        Logger $logger,
        array $components = [],
        array $data = []
    ) {
        $this->context = $context;
        $this->uiComponentFactory = $uiComponentFactory;
        $this->components = $components;
        $this->urlBuilder = $urlBuilder;
        $this->data = $data;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
        $this->groupRepository = $groupRepository;
        $this->country = $country;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['status']) {
                    $item['status'] = 'active';
                } else {
                    $item['status'] = 'inactive';
                }
                if (isset($item['country_id'])) {
                    try {
                        $country = $this->country->create();
                        $this->logger->info($country->loadByCode($item['country_id'])->getName());

                        $item['country_id'] = $country->loadByCode($item['country_id'])->getName();
                    } catch (NoSuchEntityException $e) {
                        $this->logger->info('NoSuchEntityException : ' . $e->getMessage());
                    } catch (LocalizedException $e) {
                        $this->logger->info('LocalizedException : ' . $e->getMessage());
                    }
                }
            }
        }
        return $dataSource;
    }
    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     * @return string
     */
    protected function getFilterType($frontendInput)
    {
        return isset($this->filterMap[$frontendInput]) ? $this->filterMap[$frontendInput] : $this->filterMap['default'];
    }
}
