<?php

namespace Modules\Experiments\Actions;

use API;
use CControllerMenuPopup;
use CControllerResponseData;


class MenuPopupExtendedAction extends CControllerMenuPopup {

	public function getResponse() {
		/** @var CControllerResponseData $response */
		$response = parent::getResponse();
		$input = $this->getInput('data', []);
		$output = $response->getData();
		$main_block = json_decode($output['main_block'] ?? '[]', true);

		if (!isset($input['hostid']) || !isset($main_block['data'])) {
			return $response;
		}

		$field_label = [
			'url_a' => 'URL A',
			'url_b' => 'URL B',
			'url_c' => 'URL C'
		];
		$host = API::Host()->get([
			'output' => null,
			'hostids' => [$input['hostid']],
			'selectInventory' => array_keys($field_label)
		]);
		$host = reset($host);
		$field_label = array_intersect_key($field_label, array_filter($host['inventory'], 'strlen'));

		foreach ($field_label as $field => $label) {
			$main_block['data']['urls'][] = [
				'label' => $label,
				'url' => $host['inventory'][$field]
			];
		}

		return new CControllerResponseData(['main_block' => json_encode($main_block)]);
	}
}