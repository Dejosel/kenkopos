#!/usr/bin/env python3
"""
Script de compilación integral para la evidencia SENA:
GA9-220501096-AA1-EV01 - Taller sobre codificación de módulos del software (Las pruebas de software)
"""

import os
import re
import shutil
import zipfile
import subprocess

def escape_html(text):
    return text.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')

def format_inline(text):
    # Bold **text**
    text = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', text)
    # Inline code `code`
    text = re.sub(r'`([^`]+)`', r'<code>\1</code>', text)
    # Links [text](url)
    text = re.sub(r'\[([^\]]+)\]\(([^)]+)\)', r'<a href="\2">\1</a>', text)
    # Badges
    text = text.replace('✅ PASS', '<span class="badge-pass">✅ PASS</span>')
    text = text.replace('✅ Aprobado', '<span class="badge-pass">✅ Aprobado</span>')
    return text

def render_table(rows):
    if len(rows) < 2:
        return ""
    header_cols = [c.strip() for c in rows[0].split('|')[1:-1]]
    
    start_row = 1
    if len(rows) > 1 and '---' in rows[1]:
        start_row = 2

    html = ['<table>', '<thead>', '<tr>']
    for h in header_cols:
        html.append(f'<th>{format_inline(h)}</th>')
    html.extend(['</tr>', '</thead>', '<tbody>'])

    for r in rows[start_row:]:
        cols = [c.strip() for c in r.split('|')[1:-1]]
        html.append('<tr>')
        for c in cols:
            html.append(f'<td>{format_inline(c)}</td>')
        html.append('</tr>')

    html.extend(['</tbody>', '</table>'])
    return '\n'.join(html)

