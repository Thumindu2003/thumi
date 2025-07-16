const http = require('http');
const socketIo = require('socket.io');

const server = http.createServer();
const io = socketIo(server, {
    cors: {
        origin: "*",
    }
});

io.on('connection', (socket) => {
    console.log('Client connected:', socket.id);
    // No need to handle anything here for now
});

// Export a function to emit cart updates (for PHP to call via HTTP)
server.on('request', (req, res) => {
    if (req.method === 'POST' && req.url === '/cart-update') {
        let body = '';
        req.on('data', chunk => body += chunk);
        req.on('end', () => {
            io.emit('cartUpdate', JSON.parse(body));
            res.writeHead(200);
            res.end('ok');
        });
    }
});

server.listen(3000, () => {
    console.log('Real-time server running on port 3000');
});
