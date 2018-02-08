<?php
/* 
 *******************************************************************************
 * Copyright 2011-2017 DANTE Ltd. and GÉANT on behalf of the GN3, GN3+, GN4-1 
 * and GN4-2 consortia
 *
 * License: see the web/copyright.php file in the file structure
 *******************************************************************************
 */
?>
<?php
    $o = new stdClass();
    if (isset($_REQUEST['data'])) {
        $o = json_decode($_REQUEST['data']);
    }
    $sp_problem = array(
    'technical' =>  _("I suspect a Technical Problem with the IdP"),
    'abuse-copyright' => _("A user from this IdP has allegedly infringed copyrights"),
    'abuse-network' => _("A user from this IdP has conducted malicious network operations (spam, DDoS, ...)")
    );
    $idp_problem = array(
    'technical' =>  _("User claims connectivity problems but has been authenticated successfully"),
    'abuse-copyright' => _("User claims that mandatory open port is not open")   
    );
    $queryType = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
    $res = '';
    $javascript = "<script>
    var mac = $('#mac');
    mac.on('keyup', formatMAC);
    var now = new Date();
    var datefrom = new Date();
    datefrom.setMonth(datefrom.getMonth() - 3);
    $('#timestamp').datetimepicker({
        timeFormat: 'HH:mm z',
        controlType: 'select',
        minDateTime: datefrom,
        maxDateTime: now
    });
</script>
    ";
    if ($queryType == 'sp') {
        $select = "<div id='sp_abuse_problem'>
<select style='margin-left: 0px;' id='select_sp_problem'>";
        foreach ($sp_problem as $pname => $pdesc) {
            $select = $select . "<option value='$pname'>$pdesc</option>\n";
        }
        $select = $select . "</select></div>";
        $res = "
<table id='sp_questions'>
    <tr>
        <td>" . _("Select your problem") . "</td>
        <td>$select</td>
    </tr>
    <tr>
        <td>" . _("What is the realm of the IdP in question?") . "</td>
        <td>
                <input type='text' name='admin_realm' id='admin_realm' value=''>
                <button id='realm_in_db_admin' accesskey='R' type='button'>" .
                _("Check if this value is registered") .
                "</button>
        </td>
    </tr>
    <tr class='hidden_row'>
        <td>" . _("What is the authentication timestamp of the user session in question?") . "</td>
        <td><input type='text' id='timestamp' name='timestamp'>
            <div id='datepicker'></div>
        </td>
    </tr>
    <tr class='hidden_row'>
        <td>" . _("What is the MAC address of the user session in question?") . "</td>
        <td><input type='text' id='mac' name='mac'></td>
    </tr>
    <tr class='hidden_row'>
        <td>" . _("Additional comments") . "</td>
        <td><textarea id='freetext' name='freetext' cols='60' rows='5'></textarea></td>
    </tr>
    <tr class='hidden_row'>
        <td>" . _("Please specify an email address on which the IdP can contact you") . "</td>
        <td><input type='text' id='email' name='email'></td>
    </tr>
    <tr class='hidden_row' id='send_query_to_idp'>
        <td>" . _("Now you can send your query") . "</td>
        <td><button type='submit' id='submit_idp_query' name='go'>" . _("Send") . "</button></td>
    </tr>
 </table>";
        $res = $res . $javascript;
    }
    if ($queryType == 'idp') {
        $select = "<div id='idp_reported_problem' style='display:;'>
<select style='margin-left:0px;' id='select_idp_problem'>";
        foreach ($idp_problem as $pname => $pdesc) {
            $select = $select . "<option value='$pname'>$pdesc</option>\n";
        }
        $select = $select . "</select></div>";
        $res = "
<table id='idp_questions'>
    <tr>
        <td>" . _("Select your problem") . "</td>
        <td>$select</td>
    </tr>
    <tr>
        <td>" . _("Can you identify the SP by means of its Operator-Name attribute?") . "</td>
        <td><input type='text' id='opname' name='opname' value=''></td>
    </tr>
    <tr id='spmanually'>
        <td>" . _("or") . '<br>' . _("select the SP manually:") . "</td>
        <td>
            <div id='select_asp_country'><a href='' id='asp_countries_list'>
            <span id='opnameselect'>" . _("click to select country and organisation") . "</a></span>
            </div>
            <div id='select_asp_area'></div>
        </td>
    </tr>
    <tr id='asp_desc' style='display: none;'>
        <td>" . _("or") . ' ' . _("at least describe the SP location") . "</td>
        <td><input type='text' id='asp_location' name='asp_location' value=''></td>
    </tr>
    <tr>
        <td>" . _("What is the outer ID of the user session in question?") . "</td>
        <td><input type='text' id='outer_id' name='outer_id' value=''></td>
    </tr>
    <tr>
        <td>" . _("What is the authentication timestamp of the user session in question?") . "</td>
        <td>
            <input type='text' id='timestamp' name='timestamp'>
            <div id='datepicker'></div>
        </td>
    </tr>
    <tr>
        <td>" . _("What is the MAC address of the user session in question?") . "</td>
        <td><input type='text' id='mac' name='mac'></td>
    </tr>
    <tr>
        <td>" . _("Additional comments about the problem") . "</td>
        <td><textarea id='freetext' name='freetext' cols='60' rows='5'></textarea></td>
    </tr>
    <tr>
        <td>" . _("Do you have any contact details by which the user wishes to be contacted by the SP?") . "</td>
        <td><textarea id='c_details' name='c_details' cols='60' rows='5'></textarea></td>
    </tr>
    <tr>
        <td>" . _("Please specify an email address on which the SP can contact you") . "</td>
        <td><input type='text' id='email' name='email'></td>
    </tr>
    <tr class='hidden_row' id='send_query_to_sp'>
        <td>" . _("Now you can send your query") . "</td>
        <td><button type='submit' id='submit_sp_query' name='go'>" . _("Send") . "</button></td>
    </tr>
</table>";
        $res = $res . $javascript;
    }
    if ($queryType == 'idp_send' || $queryType == 'sp_send') {
        require_once(dirname(dirname(dirname(__FILE__))) . "/config/_config.php");
        $languageInstance = new \core\common\Language();
        $languageInstance->setTextDomain("web_user");
        $cat = new \core\CAT();
        $returnArray = array();
        if (count((array)$o)  > 0) {
            foreach ($o as $key => $value) {
                $value = trim($value);
                switch ($key) {
                    case 'realm':                       
                    case 'email':
                    case 'mac':
                    case 'freetext':
                    case 'timestamp':
                    case 'opname':
                    case 'outerid':
                    case 'cdetails':
                        $returnArray[$key] = $value;
                        break;
                    case 'idpcontact':
                        $returnArray[$key] = base64_decode($value);
                        break;
                    case 'reason':
                        if ($queryType == 'idp_send') {
                            $returnArray[$key] = $sp_problem[$value];
                        } else {
                            $returnArray[$key] = $idp_problem[$value];
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        $returnArray['status'] = 1;
        $res = json_encode($returnArray);
    }
    echo $res;
