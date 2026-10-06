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
    if ($herramienta !== 'tester') {
      http_response_code(404);
      return 'Herramienta no encontrada';
    }

    $this->render('api/distribucion/tester', array(
      'titulo' => 'Tester API Distribucion',
      'api_base_url' => ERP_API_BASE_URL,
      'erp_url' => 'http://panel.com.local'
    ));
  }
}
