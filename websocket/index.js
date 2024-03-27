const { default: axios } = require("axios");
const express = require("express");
var cors = require('cors');
const app = express();

app.use(cors());

const server = require('http').createServer(app).listen(4545);
const io = require('socket.io')(server, {
    cors: {
        origin: "*",
        methods: ["PUT", "GET", "POST", "DELETE", "OPTIONS"],
        credentials: false
    }
})
app.set('socketio', io)
let connections = [];

function errorLogger(error, req, res, next) {
    console.error(error);
    next(error);
}
function errorResponder(error, req, res, next) {
    if (error.status) {
        return res.status(error.status).json({
            success: false,
            errMsg: error.message
        });
    }
}
function failSafeHandler(error, req, res, next) {
    return res.status(500).json({
        success: false,
    });
}

app.use(express.json({ extended: false }));
app.use(errorLogger)
app.use(errorResponder)
app.use(failSafeHandler)
app.listen(process.env.PORT, () => console.log(`Server listening to port ${5143}`));

app.post('/msg/ws', async (req, res) => {
    const to = req.body.to
    const user = req.body.user
    const audios = req.body.audios
    const body = {
        user,
        audios
    }
    io.to(to).emit('message', body)
    return res.status(200).json({
        success: true
    })
})


io.on('connection', async (socket) => {
    await axios.post('https://api.artps.ir/api/v1/socket', {
        username: socket.handshake.query.username,
        socketId: socket.id
    })
})
io.on('error', (error) => {
    console.log(error)
})
