<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 03/05/2018
 * Time: 16:59
 */

namespace App\Rules;


use App\Helpers\SparkyValidator;
use Illuminate\Contracts\Validation\Rule;

class SendingDomain implements Rule
{

	/**
	 * Determine if the validation rule passes
	 * @param string $attribute
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function passes( $attribute, $value )
	{
		if ( filter_var($value, FILTER_VALIDATE_EMAIL) !== false )
			return SparkyValidator::validateSendingDomain(substr($value, strrpos($value, '@') + 1));

		return false;
	}

	/**
	 * Get the validation error message
	 * @return string
	 */
	public function message()
	{
		return "The sender email must be of a domain registered and verified in Sparkpost";
	}


}