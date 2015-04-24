EmailReader
===========

[![Build Status](https://img.shields.io/travis/smart-io/email-reader/master.svg?style=flat)](https://travis-ci.org/smart-io/email-reader)
[![Latest Stable Version](http://img.shields.io/packagist/v/smart-io/email-reader.svg?style=flat)](https://packagist.org/packages/smart-io/email-reader)
[![Total Downloads](https://img.shields.io/packagist/dm/smart-io/email-reader.svg?style=flat)](https://packagist.org/packages/smart-io/email-reader)
[![License](https://img.shields.io/packagist/l/smart-io/email-reader.svg?style=flat)](https://packagist.org/packages/smart-io/email-reader)

This library will read emails of a specific mailbox, will parse them and finally, dispatch them to handlers.
[How to install](#installation)

This library is used to read email (you can inject your own by implementing the DriverInterface) :
https://github.com/tedious/Fetch

Inside our Fetch driver, we used this library to parse the last email reply :
https://github.com/willdurand/EmailReplyParser

##How is it working ?

1. This library contains a command which is gonna check all new emails in your mailbox and give them to gearman. 1 new email = 1 new task on gearman.
2. The gearman job will read the email, parse it and then, dispatch it to your application.
3. Once gearman is finished with this email, the email is move to another folder to mark it as processed.

##configuration
| Config            | Default | Description                                          |
|-------------------|---------|------------------------------------------------------|
| domain            |         | Address of your mail serveur  (eg. imap.gmail.com)   |
| port              | 143     | SSL port is usually 993, normal is 143               |
| service           | Imap    | Imap or Pop                                          |
| username          |         | You email username, most of the time it's your email |
| password          |         | Your email password                                  |
| main mailbox      |         | The mailbox where this library will read new emails  |
| processed mailbox |         | Where to move the emails once processed              |

###example :

```php
use Smart\EmailReader\Config\EmailServerConfig;

$configs = (EmailServerConfig())
    ->setDomain('imap.gmail.com')
    ->setPort(993)
    ->setService(EmailServerConfig::IMAP)
    ->setUsername('my.email@gmail.com')
    ->setPassword('my_plain_text_password')
    ->setMainMailbox('INDEX')
    ->setProcessedMailbox('Processed');
```

##Dispatch

This library come with a dispatcher base class which allows you to handle different kind of email. The handlers on the dispatcher work like a router on the email subject.

###example

 _Imagine we have support ticket system on our app. We want our customer to be able to reply directly to the email to add their reply in our ticket system._

```php
use Smart\EmailReader\Dispatcher\Dispatcher;

class DispatcherApp extends Dispatcher
{
    public function __construct()
    {
        $this->addDispatcher(new SupportEmailDispatcher());
        //you can add as many dispatchers as you want....
    }
}
```

```php
use Smart\EmailReader\Dispatcher\Dispatcher;
use Smart\EmailReader\EmailEntity;

class SupportEmailDispatcher extends Dispatcher
{
    public function __construct()
    {
        $this->addHandler('#\[support\-(?<id>[0-9]+)\]#i', [$this, 'handleNewReply']);
    }
    
    public function handleNewReply($matches, EmailEntity $email)
    {
        $ticketId = isset($matches['id']) ? (int)$matches['id'] : null;
        $newReply = $email->getLastReply();
        //your logic here...
    }
}
```

###About dispatchers :

 - The dispatcher don't stop at the first match, different regexs can overlap themself
 - If you return false on a dispatcher, the dispatcher will stop there and complete the task
 - Currently, you can only dispatch the email based on the subject

##Installation

###Dependencies

This should be place in your app container

```php
$emailReader = new Fetch(
    new EmailServerConfig() //check the configuration section
); 

$emailLoggerLogger = new EmailReaderLogger(
    '/log/path_to_your_log_file'
);
```

###Gearman job

We use Sinergi gearman : https://github.com/sinergi/gearman

```php
//add this in sinergi gearman :

new EmailReaderDispatchJob(
    $emailReader,
    new DispatcherApp(), //your own dispatcher
    $emailLoggerLogger
);
```

###Register Command

We use symfony console : https://github.com/symfony/Console

```php

$consoleApp = new ConsoleApplication();
$gearmanDispatcher = '...'; //get your gearman dispatcher

$consoleApp->add(
    new EmailReaderSendCommand(
        $gearmanDispatcher,
        $emailReader
    )
);
```

Finally, you just need to add a cron on that command every few minutes to read incoming email and dispatch them into your application.
