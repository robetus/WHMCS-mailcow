<?php
/**
 * MailCow Provisioning Module by Websavers Inc
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Require any libraries needed for the module to function.
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;

// Also, perform any initialization required by the service's library.

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related abilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Provisioning_Module_Meta_Data_Parameters
 *
 * @return array
 */
function mailcow_MetaData()
{
    return array(
        'DisplayName' => 'MailCow',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '80', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '443', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}

/**
 * Define product configuration options.
 *
 * The values you return here define the configuration options that are
 * presented to a user when configuring a product for use with the module. These
 * values are then made available in all module function calls with the key name
 * configoptionX - with X being the index number of the field from 1 to 24.
 *
 * You can specify up to 24 parameters, with field types:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each and their possible configuration parameters are provided in
 * this sample function.
 *
 * @return array
 */
function mailcow_ConfigOptions()
{
    return array(
        // a text field type allows for single line text input
        'Text Field' => array(
            'Type' => 'text',
            'Size' => '25',
            'Default' => '1024',
            'Description' => 'Enter in megabytes',
        ),
        // a password field type allows for masked text input
        'Password Field' => array(
            'Type' => 'password',
            'Size' => '25',
            'Default' => '',
            'Description' => 'Enter secret value here',
        ),
        // the yesno field type displays a single checkbox option
        'Checkbox Field' => array(
            'Type' => 'yesno',
            'Description' => 'Tick to enable',
        ),
        // the dropdown field type renders a select menu of options
        'Dropdown Field' => array(
            'Type' => 'dropdown',
            'Options' => array(
                'option1' => 'Display Value 1',
                'option2' => 'Second Option',
                'option3' => 'Another Option',
            ),
            'Description' => 'Choose one',
        ),
        // the radio field type displays a series of radio button options
        'Radio Field' => array(
            'Type' => 'radio',
            'Options' => 'First Option,Second Option,Third Option',
            'Description' => 'Choose your option!',
        ),
        // the textarea field type allows for multi-line text input
        'Textarea Field' => array(
            'Type' => 'textarea',
            'Rows' => '3',
            'Cols' => '60',
            'Description' => 'Freeform multi-line text input field',
        ),
    );
}

/**
 * Provision a new instance of a product/service.
 *
 * Attempt to provision a new instance of a given product/service. This is
 * called any time provisioning is requested inside of WHMCS. Depending upon the
 * configuration, this can be any of:
 * * When a new order is placed
 * * When an invoice for a new order is paid
 * * Upon manual request by an admin user
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function mailcow_CreateAccount(array $params)
{
    try {
      
      $mailcow = new mailcow_api($params['serverhostname'], $params['serverusername'], $params['serverpassword']);
      $result = $mailcow->addDomain($params['domain'], $params['configoptions']['Email Accounts']);
      
      logModuleCall(
          'mailcow',
          __FUNCTION__,
          $params,
          print_r($result, true),
          null
      );
      
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Suspend an instance of a product/service.
 *
 * Called when a suspension is requested. This is invoked automatically by WHMCS
 * when a product becomes overdue on payment or can be called manually by admin
 * user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */

function mailcow_SuspendAccount(array $params)
{
    try {
      
      $mailcow = new mailcow_api($params['serverhostname'], $params['serverusername'], $params['serverpassword']);
      $result = $mailcow->disableDomain($params['domain'], $params['configoptions']['Email Accounts']);
      
      logModuleCall(
          'mailcow',
          __FUNCTION__,
          $params,
          print_r($result, true),
          null
      );
      
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Un-suspend instance of a product/service.
 *
 * Called when an un-suspension is requested. This is invoked
 * automatically upon payment of an overdue invoice for a product, or
 * can be called manually by admin user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */

function mailcow_UnsuspendAccount(array $params)
{
    try {
      
      $mailcow = new mailcow_api($params['serverhostname'], $params['serverusername'], $params['serverpassword']);
      $result = $mailcow->activateDomain($params['domain'], $params['configoptions']['Email Accounts']);
      
      logModuleCall(
          'mailcow',
          __FUNCTION__,
          $params,
          print_r($result, true),
          null
      );
      
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Terminate instance of a product/service.
 *
 * Called when a termination is requested. This can be invoked automatically for
 * overdue products if enabled, or requested manually by an admin user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
 
function mailcow_TerminateAccount(array $params)
{
    try {
      $mailcow = new mailcow_api($params['serverhostname'], $params['serverusername'], $params['serverpassword']);
      $result = $mailcow->removeDomain($params['domain']);
      
      logModuleCall(
          'mailcow',
          __FUNCTION__,
          $params,
          print_r($result, true),
          null
      );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

*/

/**
 * Change the password for an instance of a product/service.
 *
 * Called when a password change is requested. This can occur either due to a
 * client requesting it via the client area or an admin requesting it from the
 * admin side.
 *
 * This option is only available to client end users when the product is in an
 * active status.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
 
/*
function mailcow_ChangePassword(array $params)
{
    try {
        // Call the service's change password function, using the values
        // provided by WHMCS in `$params`.
        //
        // A sample `$params` array may be defined as:
        //
        // ```
        // array(
        //     'username' => 'The service username',
        //     'password' => 'The new service password',
        // )
        // ```
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
*/

/**
 * Upgrade or downgrade an instance of a product/service.
 *
 * Called to apply any change in product assignment or parameters. It
 * is called to provision upgrade or downgrade orders, as well as being
 * able to be invoked manually by an admin user.
 *
 * This same function is called for upgrades and downgrades of both
 * products and configurable options.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return string "success" or an error message
 */
function mailcow_ChangePackage(array $params)
{
    try {
        
        $mailcow = new mailcow_api($params['serverhostname'], $params['serverusername'], $params['serverpassword']);
        $result = $mailcow->editDomain($params['domain'], $params['configoptions']['Email Accounts']);
        
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            print_r($result, true),
            null
        );
        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Test connection with the given server parameters.
 *
 * Allows an admin user to verify that an API connection can be
 * successfully made with the given configuration parameters for a
 * server.
 *
 * When defined in a module, a Test Connection button will appear
 * alongside the Server Type dropdown when adding or editing an
 * existing server.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function mailcow_TestConnection(array $params)
{
    try {
        // Call the service's connection test function.

        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}

/**
 * Additional actions an admin user can invoke.
 *
 * Define additional actions that an admin user can perform for an
 * instance of a product/service.
 *
 * @see mailcow_buttonOneFunction()
 *
 * @return array
 */
function mailcow_AdminCustomButtonArray(){
  /*
    return array(
        "Button 1 Display Value" => "buttonOneFunction",
        "Button 2 Display Value" => "buttonTwoFunction",
    );
  */
}

/**
 * Additional actions a client user can invoke.
 *
 * Define additional actions a client user can perform for an instance of a
 * product/service.
 *
 * Any actions you define here will be automatically displayed in the available
 * list of actions within the client area.
 *
 * @return array
 */
function mailcow_ClientAreaCustomButtonArray()
{
    return array(
        "Action 1 Display Value" => "actionOneFunction",
        "Action 2 Display Value" => "actionTwoFunction",
    );
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 * @see mailcow_AdminCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function mailcow_buttonOneFunction(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Custom function for performing an additional action.
 *
 * You can define an unlimited number of custom functions in this way.
 *
 * Similar to all other module call functions, they should either return
 * 'success' or an error message to be displayed.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 * @see mailcow_ClientAreaCustomButtonArray()
 *
 * @return string "success" or an error message
 */
function mailcow_actionOneFunction(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

/**
 * Admin services tab additional fields.
 *
 * Define additional rows and fields to be displayed in the admin area service
 * information and management page within the clients profile.
 *
 * Supports an unlimited number of additional field labels and content of any
 * type to output.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 * @see mailcow_AdminServicesTabFieldsSave()
 *
 * @return array
 */
 
 /*
function mailcow_AdminServicesTabFields(array $params)
{
    try {
        // Call the service's function, using the values provided by WHMCS in
        // `$params`.
        $response = array();

        // Return an array based on the function's response.
        return array(
            'Number of Apples' => (int) $response['numApples'],
            'Number of Oranges' => (int) $response['numOranges'],
            'Last Access Date' => date("Y-m-d H:i:s", $response['lastLoginTimestamp']),
            'Something Editable' => '<input type="hidden" name="mailcow_original_uniquefieldname" '
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />'
                . '<input type="text" name="mailcow_uniquefieldname"'
                . 'value="' . htmlspecialchars($response['textvalue']) . '" />',
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, simply return no additional fields to display.
    }

    return array();
}

*/

/**
 * Execute actions upon save of an instance of a product/service.
 *
 * Use to perform any required actions upon the submission of the admin area
 * product management form.
 *
 * It can also be used in conjunction with the AdminServicesTabFields function
 * to handle values submitted in any custom fields which is demonstrated here.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 * @see mailcow_AdminServicesTabFields()
 */
function mailcow_AdminServicesTabFieldsSave(array $params)
{
    // Fetch form submission variables.
    $originalFieldValue = isset($_REQUEST['mailcow_original_uniquefieldname'])
        ? $_REQUEST['mailcow_original_uniquefieldname']
        : '';

    $newFieldValue = isset($_REQUEST['mailcow_uniquefieldname'])
        ? $_REQUEST['mailcow_uniquefieldname']
        : '';

    // Look for a change in value to avoid making unnecessary service calls.
    if ($originalFieldValue != $newFieldValue) {
        try {
            // Call the service's function, using the values provided by WHMCS
            // in `$params`.
        } catch (Exception $e) {
            // Record the error in WHMCS's module log.
            logModuleCall(
                'mailcow',
                __FUNCTION__,
                $params,
                $e->getMessage(),
                $e->getTraceAsString()
            );

            // Otherwise, error conditions are not supported in this operation.
        }
    }
}

/**
 * Perform single sign-on for a given instance of a product/service.
 *
 * Called when single sign-on is requested for an instance of a product/service.
 *
 * When successful, returns a URL to which the user should be redirected.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function mailcow_ServiceSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on token retrieval function, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Perform single sign-on for a server.
 *
 * Called when single sign-on is requested for a server assigned to the module.
 *
 * This differs from ServiceSingleSignOn in that it relates to a server
 * instance within the admin area, as opposed to a single client instance of a
 * product/service.
 *
 * When successful, returns a URL to which the user should be redirected to.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function mailcow_AdminSingleSignOn(array $params)
{
    try {
        // Call the service's single sign-on admin token retrieval function,
        // using the values provided by WHMCS in `$params`.
        $response = array();

        return array(
            'success' => true,
            'redirectTo' => $response['redirectUrl'],
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return array(
            'success' => false,
            'errorMsg' => $e->getMessage(),
        );
    }
}

/**
 * Client area output logic handling.
 *
 * This function is used to define module specific client area output. It should
 * return an array consisting of a template file and optional additional
 * template variables to make available to that template.
 *
 * The template file you return can be one of two types:
 *
 * * tabOverviewModuleOutputTemplate - The output of the template provided here
 *   will be displayed as part of the default product/service client area
 *   product overview page.
 *
 * * tabOverviewReplacementTemplate - Alternatively using this option allows you
 *   to entirely take control of the product/service overview page within the
 *   client area.
 *
 * Whichever option you choose, extra template variables are defined in the same
 * way. This demonstrates the use of the full replacement.
 *
 * Please Note: Using tabOverviewReplacementTemplate means you should display
 * the standard information such as pricing and billing details in your custom
 * template or they will not be visible to the end user.
 *
 * @param array $params common module parameters
 *
 * @see http://docs.whmcs.com/Provisioning_Module_SDK_Parameters
 *
 * @return array
 */
function mailcow_ClientArea(array $params)
{
    // Determine the requested action and set service call parameters based on
    // the action.
    $requestedAction = isset($_REQUEST['customAction']) ? $_REQUEST['customAction'] : '';

    if ($requestedAction == 'manage') {
        $serviceAction = 'get_usage';
        $templateFile = 'templates/manage.tpl';
    } else {
        $serviceAction = 'get_stats';
        $templateFile = 'templates/overview.tpl';
    }

    try {
        // Call the service's function based on the request action, using the
        // values provided by WHMCS in `$params`.
        $response = array();

        $extraVariable1 = 'abc';
        $extraVariable2 = '123';

        return array(
            'tabOverviewReplacementTemplate' => $templateFile,
            'templateVariables' => array(
                'extraVariable1' => $extraVariable1,
                'extraVariable2' => $extraVariable2,
            ),
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'mailcow',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        // In an error condition, display an error page.
        return array(
            'tabOverviewReplacementTemplate' => 'error.tpl',
            'templateVariables' => array(
                'usefulErrorHelper' => $e->getMessage(),
            ),
        );
    }
}



class mailcow_api{
  
  private $curl;
  private $cookie;
  public $baseurl;
  public $aliases = 500;
  public $MAILBOXQUOTA;
  
  public function __construct($server_hostname, $server_login, $server_password, $mquota = 30720){
    
    $this->baseurl = 'https://' . $server_hostname;
    $this->MAILBOXQUOTA = $mquota;

    $this->curl = new Curl();
    $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
    $this->curl->setCookieFile('');
    $this->curl->setCookieJar(dirname(__FILE__) . '/cookiejar.txt');
    
    $data = array(
      'login_user' => $server_login,
      'pass_user' => html_entity_decode($server_password),
    );
    
    //Create session
    $this->curl->post($this->baseurl, $data);
    //$this->cookie = $this->curl->getCookie('PHPSESSID');
        
  }
  
  public function addDomain($domain, $num_mailboxes){
    
    return $this->_addOrEditDomain($domain, $num_mailboxes, 'create');
    
  }
  
  public function editDomain($domain, $num_mailboxes){
    
    return $this->_addOrEditDomain($domain, $num_mailboxes, 'edit');
    
  }
  
  public function disableDomain($domain, $num_mailboxes){
    
    return $this->_addOrEditDomain($domain, $num_mailboxes, 'disable');
    
  }
  
  public function activateDomain($domain, $num_mailboxes){
    
    return $this->_addOrEditDomain($domain, $num_mailboxes, 'activate');
    
  }
  
  public function removeDomain($domain){
    
    $data = array( /** Those commented out hopefully aren't necessary to submit... **/
      'domain' => urlencode($domain),
      'mailbox_delete_domain' => '',
    );
    
    $this->curl->post($this->baseurl . '/mailbox.php', $data);
    
    if ($this->curl->error) {
      
      return array( 
        'error' => $this->curl->errorCode,
        'error_message' => $this->curl->errorMessage,
      );
      
    } else {
      
      return $this->curl->response;
      
    }
      
  }
  
  private function _addOrEditDomain($domain, $num_mailboxes, $action){
    
    $data = array( /** Those commented out hopefully aren't necessary to submit... **/
      'domain' => urlencode($domain),
      'description' => 'None',
      'aliases' => $this->aliases,
      'mailboxes' => $num_mailboxes,
      'maxquota' => $this->MAILBOXQUOTA, //per mailbox
      'quota' => $this->MAILBOXQUOTA * $num_mailboxes, //for domain
      //'backupmx' => 'on',
      //'relay_all_recipients' => 'on',
    );
    
    if ($action != 'disable'){
      $data['active'] = 'on';
    }
    
    //logActivity("Add a domain? $addDomain"); //DEBUG
    
    switch ($action){
      
      case 'create':
        $data['mailbox_add_domain'] = '';
        break;
      case 'edit':
      case 'disable':
      case 'activate':
        $data['mailbox_edit_domain'] = '';
        break;
        
    }
    
    $this->curl->post($this->baseurl . '/mailbox.php', $data);
    
    if ($this->curl->error) {
      
      return array( 
        'error' => $this->curl->errorCode,
        'error_message' => $this->curl->errorMessage,
      );
      
    } else {
      
      return $this->curl->response;
      
    }
      
  }
  
} /* Close MailCow_API class */
