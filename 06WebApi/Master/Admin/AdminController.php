<?php
declare(strict_types=1);
namespace WebApi\Master\Admin;

use Base\Auth\Auth;
use Application\Master\Admin\Commands\Create\CreateAdminCommand;
use Application\Master\Admin\Commands\Update\UpdateAdminCommand;
use Application\Master\Admin\Commands\Delete\DeleteAdminCommand;
use Application\Master\Admin\Queries\GetAdminByListQuery;
use Application\Master\Admin\Queries\GetAdminByIdQuery;

class AdminController
{
    public function __construct(
        private CreateAdminCommand $createCmd,
        private UpdateAdminCommand $updateCmd,
        private DeleteAdminCommand $deleteCmd,
        private GetAdminByListQuery $getList,
        private GetAdminByIdQuery   $getById,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin']);
        $search = trim($_GET['search'] ?? '');
        $list   = $this->getList->execute($search);
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/master/admin/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin']);
        $res = $this->createCmd->execute($_POST, Auth::id());
        $_SESSION['flash'] = ['type' => $res['success'] ? 'success' : 'danger', 'msg' => $res['message']];
        $this->redirect('/master/admin');
    }

    public function update(): void
    {
        Auth::requireRole(['admin']);
        $id  = (int)($_POST['id'] ?? 0);
        $res = $this->updateCmd->execute($id, $_POST, Auth::id());
        $_SESSION['flash'] = ['type' => $res['success'] ? 'success' : 'danger', 'msg' => $res['message']];
        $this->redirect('/master/admin');
    }

    public function delete(): void
    {
        Auth::requireRole(['admin']);
        $id  = (int)($_POST['id'] ?? 0);
        $res = $this->deleteCmd->execute($id, Auth::id());
        $_SESSION['flash'] = ['type' => $res['success'] ? 'success' : 'danger', 'msg' => $res['message']];
        $this->redirect('/master/admin');
    }

    public function getById(): void
    {
        Auth::requireRole(['admin']);
        $id = (int)($_GET['id'] ?? 0);
        $e  = $this->getById->execute($id);
        header('Content-Type: application/json');
        if (!$e) { echo json_encode(null); return; }
        echo json_encode([
            'id'       => $e->getId(),
            'user_id'  => $e->getUserId(),
            'nama'     => $e->getNama(),
            'email'    => $e->getEmail(),
            'no_telp'  => $e->getNoTelp(),
            'alamat'   => $e->getAlamat(),
            'is_active'=> $e->getIsActive(),
            'username' => $e->getUser()?->getUsername(),
        ]);
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
