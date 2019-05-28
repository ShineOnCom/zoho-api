<?php

namespace ShineOnCom\Zoho\Models;

/**
 * Class Customer
 *
 * @property int $id
 * @property User $Owner
 * @property string $Email
 * @property string $$currency_symbol
 * @property User $Created_By
 * @property User $Modified_By
 * @property User $Account_Name
 * @property string $First_Name
 * @property string $Last_Name
 * @property string $Full_Name
 * @property string $Currency
 */
class Contact extends AbstractModel
{
    /** @var string $resource_name */
    public static $resource_name = 'contact';

    /** @var string $resource_name_many */
    public static $resource_name_many = 'contacts';

    /** @var array $dates */
    protected $dates = [
        'Last_Activity_Time',
        'Created_Time',
        'Modified_Time',
    ];

    /** @var array $casts */
    protected $casts = [];


    /**
     * Select fields for the API Query Language
     *
     * @var array $fields
     */
    public static $fields = [
        'Owner', 'Email', 'Other_Phone', 'Mailing_State', 'Other_State', 'Other_Country', 'Last_Activity_Time', 'Department', 'Assistant', 'Exchange_Rate', 'Currency', 'Mailing_Country', 'Reporting_To', 'Other_City', 'Created_Time', 'Home_Phone', 'Created_By', 'Secondary_Email', 'Description', 'Vendor_Name', 'Mailing_Zip', 'Twitter', 'Other_Zip', 'Mailing_Street', 'Salutation', 'First_Name', 'Full_Name', 'Asst_Phone', 'Modified_By', 'Skype_ID', 'Phone', 'Account_Name', 'Email_Opt_Out', 'Modified_Time', 'Date_of_Birth', 'Mailing_City', 'Title', 'Other_Street', 'Mobile', 'Last_Name', 'Lead_Source', 'Tag', 'Fax'
    ];
}