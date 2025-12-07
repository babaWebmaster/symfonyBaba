const chokidar = require('chokidar');
const WebSocket = require('ws');

function connect() {
    return new Promise(resolve => {
        const ws = new WebSocket('ws://localhost:8080/ws');

        ws.on('open', () => {
            console.log("📡 WebSocket connecté à Webpack Dev Server");
            resolve(ws);
        });

        ws.on('error', () => {
            console.log("⏳ Webpack pas prêt, nouvelle tentative dans 1s...");
            setTimeout(() => resolve(connect()), 1000);
        });
    });
}

(async () => {
    const ws = await connect();

    const watcher = chokidar.watch('./templates', {
        ignored: /(^|[\/\\])\../,
        persistent: true,
    });

    watcher.on('change', path => {
        console.log(`🔄 Twig modifié : ${path}`);
        if (ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({ type: 'reload' }));
        }
    });
})();
