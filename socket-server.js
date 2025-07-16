const http = require('http');
const { Server } = require('socket.io');

const server = http.createServer();
const io = new Server(server, {
  cors: { origin: "*" }
});

io.on('connection', (socket) => {
  console.log('Client connected:', socket.id);
  // Example: listen for cart updates
  socket.on('cartUpdate', (data) => {
    io.emit('cartUpdate', data); // broadcast to all clients
  });
});

server.listen(3000, () => {
  console.log('Socket.IO server running on port 3000');
});