def generate_html(md_path, html_path):
    with open(md_path, 'r', encoding='utf-8') as f:
        content = f.read()

    html_lines = []
    lines = content.split('\n')
    in_table = False
    table_rows = []
    in_code = False
    code_block = []
    code_lang = ""

    for line in lines:
        # Code blocks
        if line.startswith('```'):
            if in_code:
                in_code = False
                code_text = '\n'.join(code_block)
                html_lines.append(f'<pre><code class="language-{code_lang}">{escape_html(code_text)}</code></pre>')
                code_block = []
                code_lang = ""
            else:
                in_code = True
                code_lang = line[3:].strip()
            continue

        if in_code:
            code_block.append(line)
            continue

        # Tables
        if line.strip().startswith('|') and line.strip().endswith('|'):
            if not in_table:
                in_table = True
                table_rows = []
            table_rows.append(line.strip())
            continue
        else:
            if in_table:
                in_table = False
                html_lines.append(render_table(table_rows))
                table_rows = []

        # Images ![alt](src)
        img_match = re.match(r'!\[(.*?)\]\((.*?)\)', line.strip())
        if img_match:
            alt, src = img_match.groups()
            html_lines.append(f'''<div class="img-container">
                <img src="{src}" alt="{alt}" />
            </div>''')
            continue

        # Headers
        if line.startswith('# '):
            html_lines.append(f'<h1>{format_inline(line[2:])}</h1>')
        elif line.startswith('## '):
            html_lines.append(f'<h2>{format_inline(line[3:])}</h2>')
        elif line.startswith('### '):
            html_lines.append(f'<h3>{format_inline(line[4:])}</h3>')
        elif line.startswith('#### '):
            html_lines.append(f'<h4>{format_inline(line[5:])}</h4>')
        elif line.startswith('- ') or line.startswith('* '):
            html_lines.append(f'<li>{format_inline(line[2:])}</li>')
        elif line.strip() == '---':
            html_lines.append('<hr/>')
        elif line.strip() == '':
            html_lines.append('<p></p>')
        else:
            html_lines.append(f'<p>{format_inline(line)}</p>')

    if in_table:
        html_lines.append(render_table(table_rows))

    body_html = '\n'.join(html_lines)

    full_html = f"""<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>GA9-220501096-AA1-EV01 - Taller Pruebas de Software</title>
<style>
    @page {{
        size: A4;
        margin: 18mm 15mm 18mm 15mm;
        @bottom-right {{
            content: "Página " counter(page);
            font-size: 8.5pt;
            color: #64748b;
        }}
    }}
    body {{
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        color: #0f172a;
        line-height: 1.55;
        font-size: 10.5pt;
        background: #ffffff;
        margin: 0;
        padding: 10px;
    }}
    h1 {{
        color: #047857;
        font-size: 18pt;
        border-bottom: 2.5px solid #047857;
        padding-bottom: 6px;
        margin-top: 20px;
        margin-bottom: 12px;
    }}
    h2 {{
        color: #0284c7;
        font-size: 13.5pt;
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 4px;
        margin-top: 18px;
        margin-bottom: 8px;
        page-break-after: avoid;
    }}
    h3 {{
        color: #1e293b;
        font-size: 11.5pt;
        margin-top: 14px;
        margin-bottom: 6px;
        page-break-after: avoid;
    }}
    h4 {{
        color: #334155;
        font-size: 10.5pt;
        margin-top: 12px;
        margin-bottom: 4px;
        page-break-after: avoid;
    }}
    p {{
        margin-bottom: 8px;
        text-align: justify;
    }}
    hr {{
        border: 0;
        border-top: 1px solid #e2e8f0;
        margin: 16px 0;
    }}
    table {{
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0;
        font-size: 9pt;
        page-break-inside: avoid;
    }}
    th, td {{
        border: 1px solid #cbd5e1;
        padding: 6px 8px;
        text-align: left;
    }}
    th {{
        background-color: #f1f5f9;
        color: #0f172a;
        font-weight: 600;
    }}
    tr:nth-child(even) {{
        background-color: #f8fafc;
    }}
    pre {{
        background: #0f172a;
        color: #f8fafc;
        padding: 10px 14px;
        border-radius: 6px;
        font-family: 'SF Mono', Menlo, Monaco, Consolas, monospace;
        font-size: 8.5pt;
        line-height: 1.45;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        page-break-inside: avoid;
        margin: 10px 0;
    }}
    code {{
        font-family: 'SF Mono', Menlo, Monaco, Consolas, monospace;
        background: #e2e8f0;
        color: #0f172a;
        padding: 1px 4px;
        border-radius: 3px;
        font-size: 9pt;
    }}
    pre code {{
        background: transparent;
        color: inherit;
        padding: 0;
    }}
    li {{
        margin-bottom: 4px;
    }}
    .img-container {{
        text-align: center;
        margin: 14px 0;
        page-break-inside: avoid;
    }}
    .img-container img {{
        max-width: 96%;
        height: auto;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }}
    .badge-pass {{
        background: #dcfce7;
        color: #15803d;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 8.5pt;
        display: inline-block;
    }}
</style>
</head>
<body>
{body_html}
</body>
</html>"""

    with open(html_path, 'w', encoding='utf-8') as f:
        f.write(full_html)
    print(f"HTML generado exitosamente en: {html_path}")

