#!/usr/bin/env php
<?php

if (php_sapi_name() !== 'cli') {
    exit;
}

require __DIR__ . '/vendor/autoload.php';

use HeadlessChromium\BrowserFactory;
use Minicli\App;
use Minicli\Command\CommandCall;

$app = new App();
$app->setSignature('./minicli pdf url=https://takeascreenshotofthispage.com');

$app->registerCommand('pdf', function(CommandCall $input) {
    try {
        $browserFactory = new BrowserFactory();
        $browserFactory->addOptions(['noSandbox' => true]);

        // starts headless chrome
        $browser = $browserFactory->createBrowser();

        // creates a new page and navigate to an URL
        $page = $browser->createPage();
        $page->navigate($input->getParam('url'))->waitForNavigation();

        // get page title
        $pageTitle = $page->evaluate('document.title')->getReturnValue();
        echo $pageTitle . PHP_EOL;

        // screenshot - Say "Cheese"! 😄
        $page->screenshot()->saveToFile('./test.png');
        echo 'printed screenshot';

        // pdf
        $page->pdf(['printBackground' => false])->saveToFile('./test.pdf');

        echo 'printed pdf';
    } catch (\Exception $e) {
        echo $e->getMessage();
    } finally {
        // bye
        $browser->close();
    }
});

$app->runCommand($argv);
