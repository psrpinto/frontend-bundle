<?php

namespace Rj\FrontendBundle\Util;

class Util
{
    public static function hasQuestionHelper()
    {
        return class_exists('Symfony\Component\Console\Question\Question');
    }

    public static function containsNotUrl($subject)
    {
        return static::containsUrl($subject, true);
    }

    public static function containsUrl($subject, $negate = false)
    {
        if (is_string($subject)) {
            $subject = array($subject);
        }

        $flags = $negate ? PREG_GREP_INVERT : null;

        $result = preg_grep('|^(https?:)?//|', $subject, $flags);

        return !empty($result);
    }
}
