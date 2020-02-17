<?php
namespace Main\Models;
use PDO;

class Model {
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function customerList() {
        $customers = [];

        $customerWithCarRows = $this->db->query("SELECT * FROM Customers " .
            "WHERE customerPersonalNumber IN (SELECT checkedOutBy FROM Cars WHERE checkedOutBy IS NOT NULL)");

        foreach ($customerWithCarRows as $customerRow) {
            $customer = ["customerPersonalNumber" => htmlspecialchars($customerRow["customerPersonalNumber"]),
                         "customerName" => htmlspecialchars($customerRow["customerName"]),
                         "customerAddress" => htmlspecialchars($customerRow["customerAddress"]),
                         "customerPostalAddress" => htmlspecialchars($customerRow["customerPostalAddress"]),
                         "customerPhoneNumber" => htmlspecialchars($customerRow["customerPhoneNumber"]),
                         "hasCar" => True];
            $customers[] = $customer;
        }

        $customerWithoutCarRows = $this->db->query("SELECT * FROM Customers" .
            " WHERE customerPersonalNumber NOT IN (SELECT checkedOutBy FROM Cars WHERE checkedOutBy IS NOT NULL)");

        foreach ($customerWithoutCarRows as $customerRow) {
            $customer = ["customerPersonalNumber" => htmlspecialchars($customerRow["customerPersonalNumber"]),
                         "customerName" => htmlspecialchars($customerRow["customerName"]),
                         "customerAddress" => htmlspecialchars($customerRow["customerAddress"]),
                         "customerPostalAddress" => htmlspecialchars($customerRow["customerPostalAddress"]),
                         "customerPhoneNumber" => htmlspecialchars($customerRow["customerPhoneNumber"]),
                         "hasCar" => False];
            $customers[] = $customer;
        }
        return $customers;
    }

