<?php

namespace AppBundle\Twig;

class EmailObfuscatorExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('obfuscateEmailAddress', [$this, 'obfuscateEmailAddress'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('obfuscateEmailLink',    [$this, 'obfuscateEmailLink'],    ['is_safe' => ['html']])
        ];
    }

    function obfuscateEmailAddress($email)
    {
        $alwaysEncode = ['.', ':', '@'];

        $result = '';

        // Encode string using oct and hex character codes
        for ($i = 0; $i < strlen($email); $i++) {
            // Encode 25% of characters including several that always should be encoded
            if (in_array($email[ $i ], $alwaysEncode) || mt_rand(1, 100) < 25) {
                if (mt_rand(0, 1)) {
                    $result .= '&#' . ord($email[ $i ]) . ';';
                } else {
                    $result .= '&#x' . dechex(ord($email[ $i ])) . ';';
                }
            } else {
                $result .= $email[ $i ];
            }
        }

        return $result;
    }

    function obfuscateEmailLink($email, $params = [])
    {
        if (!is_array($params)) {
            $params = [];
        }

        // Tell search engines to ignore obfuscated uri
        if (!isset($params['rel'])) {
            $params['rel'] = 'nofollow';
        }

        $neverEncode = ['.', '@', '+']; // Don't encode those as not fully supported by IE & Chrome

        $urlEncodedEmail = '';
        for ($i = 0; $i < strlen($email); $i++) {
            // Encode 25% of characters
            if (!in_array($email[ $i ], $neverEncode) && mt_rand(1, 100) < 25) {
                $charCode        = ord($email[ $i ]);
                $urlEncodedEmail .= '%';
                $urlEncodedEmail .= dechex(($charCode >> 4) & 0xF);
                $urlEncodedEmail .= dechex($charCode & 0xF);
            } else {
                $urlEncodedEmail .= $email[ $i ];
            }
        }

        $obfuscatedEmail    = $this->obfuscateEmailAddress($email);
        $obfuscatedEmailUrl = $this->obfuscateEmailAddress('mailto:' . $urlEncodedEmail);

        $attribs = [];
        foreach ($params as $param => $value) {
            $attribs[] = $param . '="' . htmlspecialchars($value) . '"';
        }

        return sprintf("<a href=\"%s\" %s>%s</a>", $obfuscatedEmailUrl, join(' ', $attribs), $obfuscatedEmail);
    }

    public function getName()
    {
        return 'obfuscate_email_extension';
    }
}