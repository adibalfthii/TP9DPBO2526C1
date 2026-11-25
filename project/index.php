<?php
require_once __DIR__ . '/models/TabelKandidat.php';
require_once __DIR__ . '/views/ViewKandidat.php';
require_once __DIR__ . '/presenters/PresenterKandidat.php';

require_once __DIR__ . '/models/TabelLowongan.php';
require_once __DIR__ . '/views/ViewLowongan.php';
require_once __DIR__ . '/presenters/PresenterLowongan.php';

$page = $_POST['page'] ?? $_GET['page'] ?? 'kandidat';

if ($page === 'lowongan') {
    $model = new TabelLowongan('localhost', 'mvp_kandidat_db', 'root', '');
    $view = new ViewLowongan();
    $presenter = new PresenterLowongan($model, $view);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'tambah') {
            $presenter->tambahLowongan(
                $_POST['judulPosisi'],
                $_POST['departemen'],
                $_POST['lokasi'],
                (int)$_POST['kuota'],
                $_POST['status']
            );
        } elseif ($action === 'ubah' && isset($_POST['id'])) {
            $presenter->ubahLowongan(
                (int)$_POST['id'],
                $_POST['judulPosisi'],
                $_POST['departemen'],
                $_POST['lokasi'],
                (int)$_POST['kuota'],
                $_POST['status']
            );
        } elseif ($action === 'hapus' && isset($_POST['id'])) {
            $presenter->hapusLowongan((int)$_POST['id']);
        }
        header("Location: index.php?page=lowongan");
        exit();
    } else {
        $screen = $_GET['screen'] ?? 'list';
        if ($screen === 'add') {
            echo $presenter->tampilkanFormLowongan();
        } elseif ($screen === 'edit' && isset($_GET['id'])) {
            echo $presenter->tampilkanFormLowongan((int)$_GET['id']);
        } else {
            echo $presenter->tampilkanLowongan();
        }
    }
} else {
    $model = new TabelKandidat('localhost', 'mvp_kandidat_db', 'root', '');
    $view = new ViewKandidat();
    $presenter = new PresenterKandidat($model, $view);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'tambah') {
            $presenter->tambahKandidat(
                $_POST['nama'],
                $_POST['posisi'],
                $_POST['domisili'],
                (int)$_POST['skorTes'],
                (int)$_POST['pengalamanTahun']
            );
        } elseif ($action === 'ubah' && isset($_POST['id'])) {
            $presenter->ubahKandidat(
                (int)$_POST['id'],
                $_POST['nama'],
                $_POST['posisi'],
                $_POST['domisili'],
                (int)$_POST['skorTes'],
                (int)$_POST['pengalamanTahun']
            );
        } elseif ($action === 'hapus' && isset($_POST['id'])) {
            $presenter->hapusKandidat((int)$_POST['id']);
        }
        header("Location: index.php?page=kandidat");
        exit();
    } else {
        $screen = $_GET['screen'] ?? 'list';
        if ($screen === 'add') {
            echo $presenter->tampilkanFormKandidat();
        } elseif ($screen === 'edit' && isset($_GET['id'])) {
            echo $presenter->tampilkanFormKandidat((int)$_GET['id']);
        } else {
            echo $presenter->tampilkanKandidat();
        }
    }
}
?>