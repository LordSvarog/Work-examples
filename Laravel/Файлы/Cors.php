<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Response;
 
class Cors
{
    /**
     * Массив доменов, с которых будем принимать запросы.
     *
     * @var array
     */
    protected $domains = [
        'https://yandex.ru',
        'https://turbopages.org',
    ];
 
    /**
     * Метод, который обрабатывает все запросы, приходящие на сервер.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        // проверим, присутствует ли заголовок HTTP_ORIGIN в запросе
        // и разрешен ли домен
        $origin = $request->headers->get('Origin');
        if(!$origin || !in_array($origin, $this->domains, true)) {
            return new Response('Forbidden', 403);
        }
 
        //если есть, то устанавливаем нужные заголовки
      if ($request->isMethod('OPTIONS')){
        $response = response('', 204);
      } else {
        $response = $next($request);
      }
      return $response
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Allow-Headers', 'Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type'
            );
    }
}