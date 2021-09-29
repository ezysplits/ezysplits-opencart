<?php

class ModelExtensionPaymentEzysplits extends Model
{
    public function getMethod($address, $total)
    {
        $this->language->load('extension/payment/ezysplits');

        $method_data = array();

        if ($this->session->data['currency'] == 'INR' || $this->session->data['payment_address']['country'] != 'India') {
            $method_data = array(
                'code'      => 'ezysplits',
                'title'     => $this->language->get('text_title'),
                'terms'     => '',
                'sort_order'=> $this->config->get('payment_ezysplits_sort_order'),
            );
        }

        return $method_data;
    }
}
