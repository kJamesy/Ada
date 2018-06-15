<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 14/06/2018
 * Time: 13:14
 */

namespace App\Helpers;


class Slug
{

	/**
	 * Generate a unique slug
	 * @param $existingSlugs
	 * @param $proposedSlug
	 *
	 * @return mixed
	 */
	public static function generateUniqueSlug($existingSlugs, $proposedSlug)
	{
		$exists = false;

		if ( $existingSlugs ) {
			foreach ( $existingSlugs as $existingSlug ) {
				if ( strtolower( $proposedSlug ) === strtolower( $existingSlug ) ) {
					$exists = strtolower( $existingSlug );
					break;
				}
			}
		}

		if ( $exists ) {
			$parts = explode("-", $exists);
			$length = count($parts);
			$lastPart = $parts[$length-1];
			$newSlug = '';

			if( (int) $lastPart > 0 ) {
				$allOtherParts = $parts;
				unset($allOtherParts[$length-1]);
				$allOtherParts[] = (int) $lastPart + 1;

				foreach ($allOtherParts as $key=>$part) {
					if( $key !== $length )
						$newSlug .= $part . "-";
					else
						$newSlug .= $part;
				}
			}
			else
				$newSlug = $exists . '-1';

			return static::generateUniqueSlug($existingSlugs, $newSlug);
		}

		return $proposedSlug;
	}

}