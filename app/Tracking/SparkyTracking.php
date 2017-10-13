<?php

namespace App\Tracking;

use App\Tracking\Traits\ClickTracking;
use App\Tracking\Traits\DeliveryTracking;
use App\Tracking\Traits\FailureTracking;
use App\Tracking\Traits\InjectionTracking;
use App\Tracking\Traits\OpenTracking;
use Carbon\Carbon;

class SparkyTracking
{
	use InjectionTracking;
	use DeliveryTracking;
	use OpenTracking;
	use ClickTracking;
	use FailureTracking;

	protected $event_body;

	/**
	 * SparkyTracking constructor.
	 *
	 * @param $event_body
	 */
	public function __construct($event_body)
	{
		$this->event_body = (array) $event_body;
	}

	/**
	 * Handle sparky events
	 * @return \App\Click|\App\Delivery|\App\Open|array|\Illuminate\Database\Eloquent\Model|null|string|static
	 */
	public function handleEvents()
	{
		$feedback = '';

		foreach ( $this->event_body as $an_event_body ) {
			$event = $this->getTheEvent($an_event_body);

			if ( $event ) {
				$event_type = $this->getEventType($event);
				$email_id = $this->getEmailId($event);
				$subscriber_id = $this->getSubscriberId($event);
				$event_time = $this->getEventTime($event);

				if ( $event_type && $email_id && $subscriber_id && $event_time ) {
					switch ( $event_type ) {
						case 'injection':
							$feedback = self::recordInjection($email_id, $subscriber_id, $event_time);
							break;
						case 'delivery' :
							$feedback = self::recordDelivery($email_id, $subscriber_id, $event_time);
							break;
						case 'open':
							$ip_address = $this->getEventIpAddress($event);
							$country_code = $this->getEventCountryCode($event);
							$user_agent = $this->getEventUserAgent($event);

							if ( $ip_address && $country_code && $user_agent )
								$feedback = self::recordOpen($email_id, $subscriber_id, $user_agent, $ip_address, $country_code, $event_time);
							break;
						case 'click':
							$link = $this->getEventTargetLink($event);
							$ip_address = $this->getEventIpAddress($event);
							$country_code = $this->getEventCountryCode($event);
							$user_agent = $this->getEventUserAgent($event);

							if ( $link && $ip_address && $country_code && $user_agent )
								$feedback = self::recordClick($email_id, $subscriber_id, $user_agent, $ip_address, $country_code, $link, $event_time);
							break;
						case 'bounce':
						case 'spam_complaint':
						case 'out_of_band':
						case 'policy_rejection':
						case 'delay':
						case 'generation_failure':
						case 'generation_rejection':
						case 'list_unsubscribe':
						case 'link_unsubscribe':
							$failure_type = ucfirst(str_replace('_', ' ', $event_type));
							$reason = $this->getEventRawReason($event, $event_type);
							$feedback = self::recordFailure($email_id, $subscriber_id, $failure_type, $reason, $event_time);
							break;
					}
				}

			}

		}

		return $feedback;
	}

	/**
	 * Get reason for specified event type (failures)
	 * @param $event
	 * @param $event_type
	 *
	 * @return string|null
	 */
	public function getEventRawReason($event, $event_type)
	{
		$reason = null;

		switch ($event_type) {
			case 'bounce':
			case 'out_of_band':
			case 'policy_rejection':
			case 'delay':
			case 'generation_failure':
			case 'generation_rejection':
				$reason = $this->getObjPropValue($event, 'raw_reason');
				break;
		}

		return $reason;
	}

	/**
	 * Get the event target link url (for clicks)
	 * @param $event
	 *
	 * @return null
	 */
	public function getEventTargetLink($event)
	{
		return $this->getObjPropValue($event, 'target_link_url');
	}

	/**
	 * Get the event country iso3166 alpha2 code
	 * @param $event
	 *
	 * @return null
	 */
	protected function getEventCountryCode($event)
	{
		$geo_ip = $this->getObjPropValue($event, 'geo_ip');
		return $geo_ip ? $this->getObjPropValue($geo_ip, 'country') : null;
	}

	/**
	 * Get the event IP address
	 * @param $event
	 *
	 * @return string
	 */
	protected function getEventIpAddress($event)
	{
		return $this->getObjPropValue($event, 'ip_address');
	}

	/**
	 * Get the event User agent
	 * @param $event
	 *
	 * @return null
	 */
	protected function getEventUserAgent($event)
	{
		return $this->getObjPropValue($event, 'user_agent');
	}

	/**
	 * Get the event time
	 * @param $event
	 *
	 * @return null|Carbon
	 */
	protected function getEventTime($event)
	{
		$timestamp = $this->getObjPropValue($event, 'timestamp');
		return $timestamp ? Carbon::createFromTimestampUTC($timestamp) : null;
	}

	/**
	 * Get the event type
	 * @param $event
	 *
	 * @return string
	 */
	protected function getEventType($event)
	{
		return strtolower($this->getObjPropValue($event, 'type'));
	}

	/**
	 * Get the email_id
	 * @param $event
	 *
	 * @return null
	 */
	protected function getEmailId($event)
	{
		$meta = $this->getObjPropValue($event, 'rcpt_meta');
		return $meta ? $this->getObjPropValue($meta, 'email_id') : null;
	}

	/**
	 * Get the subscriber_id
	 * @param $event
	 *
	 * @return null
	 */
	protected function getSubscriberId($event)
	{
		$meta = $this->getObjPropValue($event, 'rcpt_meta');
		return $meta ? $this->getObjPropValue($meta, 'subscriber_id') : null;
	}

	/**
	 * Get the track/message/gen/unsubscribe event
	 * @param $event_body
	 *
	 * @return null
	 */
	protected function getTheEvent($event_body)
	{
		$event_body = $this->getObjPropValue($event_body, 'msys');

		$event = null;

		if ( $event_body ) {
			$event = $this->getObjPropValue( $event_body, 'message_event' );

			if ( ! $event )
				$event = $this->getObjPropValue( $event_body, 'track_event' );
			if ( ! $event )
				$event = $this->getObjPropValue( $event_body, 'gen_event' );
			if ( ! $event )
				$event = $this->getObjPropValue( $event_body, 'unsubscribe_event' );
		}

		return $event;
	}

	/**
	 * Get value of an object's property
	 * @param $obj
	 * @param $prop
	 *
	 * @return null
	 */
	protected function getObjPropValue($obj, $prop)
	{
		return ( is_object($obj) && property_exists( $obj, $prop) ) ? $obj->{$prop} :  null;
	}

}