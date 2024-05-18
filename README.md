## Step Running The WhatsApp Blasting System

The WhatsApp Blasting System using Laravel and Library WhatsApp (https://wwebjs.dev/guide/)

- Run the command on the terminal
```bash
$ php artisan migrate
```
```bash
$ php artisan serve
```

- Run the command on the other terminal to running the queue
```bash
$ php artisan queue:work
```

- Run the command on the other terminal to running WhatsApp Service
```bash
$ cd public/js
```
```bash
$ node whatsapp.js
```
Wait for the QR to appear on the terminal, then link the device and wait for **Client is ready!** to appear. As below:
![image](https://github.com/nuraeni28/inside-app/assets/68740508/b987c6d4-93ee-4ec3-8a22-7f7608b3e90a)


## Send Blasting With API 

```http
POST /api/send-blast-message
```
- Body
Example :
```javascript
[{
    "phone": ["628987494849"],
    "message": "Example low priority",
    "priority" : "low"
},
{
    "phone": ["628567892797"],
    "message": "Example high priority",
    "priority" : "high"
}
]

```

- Responses
```javascript
{
  "message" : string,
  "success" : bool,
  "data"    : array
}
```


