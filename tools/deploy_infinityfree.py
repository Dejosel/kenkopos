#!/usr/bin/env python3
"""
Script para sincronizar y subir los módulos PHP de KenkoPOS a InfinityFree (htdocs).
Excluye Laravel, React/frontend, node_modules y entornos virtuales.
"""

import os
import sys
import ftplib
import socket

FTP_HOST = 'ftpupload.net'
FTP_PORT = 21
FTP_USER = 'if0_42272128'
FTP_PASS = '3lv7dCMYsj'
REMOTE_ROOT = '/htdocs'

# Directorios y archivos a incluir (Solo PHP Web Stack)
INCLUDED_PATHS = [
    'index.php',
    '.htaccess',
    'config',
    'app',
    'api',
    'database',
    'public'
]

EXCLUDED_PATTERNS = [
    '.DS_Store',
    '__pycache__',
    'node_modules',
    '.git',
    '*.pyc'
]

def should_exclude(filename):
    for pat in EXCLUDED_PATTERNS:
        if pat.startswith('*') and filename.endswith(pat[1:]):
            return True
        if filename == pat:
            return True
    return False

def ensure_remote_dir(ftp, remote_dir):
    parts = [p for p in remote_dir.split('/') if p]
    current = ""
    for part in parts:
        current += "/" + part
        try:
            ftp.cwd(current)
        except Exception:
            try:
                ftp.mkd(current)
                print(f"[DIR CREADO] {current}")
            except Exception as e:
                pass

def upload_file(ftp, local_path, remote_path):
    remote_dir = os.path.dirname(remote_path).replace('\\', '/')
    ensure_remote_dir(ftp, remote_dir)
    filename = os.path.basename(local_path)
    
    with open(local_path, 'rb') as f:
        print(f"  -> Subiendo: {remote_path} ({os.path.getsize(local_path)} bytes)...")
        ftp.cwd(remote_dir)
        ftp.storbinary(f'STOR {filename}', f)

def sync_directory(ftp, local_dir, remote_base):
    for root, dirs, files in os.walk(local_dir):
        # Filter directories
        dirs[:] = [d for d in dirs if not should_exclude(d)]
        
        rel_path = os.path.relpath(root, local_dir)
        if rel_path == '.':
            target_remote_dir = remote_base
        else:
            target_remote_dir = os.path.join(remote_base, rel_path).replace('\\', '/')
        
        ensure_remote_dir(ftp, target_remote_dir)
        
        for file in files:
            if should_exclude(file):
                continue
            local_file_path = os.path.join(root, file)
            remote_file_path = os.path.join(target_remote_dir, file).replace('\\', '/')
            upload_file(ftp, local_file_path, remote_file_path)

def main():
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    print("=" * 60)
    print("🚀 INICIANDO DESPLIEGUE A INFINITYFREE (PHP WEB STACK)")
    print(f"Servidor: {FTP_HOST}:{FTP_PORT}")
    print(f"Usuario:  {FTP_USER}")
    print(f"Destino:  {REMOTE_ROOT}")
    print("=" * 60)

    try:
        ftp = ftplib.FTP()
        ftp.connect(FTP_HOST, FTP_PORT, timeout=15)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.af = socket.AF_INET
        ftp.set_pasv(True)
        print("✅ Conexión FTP autenticada correctamente.")
    except Exception as e:
        print(f"❌ Error al conectar a FTP: {e}")
        sys.exit(1)

    try:
        # Asegurar directorio /htdocs
        ensure_remote_dir(ftp, REMOTE_ROOT)

        for item in INCLUDED_PATHS:
            local_item_path = os.path.join(base_dir, item)
            if not os.path.exists(local_item_path):
                print(f"⚠️ Aviso: '{item}' no existe localmente, omitiendo.")
                continue

            remote_item_path = os.path.join(REMOTE_ROOT, item).replace('\\', '/')

            if os.path.isfile(local_item_path):
                upload_file(ftp, local_item_path, remote_item_path)
            elif os.path.isdir(local_item_path):
                print(f"\n📂 Sincronizando directorio: {item} -> {remote_item_path}")
                sync_directory(ftp, local_item_path, remote_item_path)

        print("\n" + "=" * 60)
        print("✅ ¡DESPLIEGUE COMPLETADO CON ÉXITO EN INFINITYFREE!")
        print("=" * 60)
        
        # Verificar contenido final en /htdocs
        ftp.cwd(REMOTE_ROOT)
        print("\nContenido actual en /htdocs:")
        print(ftp.nlst())
        
        ftp.quit()
    except Exception as e:
        print(f"❌ Error durante la sincronización: {e}")
        try:
            ftp.quit()
        except:
            pass
        sys.exit(1)

if __name__ == '__main__':
    main()
