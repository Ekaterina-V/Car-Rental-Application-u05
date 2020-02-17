<?php
namespace Main\Controllers;

use Main\Core\Request;
use Main\Models\Model;
use Main\Utils\DependencyInjector;

class CarController {
    private $di;
    private $request;

    public function __construct(DependencyInjector $di, Request $request)
    {
        $this->di = $di;
        $this->request = $request;
    }

    public function carList() {
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');

        $model = new Model($db);
        $cars = $model->carList();
        $map = ["cars" => $cars];
        return $twig->loadTemplate("CarsView.twig")->render($map);
    }

    public function addCar() {
        $db = $this->di->get('PDO');
        $model = new Model($db);
        $twig = $this->di->get('Twig_Environment');
        $carModels = $model->carModelList();
        $carColors = $model->carColorList();

        $properties = ["carModels" => $carModels,
                       "carColors" => $carColors];
        return $twig->render("AddCarView.twig", $properties);

    }

    public function carAdded() {
        $form = $this->request->getForm();
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);

        $properties = ["carRegistrationNumber" => $form["carRegistrationNumber"],
                       "carModel" => $form["carModel"],
                       "carColor" => $form["carColor"],
                       "carYear" => $form["carYear"],
                       "carPrice" => $form["carPrice"]];

        $model->addCar($properties);
        return $twig->render("CarAddedView.twig", $properties);
    }

    public function editCar($carRegistrationNumber) {
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);
        $properties = $model->getCar($carRegistrationNumber);

        if ($properties === []) {
            return "No car with Registration Number " . $carRegistrationNumber;
        } else {
            $carModels = $model->carModelList();
            $carColors = $model->carColorList();

            $properties["carModels"] = $carModels;
            $properties["carColors"] = $carColors;
            return $twig->render("EditCarView.twig", $properties);
        }
    }

    public function carEdited() {
        $form = $this->request->getForm();
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);

        $properties = ["carRegistrationNumber" => $form["carRegistrationNumber"],
                       "carModel" => $form["carModel"],
                       "carColor" => $form["carColor"],
                       "carYear" => $form["carYear"],
                       "carPrice" => $form["carPrice"]];

        $model->editCar($properties);
        return $twig->render("CarEditedView.twig", $properties);
    }

    public function removeCar($carRegistrationNumber,
                              $carModel,
                              $carColor,
                              $carYear,
                              $carPrice) {
        $db = $this->di->get('PDO');
        $model = new Model($db);
        $model->removeCar($carRegistrationNumber);
        $twig = $this->di->get('Twig_Environment');

        $numberOfCars = $model->removeCar($carRegistrationNumber);

        $model->removeCarHistory($carRegistrationNumber);

        $properties = ["carRegistrationNumber" => $carRegistrationNumber,
                       "carModel" => $carModel,
                       "carColor" => $carColor,
                       "carYear" => $carYear,
                       "carPrice" => $carPrice];
        return $twig->render("CarRemovedView.twig", $properties);
    }

    public function checkOut() {
        $db = $this->di->get('PDO');
        $model = new Model($db);
        $twig = $this->di->get('Twig_Environment');
        $customers = $model->customerList();
        $cars = $model->getFreeCars();

        $properties = ["cars" => $cars,
                       "customers" => $customers];
        return $twig->render("CheckOutView.twig", $properties);
    }

    public function carCheckedOut() {
        $form = $this->request->getForm();
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);

        $properties = ["carRegistrationNumber" => $form["carRegistrationNumber"],
                       "customerPersonalNumber" => $form["customerPersonalNumber"]];
        $model->checkOutCar($properties);
        return $twig->render("CarCheckedOutView.twig", $properties);
    }

    public function checkIn() {
        $db = $this->di->get('PDO');
        $model = new Model($db);
        $twig = $this->di->get('Twig_Environment');
        $cars = $model->getCheckedOutCars();

        $properties = ["cars" => $cars];
        return $twig->render("CheckInView.twig", $properties);
    }

    public function carCheckedIn() {
        $form = $this->request->getForm();
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);

        $carRegistrationNumber = $form["carRegistrationNumber"];

        $car = $model->getCar($carRegistrationNumber);
        $checkedInTime = new \Datetime(); // now
        $checkedOutTime = \DateTime::createFromFormat("Y-m-d H:i:s", $car["checkedOutTime"]);
        $days = $checkedOutTime->diff($checkedInTime)->d + 1; // difference in days

        $properties = [
            "carRegistrationNumber" => $carRegistrationNumber,
            "customerPersonalNumber" => $car["checkedOutBy"],
            "checkedOutTime" => $car["checkedOutTime"],
            "checkedInTime" => $checkedInTime->format("Y-m-d H:i:s"),
            "days" => $days,
            "cost" => $days * $car["carPrice"]
        ];
        $model->addHistory($properties);

        $properties = ["carRegistrationNumber" => $carRegistrationNumber];
        $model->checkInCar($properties);
        return $twig->render("CarCheckedInView.twig", $properties);
    }
}