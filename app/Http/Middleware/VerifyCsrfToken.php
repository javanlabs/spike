<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */

    private $openRoutes = ['api/payload', 'api/verification'];

    public function handle($request, Closure $next)
    {
        foreach($this->openRoutes as $route) {

            if ($request->is($route)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }

  /*  public function handle($request, Closure $next)
    {
        if($request->method() == 'POST')
        {
            return $next($request);
        }

        if ($request->method() == 'GET' || $this->tokensMatch($request))
        {
            return $next($request);
        }
        throw new TokenMismatchException;
    }*/

}