def main():
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    doc_dir = os.path.join(base_dir, 'Documentacion')
    md_file = os.path.join(doc_dir, 'GA9_AA1_EV01_Taller_Pruebas.md')
    html_file = os.path.join(doc_dir, 'GA9_AA1_EV01_Taller_Pruebas.html')
    pdf_file = os.path.join(doc_dir, 'GA9_AA1_EV01_Taller_Pruebas.pdf')

    print("--> 1. Generando archivo HTML estilizado...")
    generate_html(md_file, html_file)

    print("--> 2. Compilando PDF con Chrome Headless...")
    chrome_bin = "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"
    cmd = [
        chrome_bin,
        "--headless",
        "--disable-gpu",
        f"--print-to-pdf={pdf_file}",
        html_file
    ]
    res = subprocess.run(cmd, capture_output=True)
    if os.path.exists(pdf_file) and os.path.getsize(pdf_file) > 0:
        print(f"✅ PDF generado exitosamente ({os.path.getsize(pdf_file)} bytes): {pdf_file}")
    else:
        print(f"❌ Error al generar PDF: {res.stderr}")
        return

    # 3. Crear Carpeta de Entrega SENA
    deliverable_dir = os.path.join(base_dir, 'JOSE_LUIS_HERNANDEZ_GA9_AA1_EV01')
    if os.path.exists(deliverable_dir):
        shutil.rmtree(deliverable_dir)
    os.makedirs(deliverable_dir, exist_ok=True)

    print("--> 3. Preparando carpeta del entregable...")
    # Copiar PDF, Markdown, HTML
    shutil.copy2(pdf_file, os.path.join(deliverable_dir, 'GA9-220501096-AA1-EV01_Taller_Pruebas_Software.pdf'))
    shutil.copy2(md_file, os.path.join(deliverable_dir, 'GA9-220501096-AA1-EV01_Taller_Pruebas_Software.md'))
    shutil.copy2(html_file, os.path.join(deliverable_dir, 'GA9-220501096-AA1-EV01_Taller_Pruebas_Software.html'))

    # Copiar Documentacion/assets con las capturas
    shutil.copytree(os.path.join(doc_dir, 'assets'), os.path.join(deliverable_dir, 'assets'))

    # Copiar suites de prueba y colecciones
    shutil.copytree(os.path.join(base_dir, 'tests'), os.path.join(deliverable_dir, 'tests'))
    shutil.copy2(os.path.join(base_dir, 'phpunit.xml'), deliverable_dir)
    shutil.copy2(os.path.join(base_dir, 'KenkoPOS API - Productos.postman_collection.json'), deliverable_dir)
    shutil.copy2(os.path.join(base_dir, 'KenkoPOS.postman_collection.json'), deliverable_dir)

    # Copiar código fuente esencial para comprobación
    for folder in ['api', 'app', 'config', 'database', 'public']:
        src_path = os.path.join(base_dir, folder)
        dst_path = os.path.join(deliverable_dir, folder)
        if os.path.exists(src_path):
            shutil.copytree(src_path, dst_path, ignore=shutil.ignore_patterns('__pycache__', '.DS_Store', '*.pyc'))

    # Crear archivo de texto con el repositorio y resumen
    repo_txt = f"""SERVICIO NACIONAL DE APRENDIZAJE – SENA
Programa: Tecnólogo en Análisis y Desarrollo de Software (ADSO)
Evidencia: GA9-220501096-AA1-EV01 - Taller sobre codificación de módulos del software (Pruebas de software)
Aprendiz: Jose Luis Hernandez
Instructora: Luz Karime Castellanos
Fecha: Septiembre 6 de 2026

Repositorio Oficial en GitHub:
https://github.com/Dejosel/kenkopos

Rama Principal:
main

Versión del Sistema:
v1.5.0 (PHP Web Stack + Testing Suite)

Contenido de la Entrega:
1. GA9-220501096-AA1-EV01_Taller_Pruebas_Software.pdf (Documento técnico con desarrollo completo y capturas)
2. tests/ (Suites de pruebas unitarias e integración en PHPUnit 12)
3. phpunit.xml (Configuración de pruebas de backend)
4. KenkoPOS API - Productos.postman_collection.json (Colección de pruebas automáticas con 36 aserciones)
5. assets/ (Capturas de pantalla reales de la ejecución de PHPUnit, Newman CLI, Postman UI y el POS)
6. Código fuente del proyecto KenkoPOS (api, app, config, database, public)

Instrucciones para Ejecutar las Pruebas:
- Pruebas Unitarias e Integración (PHPUnit):
  laravel-app/vendor/bin/phpunit --configuration phpunit.xml

- Pruebas Automatizadas de API REST (Newman CLI):
  php -S 127.0.0.1:8555 &
  npx newman run "KenkoPOS API - Productos.postman_collection.json" --env-var "base_url=http://127.0.0.1:8555"
"""
    with open(os.path.join(deliverable_dir, 'Repositorio_y_Instrucciones.txt'), 'w', encoding='utf-8') as f:
        f.write(repo_txt)

    # 4. Empaquetar a ZIP
    print("--> 4. Creando archivo ZIP comprimido...")
    zip_filename = os.path.join(base_dir, 'JOSE_LUIS_HERNANDEZ_GA9_AA1_EV01.zip')
    with zipfile.ZipFile(zip_filename, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk(deliverable_dir):
            for file in files:
                file_path = os.path.join(root, file)
                arcname = os.path.relpath(file_path, base_dir)
                zipf.write(file_path, arcname)

    print(f"✅ Paquete ZIP generado exitosamente ({os.path.getsize(zip_filename)} bytes): {zip_filename}")
    print("=" * 60)
    print("🎉 ¡TODOS LOS ENTREGABLES FUERON GENERADOS CORRECTAMENTE!")
    print("=" * 60)

if __name__ == '__main__':
    main()
