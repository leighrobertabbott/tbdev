<?php

namespace App\Core;

class View
{
    private string $viewsPath;
    private array $data = [];
    private ?string $layout = null;
    private array $layoutData = [];

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/../../views';
    }

    public function layout(string $layout, array $data = []): void
    {
        $this->layout = $layout;
        $this->layoutData = $data;
    }

    public function render(string $template, array $data = []): string
    {
        $this->data = array_merge($this->data, $data);
        $this->layout = null;
        $this->layoutData = [];
        
        // Add Format helper to all views
        $this->data['Format'] = \App\Core\FormatHelper::class;
        
        // Add current user to all views if not already set
        if (!isset($this->data['user'])) {
            $this->data['user'] = \App\Core\Auth::user();
        }
        
        $templateFile = $this->viewsPath . '/' . $template . '.php';
        
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View template not found: {$template}");
        }

        ob_start();
        extract($this->data);
        
        // Make Format methods available
        $Format = new \App\Core\FormatHelper();
        
        // Make $this available in views
        $view = $this;
        
        include $templateFile;
        $content = ob_get_clean();

        // If layout is specified, wrap content in layout
        if ($this->layout) {
            $layoutFile = $this->viewsPath . '/' . $this->layout . '.php';
            
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout template not found: {$this->layout}");
            }

            ob_start();
            // Merge layout data with existing data
            extract(array_merge($this->data, $this->layoutData));
            // Make content available to layout
            $content = $content;
            $view = $this;
            $Format = new \App\Core\FormatHelper();
            
            include $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }

    public function share(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public static function make(string $template, array $data = []): string
    {
        $view = new self();
        return $view->render($template, $data);
    }
}

