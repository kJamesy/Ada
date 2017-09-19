<?php

namespace App\Exporters;

use Maatwebsite\Excel\Facades\Excel;

class ResourceExporter
{

    public $resources;
    public $exportFileName;

    /**
     * ResourceExporter constructor.
     * @param $resources
     * @param $fileName
     */
    public function __construct($resources, $fileName)
    {
        $this->resources = $resources;
        $this->exportFileName = $fileName;
    }

	/**
	 * Generate excel export
	 * @param $type
	 *
	 * @return mixed|null
	 */
    public function generateExcelExport($type)
    {
        switch ($type) {
            case 'users':
                return static::generateUsersExport();
                break;
            case 'mailing_lists':
                return static::generateMailingListsExport();
                break;
	        case 'subscribers':
		        return static::generateSubscribersExport();
		        break;
	        case 'campaigns':
		        return static::generateCampaignsExport();
		        break;
	        case 'templates':
		        return static::generateTemplatesExport();
		        break;
	        case 'email_settings':
		        return static::generateEmailSettingsExport();
		        break;
	        case 'emails':
		        return static::generateEmailsExport();
		        break;
        }

        return null;
    }

    /**
     * Generate users export
     * @return mixed
     */
    public function generateUsersExport()
    {
        return Excel::create($this->exportFileName, function($excel) {
            $resources = $this->resources;
            $exportArr = [];

            if ( count($resources) ) {
                foreach ($resources as $resource) {
                    $exportArr[] = [
                        'First Name' => $resource->first_name,
                        'Last Name' => $resource->last_name,
                        'Email' => $resource->email,
                        'Username' => $resource->username,
                        'Active' => $resource->active ? '✔' : '✗',
                        'Role' => $resource->is_super_admin ? 'Super Admin' : 'User',
                        'User Since' => $resource->created_at->toDateTimeString(),
                    ];

                }
            }

            $excel->sheet('Users', function($sheet) use ($exportArr) {
                $sheet->fromArray($exportArr);
            });

        })->download('xls');
    }

    /**
     * Generate mailing lists export
     * @return mixed
     */
    public function generateMailingListsExport()
    {
        return Excel::create($this->exportFileName, function($excel) {
            $resources = $this->resources;
            $exportArr = [];

            if ( count($resources) ) {
                foreach ($resources as $resource) {
                    $exportArr[] = [
                        'Name' => $resource->name,
                        'Description' => $resource->description,
                        'Created' => $resource->created_at->toDateTimeString(),
                        'Last Updated' => $resource->updated_at->toDateTimeString(),
                    ];

                }
            }

            $excel->sheet('Mailing Lists', function($sheet) use ($exportArr) {
                $sheet->fromArray($exportArr);
            });

        })->download('xls');
    }

	/**
	 * Generate subscribers export
	 * @return mixed
	 */
	public function generateSubscribersExport()
	{
		return Excel::create($this->exportFileName, function($excel) {
			$resources = $this->resources;
			$exportArr = [];

			if ( count($resources) ) {
				foreach ($resources as $resource) {
					$exportArr[] = [
						'First Name' => $resource->first_name,
						'Last Name' => $resource->last_name,
						'Email' => $resource->email,
						'Active' => $resource->active ? '✔' : '✗',
						'Subscribed' => $resource->created_at->toDateTimeString(),
						'Last Updated' => $resource->updated_at->toDateTimeString(),
					];

				}
			}

			$excel->sheet('Subscribers', function($sheet) use ($exportArr) {
				$sheet->fromArray($exportArr);
			});

		})->download('xls');
	}

	/**
	 * Generate campaigns export
	 * @return mixed
	 */
	public function generateCampaignsExport()
	{
		return Excel::create($this->exportFileName, function($excel) {
			$resources = $this->resources;
			$exportArr = [];

			if ( count($resources) ) {
				foreach ($resources as $resource) {
					$exportArr[] = [
						'Name' => $resource->name,
						'Description' => $resource->description,
						'Created' => $resource->created_at->toDateTimeString(),
						'Last Updated' => $resource->updated_at->toDateTimeString(),
					];

				}
			}

			$excel->sheet('Campaigns', function($sheet) use ($exportArr) {
				$sheet->fromArray($exportArr);
			});

		})->download('xls');
	}

	/**
	 * Generate templates export
	 * @return mixed
	 */
	public function generateTemplatesExport()
	{
		return Excel::create($this->exportFileName, function($excel) {
			$resources = $this->resources;
			$exportArr = [];

			if ( count($resources) ) {
				foreach ($resources as $resource) {
					$exportArr[] = [
						'Name' => $resource->name,
						'Description' => $resource->description,
						'Last Edited by' => $resource->last_editor,
						'Created' => $resource->created_at->toDateTimeString(),
						'Last Updated' => $resource->updated_at->toDateTimeString(),
					];

				}
			}

			$excel->sheet('Templates', function($sheet) use ($exportArr) {
				$sheet->fromArray($exportArr);
			});

		})->download('xls');
	}

	/**
	 * Generate email settings export
	 * @return mixed
	 */
	public function generateEmailSettingsExport()
	{
		return Excel::create($this->exportFileName, function($excel) {
			$resources = $this->resources;
			$exportArr = [];

			if ( count($resources) ) {
				foreach ($resources as $resource) {
					$exportArr[] = [
						'Name' => $resource->name,
						'Description' => $resource->description,
						'Setting Value' => $resource->setting_value,
						'Created' => $resource->created_at->toDateTimeString(),
						'Last Updated' => $resource->updated_at->toDateTimeString(),
					];

				}
			}

			$excel->sheet('Email Settings', function($sheet) use ($exportArr) {
				$sheet->fromArray($exportArr);
			});

		})->download('xls');
	}

	/**
	 * Generate emails export
	 * @return mixed
	 */
	public function generateEmailsExport()
	{
		return Excel::create($this->exportFileName, function($excel) {
			$resources = $this->resources;
			$exportArr = [];

			if ( count($resources) ) {
				foreach ($resources as $resource) {
					$exportArr[] = [
						'Subject' => $resource->subject,
						'Sender' => $resource->sender ?: '—',
						'Reply-To' => $resource->reply_to_email ?: '—',
						'User' => $resource->user->name,
						'Campaign' => $resource->campaign->name,
						'Recipients' => $resource->recipients_num ?: '—',
						'Status' => $resource->friendly_status,
						'Created' => $resource->created_at->toDateTimeString(),
						'Sent' => $resource->sent_at ? $resource->sent_at->toDateTimeString() : '—',
						'Last Updated' => $resource->updated_at->toDateTimeString(),
					];

				}
			}

			$excel->sheet('Emails', function($sheet) use ($exportArr) {
				$sheet->fromArray($exportArr);
			});

		})->download('xls');
	}
}