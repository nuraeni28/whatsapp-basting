<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageId;

    public function __construct($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Processing queue job: ' . $this->messageId);

        // Cari pesan dengan prioritas tinggi (high) yang belum terkirim
        $highPriorityMessage = Message::where('priority', 'high')->whereNull('status')->orderBy('created_at', 'asc')->first();

        if ($highPriorityMessage) {
            $this->sendMessage($highPriorityMessage);
            $this->updateMessageStatus($highPriorityMessage);
            Log::info('High priority message sent: ' . $highPriorityMessage->id);
        } else {
            // Jika tidak ada pesan dengan prioritas tinggi yang belum terkirim,
            // kirim pesan dengan prioritas rendah (low) jika ada
            $lowPriorityMessage = Message::where('priority', 'low')->whereNull('status')->orderBy('created_at', 'asc')->first();

            if ($lowPriorityMessage) {
                $this->sendMessage($lowPriorityMessage);
                $this->updateMessageStatus($lowPriorityMessage);
                Log::info('Low priority message sent: ' . $lowPriorityMessage->id);
            }
        }
    }

    private function sendMessage($message)
    {
        try {
            // Mengambil isi pesan dari objek pesan
            $messageContent = $message->message;

            // Mengirimkan pesan menggunakan HTTP POST tanpa menyimpan respons
            $response = Http::post('http://localhost:3000/send-message', [
                'number' => $message->phone,
                'message' => $messageContent, // Mengirim hanya isi pesan
            ]);

            // Memeriksa kode status respons
            if ($response->successful()) {
                // Pesan berhasil terkirim, lakukan apa yang diperlukan
                Log::info('Message sent successfully to ' . $message->phone);
            } else {
                // Tangani kesalahan jika pesan tidak terkirim
                Log::error('Failed to send message to ' . $message->phone);
                // Anda juga bisa melempar pengecualian (exception) di sini jika diperlukan.
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika ada kesalahan dalam mengirim pesan
            Log::error('Error sending message: ' . $e->getMessage());
        }
    }

    private function updateMessageStatus($message)
    {
        // Perbarui status pesan menjadi "done"
        $message->status = 'done';
        $message->save();
    }
}
