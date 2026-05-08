<?php
declare(strict_types=1);
namespace WebApi\Master\User;

use Base\Auth\Auth;
use Application\Master\User\Commands\Create\CreateUserCommand;
use Application\Master\User\Commands\Update\UpdateUserCommand;
use Application\Master\User\Commands\Delete\DeleteUserCommand;
use Application\Master\User\Queries\GetUserByIdQuery;
use Application\Master\User\Queries\GetUserByListQuery;
use Shared\Master\User\Commands\Create\CreateUserRequest;
use Shared\Master\User\Commands\Update\UpdateUserRequest;
use Shared\Master\User\Commands\Delete\DeleteUserRequest;
use Shared\Master\User\Queries\GetById\GetUserByIdRequest;
use Shared\Master\User\Queries\GetByList\GetUserByListRequest;

class UserController
{
    public function __construct(
        private CreateUserCommand    $createCmd,
        private UpdateUserCommand    $updateCmd,
        private DeleteUserCommand    $deleteCmd,
        private GetUserByIdQuery     $getById,
        private GetUserByListQuery   $getList,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin']);
        $search = trim($_GET['search'] ?? '');
        $role   = $_GET['role'] ?? '';
        $list   = $this->getList->execute(new GetUserByListRequest($search, $role))->data;
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/master/user/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin']);
        $req = new CreateUserRequest(
            username: trim($_POST['username'] ?? ''),
            password: $_POST['password'] ?? '',
            role: $_POST['role'] ?? 'pelanggan',
            isActive: true,
        );
        $res = $this->createCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/user');
    }

    public function update(): void
    {
        Auth::requireRole(['admin']);
        $req = new UpdateUserRequest(
            id: (int)($_POST['id'] ?? 0),
            role: $_POST['role'] ?? 'pelanggan',
            isActive: isset($_POST['is_active']) && $_POST['is_active'] === '1',
            password: $_POST['password'] ?? '',
            userId: Auth::id(),
        );
        $res = $this->updateCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/user');
    }

    public function delete(): void
    {
        Auth::requireRole(['admin']);
        $res = $this->deleteCmd->execute(new DeleteUserRequest((int)($_POST['id'] ?? 0), Auth::id()));
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/user');
    }

    public function getById(): void
    {
        Auth::requireRole(['admin']);
        $res = $this->getById->execute(new GetUserByIdRequest((int)($_GET['id'] ?? 0)));
        header('Content-Type: application/json');
        if (!$res->found) { echo json_encode(null); return; }
        $u = $res->data;
        echo json_encode([
            'id'        => $u->getId(),
            'username'  => $u->getUsername(),
            'role'      => $u->getRole(),
            'is_active' => $u->getIsActive(),
        ]);
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
