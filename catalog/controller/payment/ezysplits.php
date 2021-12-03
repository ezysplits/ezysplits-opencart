<?php

class ControllerPaymentEzysplits extends Controller
{

    /**
     * @return mixed
     */
    public function index()
    {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->language->load('payment/ezysplits');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['text_redirect'] = $this->language->get('text_redirect');
        $data['installment_three'] = number_format((float)$order_info['total'] / 3, 2, '.', '');
        $data['installment_four'] = number_format((float)$order_info['total'] / 4, 2, '.', '');
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ezysplits.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/ezysplits.tpl', $data);
        } else {
            return $this->load->view('payment/ezysplits.tpl', $data);
        }
    }

    /**
     * Redirect to payment page for executing checkout page
     *
     * @return void
     */
    public function confirm()
    {
        if ($this->session->data['payment_method']['code'] == 'ezysplits') {
            $this->language->load('payment/ezysplits');

            $appId = trim($this->config->get('ezysplits_merchant_app_id'));
            $secretKey = trim($this->config->get('ezysplits_merchant_secret_key'));

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $getRequestData = $this->createCheckoutRequest($order_info);

            $jsonResponse = $this->createOrder($appId, $secretKey, $getRequestData);

            if (isset($jsonResponse->checkout_id)) {
                $response["status"] = 1;
                $response["redirect"] = $jsonResponse->checkout_link;
            } else {
                $response["status"] = 0;
                $response["message"] = $jsonResponse->message;
            }

            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($response));
        }
    }

    /**
     * Create order using ezysplits order api's
     *
     * @param $appId
     * @param $secretKey
     * @param $getRequestData
     * @return void
     */
    public function createOrder($appId, $secretKey, $getRequestData)
    {
        $payment_mode = trim($this->config->get('ezysplits_payment_mode'));
        if ($payment_mode == "sandbox") {
            $endpoint = "https://test-api.ezysplits.com/api/v1/checkouts";
        } else {
            $endpoint = "https://api.ezysplits.com/api/v1/checkouts";
        }
        $curlPostfield = json_encode($getRequestData);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $curlPostfield,
            CURLOPT_HTTPHEADER => array(
                'x-client-id: ' . $appId,
                'x-client-secret: ' . $secretKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $result = json_decode($response);

        return $result;
    }

    public function createCheckoutRequest($order)
    {
        $data = array(
            'order_id' => $this->session->data['order_id'] . '-' . uniqid(),
            'order_amount' => number_format((float)$order['total'], 2, '.', ''),
            'order_currency' => $order['currency_code'],
            'order_note' => "Opencart",
            'order_source' => "Opencart",
            'customer_details' => $this->getCustomerDetail($order),
            'billing_address' => $this->getRequestAddress($order),
            'shippingAddress' => $this->getRequestAddress($order),
            'checkout_items' => $this->getCheckoutItem($order),
            'order_meta' => array(
                'redirect_url' => $this->url->link('payment/ezysplits/thankyou', '', 'SSL'),
                'notify_url' => $this->url->link('payment/ezysplits/callback', '', 'SSL'),
                'cancel_url' => $this->url->link('payment/ezysplits/cancelled', '', 'SSL'),
            ),
        );

        return $data;
    }

    private function getCustomerDetail($order)
    {
        if ($order['customer_id'] == 0) {
            return array(
                'customer_id' => "opencart_guest",
                'customer_name' => $order['firstname'] . " " . $order['lastname'],
                'email' => $order['email'],
                'country_code' => $order['payment_iso_code_2'],
                'dob' => "",
                'address1' => $order['payment_iso_code_2'],
                'address2' => $order['payment_iso_code_2'],
                'postal_code' => $order['payment_iso_code_2'],
            );
        } else {
            return array(
                'customer_id' => "opencart_guest",
                'customer_name' => $order['firstname'] . " " . $order['lastname'],
                'email' => $order['email'],
                'country_code' => $order['payment_iso_code_2'],
                'dob' => "",
                'address1' => $order['payment_iso_code_2'],
                'address2' => $order['payment_iso_code_2'],
                'postal_code' => $order['payment_iso_code_2'],
            );
        }
    }

    private function getRequestAddress($order)
    {
        return array(
            'name' => $order['firstname'] . " " . $order['lastname'],
            'street1' => $order['payment_address_1'],
            'street2' => $order['payment_address_2'],
            'city' => $order['payment_city'],
            'state' => $order['payment_zone'],
            'country_code' => "+91",
            'phone' => $order['telephone'],
            'postal_code' => $order['payment_postcode'],
        );
    }

    private function getCheckoutItem($order)
    {
        $this->load->model('account/order');
        $products = $this->model_account_order->getOrderProducts($order['order_id']);
        $items = array_map(
            function ($item) use ($order) {
                return array(
                    'item_id' => $item['product_id'],
                    'name' => $item['name'],
                    'desc' => "",
                    'image_url' => "",
                    'unit_price' => $item['price'],
                    'qty' => $item['quantity'],
                );
            },
            array_values($products)
        );

        return $items;
    }

    /**
     * Process payment on client after ezysplits response
     *
     * @param mixed $postData
     * @return void
     */
    private function processResponse($postData)
    {
        $this->load->model('checkout/order');
        $this->language->load('payment/ezysplits');
        $order_id = explode('-', $postData['order_id'])[0];
        $order_info = $this->model_checkout_order->getOrder($order_id);

        $secretKey = trim($this->config->get('ezysplits_merchant_secret_key'));
        if ($order_info) {
            $ezysplits_response = array();
            $ezysplits_response["order_id"] = $postData['order_id'];
            $ezysplits_response["reference_id"] = $postData['reference_id'];
            $ezysplits_response["checkout_amount"] = $postData["checkout_amount"];
            $ezysplits_response["transaction_status"] = $postData["transaction_status"];
            $ezysplits_response["transaction_time"] = $postData["transaction_time"];

            ksort($ezysplits_response);

            $signatureData = "";
            foreach ($ezysplits_response as $key => $value) {
                $signatureData .= $key . $value;
            }

            $hash_hmac = hash_hmac('sha256', $signatureData, $secretKey, true);
            $computedSignature = base64_encode($hash_hmac);
            if ($postData["signature"] != $computedSignature) {
                $this->model_checkout_order->addOrderHistory($ezysplits_response['order_id'], 10, 'Signature missmatch! Check Ezysplits dashboard for details of Reference Id:' . $postData['reference_id']);
                $redirectUrl = $this->url->link('checkout/failure', '', true);
                return array("status" => 0, "message" => $this->language->get('error_signature_mismatch'), "redirectUrl" => $redirectUrl);
            }

            if ($ezysplits_response["transaction_status"] == 'SUCCESS') {
                if ($order_info["order_status_id"] != $this->config->get('ezysplits_order_status_id')) {
                    // only updated if it has been updated it
                    $this->model_checkout_order->addOrderHistory($ezysplits_response['order_id'], $this->config->get('ezysplits_order_status_id'), "Payment Received");
                }
                return array("status" => 1);
            } else if ($ezysplits_response["transaction_status"] == "CANCELLED") {
                $this->model_checkout_order->addOrderHistory($ezysplits_response['order_id'], 7, 'Payment Cancelled! Check Ezysplits dashboard for details of Reference Id:' . $ezysplits_response['reference_id']);
                $redirectUrl = $this->url->link('checkout/checkout', '', true);
                return array("status" => 0, "message" => $this->language->get('ezysplits_payment_cancelled'), "redirectUrl" => $redirectUrl);
            } else {
                $this->model_checkout_order->addOrderHistory($ezysplits_response['order_id'], 10, 'Payment Failed! Check Ezysplits dashboard for details of Reference Id:' . $ezysplits_response['reference_id']);
                $redirectUrl = $this->url->link('checkout/failure', '', true);
                return array("status" => 0, "message" => $this->language->get('ezysplits_payment_failed'), "redirectUrl" => $redirectUrl);
            }
        }
        return array("status" => 0, "message" => "");
    }

    /**
     * Redirect to thank you page after process payment on client
     *
     * @return void
     */
    public function thankyou()
    {
        if (!isset($_POST["reference_id"])) {
            $this->response->redirect($this->url->link('checkout/failure'));
        }

        $response = $this->processResponse($_POST);
        if ($response["status"] == 1) {
            $this->response->redirect($this->url->link('checkout/success', '', true));
        } else {
            $this->session->data['error_warning'] = $response["message"];
            $this->response->redirect($response['redirectUrl']);
        }
    }

    /**
     * Checking for notify url
     *
     * @return void
     */
    public function callback()
    {
        if (!isset($_POST["reference_id"])) {
            die();
        }
        sleep(20);
        $response = $this->processResponse($_POST);
        die();
        //do nothing
    }

    /**
     *Cancelled logic should be written here
     */
    public function cancelled()
    {
        //Cancelled Logic
    }

}