<?php

namespace EmailCheckBumm;

use EmailCheckBumm\Component\CURLService;


class EmailCheckBummApi
{

    const URL = "http://onlineemailvalidation.com/api/";

    const SYNTAX_CHECK = "syntax";
    const DOMAIN_CHECK = "domain";
    const MX_CHECK = "mxrecord";
    const TEMPMAIL_CHECK = "tempmail";
    const MAILBOX_CHECK = "mailbox";

    protected static $TEST_NAMES = array(
        self::SYNTAX_CHECK => "Syntax_check",
        self::DOMAIN_CHECK => "Domain_check",
        self::MX_CHECK => "MX check",
        self::TEMPMAIL_CHECK => "Tempmail check",
        self::MAILBOX_CHECK  => "Mailbox check"
    );

    protected $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function testEmail($email, $tests)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException(sprintf("%s is not a valid email address!", $email));
        }
        $_tests = array();
        if(is_array($tests))
        {
            foreach ($tests as $t) {
                if(!in_array($t, self::$TEST_NAMES)) {
                    throw new \InvalidArgumentException(sprintf("%s is not a valid test name!", $t));
                }
                $_tests[] = $t;
            }
        } else {
            if(!in_array((string)$t, self::$TEST_NAMES)) {
                throw new \InvalidArgumentException(sprintf("%s is not a valid test name!", $t));
            }
            $_tests[] = $t;
        }

        $payload = json_encode(
            array(
                "apikey" => $this->apiKey,
                "email" => $email,
                "tests" => $_tests
            )
        );

        $apiUrl = self::URL."test";

        $response = CURLService::post(
            $apiUrl,
            array("data" => $payload)
        );

        return $response->getContent();
    }

    public function testDomain($domain, $tests)
    {
        if (!preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $domain)) {
            throw new \InvalidArgumentException(sprintf("%s is not a valid domain name!", $domain));
        }
        $_tests = array();
        if(is_array($tests))
        {
            foreach ($tests as $t) {
                if(!in_array($t, self::$TEST_NAMES)) {
                    throw new \InvalidArgumentException(sprintf("%s is not a valid test name!", $t));
                }
                $_tests[] = $t;
            }
        } else {
            if(!in_array((string)$t, self::$TEST_NAMES)) {
                throw new \InvalidArgumentException(sprintf("%s is not a valid test name!", $t));
            }
            $_tests[] = $t;
        }

        $payload = json_encode(
            array(
                "apikey" => $this->apiKey,
                "domain" => $domain,
                "tests" => $_tests
            )
        );

        $apiUrl = self::URL."test";

        $response = CURLService::post(
            $apiUrl,
            array("data" => $payload)
        );

        return $response->getContent();
    }
}