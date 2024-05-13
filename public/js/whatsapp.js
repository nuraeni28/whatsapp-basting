const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const qrcode = require('qrcode-terminal');

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
            console.log('Succesfully send message to', number);
            res.send({ message: 'Succesfully send message to ' + number });
        })
        .catch(err => {
            console.error('Failed send message:', err);
            res.status(500).send('Failed send message to WhatsApp');
        });
});

app.listen(port, () => {
    console.log(`Server running in http://localhost:${port}`);
});
