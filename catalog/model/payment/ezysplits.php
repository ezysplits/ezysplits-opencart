<?php

class ModelPaymentEzysplits extends Model
{
    public function getMethod($address, $total)
    {
        $this->language->load('payment/ezysplits');
        
		$method_data = array();
        if ( $this->session->data['currency'] == 'INR' || $this->session->data['payment_address']['country'] != 'India') {
            $method_data = array(
                'code' => 'ezysplits',
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('ezysplits_sort_order'),
            );
        }

        return $method_data;
    }
}