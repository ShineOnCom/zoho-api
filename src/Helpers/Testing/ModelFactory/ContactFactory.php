<?php

namespace ShineOnCom\Zoho\Helpers\Testing\ModelFactory;


class ContactFactory
{
    /**
     * @param int $quantity
     * @param array $overrides
     * @return array
     */
    public static function create($quantity = 1, $overrides = [])
    {
        return $quantity == 1
            ? static::getSample($overrides)
            : array_fill(0, $quantity, static::getSample($overrides));
    }

    /**
     * Example response
     * GET https://www.zohoapis.com/crm/v2/Contacts/3995039000000237001
     *
     * @param $overrides
     * @return array
     */
    private static function getSample($overrides = [])
    {
        return array_merge([
                'Owner' =>
                    [
                        'name' => 'John | ZOHO Test',
                        'id' => '3995039000000211013',
                    ],
                'Email' => 'jane.smith@example.com',
                '$currency_symbol' => '$',
                'Other_Phone' => NULL,
                'Mailing_State' => NULL,
                'Other_State' => NULL,
                'Other_Country' => NULL,
                'Last_Activity_Time' => '2019-05-15T20:30:58+01:00',
                'Department' => NULL,
                '$process_flow' => false,
                'Assistant' => NULL,
                'Exchange_Rate' => 1,
                'Currency' => 'USD',
                'Mailing_Country' => NULL,
                'id' => '3995039000000237001',
                '$approved' => true,
                'Reporting_To' => NULL,
                '$approval' =>
                    [
                        'delegate' => false,
                        'approve' => false,
                        'reject' => false,
                        'resubmit' => false,
                    ],
                'Other_City' => NULL,
                'Created_Time' => '2019-05-15T13:10:44+01:00',
                '$editable' => true,
                'Home_Phone' => NULL,
                'Created_By' =>
                    [
                        'name' => 'John | ZOHO Test',
                        'id' => '3995039000000211013',
                    ],
                'Secondary_Email' => NULL,
                'Description' => NULL,
                'Vendor_Name' => NULL,
                'Mailing_Zip' => NULL,
                'Twitter' => NULL,
                'Other_Zip' => NULL,
                'Mailing_Street' => NULL,
                'Salutation' => NULL,
                'First_Name' => 'Jane',
                'Full_Name' => 'Jane Smith',
                'Asst_Phone' => NULL,
                'Record_Image' => NULL,
                'Modified_By' =>
                    [
                        'name' => 'John | ZOHO Test',
                        'id' => '3995039000000211013',
                    ],
                'Skype_ID' => NULL,
                'Phone' => NULL,
                'Account_Name' =>
                    [
                        'name' => 'Jane Smith',
                        'id' => '3995039000000235001',
                    ],
                'Email_Opt_Out' => false,
                'Modified_Time' => '2019-05-15T13:10:44+01:00',
                'Date_of_Birth' => NULL,
                'Mailing_City' => NULL,
                'Title' => NULL,
                'Other_Street' => NULL,
                'Mobile' => NULL,
                'Last_Name' => 'Smith',
                'Lead_Source' => NULL,
                'Tag' =>
                    [
                    ],
                'Fax' => NULL,
            ], $overrides);
    }
}