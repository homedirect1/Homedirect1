<?php


namespace Ced\Affiliate\Ui\Component\Listing\Columns;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class CustomerName extends \Magento\Ui\Component\Listing\Columns\Column
{

    protected $customerRepository;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $customer_id = $item['customer_id'];
                if (empty($item['customer_name']) && !empty($customer_id)) {
                    try {
                        $customer = $this->customerRepository->getById($customer_id);
                        if ($customer) {
                            $item['customer_name'] = $customer->getFirstname() .' '. $customer->getLastname();
                        }
                    } catch (NoSuchEntityException $e) {
                    } catch (LocalizedException $e) {
                    }
                }
            }
        }

        return $dataSource;
    }
}