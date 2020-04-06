<?php

namespace Src\Controller;

use Src\TableGateways\AttacksGateway;

class AttacksController
{
    private $requestMethod;

    private $attacksGateway;

    public function __construct($db, $requestMethod)
    {
        $this->requestMethod = $requestMethod;

        $this->attacksGateway = new AttacksGateway($db);
    }

    public function processRequest($uri)
    {
        switch ($this->requestMethod) {
            case 'GET':
                if (isset($_GET["preview"]) && $_GET["preview"] == "true") {
                    $response = $this->getAttacksPreview();
                } else if (sizeof($uri) > 3) {
                    $response = $this->getById($uri[3]);
                } else {
                    $response = $this->getFirst(1000);
                }
                break;
            case 'OPTIONS':
                break;
            case 'POST':
                $rawData = file_get_contents("php://input");
                $decoded = json_decode($rawData, true);
                $response = $this->getStatisticsResult($decoded);
        }
        if (isset($response['status_code_header'])) {
            header($response['status_code_header']);
        }
        if (isset($response['body'])) {
            if ($response['body']) {
                echo $response['body'];
            }
        }
    }

    private function getStatisticsResult($decoded){

        $this->getStartDate($decoded);
        $this->setArrays($decoded, "weaponsUsed");
        $this->setArrays($decoded, "attacksUsed");
        $this->setArrays($decoded, "tagets");

        $result = $this->attacksGateway->getStatistics($decoded);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body']=json_encode($decoded);
        print_r($response['body']);
        return $response;
    }

    private function getStartDate(&$decoded){
        if ($decoded["dateStart"]==""){
            $decoded["dateStart"]="1970-01-01";
        }
    }

    private function setArrays(&$decoded, $name){
        $i=0;
        $exploded = explode(",", $decoded[$name]);
        $decoded[$name] = "1000";
        foreach ($exploded as $value){
            if (strcmp($value,"true")==0){
                $decoded[$name]=$decoded[$name].",$i";
            }
            $i++;
        }
    }







    private function getFirst($first)
    {
        $result = $this->attacksGateway->getFirst($first);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getById($id)
    {
        if (is_numeric($id) && intval($id) >= 0 && intval($id) <= 180000) {
            $result = $this->attacksGateway->getById($id);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
        } else {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode("eroare");
        }
        return $response;
    }

    private function getAttacksPreview()
    {
        $result = $this->attacksGateway->getPreview();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
}
