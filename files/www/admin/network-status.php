<?php
/*	Determine the following:
 	- Checks files on disk for various status
	- GeoIP status updates end-to-end encrypted
	- SSID
	- network state
	- VPN status
*/

function get_network_status() {

	// Default
	$response = [
		"ssid" => "unknown",
		"status" => "waiting", 
		"message" => "Waiting to connect",
		"ip" => "0.0.0.0",
		"ip_country" => "Unspecified",
		"ip_iso" => "none",
		"vpn" => "none"
	];

	// Get Device Info
	$f1 = fopen("/www/admin/config/ssid", "r");
	$response["ssid"] = fgets($f1);
	fclose($f1);

	// Get Network Info
	$fn='/www/admin/config/networkstate';
	if (file_exists($fn)) {

		$f2 = fopen("/www/admin/config/networkstate", "r");
		$g=fgets($f2);
		if ($g) {
			if (preg_match('/online/', $g) == 1) {
				$parts_state = explode('online', $g);
				$parts = explode(' ', $parts_state[1]);

				// Waiting to verify IP
				if (preg_match('/Waiting/', $g1) == 1) {
					$response["status"] = "connecting";
					$response["message"] = "Connecting to the internet";
				} else {
					$response["status"] = "connected";
					$response["message"] = "Connected via ".$parts[2];
					$response["ip"] = $parts[1];
					$response["ip_country"] = $parts[2];
					$response["ip_iso"] = str_replace(array("(", ")"), "", $parts[3]);
				}

				// Determine VPN Status (up, down, start, stop, failed)
				$vpnstatus = fopen("/www/admin/config/vpnstatus", "r");
				$h=fgets($vpnstatus);

				if (preg_match('/up/', $h) == 1) {
					//$vpnup=1;
					$response["status"] = "tunneled";
					$response["message"] = "VPN tunneled via ".$parts[2];
					$response["vpn"] = "up";
				}
				else if (preg_match('/down/', $h) == 1) {
					//$vpnup=3;
					$response["vpn"] = "down";
				}
				else if (preg_match('/start/', $h) == 1) {
					//$vpnup=0;
					$response["status"] = "connecting";
					$response["message"] = "Connecting to VPN...";
					$response["vpn"] = "start";
				}
				else if (preg_match('/stop/', $h) == 1) {
					//$vpnup=3;
					$response["status"] = "connecting";
					$response["message"] = "VPN is stopping";
					$response["vpn"] = "stop";
				}
				else if (preg_match('/unconfigured/', $h) == 1) {
					//$vpnup=2;
					$response["vpn"] = "unconfigured";
				}
				else if (preg_match('/failed/', $h) == 1) {
					//$vpnup=4;
					$response["vpn"] = "failed";
				}
				else {
					//$vpnup=5;
					$response["vpn"] = "error";
				}

				fclose($vpnstatus);
			}
			else if (preg_match('/offline/', $g) == 1) {
				$response["status"] = "disconnected";
				$response["message"] = "Not connected to the internet";
			}
			else {
				$response["status"] = "connecting";
				$response["message"] = "Connecting to the internet";
			}
		}
		fclose($f2);
	}

	return $response;
}
?>
