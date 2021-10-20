<?php

class ControllerPaymentEzysplits extends Controller
{
    private $error = array();

    /**
     *Load method for displaying admin dashboard
     */
    public function index()
    {
        $this->language->load('payment/ezysplits');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('ezysplits', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_merchant_app_id'] = $this->language->get('entry_merchant_app_id');
        $data['entry_merchant_secret_key'] = $this->language->get('entry_merchant_secret_key');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['entry_payment_mode'] = $this->language->get('entry_payment_mode');

        $data['text_sandbox'] = $this->language->get('text_sandbox');
        $data['text_live'] = $this->language->get('text_live');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['help_merchant_id'] = $this->language->get('help_merchant_id');
        $data['help_order_status'] = $this->language->get('help_order_status');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['ezysplits_merchant_app_id'])) {
            $data['error_merchant_id'] = $this->error['ezysplits_merchant_app_id'];
        } else {
            $data['error_merchant_id'] = '';
        }

        if (isset($this->error['ezysplits_merchant_secret_key'])) {
            $data['error_secret_key'] = $this->error['ezysplits_merchant_secret_key'];
        } else {
            $data['error_secret_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/ezysplits', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('payment/ezysplits', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true);

        if (isset($this->request->post['ezysplits_merchant_app_id'])) {
            $data['ezysplits_merchant_app_id'] = $this->request->post['ezysplits_merchant_app_id'];
        } else {
            $data['ezysplits_merchant_app_id'] = $this->config->get('ezysplits_merchant_app_id');
        }

        if (isset($this->request->post['ezysplits_merchant_secret_key'])) {
            $data['ezysplits_merchant_secret_key'] = $this->request->post['ezysplits_merchant_secret_key'];
        } else {
            $data['ezysplits_merchant_secret_key'] = $this->config->get('ezysplits_merchant_secret_key');
        }

        if (isset($this->request->post['ezysplits_order_status_id'])) {
            $data['ezysplits_order_status_id'] = $this->request->post['ezysplits_order_status_id'];
        } else {
            $data['ezysplits_order_status_id'] = ($this->config->get('ezysplits_order_status_id')) ? $this->config->get('ezysplits_order_status_id') : 2;
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['ezysplits_status'])) {
            $data['ezysplits_status'] = $this->request->post['ezysplits_status'];
        } else {
            $data['ezysplits_status'] = $this->config->get('ezysplits_status');
        }

        if (isset($this->request->post['ezysplits_sort_order'])) {
            $data['ezysplits_sort_order'] = $this->request->post['ezysplits_sort_order'];
        } else {
            $data['ezysplits_sort_order'] = $this->config->get('ezysplits_sort_order');
        }

        if (isset($this->request->post['ezysplits_payment_mode'])) {
            $data['ezysplits_payment_mode'] = $this->request->post['ezysplits_payment_mode'];
        } else {
            $data['ezysplits_payment_mode'] = $this->config->get('ezysplits_payment_mode');
        }

        $this->template = 'payment/ezysplits';
        $this->children = array(
            'common/header',
            'common/footer',
        );
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/ezysplits', $data));
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/ezysplits')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['ezysplits_merchant_app_id']) {
            $this->error['ezysplits_merchant_app_id'] = $this->language->get('error_merchant_id');
        }

        if (!$this->request->post['ezysplits_merchant_secret_key']) {
            $this->error['ezysplits_merchant_secret_key'] = $this->language->get('error_secret_key');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
