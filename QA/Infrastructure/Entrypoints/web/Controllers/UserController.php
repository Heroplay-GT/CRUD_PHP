<?php

declare(strict_types=1);

namespace QA\Infrastructure\Entrypoints\Web\Controllers;

use PDOException;
use QA\Application\Ports\Out\GetUserByEmailPort;
use QA\Application\Ports\Out\UpdateUserPort;
use QA\Application\Services\Dto\Commands\DeleteUserCommand;
use QA\Application\Services\Dto\Commands\LoginCommand;
use QA\Application\Services\Dto\Queries\GetAllUsersQuery;
use QA\Application\Services\Dto\Queries\GetUserByIdQuery;
use QA\Application\Services\CreateUserService;
use QA\Application\Services\DeleteUserService;
use QA\Application\Services\GetAllUsersService;
use QA\Application\Services\GetUserByIdService;
use QA\Application\Services\LoginService;
use QA\Application\Services\UpdateUserService;
use QA\Common\Uuid;
use QA\Domain\Exceptions\InvalidCredentialsException;
use QA\Domain\Exceptions\UserAlreadyExistsException;
use QA\Domain\Exceptions\UserNotFoundException;
use QA\Domain\ValueObjects\UserEmail;
use QA\Domain\ValueObjects\UserPassword;
use QA\Infrastructure\Entrypoints\Web\Controllers\Dto\CreateUserRequest;
use QA\Infrastructure\Entrypoints\Web\Controllers\Dto\LoginWebRequest;
use QA\Infrastructure\Entrypoints\Web\Controllers\Dto\UpdateUserRequest;
use QA\Infrastructure\Entrypoints\Web\Controllers\Mapper\UserWebMapper;
use QA\Infrastructure\Entrypoints\Web\Presentation\Flash;
use QA\Infrastructure\Entrypoints\Web\Presentation\View;
use InvalidArgumentException;

/**
 * Adaptador HTTP: delega en casos de uso; persistencia vía puertos (MySQL en DI).
 */
final class UserController
{
    private const SESSION_AUTH = 'auth';

    public function __construct(
        private readonly View $view,
        private readonly UserWebMapper $mapper,
        private readonly CreateUserService $createUserUseCase,
        private readonly UpdateUserService $updateUserUseCase,
        private readonly DeleteUserService $deleteUserUseCase,
        private readonly GetUserByIdService $getUserByIdUseCase,
        private readonly GetAllUsersService $getAllUsersUseCase,
        private readonly LoginService $loginUseCase,
        private readonly GetUserByEmailPort $getUserByEmailPort,
        private readonly UpdateUserPort $updateUserPort,
    ) {
    }

    public function home(): void
    {
        echo $this->view->render('layouts/page', [
            'title' => 'Inicio',
            'content' => $this->view->render('home', [
                'title' => 'Inicio',
            ]),
        ]);
    }

    public function loginForm(): void
    {
        $errors = Flash::errors();
        $old = Flash::old();
        echo $this->view->render('layouts/page', [
            'title' => 'Iniciar sesión',
            'content' => $this->view->render('auth/login', [
                'title' => 'Iniciar sesión',
                'errors' => $errors,
                'old' => $old,
            ]),
        ]);
    }

    public function forgotForm(): void
    {
        $errors = Flash::errors();
        $old = Flash::old();
        echo $this->view->render('layouts/page', [
            'title' => 'Recuperar contraseña',
            'content' => $this->view->render('auth/forgot-password', [
                'title' => 'Recuperar contraseña',
                'errors' => $errors,
                'old' => $old,
            ]),
        ]);
    }

