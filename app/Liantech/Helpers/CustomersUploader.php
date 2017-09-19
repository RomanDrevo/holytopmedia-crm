<?php


namespace App\Liantech\Helpers;


use Liantech\Spot;
use App\Liantech\Classes\Pusher;

class MyLogger {
    public function log( $msg ) {
        print_r( $msg . "\n" );
    }
}


class CustomersUploader
{
    protected $csvFile;

    protected $client;
    protected $pusher;
    protected $add_channel;
    protected $edit_channel;
    protected $allowedColumns = ["FirstName", "LastName", "email", "password", "Phone", "Country", "currency", "campaignId", "subCampaign", "a_aid"];
    protected $existingColumns = [];


    protected $type;

    function __construct($csvFile, $type = "add")
    {
        $this->csvFile = $csvFile;
        $this->type = $type;
        $this->pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            array('encrypted' => false)
        );

        $this->pusher->set_logger(new MyLogger());

        $this->add_channel = 'add_updates_channel_' . \Auth::user()->id;
        $this->edit_channel = 'edit_updates_channel_' . \Auth::user()->id;
    }

    public function setClient($username, $password, $broker)
    {
        $this->client = new Spot($username, $password, $broker);
        return $this;
    }


    public function getCustomers()
    {
        if (isset($_FILES["customers_file"])) {

            if (isset($_FILES["customers_file"]) && $this->isCsvFile($_FILES["customers_file"])) {
                $handle = fopen($_FILES['customers_file']['tmp_name'], "r");
                $customersCount = 0;
                $customersArray = [];

                $index = 0;
                while (($line = fgetcsv($handle)) !== FALSE) {
                    if ($index == 0) {
                        //check if the fields are valid and in
                        //the correct order
                        $validator = $this->validCustomersRow($line);
                        if (!$validator["success"]) {
                            die;
                        }
                    } else {
                        if ($this->valideCustomerLine($line)) {
                            //create a customer from each row
                            $customersArray[] = [
                                "MODULE" => "Customer",
                                "COMMAND" => "add",
                                "FirstName" => $line[0],
                                "LastName" => $line[1],
                                "email" => $line[2],
                                "Phone" => (string) intval((float)$line[3]),
                                "password" => $this->generate_password(),
                                "Country" => $line[4],
                                "campaignId" => $line[5],
                                "subCampaign" => $line[6],
                                "currency" => "USD",
                                "a_aid" => "1234",
                                "birthday" => "1980-07-21"
                            ];
                            $customersCount++;
                        }
                    }
                    $index++;

                }
                $data = [
                    "action" => "file_recieved",
                    "payload" => [
                        "customers_count" => $customersCount
                    ]
                ];
                $this->pusher->trigger($this->add_channel, 'update_recieved', $data);
                return $customersArray;

            }
        }
        return false;
    }

    public function getEditedCustomers()
    {
        if (isset($_FILES["customers_file"])) {

            if (isset($_FILES["customers_file"]) && $this->isCsvFile($_FILES["customers_file"])) {
                $handle = fopen($_FILES['customers_file']['tmp_name'], "r");
                $customersCount = 0;
                $customersArray = [];

                $index = 0;
                while (($line = fgetcsv($handle)) !== FALSE) {
                    if ($index == 0) {
                        $validator = $this->validEditCustomersRow($line);
                        if (!$validator["success"]) {
                            $data = [
                                "action" => "processing_error",
                                "payload" => $validator["payload"]
                            ];
                            $this->pusher->trigger($this->edit_channel, 'update_recieved', $data);
                            die;
                        }
                    } else {
                        if ($this->validateEditCustomerLine($line, $this->existingColumns)) {
                            $customer = array();
                            foreach ($this->existingColumns as $index => $column) {
                                $customer['customerId'] = $line[0];
                                $customer[$column] = $line[$index + 1];
                            }
                            $customersCount++;
                            $customersArray[] = $customer;

                        } else {
                            $data = [
                                "action" => "processing_error",
                                "payload" =>
                                    ["error" => "one of the fields on line $index is empty or in a wrong format"]
                            ];
                            $this->pusher->trigger($this->edit_channel, 'update_recieved', $data);
                            die;
                        }
                    }
                    $index++;
                }
                $data = [
                    "action" => "file_recieved",
                    "payload" => [
                        "customers_count" => $customersCount
                    ]
                ];
                $this->pusher->trigger($this->edit_channel, 'update_recieved', $data);
                return $customersArray;

            }
        }
        return false;
    }


    public function createCustomers($customers)
    {
        foreach ($customers as $index => $customer) {
            $response = $this->createCustomer($customer);
            $data = [
                "action" => "customers_uploaded",
                "payload" => [
                    "error" => !$response["success"] ? ucfirst($this->fromCamelCase($response["data"]["errors"]["error"][0])) : "",
                    "line" => ($index + 1)
                ]
            ];
            $this->pusher->trigger($this->add_channel, 'update_recieved', $data);
        }

        $data = [
            "action" => "upload_complete",
            "payload" => []
        ];
        $this->pusher->trigger($this->add_channel, 'update_recieved', $data);

        return true;
    }


    public function createCustomer($customer)
    {

        return $this->client->callWithModuleAndCommand("Customer", "add", $customer);
    }


    public function editCustomers($customers)
    {
        foreach ($customers as $index => $customer) {

            try {
                $response = $this->editCustomer($customer);
            } catch (\Exception $e) {
                continue;
            }

            $data = [
                "action" => "customers_uploaded",
                "payload" => [
                    "error" => !$response["success"] ? ucfirst($this->fromCamelCase($response["data"]["errors"]["error"][0])) : "",
                    "line" => ($index + 1)
                ]
            ];
            $this->pusher->trigger($this->edit_channel, 'update_recieved', $data);
        }

        $data = [
            "action" => "upload_complete",
            "payload" => []
        ];
        $this->pusher->trigger($this->edit_channel, 'update_recieved', $data);
    }

    public function editCustomer($customer)
    {
        if (!$customer["customerId"])
            throw new Exception("Each customer must have an ID!");

        return $this->client->callWithModuleAndCommand("Customer", "edit", $customer);

    }


    public function validCustomersRow($customerRowsNames = [])
    {

        $names = ["first_name", "last_name", "email", "phone", "country_id", "campaign_id", "subcampaign_name"];

        if (count($customerRowsNames) != count($names)) {
            return [
                "success" => false,
                "payload" => "Not enough columns in the .csv file"
            ];
        }

        foreach ($customerRowsNames as $index => $name) {
            if ($names[$index] != $name) {
                return [
                    "success" => false,
                    "payload" => "The column name " . $name . " is incorrect"
                ];
            }
        }

        return [
            "success" => true,
            "payload" => ""
        ];

    }

    private function validEditCustomersRow($customerRowsNames = [])
    {
        $existsColumns = [];

        foreach ($customerRowsNames as $index => $name) {
            if ($index == 0) {
                if ($name != "customerId") {
                    return [
                        "success" => false,
                        "payload" => [
                            "error" => "The first column should be customerId"
                        ]
                    ];
                }
                continue;
            }

            if (!in_array($name, $this->allowedColumns)) {
                return [
                    "success" => false,
                    "payload" => [
                        "error" => "The column name " . $name . " is not allowed"
                    ]
                ];
            } else {
                $existsColumns[] = $name;
            }
        }

        $this->existingColumns = $existsColumns;

        return [
            "success" => true,
            "payload" => $existsColumns
        ];

    }

    private function generate_password($length = 12, $special_chars = true)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public function fromCamelCase($camelCaseString)
    {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        return join($a, " ");
    }

    private function isCsvFile($file)
    {
        $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');

        if (!in_array($file['type'], $mimes)) {
            return false;
        }

        return true;
    }

    private function valideCustomerLine($line)
    {
        //first name
        if (!isset($line[0]) || empty($line[0])) {
            return false;
        }
        //last name
        if (!isset($line[1]) || empty($line[1])) {
            return false;
        }
        //email
        if (!isset($line[2]) || empty($line[2]) || !filter_var($line[2], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        //phone
        if (!isset($line[3]) || empty($line[3])) {
            return false;
        }
        //country ID
        if (!isset($line[4]) || empty($line[4]) || intval($line[4]) == 0) {
            return false;
        }
        //campaign ID
        if (!isset($line[5]) || empty($line[5]) || intval($line[5]) == 0) {
            return false;
        }
        return true;
    }


    public function validateEditCustomerLine($line, $csvColumns)
    {
        foreach ($line as $index => $value) {
            //customer id
            if ($index == 0) {
                if (!isset($value) || empty($value) || !is_numeric($value)) {
                    return false;
                }
            } else {
                switch ($csvColumns[$index - 1]) {
                    case "email":
                        if (!isset($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            return false;
                        }
                        break;

                    case "campaignId":
                    case "Phone":
                    case "Country":
                        if (!isset($value) || !is_numeric($value)) {
                            return false;
                        }
                        break;
                    case "currency":
                        $currencies = ['USD', 'GBP', 'EUR', 'NIS'];
                        if (!isset($value) || in_array($value, $currencies)) {
                            return false;
                        }
                        break;

                    case "subCampaign":
                    case "FirstName":
                    case "LastName":
                    case "password":
                    case "a_aid":
                        if (!isset($value)) {
                            return false;
                        }
                        break;
                }

            }
            return true;
        }
    }


}