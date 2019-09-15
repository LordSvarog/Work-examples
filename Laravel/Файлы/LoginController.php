<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\User;
use App\Turbo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest')->except(['logout','getLogout','checking', 'logoutYa']);
  }

  public function redirectToProvider($provider)
  {
      return Socialite::driver($provider)->redirect();
  }


  public function handleProviderCallback(Request $request,$provider)
  {
      $user = Socialite::driver($provider)->user();
      return $this->findOrCreateUser($request,$user, $provider);

  }

  public function findOrCreateUser(Request $request,$user,$provider){

      $uid = DB::table('users-social')
                      ->where([['provider', $provider ],
                              ['provider_id',$user->getId()]])

                      ->first();

      if ($uid){
          $authUser = User::whereId($uid->user_id)->first();
          Auth::login($authUser, true);
          return redirect()->intended($this->redirectTo);
      }


      $token = $user->token;
      /*if ($provider == 'vkontakte'){
          $token = [
                  "access_token" => $user->token,
                  "expires_in" => $user->expiresIn,
                  "user_id" => $user->getId(),
                  "email" => $user->getEmail(),
                  "avatar"=> $user->avatar
          ];
      }*/

      $request->session()->put('socialite',['provider'=>$provider,'token'=>$token]);
      #TODO check mail
      $check_mail =User::whereEmail($user->getEmail())->first();
      if ($check_mail){
          Session::flash('message', 'Вы успешно авторизовались, но пользователь с таким электронным адресом уже существует, пожалуйста введите пароль или зарегистрируйте новый профиль');
          return redirect(route('login'));
      }

       Session::flash('message', 'Вы успешно авторизовались для дальнейшей работы требуется создать профиль на нашем сайте');

      return redirect(route('register'));
  }


  protected function authenticated(Request $request, $user)
  {
      if(Session::has('socialite')){
          $socialite = Session::pull('socialite');
          $social = Socialite::driver($socialite['provider'])->userFromToken($socialite['token']);

          DB::table('users-social')->insert([
                  'user_id' => $user->id,
                  'provider' =>$socialite['provider'],
                  'provider_id' => $social->getId(),
                  'created_at'=>date('Y-m-d H:i:s'),
                  'updated_at'=>date('Y-m-d H:i:s')
          ]);
      }
      $url = Session::pull('back-url');
      if ($url){
          $this->redirectTo = $url;
      }
  }

  public function showLoginForm(Request $request)
  {
      $url = url()->previous();
      Session::put('back-url', $url?$url:'/');
      $turbo_id= '';
      if ($request->TURBO_ID)
        $turbo_id= $request-> TURBO_ID;

      return view('auth.login', ['turbo_id'=> $turbo_id]);
  }

  protected function sendLoginResponse(Request $request)
  {
    $request->session()->regenerate();

    $this->clearLoginAttempts($request);

    if ($request-> turbo_id){
      $item = Turbo::whereTurbo_id($request-> turbo_id);
      $item = $item->first();
      if (!$item){
        $turbo= new Turbo;

        $arr=[];
        $user_id= Auth::id();
        foreach ($turbo->getFillable() as $key){
          $arr[$key]  = $request->$key;
        }
        $arr['user_id']= $user_id;
        $turbo = new Turbo($arr);

        $turbo->save();
      }
      
      $user= Auth::user()['name'] . ' ' . Auth::user()['lastname'];
      return "<script>
                window.parent.postMessage({
                  action: 'login',
                  login: '$user',
                  success: true
                }, '*');
              </script>";
    }

    return $this->authenticated($request, $this->guard()->user())
      ?: redirect()->intended($this->redirectPath());
  }

  public function checking (Request $request){
    $item = Turbo::whereTurbo_id($request->TURBO_ID);
    $item = $item->first();
    if ($item && Auth::check()){
      $user= Auth::user()['name'] . ' ' . Auth::user()['lastname'];
      return response()->json(['login' => $user], 200);
    }else{
      return response('', 401);
    }
  }

  public function logoutYa(Request $request){
    $this->guard()->logout();

    $request->session()->invalidate();

    return response()->json('', 200);
  }
}