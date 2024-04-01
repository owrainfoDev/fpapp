<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// $routes->add('/notice' , 'Notice::list' , ['filter'=>'isLoggedIn'] );

$routes->add('/login' , 'Login::index');

$routes->group('api', function ($routes) {
    $routes->add('menu' , 'Menu::ajaxMainData');
    $routes->add('alramCount' , 'Menu::getAlramCount');
    $routes->add('loginCheck', 'Login::ajaxloginCheck');
    $routes->add('authCheck' , 'Login::ajaxReqValidToken');
    $routes->add('main' , 'Home::ajaxMainData');
    $routes->add('notice' , 'Notice::proc');
    $routes->add('menuSetStd' , 'Login::ajaxSetStudent');
    $routes->add('ajax/(:segment)' , 'Ajax::ajaxProc/$1');
    $routes->add('profile' , 'Home::getProfile');
});

$routes->group('/notice' , function($routes){
    $routes->add('','Notice::index');
    $routes->add('(:num)','Notice::detail/$1');
    
    $routes->add('(:num)/edit','Notice::edit/$1');
    $routes->add('write','Notice::write');
    $routes->add('ajaxList','Notice::ajaxList');
} );

// 오늘의 급식
$routes->group('/schoolmeal' ,  ['filter' => 'isLoggedIn'] , function($routes){
    $routes->add('','Schoolmeal::index');
    $routes->add('ajaxmoreschoolmeal','Schoolmeal::moreschoolmeal');
    $routes->add('write' , 'Schoolmeal::write');
    $routes->add('edit/(:segment)' , 'Schoolmeal::edit/$1');
    $routes->add('proc/(:segment)' , 'Schoolmeal::proc/$1');
    
});

$routes->group('/schoolmealmonthly' ,  ['filter' => 'isLoggedIn'] , function($routes){
    $routes->add('','Schoolmealmonthly::index');
    $routes->add('write' , 'Schoolmealmonthly::write');
    $routes->add('edit/(:segment)' , 'Schoolmealmonthly::edit/$1');
    $routes->add('proc/(:segment)' , 'Schoolmealmonthly::proc/$1');
    $routes->add('ajaxmore','Schoolmealmonthly::morelist');
});

// 앨범
$routes->group('/album' ,  ['filter' => 'isLoggedIn'] , function($routes){
    $routes->add('','Album::index');
    $routes->add('write','Album::write');
    $routes->add('(:segment)/edit','Album::edit/$1');
    $routes->add('ajax/(:segment)','Album::func/$1');
    $routes->add('proc/(:segment)','Album::func/$1');
    $routes->add('(:segment)','Album::detail/$1');
});


// 교육계획안
$routes->group('/eduPlan' ,  ['filter' => 'isLoggedIn'] , function($routes){
    $routes->add('','EduPlan::index');
    $routes->add('weekly', "EduPlan::weeklyList");
    $routes->add('weekly/write','EduPlan::weeklywrite');
    $routes->add('weekly/(:segment)/edit','EduPlan::weeklywrite/$1');
    $routes->add('weekly/deleteProc','EduPlan::deleteProc');
    $routes->add('monthly', "EduPlan::monthlyList");
    $routes->add('monthly/write','EduPlan::monthlywrite');
    $routes->add('monthly/(:segment)/edit','EduPlan::monthlywrite/$1');
    $routes->add('monthly/deleteProc','EduPlan::deleteProc');
    // $routes->add('monthly/(:segment)/edit','EduPlan::edit');
    $routes->add('(:segment)/edit','EduPlan::edit/$1');
    $routes->add('ajax/(:segment)','EduPlan::func/$1');
    $routes->add('proc/(:segment)','EduPlan::func/$1');
    $routes->add('(:segment)','EduPlan::detail/$1');
});

// 투약의료서
$routes->group('/medicine' ,  ['filter' => 'isLoggedIn'] , function($routes){
    $routes->add('','Medicine::index');
    $routes->add('write','Medicine::write');
    $routes->add('(:segment)/edit','Medicine::edit/$1');
    $routes->add('ajax/(:segment)','Medicine::func/$1');
    $routes->add('proc/(:segment)','Medicine::func/$1');
    $routes->add('(:num)','Medicine::detail/$1');
});

// 귀가 동의서 homeCommingConsent
$routes->group('/homeCommingConsent' ,  ['filter' => 'isLoggedIn'] , function($routes){
    $routes->add('','HomeCommingConsent::index');
    $routes->add('write','HomeCommingConsent::forms');
    $routes->add('(:segment)/edit','HomeCommingConsent::forms/$1');
    $routes->add('ajax/(:segment)','HomeCommingConsent::func/$1');
    $routes->add('proc/(:segment)','HomeCommingConsent::func/$1');
    $routes->add('(:num)','HomeCommingConsent::detail/$1');
});

// 공지사항
// $routes->group('/appBoard' ,  ['filter' => 'isLoggedIn'] , function($routes){
$routes->group('/appBoard' ,  function($routes){    
    $routes->add('','AppBoard::index');
    $routes->add('write','AppBoard::forms');
    $routes->add('(:segment)/edit','AppBoard::forms/$1');
    $routes->add('ajax/(:segment)','AppBoard::func/$1');
    $routes->add('proc/(:segment)','AppBoard::func/$1');
    $routes->add('(:num)','AppBoard::detail/$1');
});

$routes->add('/fileupload' , 'FileUpload::fileUpload');
$routes->add('/removeFile' , 'FileUpload::removeFile');
$routes->add('/photoview','FileUpload::photoView');

$routes->add('/payment' , 'Payment::index');
$routes->add('/payment/more' , 'Payment::morelist');

// Would execute the show404 method of the App\Errors class
$routes->set404Override('App\Errors::show404');

// Will display a custom view
$routes->set404Override(function()
{
    echo view('errors/html/error_template');
});

// api 호출 이동
$routes->add('/redirect', 'Login::redirectReqValidToken');

// 
$routes->cli('/allbooks/order' , "AllBooks::order");