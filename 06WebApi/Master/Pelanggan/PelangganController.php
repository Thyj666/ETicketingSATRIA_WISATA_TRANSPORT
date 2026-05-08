<?php
declare(strict_types=1);
namespace WebApi\Master\Pelanggan;

use Base\Auth\Auth;
use Application\Master\Pelanggan\Commands\Create\CreatePelangganCommand;
use Application\Master\Pelanggan\Commands\Update\UpdatePelangganCommand;
use Application\Master\Pelanggan\Commands\Delete\DeletePelangganCommand;
use Application\Master\Pelanggan\Queries\GetPelangganByListQuery;
use Application\Master\Pelanggan\Queries\GetPelangganByIdQuery;

class PelangganController
{
    public function __construct(
        private CreatePelangganCommand $createCmd,
        private UpdatePelangganCommand $updateCmd,
        private DeletePelangganCommand $deleteCmd,
        private GetPelangganByListQuery $getList,
        private GetPelangganByIdQuery   $getById,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin']);
        $search = trim($_GET['search'] ?? '');
        $list   = $this->getList->execute($search);
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/master/pelanggan/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin']);
        $res = $this->createCmd->execute($_POST, Auth::id());
        $_SESSION['flash'] = ['type' => $res['success'] ? 'success' : 'danger', 'msg' => $res['message']];
        $this->redirect('/master/pelanggan');
    }

    public function update(): void
    {
        Auth::requireRole(['admin']);
        $id  = (int)($_POST['id'] ?? 0);
        $res = $this->updateCmd->execute($id, $_POST, Auth::id());
        $_SESSION['flash'] = ['type' => $res['success'] ? 'success' : 'danger', 'msg' => $res['message']];
        $this->redirect('/master/pelanggan');
    }

    public function delete(): void
    {
        Auth::requireRole(['admin']);
        $id  = (int)($_POST['id'] ?? 0);
        $res = $this->deleteCmd->execute($id, Auth::id());
        $_SESSION['flash'] = ['type' => $res['success'] ? 'success' : 'danger', 'msg' => $res['message']];
        $this->redirect('/master/pelanggan');
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
