<?php
require_once __DIR__ . '/KontrakPresenterLowongan.php';
require_once __DIR__ . '/../models/KontrakModelLowongan.php';
require_once __DIR__ . '/../views/KontrakView.php';

class PresenterLowongan implements KontrakPresenterLowongan {
    private KontrakModelLowongan $model;
    private KontrakView $view;

    public function __construct(KontrakModelLowongan $model, KontrakView $view) {
        $this->model = $model;
        $this->view = $view;
    }

    private function buildRowHtml(int $no, Lowongan $lowongan): string {
        $id = (int)$lowongan->getId();
        $judulPosisi = htmlspecialchars($lowongan->getJudulPosisi());
        $departemen = htmlspecialchars($lowongan->getDepartemen());
        $lokasi = htmlspecialchars($lowongan->getLokasi());
        $kuota = number_format($lowongan->getKuota()) . ' Orang';
        $status = htmlspecialchars($lowongan->getStatus());

        $badgeColor = ($status === 'Dibuka') ? '#16a34a' : '#dc2626';
        $badgeHtml = "<span style='color:{$badgeColor};font-weight:600;'>{$status}</span>";

        return "<tr>
            <td class='col-id'>{$no}</td>
            <td><strong>{$judulPosisi}</strong></td>
            <td>{$departemen}</td>
            <td>{$lokasi}</td>
            <td>{$kuota}</td>
            <td>{$badgeHtml}</td>
            <td class='col-actions'>
                <a href='index.php?page=lowongan&screen=edit&id={$id}' class='btn btn-edit'>Edit</a>
                <form method='post' action='index.php?page=lowongan' style='display:inline;' onsubmit=\"return confirm('Hapus lowongan ini?')\">
                    <input type='hidden' name='page' value='lowongan'>
                    <input type='hidden' name='action' value='hapus'>
                    <input type='hidden' name='id' value='{$id}'>
                    <button type='submit' class='btn btn-delete'>Hapus</button>
                </form>
            </td>
        </tr>";
    }

    public function tampilkanLowongan(): string {
        $listLowongan = $this->model->getAllLowongan();
        $totalLowongan = count($listLowongan);

        $rowsHtml = '';
        if (empty($listLowongan)) {
            $rowsHtml = '<tr><td colspan="7" style="text-align:center;color:#6b7280;padding:24px;">Belum ada data lowongan.</td></tr>';
        } else {
            $no = 1;
            foreach ($listLowongan as $lowongan) {
                $rowsHtml .= $this->buildRowHtml($no, $lowongan);
                $no++;
            }
        }

        return $this->view->renderDaftar($rowsHtml, $totalLowongan);
    }

    public function tampilkanFormLowongan(?int $id = null): string {
        if ($id !== null) {
            $lowongan = $this->model->getLowonganById($id);
            if ($lowongan === null) {
                header("Location: index.php?page=lowongan");
                exit();
            }

            $formData = [
                'id'          => $lowongan->getId(),
                'judulPosisi' => $lowongan->getJudulPosisi(),
                'departemen'  => $lowongan->getDepartemen(),
                'lokasi'      => $lowongan->getLokasi(),
                'kuota'       => $lowongan->getKuota(),
                'status'      => $lowongan->getStatus(),
            ];
            return $this->view->renderForm("Ubah Data Lowongan", "ubah", $formData);
        }

        return $this->view->renderForm("Tambah Lowongan Baru", "tambah", []);
    }

    public function tambahLowongan(string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $this->model->addLowongan($judulPosisi, $departemen, $lokasi, $kuota, $status);
        header("Location: index.php?page=lowongan");
        exit();
    }

    public function ubahLowongan(int $id, string $judulPosisi, string $departemen, string $lokasi, int $kuota, string $status): void {
        $this->model->updateLowongan($id, $judulPosisi, $departemen, $lokasi, $kuota, $status);
        header("Location: index.php?page=lowongan");
        exit();
    }

    public function hapusLowongan(int $id): void {
        $this->model->deleteLowongan($id);
        header("Location: index.php?page=lowongan");
        exit();
    }
}
?>