<?php
namespace MRBS;

use MRBS\Form\Form;

require_once MRBS_ROOT . '/auth/cms/joomla.inc';
require_once MRBS_ROOT . '/session/functions_session.inc';


/* authGet()
 * 
 * Request the user name/password
 * 
 * Returns: Nothing
 */
function authGet()
{
  global $QUERY_STRING;
  global $returl;

  print_header();

  echo "<p>".get_vocab("norights")."</p>\n";

  $target_url = this_page();
  if (isset($QUERY_STRING))
  {
    $target_url = $target_url . "?" . $QUERY_STRING;
  }
  print_login_form(this_page(), $target_url, $returl);

  exit();
}

function getUserName()
{
  return JFactory::getUser()->username;
}

// Print the logon entry on the top banner.
function print_logon_box()
{
  global $QUERY_STRING;

  $target_url = this_page();

  if (isset($QUERY_STRING))
  {
    $target_url = $target_url . "?" . $QUERY_STRING;
  }
  
  $user=getUserName();
  
  if (isset($user))
  {
    print_report_link($user);
    print_logoff_button($target_url);
  }
  else
  {
    print_unknown_user();
    print_logon_button($target_url);
  } 
  
  print_userlist_link();
}


function logoff_user()
{
  $mainframe = JFactory::getApplication('site');
  $mainframe->logout();
}


global $auth;

// Get non-standard form variables
$action = get_form_var('action', 'string', null, INPUT_POST);
$username = get_form_var('username', 'string', null, INPUT_POST);
$password = get_form_var('password', 'string', null, INPUT_POST);
$target_url = get_form_var('target_url', 'string', null, INPUT_POST);
$returl = get_form_var('returl', 'string', null, INPUT_POST);

if (isset($action))
{
  // Target of the form with sets the URL argument "action=QueryName".
  // Will eventually return to URL argument "target_url=whatever".
  if ($action == "QueryName")
  {
    print_header();
    print_login_form(this_page(), $target_url, $returl);
    exit();
  }
  
  
  // Target of the form with sets the URL argument "action=SetName".
  // Will eventually return to URL argument "target_url=whatever".
  if ($action == "SetName")
  {
    // If we're going to do something then check the CSRF token first
    Form::checkToken();
    /* First make sure the password is valid */
    if ($username == "")
    {
      logoff_user();
    }
    else
    {
      $credentials = array();
      $credentials['username'] = $username;
      $credentials['password'] = $password;
      $mainframe = JFactory::getApplication('site');
   
      if (!$mainframe->login($credentials))
      {
        print_header();
        print_login_failed();
        print_login_form(this_page(), $target_url, $returl);
        exit();
      }
      
      // Preserve the original $HTTP_REFERER by sending it as a GET parameter
      if (!empty($returl))
      {
        // check to see whether there's a query string already
        if (strpos($target_url, '?') === false)
        {
          $target_url .= "?returl=" . urlencode($returl);
        }
        else
        {
          $target_url .= "&returl=" . urlencode($returl);
        }
      }
    }

    header ("Location: $target_url"); /* Redirect browser to initial page */
    exit;
  }
}
