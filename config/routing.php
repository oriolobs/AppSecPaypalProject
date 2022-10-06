<?php

use \Pw\SlimApp\Controller\AccountSummaryController;
use \Pw\SlimApp\Controller\AccountController;
use Pw\SlimApp\Controller\DashboardController;
use \Pw\SlimApp\Controller\HomeController;
use Pw\SlimApp\Controller\LoadMoneyController;
use Pw\SlimApp\Controller\ProfileController;
use Pw\SlimApp\Controller\RequestController;
use Pw\SlimApp\Controller\SignOutController;
use Pw\SlimApp\Controller\TransactionController;
use \Pw\SlimApp\Controller\VisitsController;
use \Pw\SlimApp\Controller\CookieMonsterController;
use \Pw\SlimApp\Controller\FlashController;
use \Pw\SlimApp\Controller\PostUserController;
use \Pw\SlimApp\Controller\SignUpController;
use \Pw\SlimApp\Controller\SignInController;
use \Pw\SlimApp\Controller\SecurityController;
use \Pw\SlimApp\Controller\MoneyController;
use \Pw\SlimApp\Middleware\StartSessionMiddleware;
use Pw\SlimApp\Model\User;

$app->add(StartSessionMiddleware::class);

$app->get(
    '/',
    HomeController::class . ":showHomePage"
)->setName('home');

$app->get(
    '/sign-up',
    SignUpController::class . ":showSignUpPage"
)->setName('signup');

$app->get(
    '/visits',
    VisitsController::class . ":showVisits"
)->setName('visits');

$app->get(
    '/cookies',
    CookieMonsterController::class . ":showAdvice"
)->setName('cookies');

$app->get(
    '/flash',
    FlashController::class . ":addMessage"
)->setName('flash');

$app->post(
    '/sign-up',
    SignUpController::class . ":postSignUp"
)->setName('post_sign_up');

$app->get(
    '/activate',
    SignUpController::class . ":getToken"
)->setName('getToken');

$app->post(
    '/sign-in',
    SignInController::class . ":check"
)->setName('sign_in');

$app->get(
    '/sign-in',
    SignInController::class . ":main"
)->setName('sign_in');

$app->post(
    '/sign-out',
    SignOutController::class . ":logout"
)->setName('sign_out');

$app->get(
    '/profile',
    ProfileController::class . ":showProfilePage"
)->setName('profile');

$app->post(
    '/profile',
    ProfileController::class . ":postUploadChangeUser"
)->setName('profile');

$app->get(
    '/account/summary',
    DashboardController::class . ":showDashboardPage"
)->setName('dashboard');

$app->post(
    '/account/summary',
    DashboardController::class . ":postDashboardPage"
)->setName('dashboard');

$app->get(
    '/activation',
    SignUpController::class . ":showActivation"
)->setName('activation');

$app->get(
    '/profile/security',
    SecurityController::class . ":showSecurityPage"
)->setName('security');

$app->post(
    '/profile/security',
    SecurityController::class . ":postChangePassword"
)->setName('post_security');

$app->get(
    '/account/money/send',
    MoneyController::class . ":showSendMoney"
)->setName('get_send');

$app->post(
    '/account/money/send',
    MoneyController::class . ":postSendMoney"
)->setName('post_send');

$app->get(
    '/account/bank-account',
    LoadMoneyController::class . ":showBankAccount"
)->setName('get_bank-account');

$app->post(
    '/account/bank-account',
    LoadMoneyController::class . ":postBankAccount"
)->setName('post_bank_account');

$app->post(
    '/account/bank-account/load',
    LoadMoneyController::class . ":loadMoney"
)->setName('load_money');

$app->get(
    '/account/money/requests',
    RequestController::class . ":showRequestForm"
)->setName('request_get');

$app->post(
    '/account/money/requests',
    RequestController::class . ":postRequest"
)->setName('request_post');

$app->get(
    '/account/money/requests/pending',
    RequestController::class . ":showPendingRequests"
)->setName('unpaid_requests_get');

$app->get(
    '/account/money/requests/{id}/accept',
    RequestController::class . ":acceptRequest"
)->setName('accept_request');

$app->get(
    '/account',
    AccountController::class . ":showAccountPage"
)->setName('account');

$app->get(
    '/account/money',
    AccountSummaryController::class . ":showAccountSummaryPage"
)->setName('money');

$app->get(
    '/account/transactions',
    TransactionController::class . ":showAllTransactions"
)->setName('showTransactions');
