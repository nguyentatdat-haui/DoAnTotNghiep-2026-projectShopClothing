<?php

/**
 * Error Handler Class
 * Handles errors and exceptions with beautiful error pages
 */
class ErrorHandler
{
    private static $isDebug = false;
    
    public static function init()
    {
        self::$isDebug = Config::get('APP_DEBUG', true);
        
        // Set error handler
        set_error_handler([self::class, 'handleError']);
        
        // Set exception handler
        set_exception_handler([self::class, 'handleException']);
        
        // Set shutdown handler for fatal errors
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    /**
     * Handle PHP errors
     */
    public static function handleError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $error = [
            'type' => 'Error',
            'severity' => self::getSeverityName($severity),
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];
        
        // Always log errors regardless of debug mode
        self::logError($error);
        
        // Only exit for fatal errors, not warnings or notices
        if (in_array($severity, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            self::displayError($error);
        } else {
            // For warnings and notices, only display if debug mode is ON
            if (self::$isDebug) {
                self::displayInlineError($error);
            }
            // If debug is OFF, warnings/notices are only logged, not displayed
        }
        
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     */
    public static function handleException($exception)
    {
        $error = [
            'type' => 'Exception',
            'severity' => 'Fatal',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace()
        ];
        
        self::logError($error);
        self::displayError($error);
    }
    
    /**
     * Handle fatal errors
     */
    public static function handleShutdown()
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $errorData = [
                'type' => 'Fatal Error',
                'severity' => 'Fatal',
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
                'trace' => []
            ];
            
            self::logError($errorData);
            self::displayError($errorData);
        }
    }
    
