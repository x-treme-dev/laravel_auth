<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    //
    public function showLoginForm(){
        //dd(Auth::user());
        return view('auth.login'); // вызвать шаблон login из директории auth
    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
       
        // если аутентификация прошла успешно, то отправим пользователя на страницу profile
        if(Auth::attempt($data)){
            return redirect()->intended('profile'); 
        }
        // иначе - выведем ошибку 
        return back()->withErrors(['email' => 'Неверные учетный данные']);
    }
   
    // выйти из учетной записи
    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

     public function showRegisterForm(){
        return view('auth.register');
    }

     public function register(Request $request){
        // проверяем введенные пользователем данные
        $data = $request->validate([
            'name' => 'required|string',
            // laravel проверит, нет ли в таблице записи о пользователе с таким email'ом
           'email' => 'required|string|email|max:255|unique:users',
            // laravel сверит еще 2-е поле password
           'password' => 'required|string|min:8',
        ]);
       
        // хешируем пароль
        $data['password'] = Hash::make($data['password']);
        
        // создаем пользователя в таблице
        User::create($data);
        
        // после успешной регистрации перенаправляем на страницу login
        return redirect()->route('login')->withErrors('status', 'Регистрация прошла успешно!!!');
     }

     // метод для отображения профиля, на который происходит переход после login'а
     public function profile(){
        // передать авторизованного юзера
        $user = Auth::user(); // берем из модели
        return view('auth.profile', compact('user'));
     }
    
     // метод обновления данный для пользователя, если он хочет что-то изменить в данных профиля
     public function updateProfile(Request $request){
        $data = $request->validate([
            'name' => 'string',
           // получаем id пользователя и указываем, что обновленный email должен быть уникальным, 
           // но допустимо использовать прежнее название email'a
           'email' => 'string|max:255|email|unique:users,email,' . Auth::id(),
        ]);

        // далее получаем авторизованного пользователя и обновляем данные,
        // теми данными, что пришли из отправленного пользователем запроса 
        $user = Auth::user(); // берем из модели
        $user->update($data);

        return redirect()->route('profile');
     }
     
     // форма для обновления пароля
     public function showChangePasswordForm(){
        return view('auth.change-password');
     }

     // обновим пароль
     public function changePassword(Request $request){
        $data = $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // получаем авторизованного пользователя из модели
        $user = Auth::user();

        // при обновлении пароля проверим, что прежний пароль ввен правильно
        if(!Hash::check($data['current_password'], $user->password)){
            // если проверка не пройдет, то перенаправим пользователя
            return back()->withErrors(['current_password' => 'Текущий пароль неверен!!!']);
        }

        // если введенный пароль и прежний совпадают, то сохраним новый пароль в БД
        $user->password = Hash::make($data['new_password']);
        $user->save();

        return redirect()->route('profile')->with('status', 'Пароль изменен успешно!!!');
     }

}
