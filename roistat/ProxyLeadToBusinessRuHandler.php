<?php

require_once __DIR__ . '/BusinessRuApiLib.php';

class ProxyLeadToBusinessRuHandler {

	private $api       = null;
	private $request   = null;
	private $contactId = null;

	public function __construct($authData, $request) {

		$this->request = $request;
		$this->api     = new BusinessRuApiLib($authData['appId'], '', $authData['apiPath']);

		$this->api->repair();
	}

	public function setOrder() {

		$this->setContact();

		if (empty($this->contactId)) {
			return null;
		}

		return $this->_setOrder();
	}

	private function _setOrder() {

		$fields = json_decode($this->request['data'], 1);
		$roistat = '';

	    foreach ($fields as $key => $value) {
	        if ($key != 'roistat') {
	        	continue;
	        }

	        $roistat = $value;
	    }

		$order = [
			'status_id' => '106',
			'author_employee_id' => '8833709',
			'responsible_employee_id' => '8833709',
			'organization_id' => '8738191',
			'partner_id' => $this->contactId,
			'comment' => $this->request['text'],
			'398944' => $roistat,
		];
		$response = $this->api->request('post','customerorders', $order);

		return $response['result']['id'];
	}

	private function _setContact() {

		$contact = [
			'name' => !empty($this->request['name']) ? $this->request['name'] : 'Без имени',
			'customer' => '1',
		];
		$response = $this->api->request('post','partners', $contact);
		$this->contactId = !empty($response['result']['id']) ? $response['result']['id'] : null;
		
		if (!empty($this->request['phone'])) {
			$contactInfo = [
				'partner_id' => $this->contactId,
				'contact_info_type_id' => '1',
				'contact_info' => $this->request['phone'],
			];
			
			$this->api->request('post','partnercontactinfo', $contactInfo);
		}
		if (!empty($this->request['email'])) {
			$contactInfo = [
				'partner_id' => $this->contactId,
				'contact_info_type_id' => '4',
				'contact_info' => $this->request['email'],
			];
			
			$this->api->request('post','partnercontactinfo', $contactInfo);
		}
	}

	private function setContact() {

		if (!empty($this->request['phone'])) {
			$this->contactId = $this->getContact('phone');

			if (empty($this->contactId) && !empty($this->request['email'])) {
				$this->contactId = $this->getContact('email');
			}
			if (empty($this->contactId)) {
				$this->_setContact();
			}
		} else {
			$this->contactId = $this->getContact('email');

			if (empty($this->contactId)) {
				$this->_setContact();
			}
		}
	}

	private function getContact($type) {

		$response = $this->api->request('get','partners', [ 'extend' => 'partnercontactinfo','with_additional_fields' => 1, $type => $this->request[$type]]);

		if (!empty($response['result'][0]['id'])) {
			return $response['result'][0]['id'];
		}

		return null;
	}

}
