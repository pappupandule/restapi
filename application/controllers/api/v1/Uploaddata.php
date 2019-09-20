<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Uploaddata extends REST_Controller {

    public function index_get() {
        $postData = array();
        $postData['url'] = "https://api.imgur.com/3/image/9l0zMZu";
        $postData['method'] = "GET";
        $postData['header'] = array("Authorization: Client-ID XXXXXXXXXXXX");
        $res = Uploaddata::curl_call($postData);

        $this->response($res, REST_Controller::HTTP_OK);
    }

    public static function curl_call($postData) {
        $res = array();
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $postData['url']);

            $method = $postData['method'] ?? "GET";
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if ($method == "POST") {
                if (isset($postData['data']) && !empty($postData['data'])) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData['data']);
                }
            }

            if (isset($postData['header']) && !empty($postData['header'])) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $postData['header']);
            }

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_ENCODING, "");
            curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);

            if ($err) {
                $res['message'] = "cURL Error #:" . $err;
                $res['success'] = false;
                if ($response['status']) {
                    $res['status'] = $response['status'];
                }
                return $res;
            } else {
                return $response;
            }
        } catch(Exception $e) {
            $res['message'] = 'Message: ' .$e->getMessage();
            $res['success'] = false;
            if ($response['status']) {
                $res['status'] = $response['status'];
            }
            return $res;
        }
    }

    public function index_post() {
        $input = $this->input->post();

        $postData = array();
        $postData['url'] = "https://api.imgur.com/3/image";
        $postData['method'] = "POST";
        $postData['header'] = array("Authorization: Client-ID {$this->input->post('ClientID')}");
        $postData['data'] = array('image' => $this->input->post("image"), 'type' => $this->input->post("type"), 'name' => $this->input->post('name'));

        $res = Uploaddata::curl_call($postData);

        $res = json_decode($res, true);
        $imagePath = "";
        
        if (is_array($res) && $res['success'] == true && isset($res['data']['link']) && !empty($res['data']['link'])) {
            $imagePath = $res['data']['link'];
        }

        $this->response(["imagePath" => $imagePath], REST_Controller::HTTP_OK);
    } 
}

?>