<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

class AuthMiddleware{

	public function Check($request, $response, $next){
		if(isset($_SESSION['login'])){
        	$response = $next($request, $response);
        	return $response;
      	}
      	else{
        	return $response->withRedirect('/authorization');
      	}
	}

}