const fs = require('fs'); // Importar módulo para manipular archivos
const cron = require('node-cron');
const path = require('path');

const scriptPath = path.join(__dirname, 'cron/script_ejecutar/notificacion_diaria.php');
const logPath = path.join(__dirname, 'cron/script_ejecutar/log_cron.txt');
const phpPath = "C:/xampp/php/php.exe";

// Función para registrar la fecha y hora en el log
function registrarEjecucion() {
    const fechaHora = new Date().toISOString();
    fs.appendFileSync(logPath, `Ejecutado en: ${fechaHora}\n`, 'utf8');
}

// Programar el cron job
// Se ejecuta cada una hora->cron.schedule('0 * * * *', () => {
//
cron.schedule('0 * * * *', () => {
    console.log('Ejecutando script PHP...');

    // Guardar la fecha de ejecución en un archivo de texto
    registrarEjecucion();

    // Ejecutar el script PHP
    require('child_process').exec(`${phpPath} "${scriptPath}"`, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error al ejecutar el script: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`Advertencia: ${stderr}`);
            return;
        }
        console.log(`Salida: ${stdout}`);
    });
});
