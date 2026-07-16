<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Security\Csrf;

abstract class AbstractController
{
    protected function render(string $template, array $params = []): void
    {
        extract($params, EXTR_SKIP);
        $templateDirectory = $this->templateDirectory();
        $templatePath = $this->resolveTemplatePath($templateDirectory, $template . '.phtml');
        $layoutPath = $this->resolveTemplatePath($templateDirectory, 'layout.phtml');

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        require $layoutPath;
    }

    private function templateDirectory(): string
    {
        $srcDirectory = dirname(__DIR__);

        foreach (['templates', 'Templates'] as $directoryName) {
            $candidate = $srcDirectory . DIRECTORY_SEPARATOR . $directoryName;

            if (is_dir($candidate)) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Dossier des templates introuvable.');
    }

    private function resolveTemplatePath(string $templateDirectory, string $fileName): string
    {
        $path = $templateDirectory . DIRECTORY_SEPARATOR . $fileName;

        if (is_file($path)) {
            return $path;
        }

        $lowerFileName = strtolower($fileName);
        $entries = scandir($templateDirectory) ?: [];

        foreach ($entries as $entry) {
            if (strtolower($entry) === $lowerFileName) {
                return $templateDirectory . DIRECTORY_SEPARATOR . $entry;
            }
        }

        throw new \RuntimeException("Template introuvable : {$fileName}");
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    protected function csrfField(): string
    {
        return Csrf::field();
    }

    protected function requireValidCsrfToken(string $redirectPath): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? null)) {
            $this->setFlash('error', 'Session expirée, veuillez recommencer.');
            $this->redirect($redirectPath);
        }
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            $this->setFlash('error', 'Veuillez vous connecter.');
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            $this->setFlash('error', 'Accès réservé à l’administrateur.');
            $this->redirect('/');
        }
    }
}