    public function addCustomer($properties) {
        $customersQuery = "INSERT INTO Customers(customerPersonalNumber,
                                                 customerName,
                                                 customerAddress,
                                                 customerPostalAddress,
                                                 customerPhoneNumber)
                           VALUES(:customerPersonalNumber,
                                  :customerName,
                                  :customerAddress,
                                  :customerPostalAddress,
                                  :customerPhoneNumber)";
        $customersStatement = $this->db->prepare($customersQuery);
        $customersStatement->execute($properties);
        if (!$customersStatement) die("Fatal error.");
    }

    public function editCustomer($properties) {
        $customersQuery = "UPDATE Customers SET customerName = :customerName, " .
                                               "customerAddress = :customerAddress, " .
                                               "customerPostalAddress = :customerPostalAddress, " .
                                               "customerPhoneNumber = :customerPhoneNumber " .
                          "WHERE customerPersonalNumber = :customerPersonalNumber";
        $customersStatement = $this->db->prepare($customersQuery);
        $customersResult = $customersStatement->execute($properties);
        if (!$customersResult) die($this->db->errorInfo()[2]);
    }

    public function getCustomer($customerPersonalNumber)
    {
        $query = "SELECT * FROM Customers WHERE customerPersonalNumber = :customerPersonalNumber";
        $statement = $this->db->prepare($query);
        $result = $statement->execute(["customerPersonalNumber" => $customerPersonalNumber]);
        if (!$result) die($this->db->errorInfo()[2]);

        $customerRows = $statement->fetchAll();
        foreach ($customerRows as $customerRow) {
            // TODO use htmlspecialchars()?
            return ["customerPersonalNumber" => $customerRow["customerPersonalNumber"],
                    "customerName" => $customerRow["customerName"],
                    "customerAddress" => $customerRow["customerAddress"],
                    "customerPostalAddress" => $customerRow["customerPostalAddress"],
                    "customerPhoneNumber" => $customerRow["customerPhoneNumber"]];
        }

        return []; // no such customer (error)
    }

    public function removeCustomer($customerPersonalNumber) {
        $historyQuery = "SELECT COUNT(*) FROM History WHERE customerPersonalNumber = :customerPersonalNumber";
        $historyStatement = $this->db->prepare($historyQuery);
        $historyResult = $historyStatement->execute(["customerPersonalNumber" => $customerPersonalNumber]);
        if (!$historyResult) die($this->db->errorInfo()[2]);
        $historyRows = $historyStatement->fetchAll();
        $numberOfCars = htmlspecialchars($historyRows[0]["COUNT(*)"]);

        if ($numberOfCars == 0) {
            $customersQuery = "DELETE FROM Customers WHERE customerPersonalNumber = :customerPersonalNumber";
            $customersStatement = $this->db->prepare($customersQuery);
            $customersResult = $customersStatement->execute(["customerPersonalNumber" => $customerPersonalNumber]);
            if (!$customersResult) die($this->db->errorInfo()[2]);
        }

        return $numberOfCars;
    }

    public function carList() {
        $carRows = $this->db->query("SELECT * FROM Cars");

        $cars = [];
        foreach ($carRows as $carRow) {
            $car = ["carRegistrationNumber" => htmlspecialchars($carRow["carRegistrationNumber"]),
                    "carModel" => htmlspecialchars($carRow["carModel"]),
                    "carColor" => htmlspecialchars($carRow["carColor"]),
                    "carYear" => htmlspecialchars($carRow["carYear"]),
                    "carPrice" => htmlspecialchars($carRow["carPrice"]),
                    "checkedOutBy" => htmlspecialchars($carRow["checkedOutBy"]),
                    "checkedOutTime" => htmlspecialchars($carRow["checkedOutTime"])];
            $cars[] = $car;
        }
        return $cars;
    }

    public function addCar($properties)
    {
        $query = "INSERT INTO Cars(carRegistrationNumber,
                                   carModel,
                                   carColor,
                                   carYear,
                                   carPrice)
                           VALUES(:carRegistrationNumber,
                                  :carModel,
                                  :carColor,
                                  :carYear,
                                  :carPrice)";
        $statement = $this->db->prepare($query);
        $statement->execute($properties);
        if (!$statement) die("Fatal error.");
    }

    public function editCar($properties) {
        $query = "UPDATE Cars SET carModel = :carModel, carColor = :carColor, carYear = :carYear, carPrice = :carPrice " .
                 "WHERE carRegistrationNumber = :carRegistrationNumber";
        $statement = $this->db->prepare($query);
        $result = $statement->execute($properties);
        if (!$result) die($this->db->errorInfo()[2]);
    }

    public function getCar($carRegistrationNumber)
    {
        $query = "SELECT * FROM Cars WHERE carRegistrationNumber = :carRegistrationNumber";
        $statement = $this->db->prepare($query);
        $result = $statement->execute(["carRegistrationNumber" => $carRegistrationNumber]);
        if (!$result) die($this->db->errorInfo()[2]);

        $carRows = $statement->fetchAll();
        foreach ($carRows as $carRow) {
            // TODO use htmlspecialchars()?
            return ["carRegistrationNumber" => $carRow["carRegistrationNumber"],
                    "carModel" => $carRow["carModel"],
                    "carColor" => $carRow["carColor"],
                    "carYear" => $carRow["carYear"],
                    "carPrice" => $carRow["carPrice"],
                    "checkedOutBy" => $carRow["checkedOutBy"],
                    "checkedOutTime" => $carRow["checkedOutTime"]];
        }

        return []; // no such car (error)
    }

    public function getFreeCars() {
        $query = "SELECT * FROM Cars WHERE checkedOutBy IS NULL";
        $statement = $this->db->prepare($query);
        $result = $statement->execute();
        if (!$result) die($this->db->errorInfo()[2]);

        $carRows = $statement->fetchAll();
        $cars = [];
        foreach ($carRows as $carRow) {
            $car = ["carRegistrationNumber" => $carRow["carRegistrationNumber"],
                    "carModel" => $carRow["carModel"],
                    "carColor" => $carRow["carColor"],
                    "carYear" => $carRow["carYear"],
                    "carPrice" => $carRow["carPrice"]];
            $cars[] = $car;
        }
        return $cars;
    }

    public function removeCar($carRegistrationNumber) {
        $historyQuery = "SELECT COUNT(*) FROM History WHERE carRegistrationNumber = :carRegistrationNumber";
        $historyStatement = $this->db->prepare($historyQuery);
        $historyResult = $historyStatement->execute(["carRegistrationNumber" => $carRegistrationNumber]);
        if (!$historyResult) die($this->db->errorInfo()[2]);
        $historyRows = $historyStatement->fetchAll();
        $numberOfCars = htmlspecialchars($historyRows[0]["COUNT(*)"]);

        if ($numberOfCars == 0) {
            $carsQuery = "DELETE FROM Cars WHERE carRegistrationNumber = :carRegistrationNumber";
            $carsStatement = $this->db->prepare($carsQuery);
            $carsResult = $carsStatement->execute(["carRegistrationNumber" => $carRegistrationNumber]);
            if (!$carsResult) die($this->db->errorInfo()[2]);
        }

        return $numberOfCars;
    }

    public function checkOutCar($properties) {
        $query = "UPDATE Cars SET checkedOutBy = :customerPersonalNumber, checkedOutTime = NOW() " .
                 "WHERE carRegistrationNumber = :carRegistrationNumber";
        $statement = $this->db->prepare($query);
        $result = $statement->execute($properties);
        if (!$result) die($this->db->errorInfo()[2]);
    }

    public function listHistory() {
        $historyRows = $this->db->query("SELECT * FROM History");

        $history = [];
        foreach ($historyRows as $historyRow) {
            $history[] = ["carRegistrationNumber" => htmlspecialchars($historyRow["carRegistrationNumber"]),
                          "customerPersonalNumber" => htmlspecialchars($historyRow["customerPersonalNumber"]),
                          "checkedOutTime" => htmlspecialchars($historyRow["checkedOutTime"]),
                          "checkedInTime" => htmlspecialchars($historyRow["checkedInTime"]),
                          "days" => htmlspecialchars($historyRow["days"]),
                          "cost" => htmlspecialchars($historyRow["cost"])
            ];
        }
        return $history;
    }

    public function carModelList() {
        $carModelRows = $this->db->query("SELECT * FROM CarModels");
        $carModels = [];
        foreach ($carModelRows as $carModelRow) {
            $carModels[] = $carModelRow["carModel"];
        }
        return $carModels;
    }

    public function carColorList() {
        $carColorRows = $this->db->query("SELECT * FROM CarColors");
        $carColors = [];
        foreach ($carColorRows as $carColorRow) {
            $carColors[] = $carColorRow["carColor"];
        }
        return $carColors;
    }

    public function getCheckedOutCars() {
        $query = "SELECT * FROM Cars WHERE checkedOutBy IS NOT NULL";
        $statement = $this->db->prepare($query);
        $result = $statement->execute();
        if (!$result) die($this->db->errorInfo()[2]);

        $carRows = $statement->fetchAll();
        $cars = [];
        foreach ($carRows as $carRow) {
            $car = ["carRegistrationNumber" => $carRow["carRegistrationNumber"],
                    "carModel" => $carRow["carModel"],
                    "carColor" => $carRow["carColor"],
                    "carYear" => $carRow["carYear"],
                    "carPrice" => $carRow["carPrice"],
                    "checkedOutBy" => $carRow["checkedOutBy"],
                    "checkedOutTime" => $carRow["checkedOutTime"]];
            $cars[] = $car;
        }
        return $cars;
    }

    public function checkInCar($properties)
    {
        $query = "UPDATE Cars SET checkedOutBy = NULL, checkedOutTime = NULL
                  WHERE carRegistrationNumber = :carRegistrationNumber";
        $statement = $this->db->prepare($query);
        $result = $statement->execute($properties);
        if (!$result) die($this->db->errorInfo()[2]);
    }

    public function addHistory($properties)
    {
        $query = "INSERT INTO History(carRegistrationNumber,
                                      customerPersonalNumber,
                                      checkedOutTime,
                                      checkedInTime,
                                      days,
                                      cost)
                           VALUES(:carRegistrationNumber,
                                  :customerPersonalNumber,
                                  :checkedOutTime,
                                  :checkedInTime,
                                  :days,
                                  :cost)";
        $statement = $this->db->prepare($query);
        $result = $statement->execute($properties);
        if (!$result) die($this->db->errorInfo()[2]);
    }

    public function removeCustomerHistory($customerPersonalNumber)
    {
        $query = "DELETE FROM History WHERE customerPersonalNumber = :customerPersonalNumber";
        $statement = $this->db->prepare($query);
        $result = $statement->execute(["customerPersonalNumber" => $customerPersonalNumber]);
        if (!$result) die($this->db->errorInfo()[2]);
    }

    public function removeCarHistory($carRegistrationNumber)
    {
        $query = "DELETE FROM History WHERE carRegistrationNumber = :carRegistrationNumber";
        $statement = $this->db->prepare($query);
        $result = $statement->execute(["carRegistrationNumber" => $carRegistrationNumber]);
        if (!$result) die($this->db->errorInfo()[2]);
    }
}