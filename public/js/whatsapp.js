const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const qrcode = require('qrcode-terminal');
const fs = require('fs');

const app = express();
const port = 3000;



const client = new Client({
    puppeteer:{
        headless:false,
    },
    authStrategy: new LocalAuth({
       
    })
});


client.on('qr', qr => {
    qrcode.generate(qr, {small: true});
});

client.on('ready', () => {
    console.log('Client is ready!');
});


client.on('message_create', message => {
    if (message.body === '!ping') {
        client.sendMessage(message.from, 'pong');
    }
});

client.initialize();

app.use(express.json());

app.post('/send-message', (req, res) => {
    const { number, message } = req.body;

    const chatId = number + '@c.us';
    client.sendMessage(chatId, message)
        .then(() => {
            console.log('Pesan berhasil dikirim ke', number);
            res.send('Pesan berhasil dikirim ke WhatsApp');
        })
        .catch(err => {
            console.error('Gagal mengirim pesan:', err);
            res.status(500).send('Gagal mengirim pesan ke WhatsApp');
        });
});

app.listen(port, () => {
    console.log(`Server berjalan di http://localhost:${port}`);
});
