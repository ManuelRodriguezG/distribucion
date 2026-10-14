<?php

class Api extends Controlador {

  /**
   * IA: Codex GPT-5
   * Fecha: 2026-10-05
   * Proposito: abrir herramienta interna read-only para diagnosticar contratos de Distribucion API.
   * Impacto: Frontend Distribucion; valida comunicacion real GET con ERP y prepara payloads sin escribir en BD.
   * Contrato: /api/distribucion/tester renderiza una vista interna; no ejecuta POST ni cambios operativos.
   */
  public function distribucion($herramienta = '') {
    if ($herramienta !== 'tester' || !$this->testerHabilitado()) {
      http_response_code(404);
      return 'Herramienta no encontrada';
    }

    $this->render('api/distribucion/tester', array(
      'titulo' => 'Tester API Distribucion',
      'api_base_url' => ERP_API_BASE_URL,
      'erp_url' => ERP_URL_RAIZ
    ));
  }

  private function testerHabilitado() {
    if (!defined('DISTRIBUCION_TESTER_API_HABILITADO') || !DISTRIBUCION_TESTER_API_HABILITADO) {
      return false;
    }
    return $this->esEntornoLocal();
  }

  private function esEntornoLocal() {
    $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
    return in_array($host, array('distribucion.artiani.com.local', 'localhost', '127.0.0.1'), true)
      || in_array($ip, array('127.0.0.1', '::1'), true);
  }
}
