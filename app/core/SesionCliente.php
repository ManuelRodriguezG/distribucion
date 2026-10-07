<?php

class SesionCliente {
  public static function iniciar($perfil, $token) {
    if (session_status() === PHP_SESSION_ACTIVE) { session_regenerate_id(true); }
    $_SESSION['distribucion_cliente'] = array(
      'perfil' => self::normalizarPerfil($perfil),
      'token' => $token,
      'inicio' => time()
    );
  }

  public static function cerrar() {
    unset($_SESSION['distribucion_cliente']);
  }

  public static function autenticado() {
    return !empty($_SESSION['distribucion_cliente']['token']);
  }

  public static function token() {
    return self::autenticado() ? $_SESSION['distribucion_cliente']['token'] : null;
  }

  public static function perfil() {
    return self::autenticado() ? $_SESSION['distribucion_cliente']['perfil'] : null;
  }

  public static function actualizarPerfil($perfilParcial) {
    if (!self::autenticado() || !is_array($perfilParcial)) {
      return;
    }
    $perfilActual = self::perfil();
    $perfilActual = is_array($perfilActual) ? $perfilActual : array();
    $_SESSION['distribucion_cliente']['perfil'] = self::normalizarPerfil(array_merge($perfilActual, $perfilParcial));
  }

  public static function permisos() {
    $perfil = self::perfil();
    return is_array($perfil) && isset($perfil['permisos']) && is_array($perfil['permisos']) ? $perfil['permisos'] : array();
  }

  public static function acciones() {
    $perfil = self::perfil();
    return is_array($perfil) && isset($perfil['acciones']) && is_array($perfil['acciones']) ? $perfil['acciones'] : array();
  }

  public static function tienePermiso($permiso) {
    $permiso = trim((string) $permiso);
    return $permiso !== '' && in_array($permiso, self::permisos(), true);
  }

  public static function tieneAccion($accion, $permisoRespaldo = '') {
    if (self::tienePermiso($permisoRespaldo)) {
      return true;
    }
    $acciones = self::acciones();
    return array_key_exists($accion, $acciones) && $acciones[$accion] === true;
  }

  public static function estatus() {
    $perfil = self::perfil();
    return is_array($perfil) && !empty($perfil['estatus']) ? $perfil['estatus'] : null;
  }

  public static function tieneListaPrecio() {
    $perfil = self::perfil();
    return is_array($perfil) && !empty($perfil['id_lista_precio']);
  }

  private static function normalizarPerfil($perfil) {
    $perfil = is_array($perfil) ? $perfil : array();
    $perfil['permisos'] = self::normalizarPermisos(isset($perfil['permisos']) ? $perfil['permisos'] : array());
    $perfil['acciones'] = self::normalizarAcciones(isset($perfil['acciones']) ? $perfil['acciones'] : array());
    return $perfil;
  }

  private static function normalizarPermisos($permisos) {
    if (is_string($permisos)) {
      $json = json_decode($permisos, true);
      $permisos = is_array($json) ? $json : explode(',', $permisos);
    }
    if (!is_array($permisos)) {
      return array();
    }

    $normalizados = array();
    foreach ($permisos as $permiso) {
      if (is_array($permiso)) {
        $permiso = isset($permiso['permiso']) ? $permiso['permiso'] : (isset($permiso['clave']) ? $permiso['clave'] : '');
      }
      $permiso = trim((string) $permiso);
      if ($permiso !== '') {
        $normalizados[$permiso] = true;
      }
    }
    return array_keys($normalizados);
  }

  private static function normalizarAcciones($acciones) {
    if (is_string($acciones)) {
      $json = json_decode($acciones, true);
      $acciones = is_array($json) ? $json : array();
    }
    if (!is_array($acciones)) {
      return array();
    }

    $normalizadas = array();
    foreach ($acciones as $clave => $valor) {
      $clave = trim((string) $clave);
      if ($clave === '') { continue; }
      $normalizadas[$clave] = filter_var($valor, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
      if ($normalizadas[$clave] === null) {
        $normalizadas[$clave] = !empty($valor);
      }
    }
    return $normalizadas;
  }
}
