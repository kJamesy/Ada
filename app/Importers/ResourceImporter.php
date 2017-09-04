<?php

namespace App\Importers;

use App\Subscriber;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ResourceImporter
{

	public $fileName;
	public $safeFileName;
	public $filePath;
	public $excelColumns;
	public $validationRules;

	/**
	 * ResourceImporter constructor.
	 *
	 * @param $fileName
	 * @param $safeFileName
	 * @param $filePath
	 * @param $excelColumns
	 * @param $rules
	 */
	public function __construct($fileName, $safeFileName, $filePath, $excelColumns, $rules)
	{
		$this->fileName = $fileName;
		$this->safeFileName = $fileName;
		$this->filePath = $filePath;
		$this->excelColumns = (array) $excelColumns;
		$this->validationRules = $rules;
	}

	/**
	 * @param $type
	 *
	 * @return mixed
	 */
	public function getResults($type)
	{
		switch ($type) {
			case 'subscribers':
				return static::handleSubscribersImport();
				break;
		}

		return null;
	}

	/**
	 * Compute good records from the file import and return a set of good/bad results or null if file doesn't exist
	 * @return mixed
	 */
	public function handleSubscribersImport()
	{
		$safeFileName = $this->safeFileName;

		$results = cache()->remember("resultsOfImport$safeFileName", 15, function() {
			$file = $this->filePath;

			if ( file_exists($file) ) {
				$excel = Excel::selectSheetsByIndex(0)->load($file, function ($reader) {
					$excelColumns = $this->excelColumns;
					$rules = $this->validationRules;
					$sheet = $reader->get($excelColumns);
					$existingRows = [];
					$goodOnes = [];
					$badRows = [];
					$rowsNum = 0;

					if ($sheet) {
						foreach ($sheet as $key => $row) {
							$subscriber = [
								'first_name' => ucfirst(strtolower( Str::ascii($row->{$excelColumns['first_name']}) )),
								'last_name' => ucfirst(strtolower( Str::ascii($row->{$excelColumns['last_name']}) )),
								'email' => strtolower($row->{$excelColumns['email']})
							];

							$validator = validator()->make($subscriber, $rules);

							if ( $validator->fails() )
								$badRows[] = $key + 2;
							else {
								$goodOnes[] = (object) $subscriber;
								$existingSub = Subscriber::findResourceByEmail($subscriber['email']);

								if ( $existingSub )
									$existingRows[] = $key + 2;
							}

							$rowsNum++;
						}
					}

					$reader->goodOnes = $goodOnes;
					$reader->existingRows = $existingRows;
					$reader->badRows = $badRows;
					$reader->rowsNum = $rowsNum;
				}, 'UTF-8');

				return (object)['goodOnes' => $excel->goodOnes, 'existingRows' => $excel->existingRows, 'badRows' => $excel->badRows, 'rowsNum' => $excel->rowsNum];
			}
			else
				return null;
		});

		if ( ! $results )
			cache()->forget("resultsOfImport$safeFileName");

		return $results;
	}

}