    /**
     * Log error to file
     */
    private static function logError($error)
    {
        $logFile = __DIR__ . '/../../storage/logs/error.log';
        $timestamp = date('Y-m-d H:i:s');
        
        $logMessage = "[{$timestamp}] {$error['type']}: {$error['message']} in {$error['file']} on line {$error['line']}\n";
        $logMessage .= "Stack trace:\n" . self::formatStackTrace($error['trace']) . "\n";
        $logMessage .= str_repeat('-', 80) . "\n";
        
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Display error page
     */
    private static function displayError($error)
    {
        // Clear any previous output
        if (ob_get_level()) {
            ob_clean();
        }
        
        http_response_code(500);
        
        if (self::$isDebug) {
            self::displayDebugError($error);
        } else {
            self::displayProductionError();
        }
        
        exit;
    }
    
    /**
     * Display debug error page (detailed)
     */
    private static function displayDebugError($error)
    {
        $fileContent = '';
        $lineNumber = $error['line'];
        $file = $error['file'];
        
        // Get file content around the error line
        if (file_exists($file)) {
            $lines = file($file);
            $start = max(0, $lineNumber - 10);
            $end = min(count($lines), $lineNumber + 10);
            
            for ($i = $start; $i < $end; $i++) {
                $line = $lines[$i];
                $lineNum = $i + 1;
                $isErrorLine = ($lineNum == $lineNumber);
                
                $fileContent .= sprintf(
                    '<div class="code-line %s"><span class="line-number">%d</span><span class="line-content">%s</span></div>',
                    $isErrorLine ? 'error-line' : '',
                    $lineNum,
                    htmlspecialchars($line)
                );
            }
        }
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Application Error</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    background: #f8f9fa; 
                    color: #333; 
                    line-height: 1.6;
                }
                .error-container { 
                    max-width: 1200px; 
                    margin: 20px auto; 
                    padding: 20px; 
                }
                .error-header { 
                    background: #dc3545; 
                    color: white; 
                    padding: 20px; 
                    border-radius: 8px 8px 0 0; 
                    margin-bottom: 0;
                }
                .error-header h1 { font-size: 24px; margin-bottom: 10px; }
                .error-header p { font-size: 16px; opacity: 0.9; }
                .error-content { 
                    background: white; 
                    border: 1px solid #dee2e6; 
                    border-top: none; 
                    border-radius: 0 0 8px 8px; 
                    padding: 20px; 
                }
                .error-info { 
                    background: #f8f9fa; 
                    border-left: 4px solid #dc3545; 
                    padding: 15px; 
                    margin-bottom: 20px; 
                    border-radius: 4px;
                }
                .error-info h3 { color: #dc3545; margin-bottom: 10px; }
                .error-info p { margin: 5px 0; }
                .file-path { 
                    background: #e9ecef; 
                    padding: 8px 12px; 
                    border-radius: 4px; 
                    font-family: monospace; 
                    font-size: 14px;
                    word-break: break-all;
                }
                .code-block { 
                    background: #f8f9fa; 
                    border: 1px solid #dee2e6; 
                    border-radius: 4px; 
                    margin: 15px 0; 
                    overflow-x: auto;
                }
                .code-line { 
                    display: flex; 
                    padding: 4px 0; 
                    border-bottom: 1px solid #e9ecef;
                }
                .code-line:last-child { border-bottom: none; }
                .code-line.error-line { 
                    background: #fff5f5; 
                    border-left: 4px solid #dc3545; 
                }
                .line-number { 
                    background: #e9ecef; 
                    padding: 4px 12px; 
                    min-width: 60px; 
                    text-align: right; 
                    font-family: monospace; 
                    font-size: 12px; 
                    color: #6c757d;
                }
                .error-line .line-number { 
                    background: #dc3545; 
                    color: white; 
                }
                .line-content { 
                    padding: 4px 12px; 
                    font-family: monospace; 
                    font-size: 14px; 
                    flex: 1;
                }
                .stack-trace { 
                    background: #f8f9fa; 
                    border: 1px solid #dee2e6; 
                    border-radius: 4px; 
                    padding: 15px; 
                    margin-top: 20px;
                }
                .stack-trace h3 { color: #495057; margin-bottom: 10px; }
                .trace-item { 
                    background: white; 
                    border: 1px solid #dee2e6; 
                    border-radius: 4px; 
                    padding: 10px; 
                    margin: 5px 0; 
                    font-family: monospace; 
                    font-size: 13px;
                }
                .trace-file { color: #6c757d; }
                .trace-line { color: #dc3545; font-weight: bold; }
                .trace-function { color: #007bff; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-header">
                    <h1>🚨 Application Error</h1>
                    <p><?= htmlspecialchars($error['type']) ?>: <?= htmlspecialchars($error['message']) ?></p>
                </div>
                
                <div class="error-content">
                    <div class="error-info">
                        <h3>Error Details</h3>
                        <p><strong>Type:</strong> <?= htmlspecialchars($error['type']) ?></p>
                        <p><strong>Severity:</strong> <?= htmlspecialchars($error['severity']) ?></p>
                        <p><strong>Message:</strong> <?= htmlspecialchars($error['message']) ?></p>
                        <p><strong>File:</strong></p>
                        <div class="file-path"><?= htmlspecialchars($error['file']) ?></div>
                        <p><strong>Line:</strong> <?= $error['line'] ?></p>
                    </div>
                    
                    <?php if ($fileContent): ?>
                    <h3>Code Context</h3>
                    <div class="code-block">
                        <?= $fileContent ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error['trace'])): ?>
                    <div class="stack-trace">
                        <h3>Stack Trace</h3>
                        <?php foreach (array_slice($error['trace'], 0, 10) as $index => $trace): ?>
                        <div class="trace-item">
                            <strong>#<?= $index ?></strong> 
                            <?php if (isset($trace['class'])): ?>
                                <span class="trace-function"><?= htmlspecialchars($trace['class'] . $trace['type'] . $trace['function']) ?></span>
                            <?php elseif (isset($trace['function'])): ?>
                                <span class="trace-function"><?= htmlspecialchars($trace['function']) ?></span>
                            <?php endif; ?>
                            <?php if (isset($trace['file'])): ?>
                                <br><span class="trace-file"><?= htmlspecialchars($trace['file']) ?></span>
                                <?php if (isset($trace['line'])): ?>
                                    <span class="trace-line">:<?= $trace['line'] ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Display inline error (for warnings/notices)
     */
    private static function displayInlineError($error)
    {
        ?>
        <div style="
            background: #fff3cd; 
            border: 1px solid #ffeaa7; 
            padding: 10px; 
            margin: 10px 0; 
            border-radius: 4px;
            font-family: monospace;
            font-size: 13px;
        ">
            <strong style="color: #856404;">⚠️ <?= htmlspecialchars($error['severity']) ?>:</strong>
            <span style="color: #856404;"><?= htmlspecialchars($error['message']) ?></span><br>
            <small style="color: #6c757d;">
                in <strong><?= htmlspecialchars($error['file']) ?></strong> on line <strong><?= $error['line'] ?></strong>
            </small>
        </div>
        <?php
    }
    
    /**
     * Display production error page (simple)
     */
    private static function displayProductionError()
    {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Server Error</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    background: #f8f9fa; 
                    color: #333; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    min-height: 100vh;
                }
                .error-container { 
                    text-align: center; 
                    max-width: 500px; 
                    padding: 40px 20px;
                }
                .error-icon { 
                    font-size: 72px; 
                    color: #dc3545; 
                    margin-bottom: 20px;
                }
                .error-title { 
                    font-size: 32px; 
                    color: #dc3545; 
                    margin-bottom: 15px;
                }
                .error-message { 
                    font-size: 18px; 
                    color: #6c757d; 
                    margin-bottom: 30px;
                }
                .error-actions { 
                    margin-top: 30px;
                }
                .btn { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background: #007bff; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 4px; 
                    margin: 0 10px;
                }
                .btn:hover { 
                    background: #0056b3; 
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h1 class="error-title">500</h1>
                <p class="error-message">Something went wrong on our end.</p>
                <p>We're working to fix this issue. Please try again later.</p>
                <div class="error-actions">
                    <a href="/" class="btn">Go Home</a>
                    <a href="javascript:history.back()" class="btn">Go Back</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Get severity name
     */
    private static function getSeverityName($severity)
    {
        $severities = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        return $severities[$severity] ?? 'Unknown';
    }
    
    /**
     * Format stack trace
     */
    private static function formatStackTrace($trace)
    {
        $formatted = '';
        foreach ($trace as $index => $item) {
            $formatted .= "#{$index} ";
            
            if (isset($item['class'])) {
                $formatted .= $item['class'] . $item['type'];
            }
            
            if (isset($item['function'])) {
                $formatted .= $item['function'] . '()';
            }
            
            if (isset($item['file'])) {
                $formatted .= ' in ' . $item['file'];
            }
            
            if (isset($item['line'])) {
                $formatted .= ' on line ' . $item['line'];
            }
            
            $formatted .= "\n";
        }
        
        return $formatted;
    }
}
