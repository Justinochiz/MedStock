<?php

namespace App\Mail;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SendOrderStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $order;
    public array $receipt;

    public function __construct($order, array $receipt = [])
    {
        $this->order = $order instanceof Collection ? $order : collect($order);
        $this->receipt = array_merge([
            'order_number' => null,
            'customer_name' => null,
            'customer_email' => null,
            'customer_phone' => null,
            'shipping_address' => null,
            'payment_method' => null,
            'status' => null,
            'date_placed' => now()->toDateTimeString(),
            'total_amount' => null,
        ], $receipt);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectOrder = $this->receipt['order_number'] ? ' #' . $this->receipt['order_number'] : '';

        return new Envelope(
            from: new Address('noreply@MedStock.test', 'my shop'),
            subject: 'Order Update' . $subjectOrder,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $total = number_format($this->order->map(function ($item) {
            return $item->quantity * $item->sell_price;
        })->sum(), 2);

        $finalTotal = $this->receipt['total_amount'] !== null
            ? number_format((float) $this->receipt['total_amount'], 2)
            : $total;

        // number_format($total->sum, 2)
        return new Content(
            view: 'email.order_status',
            with: [
                'order' => $this->order,
                'orderTotal' => $finalTotal,
                'receipt' => $this->receipt,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $computedTotal = $this->order->map(function ($item) {
            return $item->quantity * $item->sell_price;
        })->sum();

        $totalAmount = $this->receipt['total_amount'] !== null
            ? (float) $this->receipt['total_amount']
            : (float) $computedTotal;

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('email.receipt_pdf', [
            'order' => $this->order,
            'receipt' => $this->receipt,
            'orderTotal' => number_format($totalAmount, 2),
            'logoDataUri' => $this->getLogoDataUri(),
        ])->render());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfBinary = $dompdf->output();

        $orderNumber = $this->receipt['order_number'] ?: 'transaction';
        $statusRaw = strtolower(trim((string) ($this->receipt['status'] ?? '')));
        $statusSlug = in_array($statusRaw, ['delivered', 'canceled'], true)
            ? Str::slug($statusRaw)
            : '';

        $filename = $statusSlug !== ''
            ? 'receipt-' . $orderNumber . '-' . $statusSlug . '.pdf'
            : 'receipt-' . $orderNumber . '.pdf';
        $storagePath = 'receipts/' . $filename;
        $latestAliasPath = 'receipts/receipt-' . $orderNumber . '.pdf';

        // Keep a downloadable copy in public storage for admin/customer reference.
        Storage::disk('public')->put($storagePath, $pdfBinary);
        Storage::disk('public')->put($latestAliasPath, $pdfBinary);

        return [
            Attachment::fromData(fn () => $pdfBinary, $filename)
                ->withMime('application/pdf'),
        ];
    }

    private function getLogoDataUri(): ?string
    {
        $logoPath = public_path('images/medstock-logo.png');

        if (!is_file($logoPath)) {
            return null;
        }

        $content = @file_get_contents($logoPath);
        if ($content === false) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode($content);
    }
}
