<?php

namespace MRBS;


if (!isset($auth['session'])) {
    switch ($auth['type']) {
        case 'joomla':
        case 'saml':
        case 'wordpress':
            $auth['session'] = $auth['type'];
            break;
        default:
            $auth['session'] = 'php';
            break;
    }
}

// Need to be in this order
require_once "auth/auth_$auth[type].inc";
require_once "session/session_$auth[session].inc";



/* getAuthorised($level)
 * 
 * Check to see if the current user has a certain level of rights
 * 
 * $level - The access level required
 * 
 * Returns:
 *   false - The user does not have the required access
 *   true  - The user has the required access
 */
function getAuthorised($level)
{
    $user = getUserName();
    if (isset($user) == false) {
        authGet();
        return false;
    }

    return authGetUserLevel($user) >= $level;
}


/* checkAuthorised()
 *
 * Checks to see that a user is authorised to access the current page.
 * If they are not, then shows an Access Denied message and exits.
 *
 * Required page level authorisations are defined above.
 * If they are not defined then the function plays safe and requires the
 * highest level of authorisation.
 */
function checkAuthorised($just_check = false)
{
    global $page_level, $max_level;
    global $day, $month, $year, $area, $room;


    $user = getUserName();
    if (!isset($user) || empty($user)) {

        if (!isset($day) or !isset($month) or !isset($year)) {
            $day = date("d");
            $month = date("m");
            $year = date("Y");
        }
        authGet();
        exit();
    }

    return true;
}

/* getWritable($creator, $user, $room)
 * 
 * Determines if a user is able to modify an entry
 *
 * $creator - The creator of the entry
 * $user    - Who wants to modify it
 * $room    - The id of the room that the entry is in
 *
 * Returns:
 *   false  - The user does not have the required access
 *   true   - The user has the required access
 */
function getWritable($creator, $user, $room)
{
    // Always allowed to modify your own stuff
    if (strcasecmp($creator, $user) === 0) {
        return true;
    }

    // Otherwise you have to be a (booking) admin for this room
    if (auth_book_admin($user, $room)) {
        return true;
    }

    // Unathorised access
    return false;
}

/* showAccessDenied()
 * 
 * Displays an appropriate message when access has been denied
 * 
 * Returns: Nothing
 */
function showAccessDenied($day = null, $month = null, $year = null, $area = null, $room = null)
{
    global $HTTP_REFERER;

    print_header($day, $month, $year, $area, isset($room) ? $room : null);

    echo "<h1>" . get_vocab("accessdenied") . "</h1>\n";
    echo "<p>" . get_vocab("norights") . "</p>\n";
    echo "<p>\n";
    echo "<a href=\"" . htmlspecialchars($HTTP_REFERER) . "\">\n" . get_vocab("returnprev") . "</a>\n";
    echo "</p>\n";

    // Print footer and exit
    print_footer(true);
}

/* auth_can_edit_user($creator, $user)
 * 
 * Determines if a user is able to edit another user's details
 *
 * $user    - Who wants to modify it
 * $target  - The user that is being edited
 *
 * Returns:
 *   false  - The user does not have the required access
 *   true   - The user has the required access
 */
function auth_can_edit_user($user, $target)
{
    global $min_user_editing_level;

    // Always allowed to modify your own stuff
    if (strcasecmp($user, $target) === 0) {
        return true;
    }

    if (authGetUserLevel($user) >= $min_user_editing_level) {
        return true;
    }

    // Unathorised access
    return false;
}

// auth_book_admin($user, $room)
//
// Checks whether $user is allowed has booking administration rights
// for $room - ie is allowed to modify and delete other people's bookings
// and to approve bookings.
//
// At the moment $room is ignored, but is passed here so that later
// it can be enhanced to provide fine-grained permissions
// 
// Returns:  TRUE if the user is allowed has booking admin rights for
//           the room; otherwise FALSE
function auth_book_admin($user, $room)
{
    return (authGetUserLevel($user) >= 2);
}

