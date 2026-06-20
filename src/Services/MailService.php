<?php

namespace App\Services;

use Brevo\Brevo;
use Brevo\TransactionalEmails\Requests\SendTransacEmailRequest;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestSender;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestToItem;

class MailService
{
    private Brevo $brevo;
    private string $senderEmail;
    private string $senderName;

    public function __construct(string $apiKey, string $senderEmail = 'oreshko2001@gmail.com', string $senderName = 'Jan')
    {
        $this->brevo = new Brevo($apiKey);
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    public function sendOrderConfirmation(string $toEmail, string $toName, string $orderNumber, array $items): bool
    {
        $total = array_sum(array_column($items, 'price'));

        $htmlContent = $this->buildOrderHtml($orderNumber, $items, $total);

        $request = new SendTransacEmailRequest([
            'sender' => new SendTransacEmailRequestSender([
                'email' => $this->senderEmail,
                'name' => $this->senderName,
            ]),
            'to' => [
                new SendTransacEmailRequestToItem([
                    'email' => $toEmail,
                    'name' => $toName,
                ]),
            ],
            'subject' => "Заказ №{$orderNumber} оформлен — {$this->senderName}",
            'htmlContent' => $htmlContent,
        ]);

        try {
            $this->brevo->transactionalEmails->sendTransacEmail($request);
            return true;
        } catch (\Throwable $e) {
            error_log('MailService error: ' . $e->getMessage());
            return false;
        }
    }

    private function buildOrderHtml(string $orderNumber, array $items, float $total): string
    {
        $rows = '';
        foreach ($items as $item) {
            $title = htmlspecialchars($item['title'] ?? '');
            $price = number_format((float)($item['price'] ?? 0), 0, ',', ' ');
            $rows .= "<tr><td>{$title}</td><td>{$price} ₽</td></tr>";
        }

        $totalFormatted = number_format($total, 0, ',', ' ');

        return <<<HTML
        <h1>Спасибо за покупку!</h1>
        <p>Ваш заказ №<strong>{$orderNumber}</strong> успешно оформлен.</p>
        <h2>Состав заказа:</h2>
        <table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;">
        <tr><th>Товар</th><th>Цена</th></tr>
        {$rows}
        <tr><td><strong>Итого</strong></td><td><strong>{$totalFormatted} ₽</strong></td></tr>
        </table>
        <p>Связаться с продавцами можно через чат на сайте.</p>
        HTML;
    }
}
