<?php

/*
 * speedtest.widget.php
 *
 * Copyright (c) 2020 Alon Noy
 *
 * Licensed under the GPL, Version 3.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once("guiconfig.inc");

// Convert speedtest-go JSON output to the format used by the widget
function speedtest_convert_results($output) {
    $data = json_decode($output, true);
    if (!is_array($data) || empty($data['servers'][0])) {
        return null;
    }
    $server = $data['servers'][0];
    return json_encode(array(
        'timestamp' => date('c', strtotime($data['timestamp'])),
        'ping' => $server['latency'] / 1000000,     // ns -> ms
        'download' => $server['dl_speed'] * 8,      // bytes/s -> bits/s
        'upload' => $server['ul_speed'] * 8,        // bytes/s -> bits/s
        'client' => array('isp' => $data['user_info']['Isp']),
        'server' => array('name' => $server['name'])
    ));
}

if ($_REQUEST['ajax']) {
    $output = shell_exec("speedtest-go --json");
    $results = ($output === null) ? null : speedtest_convert_results($output);
    if ($results !== null) {
        $config['widgets']['speedtest_result'] = $results;
        write_config("Save speedtest results");
        echo $results;
    } else {
        echo json_encode(null);
    }
} else {
    $results = isset($config['widgets']['speedtest_result']) ? $config['widgets']['speedtest_result'] : null;
    if(($results !== null) && (!is_object(json_decode($results)))) {
        $results = null;
    }
?>
<table class="table">
	<tr>
		<td><h4>Ping <i class="fa fa-exchange"></h4></td>
		<td><h4>Download <i class="fa fa-download"></i></h4></td>
		<td><h4>Upload <i class="fa fa-upload"></h4></td>
	</tr>
	<tr>
		<td><h4 id="speedtest-ping">N/A</h4></td>
		<td><h4 id="speedtest-download">N/A</h4></td>
		<td><h4 id="speedtest-upload">N/A</h4></td>
	</tr>
	<tr>
		<td>ISP</td>
		<td colspan="2" id="speedtest-isp">N/A</td>
	</tr>
	<tr>
		<td>Host</td>
		<td colspan="2" id="speedtest-host">N/A</td>
	</tr>
	<tr>
		<td colspan="3" id="speedtest-ts" style="font-size: 0.8em;">&nbsp;</td>
	</tr>
</table>
<a id="updspeed" href="#" class="fa fa-refresh" style="display: none;"></a>
<script type="text/javascript">
function update_result(results) {
    if(results != null) {
    	var date = new Date(results.timestamp);
    	$("#speedtest-ts").html(date);
    	$("#speedtest-ping").html(results.ping.toFixed(2) + "<small> ms</small>");
    	$("#speedtest-download").html((results.download / 1000000).toFixed(2) + "<small> Mbps</small>");
    	$("#speedtest-upload").html((results.upload / 1000000).toFixed(2) + "<small> Mbps</small>");
    	$("#speedtest-upload").html((results.upload / 1000000).toFixed(2) + "<small> Mbps</small>");
    	$("#speedtest-isp").html(results.client.isp);
    	$("#speedtest-host").html(results.server.name);
    } else {
    	$("#speedtest-ts").html("Speedtest failed");
    	$("#speedtest-ping").html("N/A");
    	$("#speedtest-download").html("N/A");
    	$("#speedtest-upload").html("N/A");
    	$("#speedtest-upload").html("N/A");
    	$("#speedtest-isp").html("N/A");
    	$("#speedtest-host").html("N/A");
    }
}

function update_speedtest() {
    $('#updspeed').off("click").blur().addClass("fa-spin").click(function() {
        $('#updspeed').blur();
        return false;
    });
    $.ajax({
        type: 'POST',
        url: "/widgets/widgets/speedtest.widget.php",
        dataType: 'json',
        data: {
            ajax: "ajax"
        },
        success: function(data) {
            update_result(data);
        },
        error: function() {
            update_result(null);
        },
        complete: function() {
            $('#updspeed').off("click").removeClass("fa-spin").click(function() {
                update_speedtest();
                return false;
            });
        }
    });
}
events.push(function() {
	var target = $("#updspeed").closest(".panel").find(".widget-heading-icon");
	$("#updspeed").prependTo(target).show();
    $('#updspeed').click(function() {
        update_speedtest();
        return false;
    });
    update_result(<?php echo ($results === null ? "null" : $results); ?>);
});
</script>
<?php } ?>
