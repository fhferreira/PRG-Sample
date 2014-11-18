<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\SessionServiceProvider());

// Routes
$app->get('/signup', function () use ($app) {
    return $app['twig']->render('signup.twig');
});

$app->post('/process_signup', function (Request $request) use ($app) {
    $app['session']->set('username', $request->get('username'));
    $app['session']->set('password', md5($request->get('password')));

    return $app->redirect('/index.php/signup_confirmation', 303);
});

$app->get('/signup_confirmation', function (Request $request) use ($app) {
    return $app['twig']->render(
        'signup_confirmation.twig',
        [
            'username' => $app['session']->get('username'),
            'password' => $app['session']->get('password')
        ]
    );
});

$app->post('/process_confirmation', function (Request $request) use ($app) {
    $app['session']->set('confirmed', true);

    return $app->redirect('/index.php/success', 303);
});

$app->get('/success', function (Request $request) use ($app) {
    if (! $app['session']->get('confirmed')) {
        return $app->redirect('/index.php/signup');
    }

    return $app['twig']->render(
        'success.twig',
        [
            'username'  => $app['session']->get('username')
        ]
    );
});

$app->run();
