<?php

declare(strict_types=1);

namespace larikmc\forms\helpers;

use larikmc\forms\Module;

/**
 * Signs the page/object context at render time and verifies it at submit time.
 * Context is PUBLIC metadata, never a source of authorization decisions.
 */
final class SubmissionContextHelper
{
    private const VERSION = 1;
    private const MAX_TOKEN_LENGTH = 16384;
    private const MAX_JSON_LENGTH = 8192;
    private const MAX_CONTEXT_FIELDS = 20;

    public static function issue(int $formId, array $context, bool $capturePageContext, Module $module): string
    {
        $key = self::signingKey($module);
        if ($formId <= 0 || $key === '') {
            return '';
        }

        $pageUrl = '';
        $pageTitle = '';
        $route = '';
        if ($capturePageContext) {
            $request = \Yii::$app->request;
            // Do not copy query strings: they may contain sessions, tokens or personal data.
            $path = parse_url($request->getAbsoluteUrl(), PHP_URL_PATH);
            $pageUrl = self::cleanString(rtrim($request->getHostInfo(), '/') . ($path ?: '/'), 1024);
            $pageTitle = self::cleanString((string) (\Yii::$app->view->title ?? ''), 255);
            $route = self::cleanString((string) (\Yii::$app->controller?->route ?? ''), 255);
        }

        $payload = [
            'v' => self::VERSION,
            'form_id' => $formId,
            'iat' => time(),
            'page_url' => $pageUrl,
            'page_title' => $pageTitle,
            'route' => $route,
            'data' => self::normalize($context),
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (!is_string($json) || strlen($json) > self::MAX_JSON_LENGTH) {
            return '';
        }

        return rtrim(strtr(base64_encode(\Yii::$app->security->hashData($json, $key)), '+/', '-_'), '=');
    }

    /** @return array{page_url:string,page_title:string,route:string,data:array<string,int|float|string|bool>} */
    public static function verify(string $token, int $formId, Module $module): array
    {
        $key = self::signingKey($module);
        if ($key === '' || $token === '' || strlen($token) > self::MAX_TOKEN_LENGTH || !preg_match('/^[A-Za-z0-9_-]+$/D', $token)) {
            return [];
        }

        $binary = base64_decode(strtr($token, '-_', '+/') . str_repeat('=', (4 - strlen($token) % 4) % 4), true);
        if ($binary === false) {
            return [];
        }
        $json = \Yii::$app->security->validateData($binary, $key);
        if (!is_string($json) || strlen($json) > self::MAX_JSON_LENGTH) {
            return [];
        }

        $payload = json_decode($json, true);
        if (!is_array($payload)
            || ($payload['v'] ?? null) !== self::VERSION
            || ($payload['form_id'] ?? null) !== $formId
            || !is_int($payload['iat'] ?? null)) {
            return [];
        }
        $now = time();
        if ($payload['iat'] > $now + 300 || ($module->contextTtl > 0 && $now - $payload['iat'] > $module->contextTtl)) {
            return [];
        }

        $url = self::cleanString((string) ($payload['page_url'] ?? ''), 1024);
        if ($url !== '' && !preg_match('~^https?://~i', $url)) {
            $url = '';
        }
        return [
            'page_url' => $url,
            'page_title' => self::cleanString((string) ($payload['page_title'] ?? ''), 255),
            'route' => self::cleanString((string) ($payload['route'] ?? ''), 255),
            'data' => self::normalize(is_array($payload['data'] ?? null) ? $payload['data'] : []),
        ];
    }

    /** @return array<string, int|float|string|bool> */
    private static function normalize(array $source): array
    {
        $normalized = [];
        foreach ($source as $key => $value) {
            if (count($normalized) >= self::MAX_CONTEXT_FIELDS) {
                break;
            }
            if (!is_string($key) || !preg_match('/^[a-zA-Z][a-zA-Z0-9_.-]{0,63}$/D', $key)) {
                continue;
            }
            if (is_string($value)) {
                $value = self::cleanString($value, 512);
            }
            if (is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
                $normalized[$key] = $value;
            }
        }
        return $normalized;
    }

    /** @return array<string, string> */
    public static function displayRows(array $context): array
    {
        $rows = [];
        foreach (['page_title' => 'Заголовок страницы', 'page_url' => 'URL страницы', 'route' => 'Маршрут Yii2'] as $key => $label) {
            if (!empty($context[$key]) && is_scalar($context[$key])) {
                $rows[$label] = (string) $context[$key];
            }
        }
        foreach (self::normalize(is_array($context['data'] ?? null) ? $context['data'] : []) as $key => $value) {
            $rows['Контекст: ' . $key] = is_bool($value) ? ($value ? 'Да' : 'Нет') : (string) $value;
        }
        return $rows;
    }

    private static function cleanString(string $value, int $length): string
    {
        $value = trim(preg_replace('/[\x00-\x1F\x7F]/u', ' ', $value) ?? '');
        return function_exists('mb_substr') ? mb_substr($value, 0, $length) : substr($value, 0, $length);
    }

    private static function signingKey(Module $module): string
    {
        if ($module->contextSigningKey !== null && $module->contextSigningKey !== '') {
            return $module->contextSigningKey;
        }
        // Existing Yii2 web apps usually have this key configured for CSRF cookies.
        $request = \Yii::$app->get('request', false);
        return is_object($request) ? (string) ($request->cookieValidationKey ?? '') : '';
    }
}
