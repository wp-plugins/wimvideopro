<?php
/**
 * Written by walter at 30/10/13
 */

include_once("api.php");

use Api\Api;
use \Httpful\Mime;
use \Httpful\Request;

function initApi($host, $username, $password) {
    Api::initApiAccessor($host, $username, $password);
}

function getApi() {
    return Api::getApiAccessor();
}

function apiCreateUrl($name) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('liveStream/uri?name=' . $name);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiEmbeddedLive($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('liveStream/' . $apiAccessor->username . '/' . $apiAccessor->username . '/hosts/' . $hostId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, Mime::JSON);
}

function apiGetProfile() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('profile');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiEditProfile($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('profile');
    $request->sends(Mime::JSON);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiChangePassword($password) {
    $apiAccessor = getApi();
    $request = $apiAccessor->putRequest("users/" . $apiAccessor->username . "/updateLivePwd");
    $params = array('liveStreamPwd' => $password);
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiProgrammingPool() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('programmingPool');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetCurrentProgrammings($qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('currentProgramming?' . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetProgrammings($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('programmings');
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiAddItem($progId, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('programming/' . $progId .'/items');
    $request->body($params);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetShowtimes() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('users/' . $apiAccessor->username . '/showtime?details=true');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetCalendar($progId, $qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest("programming/" . $progId . "/calendar?" . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetLiveEvents($timezone, $activeOnly) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '?timezone=' . $timezone;
    if ($activeOnly) {
        $url .= '&active=true';
    }
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiGetLive($host_id, $timezone="") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id;
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json');
}

function apiGetLiveIframe($host_id, $timezone="") {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id . '/embed';
    if (strlen($timezone))
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->getRequest($url);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'text/xml, application/xml');
}

function apiAddLive($parameters, $timezone=null) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl;
    if ($timezone)
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->postRequest($url);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'application/json', false);
}

function apiModifyLive($host_id, $parameters, $timezone=null) {
    $apiAccessor = getApi();
    $url = $apiAccessor->liveHostsUrl . '/' . $host_id;
    if ($timezone)
        $url .= '?timezone=' . $timezone;
    $request = $apiAccessor->postRequest($url);
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request,  'application/json', false);
}

function apiPublishOnShowtime($id, $parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos/' . $id . '/showtime');
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
	
}
function apiPublishAcquiredOnShowtime($id,  $acquiredId,$parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos/' . $id . '/acquired/' . $acquiredId . '/showtime');
    $request->body($parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
	
}
function apiGetThumbsVideo($contentId) {
   	$apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $contentId . '/thumbnail');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, 'text/xml, application/xml');
}

function apiGetDetailsVideo($contentId) {
   	$apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $contentId . '?details=true');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiGetDetailsShowtime($id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('users/' . $apiAccessor->username. '/showtime/' . $id . '/details');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiGetPlayerShowtime($id,$parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/'. $id . "/embeddedPlayers?" . $parameters);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteLive($host_id) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest($apiAccessor->liveHostsUrl . '/' . $host_id);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteItems($progId, $itemId, $qs) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest("programming/" . $progId . "/items" . $itemId . "?" . $qs);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiGetVideoCategories() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videoCategories');
    return $apiAccessor->execute($request);
}

function apiGetUUID() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('uuid');
    return $apiAccessor->execute($request);
}

function apiUpload($parameters) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('videos?uploadIdentifier=' . $parameters['uploadIdentifier']);
    $request->body($parameters);
    $request->sends(Mime::UPLOAD);
    $request->attach(array('file' => $parameters['file']));
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDownload($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos/' . $hostId . '/download');
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "");
}

function apiGetVideos($details=true) {
    if ($details) {
        $details = 'true';
    } else {
        $details = 'false';
    }
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('videos?details=' . $details );
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request, "application/json");
}

function apiDeleteVideo($hostId) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $hostId);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiDeleteFromShowtime($id, $stid ) {
    $apiAccessor = getApi();
    $request = $apiAccessor->deleteRequest('videos/' . $id . '/showtime/' . $stid);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiRegistration($params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('register');
    $request->sendsJson();
    $request->body(json_encode($params));
    return $apiAccessor->execute($request);
}

function apiCheckPayment($cookie) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('userpacket/payment/check');
    $request->setCookieFile($cookie);
    return $apiAccessor->execute($request);
}

function apiGetUploadProgress($contentIdentifier) {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('uploadProgress/' . $contentIdentifier);
    $request = $apiAccessor->authenticate($request);
    return $apiAccessor->execute($request);
}

function apiUpgradePacket($redirect_url, $cookie, $params) {
    $apiAccessor = getApi();
    $request = $apiAccessor->postRequest('userpacket/payment/pay?externalRedirect=true&success=' . $redirect_url);
    $request = $apiAccessor->authenticate($request);
    $request->setCookieJar($cookie);
    $request->sends(Mime::JSON);
    $request->body($params);
    $request->addHeader('Content-Lenght', strlen($params));
    return $apiAccessor->execute($request);
}

function apiGetPacket() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('userpacket/' . $apiAccessor->username);
    return $apiAccessor->execute($request, Mime::JSON);
}

function apiCommercialPacket() {
    $apiAccessor = getApi();
    $request = $apiAccessor->getRequest('commercialpacket');
    return $apiAccessor->execute($request, Mime::JSON);
}


initApi(get_option("wp_basePathWimtv"), get_option("wp_userwimtv"), get_option("wp_passwimtv"));

?>