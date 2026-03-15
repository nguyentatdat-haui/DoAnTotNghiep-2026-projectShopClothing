<?php

namespace App\Controllers;
use App\Repositories\LocationRepository;
use App\Repositories\FooterRepository;

abstract class BaseController
{
    protected $data = [];
    
    public function __construct()
    {
        // Share common data for all views (available in layouts and includes)
        // 1) City info (used by layout and footer)
        $cityInfo = function_exists('getCityInfo') ? getCityInfo() : null;
        $cityNameJp = $cityInfo['city_name_japan'] ?? (env('DEFAULT_CITY_NAME_JP') ?? '');

        // Provide both keys used in views: `city_name` (layout, homepage) and `city` (footer block)
        $this->data['city_name'] = $cityNameJp;
        $this->data['city'] = $cityNameJp;

        // 2) Footer domains grouped by `title_jp` so blocks/footer.php can iterate consistently
        try {
            $footerRepository = new FooterRepository();
            $rows = $footerRepository->getActiveWithGroupInfo();
            $dataDomain = [];
            foreach ($rows as $row) {
                $key = $row['title_jp'] ?? '';
                if (!isset($dataDomain[$key])) {
                    $dataDomain[$key] = ['domains' => []];
                }
                $dataDomain[$key]['domains'][] = $row;
            }
            $this->data['data_domain'] = $dataDomain;
        } catch (\Throwable $e) {
            // Fail-safe: don't break rendering if DB not ready; footer will simply render empty
            $this->data['data_domain'] = [];
        }

        // Cart count for header
        $this->data['cart_count'] = 0;
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $this->data['cart_count'] += (int)($item['quantity'] ?? 0);
            }
        }
    }

    protected function view($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        return \View::render($view, $data);
    }

    protected function json($data, $status = 200)
    {
        return \View::json($data, $status);
    }

    protected function redirect($url, $status = 302)
    {
        return \View::redirect($url, $status);
    }

    protected function back()
    {
        return \View::back();
    }

    protected function withInput($data = null)
    {
        return \View::withInput($data);
    }

    protected function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $fieldRules = explode('|', $rule);
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $singleRule) {
                if (strpos($singleRule, ':') !== false) {
                    list($ruleName, $ruleValue) = explode(':', $singleRule, 2);
                } else {
                    $ruleName = $singleRule;
                    $ruleValue = null;
                }
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = "The {$field} field is required.";
                        }
                        break;
                        
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "The {$field} field must be a valid email address.";
                        }
                        break;
                        
                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field][] = "The {$field} field must be at least {$ruleValue} characters.";
                        }
                        break;
                        
                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field][] = "The {$field} field must not exceed {$ruleValue} characters.";
                        }
                        break;
                        
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = "The {$field} field must be numeric.";
                        }
                        break;
                        
                    case 'unique':
                        // This would need to be implemented based on your database structure
                        break;
                }
            }
        }
        
        return $errors;
    }

    protected function hasErrors($errors)
    {
        return !empty($errors);
    }

    protected function getFirstError($errors, $field)
    {
        return $errors[$field][0] ?? null;
    }

    protected function getAllErrors($errors)
    {
        $allErrors = [];
        foreach ($errors as $field => $fieldErrors) {
            $allErrors = array_merge($allErrors, $fieldErrors);
        }
        return $allErrors;
    }

    protected function flash($key, $value = null)
    {
        if ($value === null) {
            $message = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        
        $_SESSION['flash'][$key] = $value;
    }

    protected function flashMessage($message, $type = 'info')
    {
        $this->flash('message', $message);
        $this->flash('message_type', $type);
    }

    protected function success($message)
    {
        $this->flashMessage($message, 'success');
    }

    protected function error($message)
    {
        $this->flashMessage($message, 'error');
    }

    protected function warning($message)
    {
        $this->flashMessage($message, 'warning');
    }

    protected function info($message)
    {
        $this->flashMessage($message, 'info');
    }

    protected function getFlashMessage()
    {
        return [
            'message' => $this->flash('message'),
            'type' => $this->flash('message_type')
        ];
    }

    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    protected function getRequestData()
    {
        $method = $this->getRequestMethod();
        
        switch ($method) {
            case 'GET':
                return $_GET;
            case 'POST':
                return $_POST;
            case 'PUT':
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $data);
                return $data;
            default:
                return [];
        }
    }

    protected function getRequestHeader($name)
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$name] ?? null;
    }

    protected function setResponseHeader($name, $value)
    {
        header("{$name}: {$value}");
    }

    protected function setStatusCode($code)
    {
        http_response_code($code);
    }
}
