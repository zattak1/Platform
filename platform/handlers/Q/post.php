<?php

function Q_post($params)
{
	$uri = Q_Dispatcher::uri();
	$module = $uri->module;
	$action = $uri->action;
	if (!Q::canHandle("$module/$action/post")) {
		throw new Q_Exception_MethodNotSupported(array('method' => 'POST'));
	}
	if (isset($_SERVER['CONTENT_LENGTH'])) {
		$contentLength = (int)$_SERVER['CONTENT_LENGTH'];
		foreach (array('upload_max_filesize', 'post_max_size') as $name) {
			$value = ini_get($name);
			$v = intval($value);
			switch (substr($value, -1)) {
				case 'K': $v *= 1024; break;
				case 'M': $v *= 1024*1024; break;
				case 'B': $v *= 1024*1024*1024; break;
			}
			if ($contentLength > $v) {
				throw new Q_Exception_ContentLength(array('contentLength' => $contentLength, 'exceeds' => $name));
			}
		}
	}
	// SECURITY: CSRF. Throws for a module/action on the Q/web/requireValidNonce
	// include list when the request rides on a session cookie but lacks that
	// session's nonce. This used to pass no $throwIfInvalid and discard the
	// result, and sat behind Q_Session::$nonceWasSet, which the dispatcher sets
	// on every web request -- so it checked nothing (ro#915).
	if (Q_Session::nonceIsExpected()) {
		Q_Request::requireValidNonce(true);
	}
	return Q::event("$module/$action/post", $params);
}
