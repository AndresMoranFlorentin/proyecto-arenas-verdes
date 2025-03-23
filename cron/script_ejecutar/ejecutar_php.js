const fs = require('fs');
const cron = require('node-cron');
const path = require('path');
const { exec } = require('child_process');

const scriptPath = path.join(__dirname, 'notificacion_diaria.php');
const logPath = path.join(__dirname, 'log_cron.txt');
const phpPath = "C:/xampp/php/php.exe";

// Función para registrar logs
function registrarEjecucion(mensaje) {
    const fechaHora = new Date().toISOString();
    fs.appendFileSync(logPath, `[${fechaHora}] ${mensaje}\n`, 'utf8');
}

// Programar el cron job
cron.schedule('* * * * * *', () => {
    console.log('Ejecutando script PHP...');
    registrarEjecucion('Iniciando ejecución de script PHP');

    exec(`${phpPath} "${scriptPath}"`, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error al ejecutar el script: ${error.message}`);
            registrarEjecucion(`Error: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`Advertencia: ${stderr}`);
            registrarEjecucion(`Advertencia: ${stderr}`);
        }
        console.log(`Salida: ${stdout}`);
        registrarEjecucion(`Éxito: ${stdout}`);
    });
});
