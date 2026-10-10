<?php
/** Standalone smoke test: does not need Yii installed (minimal doubles). */
namespace larikmc\forms {
    class Module {
        public ?string $contextSigningKey = 'test-secret-with-sufficient-entropy';
        public int $contextTtl = 2592000;
    }
}
namespace {
    class Yii { public static object $app; }
    final class FakeSecurity {
        public function hashData(string $data, string $key): string {
            return hash_hmac('sha256', $data, $key, true) . $data;
        }
        public function validateData(string $value, string $key): string|false {
            if (strlen($value) < 32) return false;
            $hash = substr($value, 0, 32);
            $payload = substr($value, 32);
            return hash_equals($hash, hash_hmac('sha256', $payload, $key, true)) ? $payload : false;
        }
    }
    final class FakeRequest {
        public string $cookieValidationKey = 'fallback-secret';
        public function getAbsoluteUrl(): string { return 'https://data.soft2soft.ru/catalog/furniture/?secret=must-not-be-stored'; }
        public function getHostInfo(): string { return 'https://data.soft2soft.ru'; }
    }
    class FakeApp {
        public FakeSecurity $security;
        public FakeRequest $request;
        public object $view;
        public object $controller;
        public function __construct() {
            $this->security = new FakeSecurity();
            $this->request = new FakeRequest();
            $this->view = (object)['title'=>'База производителей мебели'];
            $this->controller = (object)['route'=>'catalog/view'];
        }
        public function get(string $component, bool $throw = true): mixed {
            return $this->{$component} ?? null;
        }
    }
    \Yii::$app = new FakeApp();
    require __DIR__ . '/../src/helpers/SubmissionContextHelper.php';
    $helper = \larikmc\forms\helpers\SubmissionContextHelper::class;
    $module = new \larikmc\forms\Module();
    $token = $helper::issue(12, ['object_type'=>'database','object_id'=>42,'object_title'=>'Мебель','bad name'=>'ignored'], true, $module);
    $valid = $helper::verify($token,12,$module);
    assert($valid['page_url'] === 'https://data.soft2soft.ru/catalog/furniture/');
    assert($valid['page_title'] === 'База производителей мебели');
    assert($valid['data']['object_id'] === 42);
    assert($valid['data']['object_title'] === 'Мебель');
    assert(!isset($valid['data']['bad name']));
    assert($helper::verify($token,13,$module) === []); // Wrong form.
    $last = substr($token, -1);
    $invalid = substr($token,0,-1) . ($last === 'A' ? 'B' : 'A');
    assert($helper::verify($invalid,12,$module) === []); // Forged or corrupted.
    $module->contextSigningKey = 'another-key';
    assert($helper::verify($token,12,$module) === []);
    $module->contextSigningKey = 'test-secret-with-sufficient-entropy';
    $module->contextTtl = -1; // Disables TTL as documented (for compatibility checks).
    assert($helper::verify($token,12,$module) !== []);
    $fallback = new \larikmc\forms\Module();
    $fallback->contextSigningKey = null;
    assert($helper::verify($helper::issue(12, [], true, $fallback),12,$fallback) !== []);
    echo "PASS: signed page context, form binding, tamper resistance, fallback key, URL stripping\n";
}
