<?php

namespace GreaterMedia\MyEmma\Webhooks;

class MessageClick extends MessageOpen {

	function get_event_name() {
		return 'message_click';
	}

	function get_clicked_link() {
		return strval( $this->params['data']['link_id'] );
	}

	function get_clicked_link_meta() {
		$link_id      = $this->get_clicked_link();
		$mailing_meta = $this->get_mailing_meta( null ); // KLUDGE
		$links        = $mailing_meta['links'];

		foreach ( $links as $link ) {
			if ( strval( $link['link_id'] ) === $link_id ) {
				return $link;
			}
		}

		return null;
	}

	function get_action_to_save( $type, $group_id, $subject ) {
		$action = parent::get_action_to_save( $type, $group_id, $subject );
		$link   = $this->get_clicked_link_meta();

		if ( ! is_null( $link ) ) {
			$action['actionData'][] = array(
				'name' => 'linkID', 'value' => $link['link_id'],
			);

			$action['actionData'][] = array(
				'name' => 'linkName', 'value' => $link['link_name'],
			);

			$action['actionData'][] = array(
				'name' => 'linkTarget', 'value' => $link['link_target'],
			);
		}

		return $action;
	}

}
