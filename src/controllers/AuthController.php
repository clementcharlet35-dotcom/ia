<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\UserManager;
use App\Security\Csrf;

final class AuthController extends AbstractController
{
    public function login(): void
    {
        $this->render('login');
    }

    public function loginPost(): void
    {
        $this->requireValidCsrfToken('/login');

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->setFlash('error', 'Tous les champs sont obligatoires.');
            $this->redirect('/login');
        }

        $userManager = new UserManager();
        $user = $userManager->findByEmail($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $this->setFlash('error', 'Identifiants invalides.');
            $this->redirect('/login');
        }

        unset($user['password_hash']);
        session_regenerate_id(true);
        Csrf::regenerate();
        $_SESSION['user'] = $user;

        $this->setFlash('success', 'Connexion réussie.');
        $this->redirect('/');
    }

    public function register(): void
    {
        $this->render('register');
    }

    public function registerPost(): void
    {
        $this->requireValidCsrfToken('/register');

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
            $this->setFlash('error', 'Tous les champs sont obligatoires.');
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Email invalide.');
            $this->redirect('/register');
        }

        if ($password !== $confirmPassword) {
            $this->setFlash('error', 'Les mots de passe ne correspondent pas.');
            $this->redirect('/register');
        }

        $userManager = new UserManager();

        if ($userManager->usernameExists($username)) {
            $this->setFlash('error', 'Ce nom d\'utilisateur existe déjà.');
            $this->redirect('/register');
        }

        if ($userManager->emailExists($email)) {
            $this->setFlash('error', 'Cet email existe déjà.');
            $this->redirect('/register');
        }

        try {
            $userManager->create($username, $email, $password);
        } catch (\PDOException $exception) {
            $this->setFlash('error', 'Ce compte existe déjà.');
            $this->redirect('/register');
        }

        $this->setFlash('success', 'Inscription réussie. Connectez-vous.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->requireAuth();
        $this->requireValidCsrfToken('/');

        unset($_SESSION['user']);
        session_destroy();
        session_start();
        Csrf::regenerate();
        $this->setFlash('success', 'Déconnexion réussie.');
        $this->redirect('/');
    }
}
