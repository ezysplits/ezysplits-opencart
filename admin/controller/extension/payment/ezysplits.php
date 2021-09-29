<?php

class ControllerExtensionPaymentEzysplits extends Controller
{
    private $error = array();

    public function index()
    {
        $this->language->load('extension/payment/ezysplits');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('payment_ezysplits', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['heading_title']      = $this->language->get('heading_title');

        $data['text_edit']          = $this->language->get('text_edit');
        $data['text_enabled']       = $this->language->get('text_enabled');
        $data['text_disabled']      = $this->language->get('text_disabled');
        $data['text_yes']           = $this->language->get('text_yes');
        $data['text_no']            = $this->language->get('text_no');

        $data['entry_merchant_id']  = $this->language->get('entry_merchant_id');
        $data['entry_secret_key']   = $this->language->get('entry_secret_key');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_status']       = $this->language->get('entry_status');
        $data['entry_sort_order']   = $this->language->get('entry_sort_order');

        $data['entry_payment_mode'] = $this->language->get('entry_payment_mode');

        $data['text_sandbox']       = $this->language->get('text_sandbox');
        $data['text_live']          = $this->language->get('text_live');

        $data['button_save']        = $this->language->get('button_save');
        $data['button_cancel']      = $this->language->get('button_cancel');

        $data['help_merchant_id']   = $this->language->get('help_merchant_id');
        $data['help_order_status']  = $this->language->get('help_order_status');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['payment_ezysplits_merchant_id'])) {
            $data['error_merchant_id'] = $this->error['payment_ezysplits_merchant_id'];
        } else {
            $data['error_merchant_id'] = '';
        }

        if (isset($this->error['payment_ezysplits_secret_key'])) {
            $data['error_secret_key'] = $this->error['payment_ezysplits_secret_key'];
        } else {
            $data['error_secret_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => false,
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_extension'),
            'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', 'SSL'),
            'separator' => ' :: ',
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/payment/ezysplits', 'user_token=' . $this->session->data['user_token'], 'SSL'),
            'separator' => ' :: ',
        );

        $data['action'] = $this->url->link('extension/payment/ezysplits', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_ezysplits_merchant_id'])) {
            $data['ezysplits_merchant_id'] = $this->request->post['payment_ezysplits_merchant_id'];
        } else {
            $data['ezysplits_merchant_id'] = $this->config->get('payment_ezysplits_merchant_id');
        }

        if (isset($this->request->post['payment_ezysplits_secret_key'])) {
            $data['ezysplits_secret_key'] = $this->request->post['payment_ezysplits_secret_key'];
        } else {
            $data['ezysplits_secret_key'] = $this->config->get('payment_ezysplits_secret_key');
        }

        if (isset($this->request->post['payment_ezysplits_order_status_id'])) {
            $data['ezysplits_order_status_id'] = $this->request->post['payment_ezysplits_order_status_id'];
        } else {
            $data['ezysplits_order_status_id'] = ($this->config->get('payment_ezysplits_order_status_id')) ? $this->config->get('payment_ezysplits_order_status_id') : 2;
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_ezysplits_status'])) {
            $data['ezysplits_status'] = $this->request->post['payment_ezysplits_status'];
        } else {
            $data['ezysplits_status'] = $this->config->get('payment_ezysplits_status');
        }

        if (isset($this->request->post['payment_ezysplits_sort_order'])) {
            $data['ezysplits_sort_order'] = $this->request->post['payment_ezysplits_sort_order'];
        } else {
            $data['ezysplits_sort_order'] = $this->config->get('payment_ezysplits_sort_order');
        }

        if (isset($this->request->post['payment_ezysplits_payment_mode'])) {
            $data['ezysplits_payment_mode'] = $this->request->post['payment_ezysplits_payment_mode'];
        } else {
            $data['ezysplits_payment_mode'] = $this->config->get('payment_ezysplits_payment_mode');
        }

        $this->template = 'extension/payment/ezysplits';
        $this->children = array(
            'common/header',
            'common/footer',
        );
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/ezysplits', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/ezysplits')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_ezysplits_merchant_id']) {
            $this->error['payment_ezysplits_merchant_id'] = $this->language->get('error_merchant_id');
        }

        if (!$this->request->post['payment_ezysplits_secret_key']) {
            $this->error['payment_ezysplits_secret_key'] = $this->language->get('error_secret_key');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
