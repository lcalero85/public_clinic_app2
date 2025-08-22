<?php
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportExporter {
    public static function toPDF($html, $filename = "reporte.pdf") {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, ["Attachment" => true]);
    }
}
?>