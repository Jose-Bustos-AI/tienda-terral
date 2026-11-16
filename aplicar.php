<?php
/**
 * Pone al día el módulo Terral de una tienda ya instalada, sin reinstalarla:
 * copia los archivos de la carpeta "terral" de al lado sobre modules/terral y
 * ejecuta los ajustes de la versión. No recibe ningún dato y se puede repetir.
 */
require dirname(__DIR__) . '/config/config.inc.php';

header('Content-Type: text/plain; charset=utf-8');

$origen = __DIR__ . '/terral';
$destino = _PS_MODULE_DIR_ . 'terral';
if (!is_dir($origen) || !is_dir($destino)) {
    exit('No encuentro el módulo.');
}

$copiados = 0;
$archivos = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($origen, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($archivos as $archivo) {
    $relativa = substr($archivo->getPathname(), strlen($origen));
    if ($archivo->isDir()) {
        if (!is_dir($destino . $relativa)) {
            mkdir($destino . $relativa, 0755, true);
        }
        continue;
    }
    if (copy($archivo->getPathname(), $destino . $relativa)) {
        ++$copiados;
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($destino . $relativa, true);
        }
    }
}

$modulo = Module::getInstanceByName('terral');
if (!$modulo || !method_exists($modulo, 'actualizar')) {
    exit($copiados . ' archivos copiados, pero el módulo no responde.');
}

echo $copiados . ' archivos | ' . $modulo->actualizar();
