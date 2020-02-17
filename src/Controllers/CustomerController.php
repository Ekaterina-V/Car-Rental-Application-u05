<?php
namespace Main\Controllers;

use Main\Core\Request;
use Main\Models\Model;
use Main\Utils\DependencyInjector;

class CustomerController {
    private $di;
    private $request;

    public function __construct(DependencyInjector $di, Request $request)
    {
        $this->di = $di;
        $this->request = $request;
    }

    public function listCustomers() {
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');

        $model = new Model($db);
        $customers = $model->customerList();
        $properties = ["customers" => $customers];
        return $twig->loadTemplate("CustomersView.twig")->render($properties);
    }

    public function addCustomer() {
        $emptyMap = [];
        return $this->di->get('Twig_Environment')->loadTemplate("AddCustomerView.twig")->render($emptyMap);
    }

    public function customerAdded() {
        $form = $this->request->getForm();
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);

        $properties = ["customerPersonalNumber" => $form["customerPersonalNumber"],
                       "customerName" => $form["customerName"],
                       "customerAddress" => $form["customerAddress"],
                       "customerPostalAddress" => $form["customerPostalAddress"],
                       "customerPhoneNumber" => $form["customerPhoneNumber"]];

        $model->addCustomer($properties);
        return $twig->render("CustomerAddedView.twig", $properties);
    }

    public function editCustomer($customerPersonalNumber) {
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);
        $properties = $model->getCustomer($customerPersonalNumber);

        if ($properties === []) {
            return "No customer with personal number " . $customerPersonalNumber;
        } else {
            return $twig->render("EditCustomerView.twig", $properties);
        }
    }

    public function customerEdited() {
        $form = $this->request->getForm();
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);

        $properties = ["customerPersonalNumber" => $form["customerPersonalNumber"],
                       "customerName" => $form["customerName"],
                       "customerAddress" => $form["customerAddress"],
                       "customerPostalAddress" => $form["customerPostalAddress"],
                       "customerPhoneNumber" => $form["customerPhoneNumber"]];

        $model->editCustomer($properties);
        return $twig->render("CustomerEditedView.twig", $properties);
    }

    public function removeCustomer($customerPersonalNumber,
                                   $customerName,
                                   $customerAddress,
                                   $customerPostalAddress,
                                   $customerPhoneNumber) {
        $db = $this->di->get('PDO');
        $model = new Model($db);
        $model->removeCustomer($customerPersonalNumber);
        $twig = $this->di->get('Twig_Environment');

        $numberOfCars = $model->removeCustomer($customerPersonalNumber);
        $model->removeCustomerHistory($customerPersonalNumber);

        $properties = ["customerPersonalNumber" => $customerPersonalNumber,
                       "customerName" => $customerName,
                       "customerAddress" => $customerAddress,
                       "customerPostalAddress" => $customerPostalAddress,
                       "customerPhoneNumber" => $customerPhoneNumber,
                       "numberOfCars" => $numberOfCars];
        return $twig->render("CustomerRemovedView.twig", $properties);
    }
}