    public function authenticate(): void
    {
        try {
            $req = LoginWebRequest::fromPost($_POST);
            $errors = [];
            if ($req->email === '' || !filter_var($req->email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email inválido.';
            }
            if ($req->password === '') {
                $errors['password'] = 'La contraseña es obligatoria.';
            }
            if ($errors !== []) {
                Flash::setErrors($errors);
                Flash::setOld(['email' => $req->email]);
                Flash::setMessage('Corrige los errores del formulario.');
                $this->view->redirectToRoute('auth.login');
            }

            $user = $this->loginUseCase->execute(
                new LoginCommand($req->email, $req->password),
            );

            $_SESSION[self::SESSION_AUTH] = [
                'id' => $user->id()->value(),
                'name' => $user->name()->value(),
                'email' => $user->email()->value(),
                'role' => $user->role(),
            ];

            $this->view->redirectToRoute('home');
        } catch (InvalidCredentialsException) {
            Flash::setMessage('Credenciales incorrectas.');
            $this->view->redirectToRoute('auth.login');
        } catch (InvalidArgumentException $e) {
            Flash::setMessage($e->getMessage());
            $this->view->redirectToRoute('auth.login');
        }
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_AUTH]);
        session_regenerate_id(true);
        $this->view->redirectToRoute('auth.login');
    }

    public function sendForgot(): void
    {
        $emailRaw = trim((string) ($_POST['email'] ?? ''));
        if ($emailRaw === '') {
            Flash::setErrors(['email' => 'El email es obligatorio.']);
            Flash::setOld(['email' => $emailRaw]);
            Flash::setMessage('Corrige los errores del formulario.');
            $this->view->redirectToRoute('auth.forgot');
        }

        try {
            $email = new UserEmail($emailRaw);
        } catch (InvalidArgumentException) {
            Flash::setErrors(['email' => 'Email inválido.']);
            Flash::setOld(['email' => $emailRaw]);
            Flash::setMessage('Corrige los errores del formulario.');
            $this->view->redirectToRoute('auth.forgot');
        }

        $user = $this->getUserByEmailPort->getByEmail($email);
        if ($user === null) {
            Flash::setSuccess('Si el correo está registrado, recibirás instrucciones.');
            $this->view->redirectToRoute('auth.forgot');
        }

        $temp = bin2hex(random_bytes(5));
        $updated = $user->changePassword(UserPassword::fromPlainText($temp));
        $this->updateUserPort->update($updated);

        $body = $this->view->render('emails/forgot-password', [
            'temporaryPassword' => $temp,
            'userName' => $user->name()->value(),
        ]);

        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: QA Demo <noreply@localhost>',
        ]);
        @mail($emailRaw, 'Contraseña temporal (QA demo)', $body, $headers);

        Flash::setSuccess(
            'Se generó una contraseña nueva para tu cuenta. '
            . 'En local el correo casi nunca llega (revisa MailHog si lo usas). '
            . 'Contraseña temporal para entrar ahora: ' . $temp
        );
        $this->view->redirectToRoute('auth.forgot');
    }

    public function index(): void
    {
        try {
            $models = $this->getAllUsersUseCase->execute(new GetAllUsersQuery());
            $responses = array_map(fn ($m) => $this->mapper->userModelToResponse($m), $models);

            echo $this->view->render('layouts/page', [
                'title' => 'Usuarios',
                'content' => $this->view->render('users/list', [
                    'title' => 'Usuarios',
                    'users' => $responses,
                ]),
            ]);
        } catch (PDOException) {
            http_response_code(500);
            echo $this->view->render('layouts/page', [
                'title' => 'Base de datos',
                'content' => '<p class="prose">No se pudo conectar o falta la tabla. Ejecuta <code>Infrastructure/Adapters/Persistence/MySQL/schema.sql</code> en MySQL y revisa <code>config/database.php</code>.</p>',
            ]);
        }
    }

    public function createForm(): void
    {
        $errors = Flash::errors();
        $old = Flash::old();
        echo $this->view->render('layouts/page', [
            'title' => 'Nuevo usuario',
            'content' => $this->view->render('users/create', [
                'title' => 'Nuevo usuario',
                'errors' => $errors,
                'old' => $old,
            ]),
        ]);
    }

    public function store(): void
    {
        $web = CreateUserRequest::fromPost($_POST);
        $errors = [];
        if ($web->name === '') {
            $errors['name'] = 'El nombre es obligatorio.';
        }
        if ($web->email === '' || !filter_var($web->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido.';
        }
        if ($web->password === '') {
            $errors['password'] = 'La contraseña es obligatoria.';
        }
        if ($web->roleId === '') {
            $errors['role_id'] = 'El rol es obligatorio.';
        }

        if ($errors !== []) {
            Flash::setErrors($errors);
            Flash::setOld([
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            Flash::setMessage('Corrige los errores del formulario.');
            $this->view->redirectToRoute('users.create');
        }

        try {
            $command = $this->mapper->toCreateUserCommand(Uuid::v4(), $web);
            $this->createUserUseCase->execute($command);
            Flash::setSuccess('Usuario creado.');
            $this->view->redirectToRoute('users.index');
        } catch (UserAlreadyExistsException $e) {
            Flash::setMessage($e->getMessage());
            Flash::setOld([
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            $this->view->redirectToRoute('users.create');
        } catch (InvalidArgumentException $e) {
            Flash::setMessage($e->getMessage());
            Flash::setOld([
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            $this->view->redirectToRoute('users.create');
        } catch (PDOException) {
            Flash::setMessage('Error de base de datos. Verifica MySQL y schema.sql.');
            Flash::setOld([
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            $this->view->redirectToRoute('users.create');
        }
    }

    public function show(): void
    {
        $id = trim((string) ($_GET['id'] ?? ''));
        if ($id === '') {
            $this->notFound();

            return;
        }

        try {
            $user = $this->getUserByIdUseCase->execute(new GetUserByIdQuery($id));
            $response = $this->mapper->userModelToResponse($user);

            echo $this->view->render('layouts/page', [
                'title' => 'Usuario',
                'content' => $this->view->render('users/show', [
                    'title' => 'Detalle',
                    'user' => $response,
                ]),
            ]);
        } catch (UserNotFoundException) {
            $this->notFound();
        } catch (InvalidArgumentException) {
            $this->notFound();
        }
    }

    public function editForm(): void
    {
        $id = trim((string) ($_GET['id'] ?? ''));
        if ($id === '') {
            $this->notFound();

            return;
        }

        try {
            $user = $this->getUserByIdUseCase->execute(new GetUserByIdQuery($id));
            $row = [
                'id' => $user->id()->value(),
                'name' => $user->name()->value(),
                'email' => $user->email()->value(),
                'role' => $user->role(),
                'status' => $user->status(),
            ];
            $errors = Flash::errors();
            $old = Flash::old();
            if ($old !== []) {
                $row['name'] = $old['name'] ?? $row['name'];
                $row['email'] = $old['email'] ?? $row['email'];
                $row['status'] = $old['status'] ?? $row['status'];
                $row['role'] = $old['role_id'] ?? $row['role'];
            }
            $roleId = $row['role'];

            echo $this->view->render('layouts/page', [
                'title' => 'Editar usuario',
                'content' => $this->view->render('users/edit', [
                    'title' => 'Editar usuario',
                    'user' => $row,
                    'roleId' => $roleId,
                    'errors' => $errors,
                    'old' => $old,
                ]),
            ]);
        } catch (UserNotFoundException) {
            $this->notFound();
        } catch (InvalidArgumentException) {
            $this->notFound();
        }
    }

    public function update(): void
    {
        $web = UpdateUserRequest::fromPost($_POST);
        if ($web->id === '') {
            Flash::setMessage('Identificador no válido.');
            $this->view->redirectToRoute('users.index');
        }

        $errors = [];
        if ($web->name === '') {
            $errors['name'] = 'El nombre es obligatorio.';
        }
        if ($web->email === '' || !filter_var($web->email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido.';
        }
        if ($web->password !== '' && strlen($web->password) < 8) {
            $errors['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        if ($web->roleId === '') {
            $errors['role_id'] = 'El rol es obligatorio.';
        }

        if ($errors !== []) {
            Flash::setErrors($errors);
            Flash::setOld([
                'id' => $web->id,
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            Flash::setMessage('Corrige los errores del formulario.');
            $this->view->redirectToRoute('users.edit', ['id' => $web->id]);
        }

        try {
            $this->updateUserUseCase->execute($this->mapper->toUpdateUserCommand($web));
            Flash::setSuccess('Usuario actualizado.');
            $this->view->redirectToRoute('users.show', ['id' => $web->id]);
        } catch (UserNotFoundException $e) {
            Flash::setMessage($e->getMessage());
            $this->view->redirectToRoute('users.index');
        } catch (UserAlreadyExistsException $e) {
            Flash::setMessage($e->getMessage());
            Flash::setOld([
                'id' => $web->id,
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            $this->view->redirectToRoute('users.edit', ['id' => $web->id]);
        } catch (InvalidArgumentException $e) {
            Flash::setMessage($e->getMessage());
            Flash::setOld([
                'id' => $web->id,
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            $this->view->redirectToRoute('users.edit', ['id' => $web->id]);
        } catch (PDOException) {
            Flash::setMessage('Error de base de datos.');
            Flash::setOld([
                'id' => $web->id,
                'name' => $web->name,
                'email' => $web->email,
                'role_id' => $web->roleId,
                'status' => $web->status,
            ]);
            $this->view->redirectToRoute('users.edit', ['id' => $web->id]);
        }
    }

    public function destroy(): void
    {
        $id = trim((string) ($_POST['id'] ?? ''));
        if ($id === '') {
            Flash::setMessage('Identificador no válido.');
            $this->view->redirectToRoute('users.index');
        }

        try {
            $this->deleteUserUseCase->execute(new DeleteUserCommand($id));
            Flash::setSuccess('Usuario eliminado.');
            $this->view->redirectToRoute('users.index');
        } catch (UserNotFoundException $e) {
            Flash::setMessage($e->getMessage());
            $this->view->redirectToRoute('users.index');
        } catch (PDOException) {
            Flash::setMessage('Error de base de datos.');
            $this->view->redirectToRoute('users.index');
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo $this->view->render('layouts/page', [
            'title' => 'No encontrado',
            'content' => '<p>Usuario no encontrado.</p>',
        ]);
    }
}
