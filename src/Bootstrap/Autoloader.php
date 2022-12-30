<?php

namespace Application\Bootstrap;

class Autoloader
{
    /**
     * @var string
     */
    protected string $configPath;

    /**
     * @var string
     */
    protected string $applicationPath;

    /**
     * @var array
     */
    protected array $pathsCommands = [];

    /**
     * @var array
     */
    protected array $pathsActions = [];

    /**
     * @var array
     */
    protected array $includePaths = [];

    /**
     * @var array
     */
    protected array $actions = [];

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * @param string $configPath
     */
    public function setConfigPath(string $configPath): void
    {
        $this->configPath = $configPath;
        $this->addIncludePath($this->configPath);
    }

    /**
     * @return string
     */
    public function getApplicationPath(): string
    {
        return $this->applicationPath;
    }

    /**
     * @param string $applicationPath
     */
    public function setApplicationPath(string $applicationPath): void
    {
        $this->applicationPath = $applicationPath;
        $this->addIncludePath($this->applicationPath);
    }

    /**
     * @return array
     */
    public function getPathsCommands(): array
    {
        return $this->pathsCommands;
    }

    /**
     * @param array $pathsCommands
     */
    public function setPathsCommands(array $pathsCommands): void
    {
        $this->pathsCommands = $pathsCommands;
    }

    /**
     * @return array
     */
    public function getPathsActions(): array
    {
        return $this->pathsActions;
    }

    /**
     * @param array $pathsActions
     */
    public function setPathsActions(array $pathsActions): void
    {
        $this->pathsActions = $pathsActions;
    }

    /**
     * @return array
     */
    public function getIncludePaths(): array
    {
        return $this->includePaths;
    }

    /**
     * @param array $includePaths
     */
    public function setIncludePaths(array $includePaths): void
    {
        $this->includePaths = $includePaths;
    }

    /**
     * @param string $includePath
     * @return void
     */
    public function addIncludePath(string $includePath): void
    {
        $this->addIncludePaths([$includePath]);
    }

    /**
     * @param array $includePaths
     * @return void
     */
    public function addIncludePaths(array $includePaths): void
    {
        $includePaths = array_merge($this->includePaths, $includePaths);
        $this->setIncludePaths($includePaths);
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @param string $includePath
     * @return string
     */
    public function sanitizeIncludePath(string $includePath): string
    {
        $includePath = sprintf('%s%s%s', DIRECTORY_SEPARATOR, $includePath, DIRECTORY_SEPARATOR);

        return realpath($includePath);
    }

    /**
     * @param string $directory
     * @param $callable
     * @return void
     */
    public function loadDirectory(string $directory, $callable = null): void
    {
        $directory = sprintf('%s%s*.php', $directory, DIRECTORY_SEPARATOR);

        foreach (glob($directory) as $file) {
            $this->requireOnceFile($file);
            $pathinfo = pathinfo($file);

            if ($callable) {
                call_user_func_array($callable, [$pathinfo['filename'], $file, $directory]);
            }
        }
    }

    /**
     * @return void
     */
    public function autoloadActionsDirectory(): void
    {
        $directory = sprintf('%s%sActions', $this->applicationPath, DIRECTORY_SEPARATOR);
        $this->loadDirectory($directory);
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->updateIncludePath();
        $this->registerAutoload();
    }

    /**
     * @return void
     */
    protected function updateIncludePath(): void
    {
        $currentIncludePath = get_include_path();
        $includePath = PATH_SEPARATOR . implode(PATH_SEPARATOR, $this->includePaths) . PATH_SEPARATOR;
        $includePath = sprintf('%s%s', $currentIncludePath, $includePath);
        set_include_path($includePath);
    }

    /**
     * @return void
     */
    protected function registerAutoload(): void
    {
        spl_autoload_register([$this, 'autoload']);
    }

    /**
     * @param string $class
     * @return void
     */
    protected function autoload(string $class): void
    {
        $class = str_replace('\\', '/', $class);
        $class = str_replace('Application/', '', $class);

        $this->requireOnceClass($class);
    }

    /**
     * @param string $class
     * @return void
     */
    protected function requireOnceClass(string $class): void
    {
        $this->requireOnceFile("{$class}.php");
    }

    /**
     * @param string $filename
     * @return mixed
     */
    protected function requireOnceFile(string $filename): mixed
    {
        return require_once $filename;
    }
}