<?php

function Q_delete($params)
{
	$uri = Q_Dispatcher::uri();
	$module = $uri->module;
	$action = $uri->action;
	if (!Q::canHandle("$module/$action/delete")) {
		throw new Q_Exception_MethodNotSupported(array('method' => 'DELETE'));
	}
	// SECURITY: CSRF, as in Q/post. This used to pass no $throwIfInvalid and
	// discard the result, so a form POST with Q.method=DELETE reached the
	// handler unchecked (ro#915).
	if (Q_Session::nonceIsExpected()) {
		Q_Request::requireValidNonce(true);
	}
	return Q::event("$module/$action/delete", $params);
}
